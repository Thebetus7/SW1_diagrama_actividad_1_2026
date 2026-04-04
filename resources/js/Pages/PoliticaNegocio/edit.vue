<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import * as go from 'gojs';

const props = defineProps({
    politica: Object,
    logJson: String
});

const form = useForm({
    json: props.logJson || ''
});

const diagramDiv = ref(null);
let myDiagram = null;

// Parámetros de GoJS
const MINLENGTH = 200;
const MINBREADTH = 20;

// Utilidades layout GoJS
function computeMinPoolSize(pool) {
    let len = MINLENGTH;
    pool.memberParts.each(lane => {
        if (!(lane instanceof go.Group)) return;
        const holder = lane.placeholder;
        if (holder !== null) {
            len = Math.max(len, holder.actualBounds.height);
        }
    });
    return new go.Size(NaN, len);
}

function computeLaneSize(lane) {
    const sz = computeMinLaneSize(lane);
    if (lane.isSubGraphExpanded) {
        const holder = lane.placeholder;
        if (holder !== null) {
            const hsz = holder.actualBounds;
            sz.width = Math.ceil(Math.max(sz.width, hsz.width));
        }
    }
    const hdr = lane.findObject('HEADER');
    if (hdr !== null) sz.width = Math.ceil(Math.max(sz.width, hdr.actualBounds.width));
    return sz;
}

function computeMinLaneSize(lane) {
    if (!lane.isSubGraphExpanded) return new go.Size(1, MINLENGTH);
    return new go.Size(MINBREADTH, MINLENGTH);
}

function relayoutDiagram(diagram) {
    diagram.layout.invalidateLayout();
    diagram.findTopLevelGroups().each(g => {
        if (g.category === 'Pool') g.layout.invalidateLayout();
    });
    diagram.layoutDiagram();
}

function relayoutLanes() {
    myDiagram.nodes.each(lane => {
        if (!(lane instanceof go.Group)) return;
        if (lane.category === 'Pool') return;
        lane.layout.isValidLayout = false;
    });
    myDiagram.layoutDiagram();
}

class LaneResizingTool extends go.ResizingTool {
    isLengthening() { return this.handle.alignment === go.Spot.Bottom; }
    computeMinPoolSize() {
        const lane = this.adornedObject.part;
        const msz = computeMinLaneSize(lane);
        if (this.isLengthening()) {
            const sz = computeMinPoolSize(lane.containingGroup);
            msz.height = Math.max(msz.height, sz.height);
        } else {
            const sz = computeLaneSize(lane);
            msz.width = Math.max(msz.width, sz.width);
            msz.height = Math.max(msz.height, sz.height);
        }
        return msz;
    }
    resize(newr) {
        const lane = this.adornedObject.part;
        if (this.isLengthening()) {
            lane.containingGroup.memberParts.each(lane => {
                if (!(lane instanceof go.Group)) return;
                const shape = lane.resizeObject;
                if (shape !== null) shape.height = newr.height;
            });
        } else {
            super.resize(newr);
        }
        relayoutDiagram(this.diagram);
    }
}

class PoolLayout extends go.GridLayout {
    constructor() {
        super();
        this.cellSize = new go.Size(1, 1);
        this.wrappingColumn = Infinity;
        this.wrappingWidth = Infinity;
        this.isRealtime = false;
        this.alignment = go.GridAlignment.Position;
        this.comparer = (a, b) => {
            const ax = a.location.x;
            const bx = b.location.x;
            if (isNaN(ax) || isNaN(bx)) return 0;
            if (ax < bx) return -1;
            if (ax > bx) return 1;
            return 0;
        };
        this.boundsComputation = (part, layout, rect) => {
            part.getDocumentBounds(rect);
            rect.inflate(-1, -1);
            return rect;
        };
    }
    doLayout(coll) {
        const diagram = this.diagram;
        if (diagram === null) return;
        diagram.startTransaction('PoolLayout');
        const pool = this.group;
        if (pool !== null && pool.category === 'Pool') {
            const minsize = computeMinPoolSize(pool);
            pool.memberParts.each(lane => {
                if (!(lane instanceof go.Group)) return;
                if (lane.category !== 'Pool') {
                    const shape = lane.resizeObject;
                    if (shape !== null) {
                        const sz = computeLaneSize(lane);
                        shape.width = !isNaN(shape.width) ? Math.max(shape.width, sz.width) : sz.width;
                        shape.height = isNaN(shape.height) ? minsize.height : Math.max(shape.height, minsize.height);
                        const cell = lane.resizeCellSize;
                        if (!isNaN(shape.width) && !isNaN(cell.width) && cell.width > 0) shape.width = Math.ceil(shape.width / cell.width) * cell.width;
                        if (!isNaN(shape.height) && !isNaN(cell.height) && cell.height > 0) shape.height = Math.ceil(shape.height / cell.height) * cell.height;
                    }
                }
            });
        }
        super.doLayout(coll);
        diagram.commitTransaction('PoolLayout');
    }
}

function stayInGroup(part, pt, gridpt) {
    const grp = part.containingGroup;
    if (grp === null) return pt;
    const back = grp.resizeObject;
    if (back === null) return pt;
    if (part.diagram.lastInput.shift) return pt;
    const r = back.getDocumentBounds();
    const b = part.actualBounds;
    const loc = part.location;
    const m = grp.placeholder.padding;
    const x = Math.max(r.x + m.left, Math.min(pt.x, r.right - m.right - b.width - 1)) + (loc.x - b.x);
    const y = Math.max(r.y + m.top, Math.min(pt.y, r.bottom - m.bottom - b.height - 1)) + (loc.y - b.y);
    return new go.Point(x, y);
}

function groupStyle(grp) {
    grp.layerName = 'Background';
    grp.background = 'transparent';
    grp.movable = true;
    grp.copyable = false;
    grp.avoidable = false;
    grp.minLocation = new go.Point(-Infinity, NaN);
    grp.maxLocation = new go.Point(Infinity, NaN);
    grp.bindTwoWay('location', 'loc', go.Point.parse, go.Point.stringify);
}

function updateCrossLaneLinks(group) {
    group.findExternalLinksConnected().each(l => {
        l.visible = l.fromNode.isVisible() && l.toNode.isVisible();
    });
}

onMounted(() => {
    myDiagram = new go.Diagram(diagramDiv.value, {
        resizingTool: new LaneResizingTool(),
        layout: new PoolLayout(),
        mouseDragOver: e => {
            if (!e.diagram.selection.all(n => n instanceof go.Group)) {
                e.diagram.currentCursor = 'not-allowed';
            }
        },
        mouseDrop: e => {
            if (!e.diagram.selection.all(n => n instanceof go.Group)) {
                e.diagram.currentTool.doCancel();
            }
        },
        'commandHandler.copiesGroupKey': true,
        SelectionMoved: e => relayoutDiagram(e.diagram),
        SelectionCopied: e => relayoutDiagram(e.diagram),
        'animationManager.isEnabled': false,
        'undoManager.isEnabled': true
    });

    myDiagram.nodeTemplate =
        new go.Node('Auto', { dragComputation: stayInGroup })
            .bindTwoWay('location', 'loc', go.Point.parse, go.Point.stringify)
            .add(
                new go.Shape('Rectangle', { fill: 'white', portId: '', cursor: 'pointer', fromLinkable: true, toLinkable: true }),
                new go.TextBlock({ margin: 5 }).bind('text', 'key')
            );

    myDiagram.groupTemplateMap.add('Lane',
        new go.Group('Vertical')
            .apply(groupStyle)
            .set({
                selectionObjectName: 'SHAPE',
                resizable: true,
                resizeObjectName: 'SHAPE',
                layout: new go.LayeredDigraphLayout({
                    isInitial: false, isOngoing: false,
                    direction: 90, columnSpacing: 10,
                    layeringOption: go.LayeredDigraphLayering.LongestPathSource
                }),
                computesBoundsAfterDrag: true,
                computesBoundsIncludingLinks: false,
                computesBoundsIncludingLocation: true,
                handlesDragDropForMembers: true,
                mouseDrop: (e, grp) => {
                    if (!e.shift) return;
                    if (!e.diagram.selection.any(n => n instanceof go.Group)) {
                        const ok = grp.addMembers(grp.diagram.selection, true);
                        if (ok) updateCrossLaneLinks(grp);
                        else grp.diagram.currentTool.doCancel();
                    } else {
                        e.diagram.currentTool.doCancel();
                    }
                },
                subGraphExpandedChanged: grp => {
                    const shp = grp.resizeObject;
                    if (grp.diagram.undoManager.isUndoingRedoing) return;
                    if (grp.isSubGraphExpanded) {
                        shp.width = grp.data.savedBreadth;
                    } else {
                        if (!isNaN(shp.width)) grp.diagram.model.set(grp.data, 'savedBreadth', shp.width);
                        shp.width = NaN;
                    }
                    updateCrossLaneLinks(grp);
                }
            })
            .bindTwoWay('isSubGraphExpanded', 'expanded')
            .add(
                new go.Panel('Horizontal', { name: 'HEADER', angle: 0, alignment: go.Spot.Center })
                    .add(
                        new go.Panel('Horizontal')
                            .bindObject('visible', 'isSubGraphExpanded')
                            .add(
                                new go.Shape('Diamond', { width: 8, height: 8, fill: 'white' }).bind('fill', 'color'),
                                new go.TextBlock({ font: 'bold 13pt sans-serif', editable: true, margin: new go.Margin(2, 0, 0, 0) })
                                    .bindTwoWay('text')
                            ),
                        go.GraphObject.build('SubGraphExpanderButton', { margin: 5 })
                    ),
                new go.Panel('Auto')
                    .add(
                        new go.Shape('Rectangle', { name: 'SHAPE', fill: 'white' })
                            .bind('fill', 'color')
                            .bindTwoWay('desiredSize', 'size', go.Size.parse, go.Size.stringify),
                        new go.Placeholder({ padding: 12, alignment: go.Spot.TopLeft }),
                        new go.TextBlock({ name: 'LABEL', font: 'bold 13pt sans-serif', editable: true, angle: 90, alignment: go.Spot.TopLeft, margin: new go.Margin(4, 0, 0, 2) })
                            .bindObject('visible', 'isSubGraphExpanded', e => !e)
                            .bindTwoWay('text')
                    )
            )
    );

    myDiagram.groupTemplateMap.get('Lane').resizeAdornmentTemplate =
        new go.Adornment('Spot')
            .add(
                new go.Placeholder(),
                new go.Shape({ alignment: go.Spot.Bottom, desiredSize: new go.Size(50, 7), fill: 'lightblue', stroke: 'dodgerblue', cursor: 'row-resize' })
                    .bindObject('visible', '', ad => ad.adornedPart && ad.adornedPart.isSubGraphExpanded),
                new go.Shape({ alignment: go.Spot.Right, desiredSize: new go.Size(7, 50), fill: 'lightblue', stroke: 'dodgerblue', cursor: 'col-resize' })
                    .bindObject('visible', '', ad => ad.adornedPart && ad.adornedPart.isSubGraphExpanded)
            );

    myDiagram.groupTemplateMap.add('Pool',
        new go.Group('Auto')
            .apply(groupStyle)
            .set({
                layout: new PoolLayout({ cellSize: new go.Size(1, 1), spacing: new go.Size(0, 0) })
            })
            .add(
                new go.Shape({ fill: 'white' }).bind('fill', 'color'),
                new go.Panel('Table', { defaultRowSeparatorStroke: 'black' })
                    .add(
                        new go.Panel('Horizontal', { row: 0, angle: 0 })
                            .add(new go.TextBlock({ font: 'bold 16pt sans-serif', editable: true, margin: new go.Margin(2, 0, 0, 0) }).bindTwoWay('text')),
                        new go.Placeholder({ row: 1 })
                    )
            )
    );

    myDiagram.linkTemplate =
        new go.Link({ routing: go.Routing.AvoidsNodes, corner: 5, relinkableFrom: true, relinkableTo: true })
            .add(
                new go.Shape(),
                new go.Shape({ toArrow: 'Standard' })
            );

    if (props.logJson) {
        myDiagram.model = go.Model.fromJson(props.logJson);
    } else {
        // Fallback model
        myDiagram.model = new go.GraphLinksModel();
    }
    
    myDiagram.delayInitialization(relayoutDiagram);
});

// Guardar
const saveDiagram = () => {
    form.json = myDiagram.model.toJson();
    form.put(route('politica_negocio.update', props.politica.id), {
        preserveScroll: true,
        onSuccess: () => alert('Diagrama guardado exitosamente.')
    });
};

const addActivity = () => {
    if (!myDiagram) return;
    myDiagram.startTransaction('add node');
    const newNodeData = { text: "Nueva Act.", group: "Lane1" };
    myDiagram.model.addNodeData(newNodeData);
    myDiagram.commitTransaction('add node');
};

const addLane = () => {
    if (!myDiagram) return;
    myDiagram.startTransaction('add lane');
    const newLaneData = { text: "Nuevo Carril", isGroup: true, category: "Lane", group: "Pool1", color: "lightgrey" };
    myDiagram.model.addNodeData(newLaneData);
    myDiagram.commitTransaction('add lane');
    relayoutLanes();
};

</script>

<template>
    <AppLayout :title="'Editando: ' + politica.nombre">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Diagramador: {{ politica.nombre }}
                </h2>
                <div class="flex space-x-2">
                    <PrimaryButton @click="saveDiagram" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Guardar Cambios
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Barra de herramientas base -->
                <div class="bg-white p-4 shadow-sm sm:rounded-lg mb-4 flex space-x-4 border border-gray-200">
                    <button @click="addActivity" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded shadow-sm text-sm font-medium">
                        + Agregar Actividad
                    </button>
                    <button @click="addLane" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded shadow-sm text-sm font-medium">
                        + Agregar Carril
                    </button>
                    <button @click="relayoutLanes" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded shadow-sm text-sm font-medium">
                        Re-organizar
                    </button>
                </div>

                <!-- GoJS Container -->
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden border border-gray-200">
                    <div ref="diagramDiv" style="width: 100%; height: 75vh;" class="bg-gray-50"></div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

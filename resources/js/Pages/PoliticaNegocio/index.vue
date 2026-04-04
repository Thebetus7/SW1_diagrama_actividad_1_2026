<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PoliticasTable from '@/Components/PoliticaNegocio/PoliticasTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    politicas: Array,
    usuarios: Array,   // todos los usuarios del sistema
});

// ─── Modal: Crear política ───────────────────────────────────────────────────
const isCreating = ref(false);

const form = useForm({
    nombre: '',
    descripcion: '',
    estado: 'borrador'
});

const openCreateModal = () => {
    form.reset();
    isCreating.value = true;
};

const submitCreate = () => {
    form.post(route('politica_negocio.store'), {
        onSuccess: () => { isCreating.value = false; }
    });
};

// ─── Eliminar (softdelete) ────────────────────────────────────────────────────
const deleteForm = useForm({});

const eliminarPolitica = (politica) => {
    if (!confirm(`¿Eliminar la política "${politica.nombre}"? Se podrá restaurar luego.`)) return;
    deleteForm.delete(route('politica_negocio.destroy', politica.id), {
        preserveScroll: true,
    });
};

// ─── Modal: Colaboradores ─────────────────────────────────────────────────────
const showColabModal = ref(false);
const politicaSeleccionada = ref(null);

/**
 * La lista de todos los usuarios del sistema MENOS los que ya son colaboradores
 * de la política seleccionada.
 */
const usuariosDisponibles = computed(() => {
    if (!politicaSeleccionada.value) return props.usuarios;
    const colaboradorIds = (politicaSeleccionada.value.colaboradores ?? []).map(c => c.id_user_colab);
    return props.usuarios.filter(u => !colaboradorIds.includes(u.id));
});

const colaboradoresActuales = computed(() => {
    return politicaSeleccionada.value?.colaboradores ?? [];
});

const openColabModal = (politica) => {
    politicaSeleccionada.value = politica;
    showColabModal.value = true;
};

const colabForm = useForm({ id_user_colab: null, estado: 'leer' });

const agregarColaborador = (userId) => {
    colabForm.id_user_colab = userId;
    colabForm.post(route('colaborador.store', politicaSeleccionada.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Actualiza la lista local del modal sin recargar la página
            const usuario = props.usuarios.find(u => u.id === userId);
            politicaSeleccionada.value.colaboradores.push({
                id: Date.now(), // temporal hasta reload
                id_user_colab: userId,
                id_politica: politicaSeleccionada.value.id,
                estado: colabForm.estado,
                usuario,
            });
        }
    });
};

const quitarColaborador = (colab) => {
    router.delete(route('colaborador.destroy', {
        politica_negocio: politicaSeleccionada.value.id,
        colaborador: colab.id,
    }), {
        preserveScroll: true,
        onSuccess: () => {
            politicaSeleccionada.value.colaboradores =
                politicaSeleccionada.value.colaboradores.filter(c => c.id !== colab.id);
        }
    });
};
</script>

<template>
    <AppLayout title="Políticas de Negocio">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Catálogo de Políticas de Negocio y Diagramas
                </h2>
                <PrimaryButton @click="openCreateModal">
                    Crear Política
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <PoliticasTable :politicas="politicas">
                    <!-- Acciones minimalistas: solo iconos con tooltip al hover -->
                    <template #acciones="{ politica }">
                        <div class="flex items-center justify-end space-x-1">

                            <!-- Editar diagrama -->
                            <Link
                                :href="route('politica_negocio.edit', politica.id)"
                                title="Editar Diagrama"
                                class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors duration-150"
                            >
                                <!-- Lápiz -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </Link>

                            <!-- Colaboradores -->
                            <button
                                @click="openColabModal(politica)"
                                title="Gestionar Colaboradores"
                                class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors duration-150"
                            >
                                <!-- Personas -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </button>

                            <!-- Eliminar (softdelete) -->
                            <button
                                @click="eliminarPolitica(politica)"
                                title="Eliminar Política"
                                class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors duration-150"
                            >
                                <!-- Papelera -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>

                        </div>
                    </template>
                </PoliticasTable>
            </div>
        </div>

        <!-- ──────────────────────────────────────────────────────────────────
             Modal: Crear nueva política
        ─────────────────────────────────────────────────────────────────── -->
        <DialogModal :show="isCreating" @close="isCreating = false">
            <template #title>Nueva Política de Negocio</template>

            <template #content>
                <div class="mt-4">
                    <InputLabel for="nombre" value="Nombre de la Política" />
                    <TextInput id="nombre" v-model="form.nombre" type="text" class="mt-1 block w-full" required />
                    <InputError :message="form.errors.nombre" class="mt-2" />
                </div>
                <div class="mt-4">
                    <InputLabel for="descripcion" value="Descripción Opcional" />
                    <textarea
                        id="descripcion"
                        v-model="form.descripcion"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                        rows="3"
                    ></textarea>
                    <InputError :message="form.errors.descripcion" class="mt-2" />
                </div>
                <div class="mt-4">
                    <InputLabel for="estado" value="Estado Inicial" />
                    <select
                        id="estado"
                        v-model="form.estado"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                    >
                        <option value="borrador">Borrador</option>
                        <option value="publicado">Publicado</option>
                    </select>
                    <InputError :message="form.errors.estado" class="mt-2" />
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="isCreating = false">Cancelar</SecondaryButton>
                <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submitCreate">
                    Aceptar y Dibujar Diagrama
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- ──────────────────────────────────────────────────────────────────
             Modal: Gestionar colaboradores
        ─────────────────────────────────────────────────────────────────── -->
        <DialogModal :show="showColabModal" @close="showColabModal = false" max-width="2xl">
            <template #title>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Colaboradores — <span class="text-indigo-600">{{ politicaSeleccionada?.nombre }}</span></span>
                </div>
            </template>

            <template #content>
                <div class="grid grid-cols-2 gap-4 mt-2">

                    <!-- PANEL IZQUIERDO: Usuarios del sistema disponibles -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-100 px-4 py-2 border-b border-gray-200">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Usuarios del sistema</p>
                        </div>
                        <div class="p-3 space-y-1 max-h-64 overflow-y-auto">
                            <div
                                v-for="user in usuariosDisponibles"
                                :key="user.id"
                                class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-indigo-50 transition-colors"
                            >
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ user.name }}</p>
                                    <p class="text-xs text-gray-400">{{ user.email }}</p>
                                </div>
                                <button
                                    @click="agregarColaborador(user.id)"
                                    title="Añadir como colaborador"
                                    class="ml-2 p-1 rounded-full bg-indigo-100 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                            <p v-if="usuariosDisponibles.length === 0" class="text-xs text-center text-gray-400 py-4 italic">
                                Todos los usuarios ya son colaboradores.
                            </p>
                        </div>
                    </div>

                    <!-- PANEL DERECHO: Colaboradores actuales del diagrama -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-100 px-4 py-2 border-b border-gray-200">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Unidos a mi diagrama</p>
                        </div>
                        <div class="p-3 space-y-1 max-h-64 overflow-y-auto">
                            <div
                                v-for="colab in colaboradoresActuales"
                                :key="colab.id"
                                class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-red-50 transition-colors"
                            >
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ colab.usuario?.name }}</p>
                                    <span :class="[
                                        'text-xs px-1.5 py-0.5 rounded-full font-medium',
                                        colab.estado === 'editar' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'
                                    ]">
                                        {{ colab.estado }}
                                    </span>
                                </div>
                                <button
                                    @click="quitarColaborador(colab)"
                                    title="Quitar colaborador"
                                    class="ml-2 p-1 rounded-full bg-red-100 text-red-500 hover:bg-red-500 hover:text-white transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                            </div>
                            <p v-if="colaboradoresActuales.length === 0" class="text-xs text-center text-gray-400 py-4 italic">
                                Aún no hay colaboradores en este diagrama.
                            </p>
                        </div>
                    </div>

                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="showColabModal = false">Cerrar</SecondaryButton>
            </template>
        </DialogModal>

    </AppLayout>
</template>

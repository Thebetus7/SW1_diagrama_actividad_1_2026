<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    politicas: {
        type: Array,
        required: true,
        default: () => []
    }
});

// Buscador de nombre de política
const search = ref('');

const politicasFiltradas = computed(() => {
    if (!search.value.trim()) return props.politicas;
    return props.politicas.filter(p =>
        p.nombre.toLowerCase().includes(search.value.toLowerCase())
    );
});
</script>

<template>
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

        <!-- Buscador -->
        <div class="px-6 pt-5 pb-4 border-b border-gray-200">
            <div class="relative max-w-sm">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                    <!-- Icono lupa -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                </span>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar política..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none transition"
                />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <!-- THEAD más oscuro -->
                <thead class="bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-200 uppercase">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-200 uppercase">
                            Nombre de Política
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-200 uppercase">
                            Estado
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-200 uppercase">
                            Colaboradores
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-200 uppercase">
                            Actualización
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold tracking-wider text-right text-gray-200 uppercase">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Filas alternas: par=blanco, impar=gris tenue -->
                    <tr
                        v-for="(politica, index) in politicasFiltradas"
                        :key="politica.id"
                        :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                        class="border-b border-gray-100 hover:bg-indigo-50 transition-colors duration-150"
                    >
                        <!-- ID -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">
                            #{{ politica.id }}
                        </td>

                        <!-- Nombre + descripción -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ politica.nombre }}</div>
                            <div class="text-xs text-gray-400 truncate max-w-xs">{{ politica.descripcion }}</div>
                        </td>

                        <!-- Badge estado -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="[
                                'px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full',
                                politica.estado === 'publicado'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-amber-100 text-amber-700'
                            ]">
                                {{ politica.estado }}
                            </span>
                        </td>

                        <!-- Avatares de colaboradores -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div v-if="politica.colaboradores && politica.colaboradores.length > 0" class="flex items-center space-x-1">
                                <template v-for="(colab, ci) in politica.colaboradores.slice(0, 3)" :key="colab.id">
                                    <span
                                        :title="colab.usuario?.name"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 border border-indigo-200"
                                    >
                                        {{ colab.usuario?.name?.split(' ')[0] ?? '?' }}
                                    </span>
                                </template>
                                <span
                                    v-if="politica.colaboradores.length > 3"
                                    class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600"
                                >
                                    +{{ politica.colaboradores.length - 3 }}
                                </span>
                            </div>
                            <span v-else class="text-xs text-gray-400 italic">Sin colaboradores</span>
                        </td>

                        <!-- Fecha -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                            {{ new Date(politica.updated_at).toLocaleDateString('es-PE') }}
                        </td>

                        <!-- Slot de acciones (iconos minimalistas) -->
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <slot name="acciones" :politica="politica"></slot>
                        </td>
                    </tr>

                    <!-- Sin resultados de búsqueda -->
                    <tr v-if="politicasFiltradas.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400 italic">
                            {{ search ? 'No se encontraron políticas con ese nombre.' : 'No hay políticas registradas.' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

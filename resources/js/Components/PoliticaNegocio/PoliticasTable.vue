<script setup>
import { computed } from 'vue';

const props = defineProps({
    politicas: {
        type: Array,
        required: true,
        default: () => []
    }
});
</script>

<template>
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6 overflow-x-auto bg-white border-b border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Nombre de Política
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Estado
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Última Actualización
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="politica in politicas" :key="politica.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ politica.id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ politica.nombre }}
                            </div>
                            <div class="text-sm text-gray-500 truncate max-w-xs">
                                {{ politica.descripcion }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="[
                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                politica.estado === 'publicado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                            ]">
                                {{ politica.estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ new Date(politica.updated_at).toLocaleDateString() }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                            <!-- Slot dinámico para las acciones, pasándole la "politica" concreta -->
                            <slot name="acciones" :politica="politica"></slot>
                        </td>
                    </tr>
                    <tr v-if="politicas.length === 0">
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No se encontraron políticas registradas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

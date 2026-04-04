<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PoliticasTable from '@/Components/PoliticaNegocio/PoliticasTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    politicas: Array
});

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
        onSuccess: () => {
            isCreating.value = false;
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
                <!-- Uso de nuestro componente encapsulado para la tabla -->
                <PoliticasTable :politicas="politicas">
                    <!-- Definimos el slot de acciones desde acá según los requerimientos -->
                    <template #acciones="{ politica }">
                        <Link
                            :href="route('politica_negocio.edit', politica.id)"
                            class="text-indigo-600 hover:text-indigo-900 mx-2"
                        >
                            Editar Diagrama
                        </Link>
                        <!-- Aquí podríamos poner botones para eliminar, duplicar, dependiendo del rol en el futuro -->
                    </template>
                </PoliticasTable>
            </div>
        </div>

        <!-- Modal de creación -->
        <DialogModal :show="isCreating" @close="isCreating = false">
            <template #title>
                Nueva Política de Negocio
            </template>

            <template #content>
                 <div class="mt-4">
                    <InputLabel for="nombre" value="Nombre de la Política" />
                    <TextInput
                        id="nombre"
                        v-model="form.nombre"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
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
                <SecondaryButton @click="isCreating = false">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submitCreate"
                >
                    Aceptar y Dibujar Diagrama
                </PrimaryButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

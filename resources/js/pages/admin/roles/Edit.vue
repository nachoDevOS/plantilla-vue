<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const permissions = page.props.permissions || [];
const role = page.props.role || { id: null, name: '', permissions: [] };

const form = useForm({ name: role.name, permissions: role.permissions });

function submit() {
    form.put(`/admin/roles/${role.id}`);
}
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4 dark:text-white">Editar rol</h1>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border dark:border-gray-700">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                <input v-model="form.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-900 dark:border-gray-700" />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permisos</label>
                <div class="mt-2 grid grid-cols-2 gap-2">
                    <label v-for="p in permissions" :key="p.id" class="inline-flex items-center gap-2">
                        <input type="checkbox" :value="p.name" v-model="form.permissions" class="rounded" />
                        <span class="text-sm dark:text-gray-200">{{ p.name }}</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-2">
                <button @click.prevent="submit" class="btn btn-primary">Guardar</button>
                <a href="/admin/roles" class="btn">Cancelar</a>
            </div>
        </div>
    </div>
</template>

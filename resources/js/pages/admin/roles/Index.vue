<script setup lang="ts">
import { usePage, Inertia } from '@inertiajs/vue3';
import { computed } from 'vue';
const page = usePage();
const roles = computed(() => page.props.roles || []);

function removeRole(id: number) {
    if (!confirm('¿Eliminar rol?')) return;
    Inertia.delete(`/admin/roles/${id}`);
}
</script>

<template>
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles</h1>
            <a href="/admin/roles/create" class="btn btn-primary">Crear rol</a>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <table class="w-full table-auto">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Permisos</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-4 py-4 font-medium text-gray-900 dark:text-white">{{ role.name }}</td>
                        <td class="px-4 py-4 text-gray-500 dark:text-gray-400">
                            <span v-if="role.permissions.length === 0" class="text-xs text-gray-400 dark:text-gray-500">—</span>
                            <ul v-else class="flex flex-wrap gap-2">
                                <li v-for="p in role.permissions" :key="p" class="text-xs rounded-full bg-gray-100 px-2 py-1 dark:bg-gray-700/50 dark:text-gray-200">{{ p }}</li>
                            </ul>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <a :href="`/admin/roles/${role.id}/edit`" class="text-sm text-primary-600 dark:text-primary-400 mr-3">Editar</a>
                            <button @click="removeRole(role.id)" class="text-sm text-red-600">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

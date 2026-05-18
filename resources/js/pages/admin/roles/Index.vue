<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { roles as rolesIndex } from '@/routes/admin';
import { create, destroy, edit } from '@/routes/admin/roles';

type Role = {
    id: number;
    name: string;
    permissions: string[];
};

const { roles = [] } = defineProps<{
    roles?: Role[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Roles', href: rolesIndex() }],
    },
});

function removeRole(role: Role) {
    if (!confirm(`Eliminar el rol "${role.name}"?`)) {
        return;
    }

    router.delete(destroy.url(role.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Roles" />

    <div class="flex flex-col gap-6 p-4">
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Roles
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Administra los roles y permisos disponibles para el sistema.
                </p>
            </div>

            <Button as-child>
                <Link :href="create()">
                    <Plus class="h-4 w-4" />
                    Crear rol
                </Link>
            </Button>
        </div>

        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"
        >
            <table class="w-full table-auto">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-5 py-3.5 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400"
                        >
                            Nombre
                        </th>
                        <th
                            class="px-5 py-3.5 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400"
                        >
                            Permisos
                        </th>
                        <th
                            class="px-5 py-3.5 text-right text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400"
                        >
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr
                        v-for="role in roles"
                        :key="role.id"
                        class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/30"
                    >
                        <td
                            class="px-5 py-4 font-medium text-gray-900 dark:text-white"
                        >
                            {{ role.name }}
                        </td>
                        <td class="px-5 py-4 text-gray-500 dark:text-gray-400">
                            <span
                                v-if="role.permissions.length === 0"
                                class="text-xs text-gray-400 dark:text-gray-500"
                            >
                                Sin permisos
                            </span>
                            <ul v-else class="flex flex-wrap gap-2">
                                <li
                                    v-for="permission in role.permissions"
                                    :key="permission"
                                    class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700 dark:bg-gray-700/50 dark:text-gray-200"
                                >
                                    {{ permission }}
                                </li>
                            </ul>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <Button as-child variant="ghost" size="sm">
                                    <Link :href="edit(role.id)">
                                        <Pencil class="h-4 w-4" />
                                        Editar
                                    </Link>
                                </Button>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    class="text-red-600 hover:text-red-700 dark:text-red-400"
                                    @click="removeRole(role)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                    Eliminar
                                </Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="roles.length === 0">
                        <td
                            colspan="3"
                            class="px-5 py-12 text-center text-sm text-gray-500 dark:text-gray-400"
                        >
                            Todavía no hay roles registrados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import type { User } from '@/types';

type PaginatedUsers = {
    data: User[];
    current_page: number;
    last_page: number;
    total: number;
    from: number;
    to: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
};

const { users } = defineProps<{
    users: PaginatedUsers;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/admin/dashboard' },
            { title: 'Usuarios', href: '/admin/users' },
        ],
    },
});

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function paginationLabel(label: string) {
    return label.replace('&laquo;', '‹').replace('&raquo;', '›');
}
</script>

<template>
    <Head title="Usuarios" />

    <div class="flex flex-col gap-6 p-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Usuarios</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ users.total }} {{ users.total === 1 ? 'usuario registrado' : 'usuarios registrados' }}
                </p>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <table class="w-full min-w-[600px] text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-700/50">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Nombre
                        </th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Email
                        </th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Registro
                        </th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Estado
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr
                        v-for="user in users.data"
                        :key="user.id"
                        class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/30"
                    >
                        <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">
                            {{ user.name }}
                        </td>
                        <td class="px-5 py-4 text-gray-500 dark:text-gray-400">
                            {{ user.email }}
                        </td>
                        <td class="px-5 py-4 text-gray-500 dark:text-gray-400">
                            {{ formatDate(user.created_at) }}
                        </td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="
                                    user.email_verified_at
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                                "
                            >
                                {{ user.email_verified_at ? 'Verificado' : 'Sin verificar' }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="4" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500">
                            No hay usuarios registrados aún.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="users.last_page > 1"
            class="flex flex-col items-center justify-between gap-3 sm:flex-row"
        >
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Mostrando {{ users.from }} – {{ users.to }} de {{ users.total }}
            </p>
            <div class="flex items-center gap-1">
                <template
                    v-for="(link, i) in users.links"
                    :key="i"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-lg px-2 text-sm transition-colors"
                        :class="[
                            link.active
                                ? 'bg-gray-900 font-semibold text-white dark:bg-white dark:text-gray-900'
                                : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700',
                        ]"
                    >
                        {{ paginationLabel(link.label) }}
                    </Link>
                    <span
                        v-else
                        class="inline-flex h-8 min-w-[2rem] cursor-not-allowed items-center justify-center rounded-lg px-2 text-sm text-gray-300 transition-colors dark:text-gray-600"
                    >
                        {{ paginationLabel(link.label) }}
                    </span>
                </template>
            </div>
        </div>
    </div>
</template>

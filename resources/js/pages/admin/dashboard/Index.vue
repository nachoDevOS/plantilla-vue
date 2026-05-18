<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Users, TrendingUp, Globe } from 'lucide-vue-next';

const {
    stats = { totalUsers: 0, newUsersThisMonth: 0, activeSessions: 0 },
} = defineProps<{
    stats?: {
        totalUsers: number;
        newUsersThisMonth: number;
        activeSessions: number;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: '/admin/dashboard' }],
    },
});

const statCards = [
    {
        label: 'Total usuarios',
        value: stats.totalUsers,
        icon: Users,
        description: 'usuarios registrados',
    },
    {
        label: 'Nuevos este mes',
        value: stats.newUsersThisMonth,
        icon: TrendingUp,
        description: 'en los últimos 30 días',
    },
    {
        label: 'Sesiones activas',
        value: stats.activeSessions,
        icon: Globe,
        description: 'sesiones en este momento',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6 p-4">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Resumen general del sistema.
            </p>
        </div>

        <!-- Stats cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="stat in statCards"
                :key="stat.label"
                class="flex flex-col gap-2 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="flex items-start justify-between">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ stat.label }}
                    </p>
                    <component :is="stat.icon" class="h-5 w-5 text-gray-400" />
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ stat.value }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    {{ stat.description }}
                </p>
            </div>
        </div>

        <!-- Content placeholder -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-white">Actividad reciente</h2>
            </div>
            <div class="px-6 py-10 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Agrega aquí gráficos, tablas de actividad reciente u otro contenido relevante.
                </p>
            </div>
        </div>
    </div>
</template>

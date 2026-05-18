<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import { edit } from '@/routes/appearance';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Configuración de apariencia',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Configuración de apariencia" />

    <h1 class="sr-only">Configuración de apariencia</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Apariencia"
            description="Personaliza el tema de la interfaz"
        />
        <AppearanceTabs scope="admin" />
    </div>
</template>

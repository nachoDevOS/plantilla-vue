<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

const stack = [
    { name: 'Laravel', description: 'Framework PHP para el backend, rutas y autenticación.' },
    { name: 'Inertia.js', description: 'Puente entre Laravel y Vue 3 sin necesitar una API REST.' },
    { name: 'Vue 3', description: 'Framework JavaScript reactivo para la interfaz de usuario.' },
    { name: 'Tailwind CSS v4', description: 'Framework CSS utility-first para los estilos.' },
    { name: 'Vite', description: 'Bundler y servidor de desarrollo ultrarrápido.' },
];
</script>

<template>
    <Head title="Acerca de" />

    <!-- Hero de sección -->
    <section class="border-b border-gray-200 px-4 py-16 sm:py-24 dark:border-gray-800">
        <div class="mx-auto max-w-3xl">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white">
                Acerca de esta plantilla
            </h1>
            <p class="mt-6 text-lg leading-relaxed text-gray-600 dark:text-gray-400">
                Una plantilla de inicio completa construida con Laravel + Vue 3, diseñada para
                acelerar el desarrollo de aplicaciones web modernas con panel de administración y
                sitio público.
            </p>
        </div>
    </section>

    <!-- Propósito -->
    <section class="px-4 py-16">
        <div class="mx-auto max-w-3xl space-y-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">¿Para qué sirve?</h2>
                <p class="mt-4 leading-relaxed text-gray-600 dark:text-gray-400">
                    Esta plantilla proporciona una base lista para producción que incluye todo lo
                    necesario para empezar un nuevo proyecto: autenticación completa, gestión de
                    usuarios, configuración de perfil, modo oscuro y una arquitectura clara separando
                    el sitio público del panel privado.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Arquitectura</h2>
                <p class="mt-4 leading-relaxed text-gray-600 dark:text-gray-400">
                    El proyecto está dividido en dos secciones bien diferenciadas:
                </p>
                <ul class="mt-4 space-y-3">
                    <li class="flex items-start gap-3">
                        <span class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gray-900 text-xs text-white dark:bg-white dark:text-gray-900">
                            1
                        </span>
                        <div>
                            <strong class="text-gray-900 dark:text-white">Sitio público</strong>
                            <span class="text-gray-600 dark:text-gray-400">
                                — accesible por cualquier visitante, con su propio layout (navbar y
                                footer).
                            </span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gray-900 text-xs text-white dark:bg-white dark:text-gray-900">
                            2
                        </span>
                        <div>
                            <strong class="text-gray-900 dark:text-white">Panel de administración</strong>
                            <span class="text-gray-600 dark:text-gray-400">
                                — protegido por autenticación, con sidebar de navegación y
                                breadcrumbs.
                            </span>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Stack -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stack tecnológico</h2>
                <div class="mt-6 divide-y divide-gray-200 rounded-xl border border-gray-200 dark:divide-gray-800 dark:border-gray-800">
                    <div
                        v-for="item in stack"
                        :key="item.name"
                        class="flex items-start gap-4 px-5 py-4"
                    >
                        <span class="min-w-[120px] text-sm font-semibold text-gray-900 dark:text-white">
                            {{ item.name }}
                        </span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ item.description }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

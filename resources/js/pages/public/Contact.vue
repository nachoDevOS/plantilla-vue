<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    message: '',
});

function submit() {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Contacto" />

    <section class="px-4 py-16 sm:py-24">
        <div class="mx-auto grid max-w-5xl gap-16 lg:grid-cols-2">
            <!-- Info -->
            <div>
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white">
                    Contacto
                </h1>
                <p class="mt-6 leading-relaxed text-gray-600 dark:text-gray-400">
                    ¿Tienes alguna pregunta o sugerencia? Rellena el formulario y te responderemos lo
                    antes posible.
                </p>
                <div class="mt-10 space-y-4">
                    <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                        <span class="text-xl">📧</span>
                        <span>contacto@miapp.com</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                        <span class="text-xl">📍</span>
                        <span>Madrid, España</span>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <form class="space-y-6" @submit.prevent="submit">
                <!-- Nombre -->
                <div>
                    <label
                        for="name"
                        class="block text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autocomplete="name"
                        placeholder="Tu nombre"
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-gray-400 dark:focus:ring-gray-700"
                    />
                    <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-500">
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="tu@email.com"
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-gray-400 dark:focus:ring-gray-700"
                    />
                    <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-500">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Mensaje -->
                <div>
                    <label
                        for="message"
                        class="block text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Mensaje <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="message"
                        v-model="form.message"
                        rows="6"
                        required
                        placeholder="¿En qué podemos ayudarte?"
                        class="mt-1.5 block w-full resize-none rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-gray-400 dark:focus:ring-gray-700"
                    />
                    <p v-if="form.errors.message" class="mt-1.5 text-xs text-red-500">
                        {{ form.errors.message }}
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-gray-700 disabled:opacity-50 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                >
                    {{ form.processing ? 'Enviando...' : 'Enviar mensaje' }}
                </button>
            </form>
        </div>
    </section>
</template>

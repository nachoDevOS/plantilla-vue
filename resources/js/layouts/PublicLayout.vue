<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Moon, Sun } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Toaster } from '@/components/ui/sonner';
import { useAppearance } from '@/composables/useAppearance';

const page = usePage();
const user = computed(() => page.props.auth?.user);

// La landing `/` trae su propio header/footer.
// Las otras paginas publicas, como `/about` y `/contact`, usan este layout completo.
const isStandaloneLanding = computed(() => page.url.split('?')[0] === '/');
const mobileMenuOpen = ref(false);

const { resolvedAppearance, updateAppearance } = useAppearance('public');

function toggleTheme() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}

const navLinks = [
    { title: 'Inicio', href: '/' },
    { title: 'Acerca de', href: '/about' },
    { title: 'Contacto', href: '/contact' },
];
</script>

<template>
    <slot v-if="isStandaloneLanding" />

    <div
        v-else
        class="flex min-h-screen flex-col bg-white text-gray-900 dark:bg-gray-950 dark:text-white"
    >
        <header
            class="sticky top-0 z-50 border-b border-gray-200 bg-white/90 backdrop-blur-sm dark:border-gray-800 dark:bg-gray-950/90"
        >
            <div
                class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
            >
                <Link
                    href="/"
                    class="flex items-center gap-2 text-lg font-bold tracking-tight text-gray-900 transition-opacity hover:opacity-80 dark:text-white"
                >
                    Solución Digital
                </Link>

                <nav class="hidden items-center gap-8 md:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="text-sm font-medium text-gray-600 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                    >
                        {{ link.title }}
                    </Link>
                </nav>

                <div class="hidden items-center gap-3 md:flex">
                    <button
                        type="button"
                        :title="
                            resolvedAppearance === 'dark'
                                ? 'Cambiar a claro'
                                : 'Cambiar a oscuro'
                        "
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                        @click="toggleTheme"
                    >
                        <Sun
                            v-if="resolvedAppearance === 'dark'"
                            class="h-4 w-4"
                        />
                        <Moon v-else class="h-4 w-4" />
                    </button>

                    <Link
                        v-if="user"
                        href="/admin/dashboard"
                        class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                    >
                        Panel de admin
                    </Link>
                    <Link
                        v-else
                        href="/login"
                        class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                    >
                        Iniciar sesión
                    </Link>
                </div>

                <button
                    type="button"
                    class="flex items-center justify-center rounded-lg p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 md:hidden dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                    :aria-label="mobileMenuOpen ? 'Cerrar menú' : 'Abrir menú'"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                >
                    <svg
                        v-if="!mobileMenuOpen"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                    <svg
                        v-else
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <div
                v-if="mobileMenuOpen"
                class="border-t border-gray-200 bg-white px-4 py-4 md:hidden dark:border-gray-800 dark:bg-gray-950"
            >
                <nav class="flex flex-col gap-1">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        @click="mobileMenuOpen = false"
                    >
                        {{ link.title }}
                    </Link>
                </nav>

                <div
                    class="mt-4 flex flex-col gap-2 border-t border-gray-200 pt-4 dark:border-gray-800"
                >
                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        @click="toggleTheme"
                    >
                        <Sun
                            v-if="resolvedAppearance === 'dark'"
                            class="h-4 w-4"
                        />
                        <Moon v-else class="h-4 w-4" />
                        {{
                            resolvedAppearance === 'dark'
                                ? 'Modo claro'
                                : 'Modo oscuro'
                        }}
                    </button>

                    <Link
                        v-if="user"
                        href="/admin/dashboard"
                        class="rounded-lg bg-gray-900 px-4 py-2 text-center text-sm font-medium text-white dark:bg-white dark:text-gray-900"
                    >
                        Panel de admin
                    </Link>
                    <Link
                        v-else
                        href="/login"
                        class="rounded-lg bg-gray-900 px-4 py-2 text-center text-sm font-medium text-white dark:bg-white dark:text-gray-900"
                    >
                        Iniciar sesión
                    </Link>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer
            class="border-t border-gray-200 bg-gray-50 py-10 dark:border-gray-800 dark:bg-gray-900"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="flex flex-col items-center justify-between gap-6 sm:flex-row"
                >
                    <div>
                        <p
                            class="text-sm font-semibold text-gray-900 dark:text-white"
                        >
                            Solución Digital
                        </p>
                        <p
                            class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                        >
                            © {{ new Date().getFullYear() }} Todos los derechos
                            reservados.
                        </p>
                    </div>
                    <nav class="flex items-center gap-6">
                        <Link
                            v-for="link in navLinks"
                            :key="link.href"
                            :href="link.href"
                            class="text-sm text-gray-500 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                        >
                            {{ link.title }}
                        </Link>
                    </nav>
                </div>
            </div>
        </footer>

        <Toaster />
    </div>
</template>

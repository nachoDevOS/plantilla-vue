import { createInertiaApp, router } from '@inertiajs/vue3';
import {
    initializeTheme,
    syncThemeWithCurrentRoute,
} from '@/composables/useAppearance';
import AdminLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName =
    import.meta.env.VITE_APP_NAME && import.meta.env.VITE_APP_NAME !== 'Laravel'
        ? import.meta.env.VITE_APP_NAME
        : 'Solución Digital';

// Inicializa light / dark mode antes de que monte la app.
initializeTheme();

router.on('navigate', (event) => {
    syncThemeWithCurrentRoute(event.detail.page.url);
});

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            // Sitio público: landing + about + contact
            case name.startsWith('public/'):
                return PublicLayout;

            // Autenticación: login, 2FA, reset, verify
            case name.startsWith('auth/'):
                return AuthLayout;

            // Settings dentro del admin: layout anidado
            case name.startsWith('admin/settings/'):
                return [AdminLayout, SettingsLayout];

            // Todo lo autenticado: dashboard, users, módulos admin
            default:
                return AdminLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
}).then(() => {
    document.documentElement.classList.add('app-ready');
    document.documentElement.classList.remove(
        'app-loading',
        'app-landing-loading',
    );
});

// Escucha flash data del servidor para mostrar toasts.
initializeFlashToast();

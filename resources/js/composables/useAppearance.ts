import type { ComputedRef, Ref } from 'vue';
import { computed, onMounted, ref } from 'vue';
import type { Appearance, ResolvedAppearance } from '@/types';

export type { Appearance, ResolvedAppearance };

type AppearanceScope = 'admin' | 'public';

export type UseAppearanceReturn = {
    appearance: Ref<Appearance>;
    resolvedAppearance: ComputedRef<ResolvedAppearance>;
    updateAppearance: (value: Appearance) => void;
};

const appearanceByScope = new Map<string, Ref<Appearance>>();

const isAppearance = (value: string | null): value is Appearance => {
    return value === 'light' || value === 'dark' || value === 'system';
};

const getScopeForPath = (path: string): AppearanceScope => {
    const normalizedPath = path.split('?')[0] || '/';

    return /^\/(admin|settings|dashboard)(?:\/|$)/.test(normalizedPath)
        ? 'admin'
        : 'public';
};

const getCurrentScope = (): AppearanceScope => {
    if (typeof window === 'undefined') {
        return 'public';
    }

    return getScopeForPath(window.location.pathname);
};

const storageKey = (scope: string) => `appearance_${scope}`;

const getStoredAppearance = (scope: string): Appearance | null => {
    if (typeof window === 'undefined') {
        return null;
    }

    const value = localStorage.getItem(storageKey(scope));

    return isAppearance(value) ? value : null;
};

const getAppearanceRef = (scope: string): Ref<Appearance> => {
    const existingAppearance = appearanceByScope.get(scope);

    if (existingAppearance) {
        return existingAppearance;
    }

    const scopedAppearance = ref<Appearance>(
        getStoredAppearance(scope) ?? 'system',
    );

    appearanceByScope.set(scope, scopedAppearance);

    return scopedAppearance;
};

const prefersDark = (): boolean => {
    if (typeof window === 'undefined') {
        return false;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
};

const resolveAppearance = (value: Appearance): ResolvedAppearance => {
    if (value === 'system') {
        return prefersDark() ? 'dark' : 'light';
    }

    return value;
};

export function updateTheme(value: Appearance): void {
    if (typeof window === 'undefined') {
        return;
    }

    document.documentElement.classList.toggle(
        'dark',
        resolveAppearance(value) === 'dark',
    );
}

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;

    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const mediaQuery = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.matchMedia('(prefers-color-scheme: dark)');
};

const handleSystemThemeChange = () => {
    syncThemeWithCurrentRoute();
};

export function syncThemeWithCurrentRoute(path?: string): void {
    if (typeof window === 'undefined') {
        return;
    }

    const scope = getScopeForPath(path ?? window.location.pathname);
    const scopedAppearance = getAppearanceRef(scope);

    scopedAppearance.value = getStoredAppearance(scope) ?? 'system';
    updateTheme(scopedAppearance.value);
}

export function initializeTheme(): void {
    if (typeof window === 'undefined') {
        return;
    }

    syncThemeWithCurrentRoute();
    mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

export function useAppearance(providedScope?: string): UseAppearanceReturn {
    const scope = providedScope ?? getCurrentScope();
    const appearance = getAppearanceRef(scope);

    onMounted(() => {
        appearance.value = getStoredAppearance(scope) ?? 'system';

        if (scope === getCurrentScope()) {
            updateTheme(appearance.value);
        }
    });

    const resolvedAppearance = computed<ResolvedAppearance>(() => {
        return resolveAppearance(appearance.value);
    });

    function updateAppearance(value: Appearance) {
        appearance.value = value;

        // Store in localStorage for client-side persistence...
        localStorage.setItem(storageKey(scope), value);

        // Store in cookie for first paint on the next full page load.
        setCookie(storageKey(scope), value);

        if (scope === getCurrentScope()) {
            updateTheme(value);
        }
    }

    return {
        appearance,
        resolvedAppearance,
        updateAppearance,
    };
}

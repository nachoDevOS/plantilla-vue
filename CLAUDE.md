# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Caveman Mode
- Ingeniero senior. Ultra-terso, técnico, directo.
- Prohibido: introducciones, relleno, agradecimientos.
- Formato: código y comandos primero.
- Sin info suficiente: pregunta breve. Con info: ejecuta.

## Stack

Laravel 13 + Inertia.js v3 + Vue 3 (TypeScript) + Tailwind CSS v4 + Laravel Fortify + Reka UI (headless). Sin librerías de UI con estilos (no PrimeVue, Vuetify, etc.).

## Comandos

```bash
composer setup      # primera instalación
composer run dev    # Laravel + queue + logs + Vite en paralelo
composer test       # config:clear + pint:check + pest
composer lint       # Pint auto-corrige
composer ci:check   # lint + format + types + tests completo

npm run lint        # ESLint auto-corrige
npm run format      # Prettier auto-corrige
npm run types:check # vue-tsc sin emitir

./vendor/bin/pest tests/Feature/Auth/LoginTest.php  # test único
```

## Arquitectura

### Secciones

| Sección | URL | Middleware | Layout |
|---|---|---|---|
| Público | `/`, `/about`, `/contact` | — | `PublicLayout.vue` |
| Auth | `/login`, etc. | — | `AuthLayout.vue` |
| Admin | `/admin/*` | `auth + verified` | `AppLayout.vue` |
| Settings | `/settings/*` | `auth` | `AppLayout` + `settings/Layout.vue` |

### Layouts (`resources/js/layouts/`)

```
AppLayout.vue          ← sidebar colapsable (Reka UI) + breadcrumbs + Toaster
AuthLayout.vue         ← centrado, con logo, sin sidebar
PublicLayout.vue       ← navbar + footer público (pure Vue + Tailwind)
settings/Layout.vue    ← anidado dentro de AppLayout, nav lateral de settings
```

### Asignación automática de layouts (`app.ts`)

```
public/*             → PublicLayout
auth/*               → AuthLayout
admin/settings/*     → [AppLayout, settings/Layout]
admin/*  (resto)     → AppLayout
```

### Rutas (`routes/web.php`)

```
GET  /               → public/Welcome.vue
GET  /about          → public/About.vue
GET  /contact        → public/Contact.vue
POST /contact        → ContactController@store
GET  /admin/dashboard → admin/dashboard/Index.vue    (auth+verified)
GET  /admin/users     → admin/users/Index.vue        (auth+verified)
/settings/*           → routes/settings.php → admin/settings/*.vue
```

Fortify gestiona login, 2FA, reset y verificación. **Registro desactivado** — usuarios solo via `php artisan db:seed`.
Dev: `admin@admin.com` / `password`.

### Estructura de páginas (`resources/js/pages/`)

```
pages/
  public/                ← landing (Welcome.vue), about, contact
  auth/                  ← login, 2FA, reset, verify
  admin/
    dashboard/           ← panel principal (Index.vue)
    users/               ← gestión de usuarios (Index.vue)
    settings/            ← perfil, seguridad, apariencia
```

### Agregar sección al admin

1. Ruta en `routes/web.php` bajo grupo `admin.`
2. Controlador en `app/Http/Controllers/Admin/`
3. Página en `resources/js/pages/admin/<modulo>/`
4. Item en `mainNavItems` de `AppSidebar.vue`

### Tipos (`resources/js/types/index.ts`)

Todos los tipos en un solo archivo: `User`, `Auth`, `BreadcrumbItem`, `NavItem`, `FlashToast`, `Appearance`.
`global.d.ts` y `vue-shims.d.ts` son declaraciones de módulo — no tocar.

### Flash toasts

```php
return back()->with(['toast' => ['type' => 'success', 'message' => 'OK']]);
// tipos: success | info | warning | error
```

`lib/flashToast.ts` escucha el evento `flash` de Inertia y lo pasa a `vue-sonner`.

### Rutas tipadas

`resources/js/routes/` — auto-generado por Wayfinder. No editar manualmente.

### Vista Pública (`pages/public/Welcome.vue`)

Landing page de **VetCare** — clínica veterinaria. Diseño generado con **Google Stitch MCP** (sistema "Sanidad Compasiva").

- **Colores:** primario teal `#0d9488`, surface `#f9fafb`, dark bg `#111827`
- **Tipografía:** Inter (Google Fonts)
- **Secciones:** Hero → Stats → Servicios → Nosotros → Testimonios → Contacto → Footer
- **Formulario:** POST `/contact` via Inertia, campos: `name`, `email`, `phone`, `business_type` (nombre mascota), `message`
- **Sin dependencias externas** — solo Lucide icons + vue-sonner

Stitch project ID: `1229533722980827420` (privado, key en `.env` / config local)

- **Breakpoints responsive:**
  - `≤900px` — nav colapsable, hero 1 columna, proof-grid 1 col
  - `641–900px` — proof-grid 2 cols (tablet)
  - `≤640px` — hero-stats 3 cols, testimonials/solutions forzado 1 col
  - `≤480px` — nav oculta "Iniciar sesión" (accesible desde menú), hero h1 30px, sections padding reducido

## Convenciones

**PHP:** PSR-4 (`App\` → `app/`), Pint, Pest. SQLite local, MySQL producción. Session/queue/cache usan driver `database` — correr `migrate` antes de `dev`.

**Frontend:** alias `@/` → `resources/js/`. `import type` obligatorio para tipos. `cn()` (`clsx` + `tailwind-merge`) para clases condicionales. `components/ui/` excluido de ESLint.

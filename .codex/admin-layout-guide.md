# Guia del Layout Admin

Esta guia explica el admin separado por partes: navbar/header, sidebar, head,
body y footer.

## Flujo general

Cuando entras a una pagina admin, por ejemplo `/admin/dashboard`, Laravel
renderiza una pagina de Vue en:

`resources/js/pages/admin/Dashboard.vue`

Pero esa pagina no aparece sola. Inertia la envuelve con:

`resources/js/layouts/AppLayout.vue`

Ese layout carga:

`resources/js/layouts/app/AppSidebarLayout.vue`

## Mapa visual del admin

```text
AppLayout.vue
└── AppSidebarLayout.vue
    ├── AppShell.vue
    ├── AppSidebar.vue
    │   ├── SidebarHeader  -> logo
    │   ├── SidebarContent -> links del menu
    │   └── SidebarFooter  -> usuario / cuenta
    ├── AppContent.vue
    │   ├── AppSidebarHeader.vue -> header superior + breadcrumbs
    │   └── <slot />             -> pagina admin actual
    └── Toaster                  -> notificaciones
```

## 1. Navbar / Header superior

Archivo:

`resources/js/components/AppSidebarHeader.vue`

Este es el header horizontal del admin. Contiene:

- Boton para abrir/cerrar sidebar.
- Breadcrumbs o migas de pan.

No contiene el menu principal. El menu principal vive en la sidebar.

## 2. Sidebar

Archivo:

`resources/js/components/AppSidebar.vue`

La sidebar tiene tres partes:

- `SidebarHeader`: logo.
- `SidebarContent`: links principales.
- `SidebarFooter`: usuario y menu de cuenta.

Los links principales estan en:

```ts
const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Usuarios',
        href: '/admin/users',
        icon: Users,
    },
];
```

Para agregar una nueva pantalla al menu:

1. Crear ruta en `routes/web.php`.
2. Crear controlador si hace falta.
3. Crear pagina Vue en `resources/js/pages/admin/`.
4. Agregar un item en `mainNavItems`.

## 3. Head

En Vue/Inertia, cada pagina controla su titulo con:

```vue
<Head title="Dashboard" />
```

Ejemplo:

`resources/js/pages/admin/Dashboard.vue`

Eso actualiza el `<title>` del navegador.

## 4. Body / contenido

Archivo contenedor:

`resources/js/components/AppContent.vue`

Aqui entra el contenido real de la pagina admin.

En `AppSidebarLayout.vue` lo veras asi:

```vue
<AppContent variant="sidebar">
    <AppSidebarHeader :breadcrumbs="breadcrumbs" />
    <slot />
</AppContent>
```

`<slot />` significa:

"Aqui coloca la pagina actual".

Si estas en `/admin/users`, ahi entra:

`resources/js/pages/admin/users/Index.vue`

## 5. Footer

El admin actual no tiene footer tradicional al fondo de pagina.

Lo mas parecido a footer en el layout admin es:

`SidebarFooter` dentro de `AppSidebar.vue`

Alli se muestra:

- usuario autenticado
- menu de cuenta
- cerrar sesion

Componente usado:

`resources/js/components/NavUser.vue`

## 6. Paginas admin actuales

Dashboard:

`resources/js/pages/admin/Dashboard.vue`

Usuarios:

`resources/js/pages/admin/users/Index.vue`

Estas paginas deben enfocarse solo en su contenido. El layout se encarga de
sidebar, header y estructura general.

## 7. Diferencia importante con Laravel Blade

En Blade podrias tener:

```blade
@extends('layouts.admin')
@section('content')
    ...
@endsection
```

En Vue/Inertia la idea es parecida:

```ts
defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: '/admin/dashboard' }],
    },
});
```

El layout se asigna automaticamente en:

`resources/js/app.ts`

```ts
case name.startsWith('settings/'):
    return [AdminLayout, SettingsLayout];

default:
    return AdminLayout;
```

Entonces:

- Pagina Vue = contenido.
- Layout Vue = estructura compartida.
- Componentes Vue = piezas reutilizables.


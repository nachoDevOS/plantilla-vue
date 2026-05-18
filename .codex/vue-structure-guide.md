# Guia de Organizacion Vue del Proyecto

Esta guia separa mentalmente el proyecto en dos zonas:

- **Landing / Welcome**: la pagina publica principal de la empresa.
- **Admin**: el panel interno con navbar, sidebar, header, body y usuario.

Como ya manejas Laravel, piensa en Vue asi:

- `resources/js/pages/` equivale a tus vistas principales.
- `resources/js/layouts/` equivale a plantillas Blade reutilizables.
- `resources/js/components/` equivale a parciales reutilizables.

## 1. Landing / Welcome

Archivo principal:

`resources/js/pages/Welcome.vue`

Esta es la vista que Laravel/Inertia muestra para la ruta `/`.

La ruta esta en:

`routes/web.php`

```php
Route::inertia('/', 'Welcome')->name('home');
```

### Como leer Welcome.vue

El archivo tiene tres partes:

```vue
<script setup lang="ts">
// Datos, variables, funciones y formulario.
</script>

<template>
    <!-- HTML de la landing. -->
</template>

<style>
/* Estilos visuales de la landing. */
</style>
```

### Secciones dentro de la landing

En el template veras estas secciones:

- `header`: menu superior de la landing.
- `section#inicio`: hero principal.
- `section#soluciones`: tarjetas de soluciones.
- `section#beneficios`: beneficios clave.
- `section#portafolio`: casos de exito con filtro.
- `section#testimonios`: testimonios.
- `section#contacto`: formulario conectado a Laravel.
- `footer`: pie de pagina de la landing.

### Donde cambiar contenido

Los textos principales estan arriba, en arrays:

- `navLinks`: links del menu.
- `solutions`: tarjetas de servicios.
- `features`: beneficios.
- `filters`: botones de filtro.
- `portfolioItems`: casos de exito.
- `testimonials`: testimonios.

Esto ayuda a no repetir HTML. Cambias el array y Vue actualiza la vista.

## 2. Layout publico

Archivo:

`resources/js/layouts/PublicLayout.vue`

Este layout se usa para paginas publicas como:

- `/about`
- `/contact`

Pero la landing `/` es especial: trae su propio header/footer. Por eso
`PublicLayout.vue` tiene esta variable:

```ts
const isStandaloneLanding = computed(() => page.url.split('?')[0] === '/');
```

Si la URL es `/`, solo muestra el slot de la landing. Si es otra pagina publica,
usa el navbar y footer del layout publico.

## 3. Admin

El admin esta separado de la landing.

Entrada principal:

`resources/js/layouts/AppLayout.vue`

Este archivo envuelve todas las paginas del panel admin.

Layout con sidebar:

`resources/js/layouts/app/AppSidebarLayout.vue`

Ese archivo organiza:

1. Shell/base
2. Sidebar
3. Header superior
4. Body/contenido
5. Toasts

## 4. Paginas admin

Las paginas reales del admin estan en:

`resources/js/pages/admin/`

Ejemplos:

- `resources/js/pages/admin/Dashboard.vue`
- `resources/js/pages/admin/users/Index.vue`

Una pagina admin normalmente solo debe encargarse de su contenido.
No deberia dibujar sidebar/header porque eso ya lo hace el layout.

## 5. Regla practica

Cuando quieras cambiar algo, ubicate asi:

| Quiero cambiar... | Archivo probable |
|---|---|
| Landing principal | `resources/js/pages/Welcome.vue` |
| Menu publico normal | `resources/js/layouts/PublicLayout.vue` |
| Sidebar admin | `resources/js/components/AppSidebar.vue` |
| Header superior admin | `resources/js/components/AppSidebarHeader.vue` |
| Cuerpo/layout admin | `resources/js/components/AppContent.vue` |
| Dashboard admin | `resources/js/pages/admin/Dashboard.vue` |
| Usuarios admin | `resources/js/pages/admin/users/Index.vue` |
| Formulario de contacto backend | `app/Http/Controllers/Public/ContactController.php` |

## 6. Cosas eliminadas para no confundirse

Quitamos el registro publico. Eso significa:

- Ya no existe pagina `resources/js/pages/auth/Register.vue`.
- Ya no existe accion `app/Actions/Fortify/CreateNewUser.php`.
- Ya no existe test `tests/Feature/Auth/RegistrationTest.php`.
- Ya no hay ruta `/register`.
- Ya no se usa `canRegister` en `Welcome.vue`, `Login.vue` ni rutas.

La regla actual es simple: los visitantes ven la landing y pueden iniciar
sesion si ya tienen usuario; los usuarios nuevos se crean desde admin o seeders.

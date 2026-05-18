# Implementacion del Proyecto

Este archivo resume lo que se integro en el sistema. La idea es que puedas
volver aqui cuando estes aprendiendo Vue/Inertia y necesites ubicarte rapido.

Regla del proyecto:

- La memoria de decisiones vive en `.codex/memory.md`.
- La explicacion de lo implementado vive en `.codex/implementation.md`.
- Las guias de aprendizaje tambien viven dentro de `.codex/`.

## Estado actual

El proyecto esta separado en cuatro zonas:

- Sitio publico: landing, about y contacto.
- Auth: login, recuperacion de password, verificacion y 2FA.
- Admin: dashboard y usuarios.
- Settings: perfil, seguridad y apariencia del usuario autenticado.

## Sitio publico

Rutas principales:

- `/` carga `resources/js/pages/Welcome.vue`.
- `/about` carga `resources/js/pages/public/About.vue`.
- `/contact` carga `resources/js/pages/public/Contact.vue`.
- `POST /contact` usa `app/Http/Controllers/Public/ContactController.php`.

La landing `Welcome.vue` es independiente. Tiene su propio header, secciones,
footer, filtro de portafolio y formulario de contacto.

La landing tambien incluye efectos dinamicos:

- Barra de progreso de scroll en el header.
- Animaciones reveal al entrar cada bloque en pantalla.
- Hero con parallax suave segun el puntero.
- Tarjeta visual tipo dashboard con metricas animadas.
- Estadisticas principales y bloque de proceso en tres pasos.
- Boton `Iniciar sesion` en el menu principal y menu movil. Abre un modal
  profesional en la landing con formulario real hacia `/login`, opcion
  `Recordarme` y enlace a recuperacion de password.

`resources/js/layouts/PublicLayout.vue` detecta si estas en `/`. Si estas en la
landing, no agrega navbar/footer extra para evitar duplicados.

## Formulario de contacto

El formulario publico envia:

- `name`
- `email`
- `phone`
- `business_type`
- `message`

El backend valida esos datos en:

`app/Http/Controllers/Public/ContactController.php`

Por ahora el envio real por correo sigue pendiente. El controlador devuelve una
respuesta con toast para confirmar que recibio el formulario.

## Registro publico eliminado

Decidimos que no se registraran usuarios desde la web publica.

Por eso se elimino:

- `resources/js/pages/auth/Register.vue`
- `app/Actions/Fortify/CreateNewUser.php`
- `tests/Feature/Auth/RegistrationTest.php`
- Configuracion `Fortify::registerView(...)`
- Uso de `canRegister`

La ruta `/register` ya no existe.

Regla actual:

- Visitantes pueden ver la landing.
- Usuarios existentes pueden iniciar sesion.
- Usuarios nuevos se crean desde admin o seeders.

Usuario inicial:

```txt
admin@admin.com
password
```

Ese usuario se crea desde:

`database/seeders/DatabaseSeeder.php`

## Admin

Rutas admin en `routes/web.php`:

- `/admin/dashboard`
- `/admin/users`

Controladores:

- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Admin/UserController.php`

Paginas Vue:

- `resources/js/pages/admin/Dashboard.vue`
- `resources/js/pages/admin/users/Index.vue`

Layout admin:

- `resources/js/layouts/AppLayout.vue`
- `resources/js/components/admin/AppShell.vue`
- `resources/js/components/admin/AppSidebar.vue`
- `resources/js/components/admin/AppSidebarHeader.vue`
- `resources/js/components/admin/AppContent.vue`
- `resources/js/components/admin/NavMain.vue`
- `resources/js/components/admin/NavUser.vue`
- `resources/js/components/admin/NavFooter.vue`
- `resources/js/components/admin/AppHeader.vue`

La pagina admin solo debe tener su contenido. Sidebar, header y estructura los
pone el layout.

El acceso al admin esta protegido con `role:admin`. Los alias de Spatie
Permission se registran en `bootstrap/app.php`:

- `role`
- `permission`
- `role_or_permission`

La version instalada usa el namespace singular
`Spatie\Permission\Middleware\...`. Si Laravel conserva una ruta cacheada con
`Spatie\Permission\Middlewares\...`, el login puede funcionar pero la redireccion
al dashboard falla. La solucion operativa es:

```bash
php artisan optimize:clear
```

El usuario inicial `admin@admin.com` debe tener password valido, email verificado
y rol `admin`.

Los componentes de sidebar, menu, navbar/topbar, footer nav, breadcrumbs y menu
de usuario del admin estan organizados dentro de:

`resources/js/components/admin/`

## Vue/Inertia

Entrada principal:

`resources/js/app.ts`

Alli se decide que layout usa cada pagina:

- `Welcome` y `public/*` usan `PublicLayout`.
- `auth/*` usa `AuthLayout`.
- `settings/*` usa `AdminLayout` + `SettingsLayout`.
- El resto usa `AdminLayout`.

## Apariencia

La configuracion de claro/oscuro del admin esta separada del sitio publico.

- Admin y settings usan `appearance_admin`.
- Sitio publico usa `appearance_public`.
- `resources/js/app.ts` escucha la navegacion de Inertia y vuelve a aplicar el
  scope actual al cambiar entre admin y publico.
- `app/Http/Middleware/HandleAppearance.php` comparte con Blade solo la cookie
  que corresponde a la zona visitada. La cookie antigua `appearance` queda como
  fallback solo para admin, no para publico.

## Recarga sin parpadeo

`resources/views/app.blade.php` agrega `app-loading` al `<html>` antes de cargar
Vue. Mientras esa clase existe, `#app` queda invisible para evitar que se vea
texto sin estilos al recargar. Cuando Inertia termina de montar,
`resources/js/app.ts` agrega `app-ready` y quita `app-loading`.

La landing `public/Welcome` agrega tambien `app-landing-loading` para conservar
su fondo oscuro durante ese instante de carga. Las demas vistas usan el fondo
normal del tema actual.

## UI

Los componentes dentro de:

`resources/js/components/ui/`

son piezas pequenas reutilizables como botones, cards, inputs, dialogs, etc.
No son paginas. Son como parciales o componentes base que otras vistas usan.

## Verificaciones recientes

Comandos que pasaron despues de limpiar el registro publico:

```bash
php artisan config:clear
php artisan route:list --path=register
npm.cmd run lint:check
npm.cmd run types:check
npm.cmd run build
```

Resultado importante:

- Laravel ya no tiene ruta `/register`.
- Vue/TypeScript compila correctamente.
- Vite build compila correctamente.

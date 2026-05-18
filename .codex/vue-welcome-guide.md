# Guia de la Vista Welcome.vue

Esta guia explica la landing principal que esta en:

`resources/js/pages/Welcome.vue`

La idea es que puedas leerla como puente entre lo que ya conoces de Laravel y
lo nuevo de Vue + Inertia.

## Que es Welcome.vue

`Welcome.vue` es un componente Vue de una sola pagina. En Laravel seria parecido
a decir: "esta es la vista principal de la ruta `/`".

La ruta esta en `routes/web.php`:

```php
Route::inertia('/', 'Welcome')->name('home');
```

Eso significa:

- Laravel recibe la peticion a `/`.
- Inertia busca el componente `resources/js/pages/Welcome.vue`.
- Vue renderiza la interfaz en el navegador.

Nota del proyecto: el registro publico de usuarios esta desactivado. El usuario
inicial se crea con el seeder:

- Email: `admin@admin.com`
- Password: `password`

## Estructura de un archivo .vue

Un archivo `.vue` normalmente tiene tres partes:

```vue
<script setup lang="ts">
// Logica del componente
</script>

<template>
    <!-- HTML dinamico -->
</template>

<style scoped>
/* Estilos solo para este componente */
</style>
```

En nuestra vista usamos las tres.

## Parte 1: script setup

La parte `<script setup lang="ts">` contiene la logica.

Ejemplo:

```ts
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
```

Esto importa herramientas:

- `ref`: crea variables reactivas.
- `computed`: crea valores calculados automaticamente.
- `onMounted`: ejecuta codigo cuando el componente ya aparecio en pantalla.
- `onBeforeUnmount`: limpia eventos antes de quitar el componente.
- `Head`: permite cambiar el `<title>` y metas desde Vue.
- `useForm`: ayuda a enviar formularios a Laravel con Inertia.

## Variables reactivas

En Vue, una variable normal no siempre actualiza la pantalla. Para eso usamos
`ref`.

```ts
const isScrolled = ref(false);
const mobileMenuOpen = ref(false);
const selectedFilter = ref<PortfolioFilter>('all');
```

Estas variables controlan:

- `isScrolled`: si el usuario ya hizo scroll para cambiar el header.
- `mobileMenuOpen`: si el menu movil esta abierto.
- `selectedFilter`: que categoria esta seleccionada en el portafolio.

Cuando cambia una de estas variables, Vue actualiza automaticamente el HTML.

## Datos como arrays

En vez de escribir cada tarjeta manualmente en el HTML, guardamos los datos en
arrays.

Ejemplo simplificado:

```ts
const solutions = [
    {
        title: 'Restaurantes',
        description: 'Sistema integral con gestion de mesas...',
        icon: Utensils,
        buttonClass: 'btn-restaurant',
    },
];
```

Luego en el template hacemos un loop con `v-for`.

Esto se parece a hacer un `foreach` en Blade:

```blade
@foreach ($solutions as $solution)
    ...
@endforeach
```

En Vue:

```vue
<article v-for="solution in solutions" :key="solution.title">
    {{ solution.title }}
</article>
```

## Iconos dinamicos

Estamos usando `lucide-vue-next`, una libreria de iconos.

Cada icono se importa como componente:

```ts
import { Utensils, Pill, Wrench } from 'lucide-vue-next';
```

Despues se puede guardar en un array:

```ts
icon: Utensils
```

Y renderizar asi:

```vue
<component :is="solution.icon" />
```

Eso significa: "Vue, renderiza el componente que venga en `solution.icon`".

## Filtro del portafolio

El filtro usa `selectedFilter`.

```ts
const selectedFilter = ref<PortfolioFilter>('all');
```

El listado final se calcula con `computed`:

```ts
const filteredPortfolioItems = computed(() =>
    selectedFilter.value === 'all'
        ? portfolioItems
        : portfolioItems.filter((item) => item.category === selectedFilter.value),
);
```

Traduccion:

- Si el filtro es `all`, muestra todos.
- Si no, muestra solo los que tienen esa categoria.

En el template:

```vue
<button
    v-for="filter in filters"
    :class="{ active: selectedFilter === filter.value }"
    @click="selectedFilter = filter.value"
>
    {{ filter.label }}
</button>
```

Puntos importantes:

- `@click` es como decir `onclick`.
- `:class` permite agregar clases dinamicamente.
- `v-for` repite elementos.

## Formulario con Inertia

El formulario usa `useForm`.

```ts
const form = useForm({
    name: '',
    email: '',
    phone: '',
    business_type: '',
    message: '',
});
```

Esto crea un objeto reactivo con los campos del formulario.

En el template conectamos inputs con `v-model`:

```vue
<input v-model="form.name" />
```

Esto significa:

- Si el usuario escribe, `form.name` cambia.
- Si `form.name` cambia desde codigo, el input tambien cambia.

Para enviar:

```ts
function submit() {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
```

Esto envia un POST a Laravel, igual que un formulario normal.

La ruta esta en `routes/web.php`:

```php
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
```

Y el controlador valida:

```php
$request->validate([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email', 'max:255'],
    'phone' => ['nullable', 'string', 'max:50'],
    'business_type' => ['nullable', 'string', 'max:120'],
    'message' => ['required', 'string', 'max:5000'],
]);
```

Cuando Laravel devuelve errores de validacion, Inertia los pone en:

```ts
form.errors
```

Por eso en el template existe:

```vue
<p v-if="form.errors.name">
    {{ form.errors.name }}
</p>
```

`v-if` significa: "solo muestra esto si existe error".

## Head: titulo y SEO

Esta parte:

```vue
<Head title="Soluciones Tecnológicas para Negocios">
    <meta
        name="description"
        content="Sistemas personalizados..."
    />
</Head>
```

Le dice a Inertia que actualice el `<title>` y la descripcion de la pagina.

Es parecido a llenar el `<head>` en Blade, pero desde Vue.

## Template: HTML con poderes

La seccion `<template>` contiene el HTML visible.

La diferencia con HTML normal es que Vue agrega directivas:

- `v-if`: renderiza si la condicion se cumple.
- `v-for`: repite elementos.
- `:class`: clases dinamicas.
- `:href`, `:src`, `:alt`: atributos dinamicos.
- `@click`: evento click.
- `@submit.prevent`: evita recargar la pagina y ejecuta una funcion.

Ejemplo:

```vue
<form @submit.prevent="submit">
```

Significa:

- Cuando se envie el formulario, no recargues el navegador.
- Ejecuta la funcion `submit`.

## Scroll suave entre secciones

La funcion:

```ts
function scrollToSection(hash: string) {
    const section = document.querySelector(hash);

    if (!section) {
        return;
    }

    mobileMenuOpen.value = false;
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
```

Busca una seccion por su `id`.

Ejemplo:

```html
<section id="contacto">
```

Y cuando haces click en "Contacto", llama:

```ts
scrollToSection('#contacto')
```

## Ciclo de vida

Esta parte:

```ts
onMounted(() => {
    handleScroll();
    window.addEventListener('scroll', handleScroll, { passive: true });
});
```

Se ejecuta cuando Vue ya monto el componente.

Aqui registramos un listener de scroll para saber si el usuario bajo la pagina.

Luego limpiamos:

```ts
onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll);
});
```

Esto evita dejar eventos vivos si el componente se desmonta.

## Estilos scoped

La parte:

```vue
<style scoped>
...
</style>
```

Significa que esos estilos aplican principalmente a este componente.

No ensucian toda la aplicacion.

Por eso la landing puede tener un diseño muy propio sin romper el dashboard,
login u otras paginas.

## Relacion con Laravel

Piensalo asi:

- Laravel sigue manejando rutas, controladores, validacion, sesiones y backend.
- Inertia conecta Laravel con Vue sin que tengas que crear una API REST.
- Vue maneja la interfaz dinamica del navegador.

Flujo del formulario:

1. Usuario llena formulario en Vue.
2. Vue envia `POST /contact` con Inertia.
3. Laravel valida en `ContactController`.
4. Laravel responde con redirect y flash toast.
5. Vue muestra el toast y limpia el formulario si todo salio bien.

## Resumen rapido de sintaxis Vue

| Vue | Significa |
|---|---|
| `{{ valor }}` | Imprimir valor |
| `v-if="condicion"` | Mostrar solo si se cumple |
| `v-for="item in items"` | Repetir elementos |
| `:class="algo"` | Clase dinamica |
| `:src="imagen"` | Atributo dinamico |
| `@click="funcion"` | Evento click |
| `v-model="form.name"` | Conectar input con dato |
| `<component :is="icon" />` | Renderizar componente dinamico |

## Que deberias tocar primero para practicar

Buenos ejercicios para aprender sin romper mucho:

1. Cambiar textos de `solutions`.
2. Agregar otra solucion al array `solutions`.
3. Cambiar colores de una tarjeta en el CSS.
4. Agregar otro caso al array `portfolioItems`.
5. Agregar una opcion nueva al select del formulario.

Si vas a agregar campos nuevos al formulario, recuerda cambiar tambien:

- `useForm` en `Welcome.vue`
- el HTML del formulario
- la validacion en `ContactController.php`

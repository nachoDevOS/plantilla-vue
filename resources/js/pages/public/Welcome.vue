<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Boxes,
    ChartLine,
    ChartPie,
    Clock,
    Database,
    Facebook,
    Gauge,
    Images,
    Instagram,
    Mail,
    Menu,
    MessageCircle,
    MonitorSmartphone,
    PawPrint,
    Pill,
    ReceiptText,
    Rocket,
    ShieldCheck,
    Sparkles,
    ShoppingCart,
    Store,
    Utensils,
    Wrench,
    X,
    Zap,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import type { Component } from 'vue';
import { Toaster } from '@/components/ui/sonner';
import { store as loginStore } from '@/routes/login';
import { request as passwordRequest } from '@/routes/password';

type BusinessCategory = 'restaurant' | 'retail' | 'pharmacy' | 'hardware' | 'vet';
type PortfolioFilter = 'all' | BusinessCategory;

// Estado reactivo de la pagina.
// ref() es una variable que Vue puede observar y usar para actualizar la vista.
const isScrolled = ref(false);
const mobileMenuOpen = ref(false);
const loginModalOpen = ref(false);
const selectedFilter = ref<PortfolioFilter>('all');
const scrollProgress = ref(0);
const pointerX = ref(0);
const pointerY = ref(0);
let revealObserver: IntersectionObserver | null = null;

// useForm es de Inertia. Sirve para enviar datos a Laravel sin recargar la pagina.
const form = useForm({
    name: '',
    email: '',
    phone: '',
    business_type: '',
    message: '',
});

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
});

// Links del menu superior de la landing.
const navLinks = [
    { label: 'Inicio', href: '#inicio' },
    { label: 'Soluciones', href: '#soluciones' },
    { label: 'Casos de Éxito', href: '#portafolio' },
    { label: 'Beneficios', href: '#beneficios' },
    { label: 'Testimonios', href: '#testimonios' },
];

// Tarjetas de "Soluciones". Si quieres agregar otro tipo de negocio, empieza aqui.
const solutions: Array<{
    key: string;
    title: string;
    description: string;
    icon: Component;
    buttonClass: string;
}> = [
    {
        key: 'restaurante',
        title: 'Restaurantes',
        description:
            'Sistema integral con gestión de mesas, pedidos, cocina, inventario y reportes para optimizar tu restaurante.',
        icon: Utensils,
        buttonClass: 'btn-restaurant',
    },
    {
        key: 'comercio',
        title: 'Comercios',
        description:
            'Punto de venta completo con control de inventario, facturación electrónica y reportes de ventas.',
        icon: ShoppingCart,
        buttonClass: 'btn-retail',
    },
    {
        key: 'farmacia',
        title: 'Farmacias',
        description:
            'Sistema especializado con control de caducidades, recetas médicas y manejo de proveedores farmacéuticos.',
        icon: Pill,
        buttonClass: 'btn-pharmacy',
    },
    {
        key: 'ferreteria',
        title: 'Ferreterías',
        description:
            'Software para gestión de múltiples almacenes, variedad de medidas y conversiones de productos.',
        icon: Wrench,
        buttonClass: 'btn-hardware',
    },
    {
        key: 'veterinaria',
        title: 'Veterinarias',
        description:
            'Historial clínico de mascotas, control de vacunas, citas, hospitalización y recordatorios automáticos.',
        icon: PawPrint,
        buttonClass: 'btn-vet',
    },
];

// Beneficios que se muestran en la seccion "Por que elegirnos".
const features = [
    {
        title: 'Aumento de Ventas',
        description:
            'Identifica oportunidades de venta cruzada, gestiona promociones y analiza el comportamiento de tus clientes para aumentar tus ingresos.',
        icon: ChartLine,
    },
    {
        title: 'Ahorro de Tiempo',
        description:
            'Automatiza procesos repetitivos como facturación, control de inventario y reportes, para enfocarte en hacer crecer tu negocio.',
        icon: Clock,
    },
    {
        title: 'Control de Inventario',
        description:
            'Mantén un control preciso de tu stock, recibe alertas de productos por agotarse y evita pérdidas por caducidad u obsolescencia.',
        icon: Boxes,
    },
    {
        title: 'Acceso en cualquier lugar',
        description:
            'Consulta tu negocio desde cualquier dispositivo con internet, manteniendo la seguridad y confidencialidad de tus datos.',
        icon: MonitorSmartphone,
    },
];

const heroStats = [
    { value: '+40%', label: 'más eficiencia' },
    { value: '24/7', label: 'acceso seguro' },
    { value: '5+', label: 'rubros listos' },
];

const workflowSteps = [
    {
        title: 'Diagnóstico',
        description: 'Revisamos ventas, inventario, facturación y tareas manuales.',
        icon: Sparkles,
    },
    {
        title: 'Implementación',
        description: 'Configuramos módulos, usuarios, productos y reportes clave.',
        icon: Database,
    },
    {
        title: 'Crecimiento',
        description: 'Medimos resultados y ajustamos el sistema según tu operación.',
        icon: Gauge,
    },
];

// Botones para filtrar los casos de exito.
const filters: Array<{ label: string; value: PortfolioFilter; icon?: Component }> = [
    { label: 'Todos', value: 'all' },
    { label: 'Restaurantes', value: 'restaurant', icon: Utensils },
    { label: 'Comercios', value: 'retail', icon: Store },
    { label: 'Farmacias', value: 'pharmacy', icon: Pill },
    { label: 'Ferreterías', value: 'hardware', icon: Wrench },
    { label: 'Veterinarias', value: 'vet', icon: PawPrint },
];

// Casos de exito que aparecen en el portafolio.
const portfolioItems: Array<{
    category: BusinessCategory;
    categoryLabel: string;
    badgeClass: string;
    title: string;
    description: string;
    image: string;
    alt: string;
}> = [
    {
        category: 'restaurant',
        categoryLabel: 'Restaurante',
        badgeClass: 'restaurant-badge',
        title: 'Restaurante "La Tradición"',
        description:
            'Sistema completo con módulos de mesas, cocina, delivery y reportes que aumentó su eficiencia en un 40%.',
        image: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        alt: 'Interior de restaurante moderno',
    },
    {
        category: 'retail',
        categoryLabel: 'Comercio',
        badgeClass: 'retail-badge',
        title: 'Tienda "El Buen Precio"',
        description:
            'Punto de venta con control de inventario que redujo las pérdidas por caducidad en un 65%.',
        image: 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        alt: 'Tienda con carrito de compras',
    },
    {
        category: 'pharmacy',
        categoryLabel: 'Farmacia',
        badgeClass: 'pharmacy-badge',
        title: 'Farmacia "Salud Total"',
        description:
            'Sistema especializado que automatizó el control de caducidades y manejo de recetas médicas.',
        image: 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        alt: 'Medicamentos en estantería de farmacia',
    },
    {
        category: 'hardware',
        categoryLabel: 'Ferretería',
        badgeClass: 'hardware-badge',
        title: 'Ferretería "El Constructor"',
        description:
            'Sistema multi-almacén con conversión de medidas que mejoró la precisión del inventario.',
        image: 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        alt: 'Personas revisando productos de ferretería',
    },
    {
        category: 'vet',
        categoryLabel: 'Veterinaria',
        badgeClass: 'vet-badge',
        title: 'Clínica "Huellitas"',
        description:
            'Gestión integral de pacientes, control de vacunas y agenda de citas automatizada.',
        image: 'https://images.unsplash.com/photo-1623366302587-bce731d08e83?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        alt: 'Atención veterinaria profesional',
    },
];

// Testimonios de clientes. Son datos estaticos por ahora.
const testimonials = [
    {
        quote:
            'El sistema para nuestro restaurante ha sido revolucionario. Ahora podemos atender más mesas con menos personal y nuestros clientes notan la diferencia en el servicio.',
        name: 'Carlos Mendoza',
        role: 'Dueño, Restaurante "La Parrilla"',
        image: 'https://randomuser.me/api/portraits/men/32.jpg',
    },
    {
        quote:
            'Desde que implementamos el sistema en nuestra farmacia, hemos reducido las pérdidas por caducidad en un 70% y el tiempo para hacer inventario se redujo de 2 días a 2 horas.',
        name: 'María Fernández',
        role: 'Gerente, Farmacia "Bienestar"',
        image: 'https://randomuser.me/api/portraits/women/44.jpg',
    },
    {
        quote:
            'El punto de venta transformó nuestro negocio. Ahora sabemos qué productos se venden más, en qué horarios y podemos tomar decisiones basadas en datos.',
        name: 'Roberto Jiménez',
        role: 'Dueño, Minimercado "El Ahorro"',
        image: 'https://randomuser.me/api/portraits/men/75.jpg',
    },
];

// computed() recalcula automaticamente cuando selectedFilter cambia.
const filteredPortfolioItems = computed(() =>
    selectedFilter.value === 'all'
        ? portfolioItems
        : portfolioItems.filter((item) => item.category === selectedFilter.value),
);

// Cambia el estilo del header cuando el usuario baja la pagina.
function handleScroll() {
    const scrollTop = window.scrollY;
    const scrollable = document.documentElement.scrollHeight - window.innerHeight;

    isScrolled.value = scrollTop > 50;
    scrollProgress.value = scrollable > 0 ? Math.min((scrollTop / scrollable) * 100, 100) : 0;
}

function handlePointerMove(event: MouseEvent) {
    pointerX.value = (event.clientX / window.innerWidth - 0.5) * 2;
    pointerY.value = (event.clientY / window.innerHeight - 0.5) * 2;
}

// Navegacion suave a una seccion de la misma pagina.
function scrollToSection(hash: string) {
    const section = document.querySelector(hash);

    if (!section) {
        return;
    }

    mobileMenuOpen.value = false;
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function openLoginModal() {
    mobileMenuOpen.value = false;
    loginModalOpen.value = true;
}

function closeLoginModal() {
    loginModalOpen.value = false;
    loginForm.clearErrors();
    loginForm.reset('password');
}

// Envia el formulario al controlador Laravel: ContactController@store.
function submit() {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function submitLogin() {
    loginForm.post(loginStore.url(), {
        preserveScroll: true,
        onSuccess: () => {
            loginModalOpen.value = false;
            loginForm.reset();
        },
        onError: () => loginForm.reset('password'),
    });
}

// onMounted() se ejecuta cuando Vue ya puso el componente en pantalla.
onMounted(() => {
    handleScroll();
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('pointermove', handlePointerMove, { passive: true });

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.16 },
    );

    revealObserver = observer;

    document.querySelectorAll('.reveal').forEach((element) => {
        observer.observe(element);
    });
});

// Limpia el evento de scroll si Vue desmonta esta pagina.
onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('pointermove', handlePointerMove);
    revealObserver?.disconnect();
});
</script>

<template>
    <Head title="Soluciones Tecnológicas para Negocios">
        <meta
            name="description"
            content="Sistemas personalizados para restaurantes, ventas, farmacias, ferreterías, veterinarias y más. Automatiza y optimiza tu negocio con soluciones tecnológicas a medida."
        />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Poppins:wght@500;600;700;800&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="landing-page">
        <header :class="{ scrolled: isScrolled }">
            <div class="scroll-progress" :style="{ width: `${scrollProgress}%` }" aria-hidden="true"></div>
            <div class="container">
                <nav aria-label="Navegación principal">
                    <button type="button" class="logo-button" @click="scrollToSection('#inicio')">
                        Solución <span>Digital</span>
                    </button>

                    <ul class="nav-links">
                        <li v-for="link in navLinks" :key="link.href">
                            <button type="button" @click="scrollToSection(link.href)">
                                {{ link.label }}
                            </button>
                        </li>
                    </ul>

                    <div class="nav-actions">
                        <button type="button" class="btn btn-sm" @click="scrollToSection('#contacto')">
                            Contacto
                        </button>
                        <button type="button" class="login-link login-cta" @click="openLoginModal">
                            Iniciar sesión
                        </button>
                    </div>

                    <div class="mobile-actions">
                        <button type="button" class="login-link login-cta" @click="openLoginModal">
                            Iniciar sesión
                        </button>
                        <button
                            type="button"
                            class="mobile-menu-button"
                            :aria-label="mobileMenuOpen ? 'Cerrar menú' : 'Abrir menú'"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                        >
                            <X v-if="mobileMenuOpen" class="icon" />
                            <Menu v-else class="icon" />
                        </button>
                    </div>
                </nav>

                <div v-if="mobileMenuOpen" class="mobile-menu">
                    <button
                        v-for="link in navLinks"
                        :key="link.href"
                        type="button"
                        @click="scrollToSection(link.href)"
                    >
                        {{ link.label }}
                    </button>
                    <button type="button" class="btn btn-sm" @click="scrollToSection('#contacto')">
                        Contacto
                    </button>
                    <button type="button" class="login-link login-cta mobile-login" @click="openLoginModal">
                        Iniciar sesión
                    </button>
                </div>
            </div>
        </header>

        <main>
            <section id="inicio" class="hero">
                <div class="hero-container">
                    <div class="hero-text reveal">
                        <div class="hero-badge">
                            <Rocket class="badge-icon" />
                            Potencia tu crecimiento
                        </div>
                        <h1>
                            Software Inteligente para
                            <br />
                            <span>Tu Negocio</span>
                        </h1>
                        <p>
                            Deja atrás las hojas de cálculo y el desorden. Automatiza inventarios,
                            ventas y facturación con una plataforma diseñada para escalar contigo.
                        </p>
                        <div class="hero-btns">
                            <button type="button" class="btn" @click="scrollToSection('#contacto')">
                                Solicitar Demo
                                <Rocket class="btn-icon" />
                            </button>
                            <button type="button" class="btn-outline" @click="scrollToSection('#soluciones')">
                                Ver Soluciones
                            </button>
                        </div>
                        <div class="hero-stats" aria-label="Resultados principales">
                            <div v-for="stat in heroStats" :key="stat.label" class="hero-stat">
                                <strong>{{ stat.value }}</strong>
                                <span>{{ stat.label }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="hero-visual reveal delay-1"
                        :style="{
                            '--pointer-x': pointerX,
                            '--pointer-y': pointerY,
                        }"
                    >
                        <div class="visual-card">
                            <img
                                src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80"
                                alt="Dashboard moderno para gestión de negocios"
                            />
                            <div class="dashboard-preview">
                                <div class="preview-header">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                                <div class="preview-grid">
                                    <div class="preview-panel wide">
                                        <div class="preview-label">
                                            <ReceiptText class="preview-icon" />
                                            Ventas de hoy
                                        </div>
                                        <strong>Bs 8.420</strong>
                                        <div class="preview-bars">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </div>
                                    <div class="preview-panel">
                                        <div class="preview-label">
                                            <Gauge class="preview-icon" />
                                            Stock
                                        </div>
                                        <strong>92%</strong>
                                    </div>
                                    <div class="preview-panel">
                                        <div class="preview-label">
                                            <ShieldCheck class="preview-icon" />
                                            Seguro
                                        </div>
                                        <strong>Activo</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="floating-icon icon-1">
                                <ChartPie class="floating-svg" />
                            </div>
                            <div class="floating-icon icon-2">
                                <ShieldCheck class="floating-svg" />
                            </div>
                            <div class="floating-icon icon-3">
                                <Zap class="floating-svg" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hero-fade" aria-hidden="true"></div>
            </section>

            <section class="proof-strip" aria-label="Proceso de implementación">
                <div class="container proof-grid">
                    <article
                        v-for="(step, index) in workflowSteps"
                        :key="step.title"
                        class="proof-item reveal"
                        :class="`delay-${Math.min(index, 3)}`"
                    >
                        <div class="proof-icon">
                            <component :is="step.icon" class="proof-svg" />
                        </div>
                        <div>
                            <h2>{{ step.title }}</h2>
                            <p>{{ step.description }}</p>
                        </div>
                    </article>
                </div>
            </section>

            <section id="soluciones" class="section business-solutions">
                <div class="container">
                    <div class="section-title">
                        <span>Nuestros Servicios</span>
                        <h2>Soluciones para tu Negocio</h2>
                        <p>
                            Sistemas especializados diseñados para las necesidades específicas de
                            cada tipo de negocio.
                        </p>
                    </div>

                    <div class="solutions-grid">
                        <article
                            v-for="(solution, index) in solutions"
                            :key="solution.key"
                            class="solution-card reveal"
                            :class="[solution.key, `delay-${Math.min(index, 3)}`]"
                        >
                            <div class="solution-icon-wrapper">
                                <component :is="solution.icon" class="card-icon" />
                            </div>
                            <h3>{{ solution.title }}</h3>
                            <p>{{ solution.description }}</p>
                            <button
                                type="button"
                                class="btn btn-sm"
                                :class="solution.buttonClass"
                                @click="scrollToSection('#contacto')"
                            >
                                Solicitar Demo
                            </button>
                        </article>
                    </div>
                </div>
            </section>

            <section id="beneficios" class="section features">
                <div class="container">
                    <div class="section-title">
                        <span>Por qué elegirnos</span>
                        <h2>Beneficios Clave</h2>
                        <p>Lo que nuestros sistemas pueden hacer por tu negocio.</p>
                    </div>

                    <div class="features-list">
                        <article
                            v-for="(feature, index) in features"
                            :key="feature.title"
                            class="feature-item reveal"
                            :class="`delay-${Math.min(index, 3)}`"
                        >
                            <div class="feature-icon">
                                <component :is="feature.icon" class="feature-svg" />
                            </div>
                            <div class="feature-text">
                                <h3>{{ feature.title }}</h3>
                                <p>{{ feature.description }}</p>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="portafolio" class="section portfolio">
                <div class="container">
                    <div class="section-title">
                        <span>Portafolio</span>
                        <h2>Casos de Éxito</h2>
                        <p>Algunos ejemplos de sistemas implementados para nuestros clientes.</p>
                    </div>

                    <div class="business-type-selector">
                        <div class="business-type-buttons" role="list" aria-label="Filtrar casos de éxito">
                            <button
                                v-for="filter in filters"
                                :key="filter.value"
                                type="button"
                                class="filter-btn"
                                :class="{ active: selectedFilter === filter.value }"
                                @click="selectedFilter = filter.value"
                            >
                                <component v-if="filter.icon" :is="filter.icon" class="filter-icon" />
                                {{ filter.label }}
                            </button>
                        </div>
                    </div>

                    <div class="portfolio-grid">
                        <article
                            v-for="item in filteredPortfolioItems"
                            :key="item.title"
                            class="portfolio-item reveal"
                        >
                            <div class="portfolio-img">
                                <img :src="item.image" :alt="item.alt" />
                                <span class="portfolio-category" :class="item.badgeClass">
                                    {{ item.categoryLabel }}
                                </span>
                            </div>
                            <div class="portfolio-info">
                                <h3>{{ item.title }}</h3>
                                <p>{{ item.description }}</p>
                                <div class="portfolio-links">
                                    <button type="button" @click="scrollToSection('#contacto')">
                                        <Images class="inline-icon" />
                                        Ver capturas
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="testimonios" class="section testimonials">
                <div class="container">
                    <div class="section-title">
                        <span>Testimonios</span>
                        <h2>Lo que dicen nuestros clientes</h2>
                        <p>Testimonios de negocios que han transformado sus operaciones.</p>
                    </div>

                    <div class="testimonials-grid">
                        <article
                            v-for="(testimonial, index) in testimonials"
                            :key="testimonial.name"
                            class="testimonial-card reveal"
                            :class="`delay-${Math.min(index, 3)}`"
                        >
                            <div class="testimonial-content">
                                <p>{{ testimonial.quote }}</p>
                            </div>
                            <div class="testimonial-author">
                                <img :src="testimonial.image" :alt="testimonial.name" />
                                <div class="author-info">
                                    <h4>{{ testimonial.name }}</h4>
                                    <p>{{ testimonial.role }}</p>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="contacto" class="section contact">
                <div class="container">
                    <div class="section-title">
                        <span>Contáctanos</span>
                        <h2>¿Listo para transformar tu negocio?</h2>
                        <p>Contáctanos para una demostración personalizada.</p>
                    </div>

                    <div class="contact-form reveal">
                        <form @submit.prevent="submit">
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="form-control"
                                    autocomplete="name"
                                    required
                                />
                                <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="form-control"
                                    autocomplete="email"
                                    required
                                />
                                <p v-if="form.errors.email" class="form-error">{{ form.errors.email }}</p>
                            </div>

                            <div class="form-group">
                                <label for="phone">Teléfono</label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    class="form-control"
                                    autocomplete="tel"
                                    required
                                />
                                <p v-if="form.errors.phone" class="form-error">{{ form.errors.phone }}</p>
                            </div>

                            <div class="form-group">
                                <label for="business-type">Tipo de Negocio</label>
                                <select
                                    id="business-type"
                                    v-model="form.business_type"
                                    class="form-control"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    <option value="restaurante">Restaurante/Cafetería</option>
                                    <option value="tienda">Tienda/Comercio</option>
                                    <option value="farmacia">Farmacia</option>
                                    <option value="ferreteria">Ferretería</option>
                                    <option value="veterinaria">Clínica Veterinaria</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <p v-if="form.errors.business_type" class="form-error">
                                    {{ form.errors.business_type }}
                                </p>
                            </div>

                            <div class="form-group">
                                <label for="message">¿Qué desafíos enfrenta tu negocio?</label>
                                <textarea
                                    id="message"
                                    v-model="form.message"
                                    class="form-control"
                                    required
                                ></textarea>
                                <p v-if="form.errors.message" class="form-error">
                                    {{ form.errors.message }}
                                </p>
                            </div>

                            <button type="submit" class="btn submit-button" :disabled="form.processing">
                                {{ form.processing ? 'Enviando...' : 'Solicitar Demostración' }}
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <div class="container">
                <div class="social-links">
                    <button type="button" aria-label="WhatsApp" @click="scrollToSection('#contacto')">
                        <MessageCircle class="social-icon" />
                    </button>
                    <button type="button" aria-label="Facebook" @click="scrollToSection('#contacto')">
                        <Facebook class="social-icon" />
                    </button>
                    <button type="button" aria-label="Instagram" @click="scrollToSection('#contacto')">
                        <Instagram class="social-icon" />
                    </button>
                    <button type="button" aria-label="Email" @click="scrollToSection('#contacto')">
                        <Mail class="social-icon" />
                    </button>
                </div>
                <p>
                    &copy; {{ new Date().getFullYear() }} Soluciones Tecnológicas para Negocios.
                    Todos los derechos reservados.
                </p>
                <p class="footer-note">
                    Sistemas especializados para restaurantes, comercios, farmacias, ferreterías y
                    veterinarias.
                </p>
            </div>
        </footer>

        <div
            v-if="loginModalOpen"
            class="login-modal-backdrop"
            tabindex="-1"
            role="presentation"
            @click.self="closeLoginModal"
            @keydown.esc="closeLoginModal"
        >
            <section
                class="login-modal reveal is-visible"
                role="dialog"
                aria-modal="true"
                aria-labelledby="login-modal-title"
            >
                <button
                    type="button"
                    class="modal-close"
                    aria-label="Cerrar inicio de sesión"
                    @click="closeLoginModal"
                >
                    <X class="icon" />
                </button>

                <div class="login-modal-header">
                    <div class="login-modal-icon">
                        <ShieldCheck class="modal-icon-svg" />
                    </div>
                    <div>
                        <p>Panel administrativo</p>
                        <h2 id="login-modal-title">Iniciar sesión</h2>
                    </div>
                </div>

                <form class="login-modal-form" @submit.prevent="submitLogin">
                    <div class="form-group">
                        <label for="login-email">Correo electrónico</label>
                        <input
                            id="login-email"
                            v-model="loginForm.email"
                            type="email"
                            class="form-control"
                            autocomplete="email"
                            placeholder="email@ejemplo.com"
                            required
                            autofocus
                        />
                        <p v-if="loginForm.errors.email" class="form-error">
                            {{ loginForm.errors.email }}
                        </p>
                    </div>

                    <div class="form-group">
                        <div class="login-label-row">
                            <label for="login-password">Contraseña</label>
                            <Link :href="passwordRequest()" class="forgot-link">
                                ¿Olvidaste?
                            </Link>
                        </div>
                        <input
                            id="login-password"
                            v-model="loginForm.password"
                            type="password"
                            class="form-control"
                            autocomplete="current-password"
                            placeholder="Tu contraseña"
                            required
                        />
                        <p v-if="loginForm.errors.password" class="form-error">
                            {{ loginForm.errors.password }}
                        </p>
                    </div>

                    <label class="remember-row">
                        <input v-model="loginForm.remember" type="checkbox" />
                        <span>Recordarme en este equipo</span>
                    </label>

                    <button type="submit" class="btn submit-button" :disabled="loginForm.processing">
                        {{ loginForm.processing ? 'Ingresando...' : 'Entrar al dashboard' }}
                    </button>
                </form>
            </section>
        </div>

        <Toaster />
    </div>
</template>

<style scoped>
:global(html) {
    scroll-behavior: smooth;
}

.landing-page {
    --primary-color: #2563eb;
    --primary-dark: #1e40af;
    --secondary-color: #0ea5e9;
    --dark-bg: #0f172a;
    --card-bg: #ffffff;
    --text-main: #334155;
    --text-light: #64748b;
    --light-bg: #f8fafc;
    --restaurant-color: #f43f5e;
    --retail-color: #10b981;
    --pharmacy-color: #8b5cf6;
    --hardware-color: #f59e0b;
    --vet-color: #0d9488;

    min-height: 100vh;
    overflow-x: hidden;
    background: #ffffff;
    color: var(--text-main);
    font-family: Inter, ui-sans-serif, system-ui, sans-serif;
    line-height: 1.6;
}

.landing-page *,
.landing-page *::before,
.landing-page *::after {
    box-sizing: border-box;
}

.landing-page h1,
.landing-page h2,
.landing-page h3,
.landing-page h4,
.landing-page h5,
.landing-page h6 {
    margin: 0;
    color: var(--dark-bg);
    font-family: Poppins, Inter, ui-sans-serif, system-ui, sans-serif;
}

.landing-page p {
    margin: 0;
}

.landing-page button {
    font: inherit;
}

.container {
    width: min(1280px, calc(100% - 48px));
    margin: 0 auto;
}

header {
    position: fixed;
    z-index: 1000;
    top: 0;
    left: 0;
    width: 100%;
    border-bottom: 1px solid rgb(255 255 255 / 10%);
    background-color: rgb(15 23 42 / 95%);
    backdrop-filter: blur(10px);
    transition:
        padding 0.3s ease,
        box-shadow 0.3s ease;
}

header.scrolled {
    box-shadow: 0 4px 20px rgb(0 0 0 / 10%);
}

.scroll-progress {
    position: absolute;
    bottom: -1px;
    left: 0;
    height: 3px;
    background: linear-gradient(90deg, #38bdf8, #22c55e, #f59e0b);
    box-shadow: 0 0 18px rgb(56 189 248 / 45%);
    transition: width 0.12s linear;
}

nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    min-height: 72px;
}

.logo-button {
    border: 0;
    background: transparent;
    color: #ffffff;
    cursor: pointer;
    font-family: Poppins, Inter, ui-sans-serif, system-ui, sans-serif;
    font-size: 26px;
    font-weight: 700;
    letter-spacing: 0;
}

.logo-button span {
    color: var(--secondary-color);
}

.nav-links {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1;
    gap: 20px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.nav-actions {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    gap: 12px;
}

.mobile-actions {
    display: none;
    align-items: center;
    gap: 10px;
}

.nav-links button:not(.btn),
.mobile-menu button:not(.btn) {
    position: relative;
    border: 0;
    background: transparent;
    color: #e2e8f0;
    cursor: pointer;
    font-size: 15px;
    font-weight: 500;
    transition: color 0.3s;
}

.login-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 38px;
    border: 1px solid rgb(255 255 255 / 18%);
    border-radius: 999px;
    background: rgb(255 255 255 / 8%);
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    padding: 9px 20px;
    text-decoration: none;
    transition:
        transform 0.3s ease,
        background 0.3s ease,
        border-color 0.3s ease,
        color 0.3s ease;
}

.nav-actions .login-cta,
.mobile-menu .login-cta {
    border: 0;
    background: linear-gradient(135deg, #38bdf8, #2563eb);
    box-shadow: 0 10px 24px rgb(37 99 235 / 34%);
    color: #ffffff;
}

.login-link:hover {
    border-color: #38bdf8;
    background: #ffffff;
    color: var(--dark-bg);
    transform: translateY(-2px);
}

.nav-actions .login-cta:hover,
.mobile-menu .login-cta:hover {
    background: linear-gradient(135deg, #0ea5e9, #1d4ed8);
    color: #ffffff;
    transform: translateY(-2px);
}

.mobile-login {
    width: 100%;
}

.nav-links button:not(.btn)::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 0;
    height: 2px;
    background-color: var(--secondary-color);
    transition: width 0.3s;
}

.nav-links button:not(.btn):hover::after {
    width: 100%;
}

.nav-links button:not(.btn):hover,
.mobile-menu button:not(.btn):hover {
    color: var(--secondary-color);
}

.mobile-menu-button {
    display: none;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border: 1px solid rgb(255 255 255 / 12%);
    border-radius: 8px;
    background: rgb(255 255 255 / 5%);
    color: #ffffff;
    cursor: pointer;
}

.mobile-menu {
    display: none;
    padding: 0 0 18px;
}

.icon,
.badge-icon,
.filter-icon,
.inline-icon,
.social-icon,
.btn-icon {
    width: 18px;
    height: 18px;
}

.hero {
    position: relative;
    display: flex;
    min-height: 100vh;
    align-items: center;
    overflow: hidden;
    padding: 140px 0 100px;
    background-color: var(--dark-bg);
    background-image:
        radial-gradient(at 0% 0%, rgb(37 99 235 / 15%) 0, transparent 50%),
        radial-gradient(at 100% 0%, rgb(14 165 233 / 15%) 0, transparent 50%);
}

.hero::before {
    content: '';
    position: absolute;
    z-index: 0;
    inset: 0;
    background-image:
        linear-gradient(rgb(255 255 255 / 3%) 1px, transparent 1px),
        linear-gradient(90deg, rgb(255 255 255 / 3%) 1px, transparent 1px);
    background-size: 40px 40px;
    mask-image: linear-gradient(to bottom, rgb(0 0 0 / 100%) 60%, rgb(0 0 0 / 0%));
}

.hero-container {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 60px;
    align-items: center;
    width: min(1280px, calc(100% - 48px));
    margin: 0 auto;
}

.hero-text {
    text-align: left;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 24px;
    padding: 8px 16px;
    border: 1px solid rgb(37 99 235 / 20%);
    border-radius: 999px;
    background: rgb(37 99 235 / 10%);
    color: #60a5fa;
    font-size: 13px;
    font-weight: 600;
    backdrop-filter: blur(5px);
}

.hero-text h1 {
    margin-bottom: 24px;
    color: #ffffff;
    font-size: 58px;
    font-weight: 800;
    letter-spacing: 0;
    line-height: 1.1;
}

.hero-text h1 span {
    background: linear-gradient(135deg, #60a5fa 0%, #a855f7 100%);
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-text p {
    max-width: 540px;
    margin-bottom: 40px;
    color: #94a3b8;
    font-size: 18px;
    line-height: 1.7;
}

.hero-btns {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.hero-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    max-width: 560px;
    margin-top: 34px;
}

.hero-stat {
    border: 1px solid rgb(255 255 255 / 10%);
    border-radius: 8px;
    background: rgb(255 255 255 / 6%);
    padding: 14px 16px;
    backdrop-filter: blur(12px);
}

.hero-stat strong {
    display: block;
    color: #ffffff;
    font-family: Poppins, Inter, ui-sans-serif, system-ui, sans-serif;
    font-size: 24px;
    line-height: 1;
}

.hero-stat span {
    display: block;
    margin-top: 6px;
    color: #a7f3d0;
    font-size: 13px;
    font-weight: 600;
}

.btn,
.btn-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    border-radius: 999px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease,
        background 0.3s ease,
        border-color 0.3s ease;
}

.btn {
    border: 0;
    background: var(--primary-color);
    box-shadow: 0 10px 25px -5px rgb(37 99 235 / 40%);
    color: #ffffff;
    padding: 16px 36px;
}

.btn:hover {
    background: var(--primary-dark);
    box-shadow: 0 15px 30px -5px rgb(37 99 235 / 50%);
    transform: translateY(-4px);
}

.btn:disabled {
    cursor: wait;
    opacity: 0.65;
    transform: none;
}

.btn-outline {
    border: 2px solid rgb(255 255 255 / 10%);
    background: transparent;
    color: #ffffff;
    padding: 14px 34px;
}

.btn-outline:hover {
    border-color: var(--primary-color);
    background: rgb(37 99 235 / 10%);
    transform: translateY(-4px);
}

.btn-sm {
    min-height: 38px;
    padding: 10px 24px;
    font-size: 14px;
}

.btn-restaurant {
    background: var(--restaurant-color);
    box-shadow: 0 4px 15px rgb(244 63 94 / 30%);
}

.btn-restaurant:hover {
    background: #e11d48;
}

.btn-retail {
    background: var(--retail-color);
    box-shadow: 0 4px 15px rgb(16 185 129 / 30%);
}

.btn-retail:hover {
    background: #059669;
}

.btn-pharmacy {
    background: var(--pharmacy-color);
    box-shadow: 0 4px 15px rgb(139 92 246 / 30%);
}

.btn-pharmacy:hover {
    background: #7c3aed;
}

.btn-hardware {
    background: var(--hardware-color);
    box-shadow: 0 4px 15px rgb(245 158 11 / 30%);
}

.btn-hardware:hover {
    background: #d97706;
}

.btn-vet {
    background: var(--vet-color);
    box-shadow: 0 4px 15px rgb(13 148 136 / 30%);
}

.btn-vet:hover {
    background: #0f766e;
}

.hero-visual {
    position: relative;
    perspective: 1000px;
    transform:
        translate3d(calc(var(--pointer-x) * 8px), calc(var(--pointer-y) * 8px), 0)
        rotateX(calc(var(--pointer-y) * -2deg))
        rotateY(calc(var(--pointer-x) * 2deg));
    transition: transform 0.18s ease-out;
}

.visual-card {
    position: relative;
    border: 1px solid rgb(255 255 255 / 10%);
    border-radius: 8px;
    background: rgb(15 23 42 / 60%);
    box-shadow: 0 25px 50px -12px rgb(0 0 0 / 50%);
    padding: 15px;
    backdrop-filter: blur(10px);
    transform: rotateY(-8deg) rotateX(4deg);
    transition: transform 0.5s ease;
}

.visual-card:hover {
    transform: rotateY(0deg) rotateX(0deg);
}

.visual-card img {
    display: block;
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 10%);
}

.dashboard-preview {
    position: absolute;
    right: 34px;
    bottom: 34px;
    width: min(76%, 380px);
    border: 1px solid rgb(255 255 255 / 18%);
    border-radius: 8px;
    background: rgb(15 23 42 / 86%);
    box-shadow: 0 24px 60px rgb(2 6 23 / 38%);
    color: #ffffff;
    overflow: hidden;
    backdrop-filter: blur(18px);
}

.preview-header {
    display: flex;
    gap: 7px;
    border-bottom: 1px solid rgb(255 255 255 / 10%);
    padding: 12px 14px;
}

.preview-header span {
    width: 9px;
    height: 9px;
    border-radius: 999px;
    background: #38bdf8;
}

.preview-header span:nth-child(2) {
    background: #22c55e;
}

.preview-header span:nth-child(3) {
    background: #f59e0b;
}

.preview-grid {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 10px;
    padding: 14px;
}

.preview-panel {
    min-height: 92px;
    border: 1px solid rgb(255 255 255 / 9%);
    border-radius: 8px;
    background: rgb(255 255 255 / 7%);
    padding: 14px;
}

.preview-panel.wide {
    grid-row: span 2;
}

.preview-label {
    display: flex;
    align-items: center;
    gap: 7px;
    color: #bae6fd;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}

.preview-icon {
    width: 15px;
    height: 15px;
}

.preview-panel strong {
    display: block;
    margin-top: 10px;
    color: #ffffff;
    font-family: Poppins, Inter, ui-sans-serif, system-ui, sans-serif;
    font-size: 26px;
}

.preview-bars {
    display: flex;
    align-items: end;
    gap: 8px;
    height: 74px;
    margin-top: 16px;
}

.preview-bars span {
    flex: 1;
    border-radius: 6px 6px 0 0;
    background: linear-gradient(180deg, #38bdf8, #2563eb);
    animation: barPulse 2.8s ease-in-out infinite;
}

.preview-bars span:nth-child(1) {
    height: 48%;
}

.preview-bars span:nth-child(2) {
    height: 72%;
    animation-delay: 0.2s;
}

.preview-bars span:nth-child(3) {
    height: 58%;
    animation-delay: 0.4s;
}

.preview-bars span:nth-child(4) {
    height: 88%;
    animation-delay: 0.6s;
}

.floating-icon {
    position: absolute;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 10px 25px rgb(0 0 0 / 20%);
    animation: float 6s ease-in-out infinite;
}

.floating-svg {
    width: 28px;
    height: 28px;
}

.icon-1 {
    top: -30px;
    right: -30px;
    color: var(--restaurant-color);
}

.icon-2 {
    bottom: 40px;
    left: -40px;
    color: var(--retail-color);
    animation-delay: 2s;
}

.icon-3 {
    top: 40%;
    right: -50px;
    color: var(--pharmacy-color);
    animation-delay: 4s;
}

.hero-fade {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100px;
    background: linear-gradient(to top, #ffffff, transparent);
    pointer-events: none;
}

@keyframes float {
    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-20px);
    }
}

@keyframes barPulse {
    0%,
    100% {
        filter: brightness(1);
        transform: scaleY(1);
    }

    50% {
        filter: brightness(1.25);
        transform: scaleY(0.82);
    }
}

.proof-strip {
    position: relative;
    z-index: 2;
    margin-top: -42px;
}

.proof-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.proof-item {
    display: flex;
    gap: 16px;
    align-items: flex-start;
    border: 1px solid rgb(226 232 240 / 75%);
    border-radius: 8px;
    background: rgb(255 255 255 / 92%);
    box-shadow: 0 20px 50px rgb(15 23 42 / 10%);
    padding: 22px;
    backdrop-filter: blur(14px);
}

.proof-icon {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    border-radius: 8px;
    background: #ecfeff;
    color: #0891b2;
}

.proof-svg {
    width: 22px;
    height: 22px;
}

.proof-item h2 {
    margin-bottom: 6px;
    font-size: 18px;
}

.proof-item p {
    color: var(--text-light);
    font-size: 14px;
    line-height: 1.55;
}

.section {
    position: relative;
    padding: 120px 0;
}

.section-title {
    max-width: 700px;
    margin: 0 auto 80px;
    text-align: center;
}

.section-title span {
    display: block;
    margin-bottom: 10px;
    color: var(--primary-color);
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.section-title h2 {
    margin-bottom: 20px;
    color: var(--dark-bg);
    font-size: 42px;
    font-weight: 700;
}

.section-title p {
    color: var(--text-light);
    font-size: 18px;
}

.business-solutions,
.portfolio {
    background: #ffffff;
}

.solutions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 30px;
}

.solution-card {
    position: relative;
    display: flex;
    overflow: hidden;
    flex-direction: column;
    align-items: flex-start;
    border: 1px solid #f1f5f9;
    border-radius: 8px;
    background: #ffffff;
    padding: 40px 30px;
    transition:
        transform 0.4s ease,
        box-shadow 0.4s ease,
        border-color 0.4s ease;
}

.solution-card:hover {
    border-color: transparent;
    box-shadow: 0 20px 40px rgb(0 0 0 / 8%);
    transform: translateY(-10px);
}

.solution-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: #cccccc;
    transition: height 0.3s;
}

.solution-card:hover::before {
    height: 6px;
}

.solution-card.restaurante::before {
    background: var(--restaurant-color);
}

.solution-card.comercio::before {
    background: var(--retail-color);
}

.solution-card.farmacia::before {
    background: var(--pharmacy-color);
}

.solution-card.ferreteria::before {
    background: var(--hardware-color);
}

.solution-card.veterinaria::before {
    background: var(--vet-color);
}

.solution-icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 70px;
    height: 70px;
    margin-bottom: 25px;
    border-radius: 8px;
    transition: transform 0.3s;
}

.restaurante .solution-icon-wrapper {
    background: rgb(244 63 94 / 10%);
    color: var(--restaurant-color);
}

.comercio .solution-icon-wrapper {
    background: rgb(16 185 129 / 10%);
    color: var(--retail-color);
}

.farmacia .solution-icon-wrapper {
    background: rgb(139 92 246 / 10%);
    color: var(--pharmacy-color);
}

.ferreteria .solution-icon-wrapper {
    background: rgb(245 158 11 / 10%);
    color: var(--hardware-color);
}

.veterinaria .solution-icon-wrapper {
    background: rgb(13 148 136 / 10%);
    color: var(--vet-color);
}

.solution-card:hover .solution-icon-wrapper {
    transform: scale(1.1) rotate(5deg);
}

.card-icon {
    width: 30px;
    height: 30px;
}

.solution-card h3 {
    margin-bottom: 15px;
    font-size: 24px;
    font-weight: 700;
}

.solution-card p {
    flex-grow: 1;
    margin-bottom: 25px;
    color: var(--text-light);
    font-size: 15px;
}

.features,
.contact {
    background-color: var(--light-bg);
}

.features-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 5%);
    padding: 30px;
    transition: transform 0.3s;
}

.feature-item:hover {
    transform: translateY(-5px);
}

.feature-icon {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    margin-right: 25px;
    border-radius: 8px;
    background: var(--primary-color);
    box-shadow: 0 10px 15px -3px rgb(37 99 235 / 30%);
    color: #ffffff;
}

.feature-svg {
    width: 24px;
    height: 24px;
}

.feature-text h3 {
    margin-bottom: 10px;
    color: var(--dark-bg);
    font-size: 20px;
}

.feature-text p {
    color: var(--text-light);
    font-size: 15px;
}

.business-type-selector {
    margin-bottom: 50px;
}

.business-type-buttons {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
}

.filter-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    background: #ffffff;
    color: var(--text-light);
    cursor: pointer;
    font-weight: 600;
    padding: 10px 25px;
    transition:
        transform 0.3s,
        box-shadow 0.3s,
        background 0.3s,
        color 0.3s,
        border-color 0.3s;
}

.filter-btn:hover,
.filter-btn.active {
    border-color: var(--primary-color);
    background: var(--primary-color);
    box-shadow: 0 4px 12px rgb(37 99 235 / 20%);
    color: #ffffff;
    transform: translateY(-2px);
}

.portfolio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
}

.portfolio-item {
    overflow: hidden;
    border: 1px solid #f1f5f9;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 10%);
    transition:
        transform 0.3s,
        box-shadow 0.3s;
}

.portfolio-item:hover {
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 10%);
    transform: translateY(-8px);
}

.portfolio-img {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.portfolio-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.portfolio-img::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgb(0 0 0 / 70%), transparent);
    opacity: 0;
    transition: opacity 0.3s;
}

.portfolio-item:hover .portfolio-img img {
    transform: scale(1.1);
}

.portfolio-item:hover .portfolio-img::after {
    opacity: 1;
}

.portfolio-category {
    position: absolute;
    z-index: 1;
    top: 15px;
    right: 15px;
    border-radius: 6px;
    box-shadow: 0 2px 5px rgb(0 0 0 / 10%);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 5px 10px;
    text-transform: uppercase;
}

.restaurant-badge,
.retail-badge,
.pharmacy-badge,
.hardware-badge,
.vet-badge {
    color: #ffffff;
}

.restaurant-badge {
    background: var(--restaurant-color);
}

.retail-badge {
    background: var(--retail-color);
}

.pharmacy-badge {
    background: var(--pharmacy-color);
}

.hardware-badge {
    background: var(--hardware-color);
}

.vet-badge {
    background: var(--vet-color);
}

.portfolio-info {
    padding: 25px;
}

.portfolio-info h3 {
    margin-bottom: 10px;
    font-size: 20px;
    font-weight: 700;
}

.portfolio-info p {
    margin-bottom: 15px;
    color: #666666;
}

.portfolio-links {
    display: flex;
}

.portfolio-links button {
    display: flex;
    align-items: center;
    gap: 6px;
    border: 0;
    background: transparent;
    color: var(--primary-color);
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 0;
    text-transform: uppercase;
}

.testimonials {
    background: var(--dark-bg);
    color: #ffffff;
}

.testimonials .section-title h2 {
    color: #ffffff;
}

.testimonials .section-title p {
    color: #94a3b8;
}

.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.testimonial-card {
    position: relative;
    border: 1px solid rgb(255 255 255 / 5%);
    border-radius: 8px;
    background: rgb(30 41 59 / 50%);
    padding: 40px 30px;
    backdrop-filter: blur(5px);
}

.testimonial-card::before {
    content: '"';
    position: absolute;
    top: -20px;
    left: 20px;
    color: var(--primary-color);
    font-family: Georgia, serif;
    font-size: 120px;
    line-height: 1;
    opacity: 0.2;
}

.testimonial-content {
    position: relative;
    margin-bottom: 20px;
    color: #e2e8f0;
    font-style: italic;
}

.testimonial-author {
    display: flex;
    align-items: center;
}

.testimonial-author img {
    width: 50px;
    height: 50px;
    margin-right: 15px;
    border-radius: 999px;
    object-fit: cover;
}

.author-info h4 {
    margin-bottom: 5px;
    color: #ffffff;
}

.author-info p {
    color: var(--secondary-color);
    font-size: 13px;
}

.contact-form {
    max-width: 700px;
    margin: 0 auto;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 20px 40px rgb(0 0 0 / 5%);
    padding: 40px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: var(--dark-bg);
    font-size: 14px;
    font-weight: 600;
}

.form-control {
    width: 100%;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
    color: var(--dark-bg);
    font-family: Inter, ui-sans-serif, system-ui, sans-serif;
    font-size: 16px;
    padding: 12px 15px;
    transition:
        border-color 0.3s,
        background 0.3s;
}

.form-control:focus {
    border-color: var(--primary-color);
    background: #ffffff;
    outline: none;
}

textarea.form-control {
    min-height: 150px;
    resize: vertical;
}

.form-error {
    margin-top: 6px;
    color: #dc2626;
    font-size: 13px;
}

.submit-button {
    width: 100%;
}

.login-modal-backdrop {
    position: fixed;
    z-index: 2000;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background:
        radial-gradient(circle at 50% 10%, rgb(56 189 248 / 20%), transparent 34%),
        rgb(2 6 23 / 72%);
    padding: 24px;
    backdrop-filter: blur(12px);
}

.login-modal {
    position: relative;
    width: min(100%, 460px);
    overflow: hidden;
    border: 1px solid rgb(226 232 240 / 70%);
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 30px 90px rgb(2 6 23 / 36%);
    padding: 34px;
}

.login-modal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #38bdf8, #22c55e, #f59e0b);
}

.modal-close {
    position: absolute;
    top: 18px;
    right: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #ffffff;
    color: var(--text-light);
    cursor: pointer;
    transition:
        background 0.25s ease,
        color 0.25s ease,
        transform 0.25s ease;
}

.modal-close:hover {
    background: #f8fafc;
    color: var(--dark-bg);
    transform: translateY(-1px);
}

.login-modal-header {
    display: flex;
    gap: 16px;
    align-items: center;
    margin-bottom: 28px;
    padding-right: 44px;
}

.login-modal-icon {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: 54px;
    height: 54px;
    border-radius: 8px;
    background: #eff6ff;
    color: var(--primary-color);
}

.modal-icon-svg {
    width: 26px;
    height: 26px;
}

.login-modal-header p {
    color: var(--primary-color);
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.login-modal-header h2 {
    margin-top: 4px;
    color: var(--dark-bg);
    font-size: 30px;
    line-height: 1.15;
}

.login-modal-form {
    display: grid;
    gap: 18px;
}

.login-label-row,
.remember-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.forgot-link {
    color: var(--primary-color);
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
}

.forgot-link:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}

.remember-row {
    justify-content: flex-start;
    color: var(--text-light);
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
}

.remember-row input {
    width: 18px;
    height: 18px;
    accent-color: var(--primary-color);
}

footer {
    border-top: 1px solid rgb(255 255 255 / 10%);
    background: var(--dark-bg);
    color: #ffffff;
    padding: 60px 0 30px;
    text-align: center;
}

.social-links {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
}

.social-links button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border: 0;
    border-radius: 999px;
    background: rgb(255 255 255 / 5%);
    color: #ffffff;
    cursor: pointer;
    transition:
        transform 0.3s,
        background 0.3s;
}

.social-links button:hover {
    background: var(--primary-color);
    transform: translateY(-5px);
}

.footer-note {
    margin-top: 10px;
    font-size: 14px;
    opacity: 0.6;
}

.reveal {
    opacity: 0;
    transform: translateY(24px);
    transition:
        opacity 0.75s ease,
        transform 0.75s ease;
}

.reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.delay-1 {
    transition-delay: 0.12s;
}

.delay-2 {
    transition-delay: 0.24s;
}

.delay-3 {
    transition-delay: 0.36s;
}

@media (prefers-reduced-motion: reduce) {
    .landing-page *,
    .landing-page *::before,
    .landing-page *::after {
        scroll-behavior: auto;
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
    }
}

@media (max-width: 900px) {
    .nav-links,
    .nav-actions {
        display: none;
    }

    .mobile-actions {
        display: flex;
    }

    .mobile-menu-button {
        display: flex;
    }

    .mobile-menu {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .mobile-menu button {
        width: 100%;
        justify-content: center;
        padding: 10px;
    }

    .hero-container {
        grid-template-columns: 1fr;
        gap: 50px;
        text-align: center;
    }

    .hero-text {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .hero-text h1 {
        font-size: 42px;
    }

    .hero-btns {
        justify-content: center;
    }

    .hero-stats {
        width: 100%;
    }

    .hero-visual,
    .visual-card,
    .visual-card:hover {
        transform: none;
    }

    .proof-strip {
        margin-top: 0;
        padding-top: 28px;
        background: #ffffff;
    }

    .proof-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .container,
    .hero-container {
        width: min(100% - 32px, 1280px);
    }

    .section {
        padding: 84px 0;
    }

    .section-title {
        margin-bottom: 52px;
    }

    .section-title h2 {
        font-size: 32px;
    }

    .section-title p,
    .hero-text p {
        font-size: 16px;
    }

    .hero {
        padding-top: 120px;
    }

    .hero-text h1 {
        font-size: 38px;
    }

    .hero-btns,
    .hero-btns .btn,
    .hero-btns .btn-outline {
        width: 100%;
    }

    .hero-stats {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .dashboard-preview {
        position: relative;
        right: auto;
        bottom: auto;
        width: 100%;
        margin-top: 12px;
    }

    .preview-grid {
        grid-template-columns: 1fr;
    }

    .preview-panel.wide {
        grid-row: auto;
    }

    .floating-icon {
        width: 45px;
        height: 45px;
    }

    .floating-svg {
        width: 20px;
        height: 20px;
    }

    .icon-1 {
        right: 0;
    }

    .icon-2 {
        left: 0;
    }

    .icon-3 {
        right: -10px;
    }

    .features-list,
    .portfolio-grid {
        grid-template-columns: 1fr;
    }

    .feature-item {
        flex-direction: column;
        gap: 18px;
    }

    .feature-icon {
        margin-right: 0;
    }

    .contact-form {
        padding: 26px;
    }

    .testimonials-grid {
        grid-template-columns: 1fr;
    }

    .solutions-grid {
        grid-template-columns: 1fr;
    }
}

@media (min-width: 641px) and (max-width: 900px) {
    .proof-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .hero-text h1 {
        font-size: 48px;
    }

    .section {
        padding: 100px 0;
    }
}

@media (max-width: 480px) {
    .logo-button {
        font-size: 20px;
    }

    .mobile-actions .login-cta {
        display: none;
    }

    .hero {
        padding-top: 100px;
    }

    .hero-text h1 {
        font-size: 30px;
    }

    .hero-stats {
        grid-template-columns: 1fr;
    }

    .section {
        padding: 60px 0;
    }

    .section-title {
        margin-bottom: 40px;
    }

    .section-title h2 {
        font-size: 26px;
    }

    .contact-form {
        padding: 20px 16px;
    }

    .testimonial-card {
        padding: 30px 20px;
    }

    .filter-btn {
        padding: 8px 16px;
        font-size: 13px;
    }

    .solution-card {
        padding: 28px 20px;
    }

    footer {
        padding: 40px 0 24px;
    }

    .proof-item {
        gap: 12px;
        padding: 18px;
    }
}
</style>

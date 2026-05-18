<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, Users, ShieldCheck } from 'lucide-vue-next';
import NavMain from '@/components/admin/NavMain.vue';
import NavUser from '@/components/admin/NavUser.vue';
import AppLogo from '@/components/AppLogo.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';

// Items principales del menu lateral del admin.
// Para agregar una nueva pantalla admin, normalmente agregas una ruta y otro item aqui.
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
    {
        title: 'Roles',
        href: '/admin/roles',
        icon: ShieldCheck,
    },
];
</script>

<template>
    <!-- Sidebar = barra lateral del admin. Tiene header, body y footer. -->
    <Sidebar collapsible="icon" variant="inset">
        <!-- Header del sidebar: logo / acceso al dashboard. -->
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/admin/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <!-- Body del sidebar: navegacion principal. -->
        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <!-- Footer del sidebar: usuario y menu de cuenta. -->
        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

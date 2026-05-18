<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { roles as rolesIndex } from '@/routes/admin';
import { create, store } from '@/routes/admin/roles';

type Permission = {
    id: number;
    name: string;
};

const { permissions = [] } = defineProps<{
    permissions?: Permission[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Roles', href: rolesIndex() },
            { title: 'Crear rol', href: create() },
        ],
    },
});

const form = useForm<{
    name: string;
    permissions: string[];
}>({
    name: '',
    permissions: [],
});

function submit() {
    form.post(store.url());
}
</script>

<template>
    <Head title="Crear rol" />

    <div class="flex max-w-3xl flex-col gap-6 p-4">
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Crear rol
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Define el nombre del rol y los permisos que tendrá.
                </p>
            </div>

            <Button as-child variant="outline">
                <Link :href="rolesIndex()">
                    <ArrowLeft class="h-4 w-4" />
                    Volver
                </Link>
            </Button>
        </div>

        <form
            class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800"
            @submit.prevent="submit"
        >
            <div class="space-y-6">
                <div class="grid gap-2">
                    <label
                        for="role-name"
                        class="text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Nombre
                    </label>
                    <Input
                        id="role-name"
                        v-model="form.name"
                        type="text"
                        required
                        autocomplete="off"
                        placeholder="Ej. supervisor"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <div>
                        <p
                            class="text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Permisos
                        </p>
                        <p
                            class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                        >
                            Puedes crear el rol sin permisos y asignarlos
                            después.
                        </p>
                    </div>

                    <div
                        v-if="permissions.length > 0"
                        class="grid gap-2 rounded-lg border border-gray-200 p-3 sm:grid-cols-2 dark:border-gray-700"
                    >
                        <label
                            v-for="permission in permissions"
                            :key="permission.id"
                            class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700/40"
                        >
                            <input
                                v-model="form.permissions"
                                type="checkbox"
                                :value="permission.name"
                                class="rounded border-gray-300 text-primary focus:ring-primary"
                            />
                            <span>{{ permission.name }}</span>
                        </label>
                    </div>
                    <p v-else class="text-sm text-gray-500 dark:text-gray-400">
                        No hay permisos registrados todavía.
                    </p>
                    <InputError :message="form.errors.permissions" />
                </div>

                <div
                    class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end"
                >
                    <Button as-child variant="outline">
                        <Link :href="rolesIndex()">Cancelar</Link>
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <Save class="h-4 w-4" />
                        {{ form.processing ? 'Guardando...' : 'Crear rol' }}
                    </Button>
                </div>
            </div>
        </form>
    </div>
</template>

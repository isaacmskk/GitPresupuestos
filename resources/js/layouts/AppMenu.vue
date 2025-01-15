<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<script setup>
import { ref } from 'vue';
import AppMenuItem from './AppMenuItem.vue';
import { useAbility } from '@casl/vue'
const { can } = useAbility();

const vela = "pepe";


const model = ref([
    {
        label: 'Home',
        items: [
        { label: 'Home', icon: 'pi pi-fw pi-home', to: '/facturas', permision: 'all' },
        { label: 'Presupuestos', icon: 'pi pi-fw pi-check-square', to: '/facturas/presupuesto', permision: 'role-list' },
        { label: 'Transacciones', icon: 'pi pi-fw pi-check-square', to: '/facturas/transaccion', permision:'role-list' },

        ]
    },
    {
        label: 'Usuarios',
        items: [
            { label: 'Users', icon: 'pi pi-fw pi-id-card', to: '/admin/users', permision: 'user-list' },
            { label: 'Roles', icon: 'pi pi-fw pi-check-square', to: '/admin/roles', permision: 'role-list' },
            { label: 'Permisos', icon: 'pi pi-fw pi-bookmark', to: '/admin/permissions', permision: 'permission-list' }
        ]
    },
    {
        label: 'Ejercicios',
        items: [
            { label: 'Ejercicios', icon: 'pi pi-fw pi-id-card', to: '/admin/exercises', permision: 'exercise-list' },
            { label: 'Categorias', icon: 'pi pi-fw pi-id-card', to: '/admin/categories', permision: 'category-list' }
        ]
    },
    {
        label: 'Posts',
        items: [
            { label: 'Posts', icon: 'pi pi-fw pi-id-card', to: '/admin/posts', permision: 'post-list' }
        ]
    }
]);
</script>


<style lang="scss" scoped></style>

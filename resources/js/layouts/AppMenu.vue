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



const model = ref([
    {
        label: 'Home',
        items: [
            { label: 'Home', icon: 'pi pi-fw pi-home', to: '/facturas', permision: 'all' },

        ]
    },
    {
        label: 'Añadir',
        items: [
            { label: 'Presupuestos', icon: 'pi pi-fw pi-check-square', to: '/facturas/presupuesto', permision: 'all' },
            { label: 'Transacciones', icon: 'pi pi-fw pi-check-square', to: '/facturas/transaccion', permision: 'all' },
            { label: 'Transacciones Automaticas', icon: 'pi pi-fw pi-check-square', to: '/admin/auto', permision: 'role-list' },
        ]
    },
    {
        label: 'Graficos',
        items: [
            { label: 'Graficas Mes', icon: 'pi pi-fw pi-check-square', to: '/facturas/graficas', permision: 'all' },
        ]
    },
    {
        label: 'Importar',
        items: [
            { label: 'Importar Transacciones', icon: 'pi pi-fw pi-check-square', to: '/facturas/importar', permision: 'all' },]
    }
]);
</script>


<style lang="scss" scoped></style>

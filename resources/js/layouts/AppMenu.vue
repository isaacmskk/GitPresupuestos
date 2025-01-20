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

        ]
    },
    {
        label: 'Añadir',
        items: [
        { label: 'Presupuestos', icon: 'pi pi-fw pi-check-square', to: '/facturas/presupuesto', permision: 'role-list' },
        { label: 'Transacciones', icon: 'pi pi-fw pi-check-square', to: '/facturas/transaccion', permision:'role-list' },
        ]
    },
    {
        label: 'Graficos',
        items: [
        { label: 'Graficas Mes', icon: 'pi pi-fw pi-check-square', to: '/facturas/graficas', permision: 'role-list' },
        ]
    },
    {
        label: 'Banco',
        items: [
        { label: 'Conexión Banco', icon: 'pi pi-fw pi-check-square', to: '/facturas/banco', permision: 'role-list' },       ]
    }
]);
</script>


<style lang="scss" scoped></style>

<template>
  <div>
    <h1>Overview</h1>
    <p>Presupuesto Restante: {{ presupuestoRestante }}</p>
    <p>Total Presupuesto: {{ totalMontoPresupuesto }}</p>
    <p>Total Ingresos: {{ totalTransacciones.ingresos }}</p>
    <p>Total Gastos: {{ totalTransacciones.gastos }}</p>
    <div class="filters">
      <h3>Filtros</h3>
      <div class="form-group">
        <label for="filterTipoDatos">Mostrar:</label>
        <Dropdown v-model="filtroTipoDatos" :options="[
          { label: 'Transacciones', value: 'transacciones' },
          { label: 'Presupuestos', value: 'presupuestos' }
        ]" optionLabel="label" optionValue="value" placeholder="Seleccione" />
      </div>
      <div v-if="filtroTipoDatos === 'transacciones'">
        <div class="form-group">
          <label for="filterTipo">Filtrar por Tipo:</label>
          <Dropdown v-model="filtroTipo" :options="[
            { label: 'Todos', value: '' },
            { label: 'Ingreso', value: 'ingreso' },
            { label: 'Gasto', value: 'gasto' }
          ]" optionLabel="label" optionValue="value" placeholder="Seleccione" />
        </div>
        <div class="form-group">
          <label for="filterMonto">Ordenar por Monto:</label>
          <Dropdown v-model="filtroMonto" :options="[
            { label: 'Sin ordenar', value: '' },
            { label: 'Mayor a menor', value: 'mayor' },
            { label: 'Menor a mayor', value: 'menor' }
          ]" optionLabel="label" optionValue="value" placeholder="Seleccione" />
        </div>
      </div>
    </div>

    <h3>{{ filtroTipoDatos === 'transacciones' ? 'Transacciones' : 'Presupuestos' }}</h3>
    <DataTable :value="datosFiltrados" responsiveLayout="scroll">
      <Column field="descripcion" header="Descripción">
        <template #body="slotProps">
          {{ slotProps.data.descripcion || 'Sin descripción' }}
        </template>
      </Column>
      <Column field="monto" header="Monto">
        <template #body="slotProps">
          {{ slotProps.data.monto }}
        </template>
      </Column>
      <Column v-if="filtroTipoDatos === 'transacciones'" field="tipo" header="Tipo">
        <template #body="slotProps">
          {{ slotProps.data.tipo }}
        </template>
      </Column>
      <Column v-if="filtroTipoDatos === 'presupuestos'" field="mes" header="Fecha">
        <template #body="slotProps">
          {{ slotProps.data.mes }}
        </template>
      </Column>
    </DataTable>

    <div class="summary">
      <p><strong>Total:</strong> {{ filtroTipoDatos === 'transacciones' ? totalTransacciones.ingresos +
        totalTransacciones.gastos : totalMontoPresupuesto }}</p>
      <p v-if="filtroTipoDatos === 'transacciones'">
        <strong>Ingresos: {{ totalTransacciones.ingresos }} | Gastos: {{ totalTransacciones.gastos }}</strong>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import Dropdown from 'primevue/dropdown';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const presupuestos = ref([]);
const transacciones = ref([]);
const userId = ref(1);
const filtroTipoDatos = ref("transacciones");
const filtroTipo = ref("");
const filtroMonto = ref("");

const fetchPresupuestos = async () => {
  const response = await axios.get("/api/presupuestos");
  presupuestos.value = response.data.presupuestos;
};

const fetchTransacciones = async () => {
  const response = await axios.get("/api/transacciones");
  transacciones.value = response.data;
};

onMounted(() => {
  fetchPresupuestos();
  fetchTransacciones();
});

const totalMontoPresupuesto = computed(() => {
  return presupuestos.value
    .filter(p => p.user_id === userId.value)
    .reduce((total, p) => total + parseFloat(p.monto), 0);
});

const totalTransacciones = computed(() => {
  const transaccionesUsuario = transacciones.value.filter(t => t.user_id === userId.value);
  return {
    ingresos: transaccionesUsuario.filter(t => t.tipo === "ingreso").reduce((total, t) => total + parseFloat(t.monto), 0),
    gastos: transaccionesUsuario.filter(t => t.tipo === "gasto").reduce((total, t) => total + parseFloat(t.monto), 0)
  };
});

const presupuestoRestante = computed(() => {
  return totalMontoPresupuesto.value - totalTransacciones.value.gastos + totalTransacciones.value.ingresos;
});

const datosFiltrados = computed(() => {
  if (filtroTipoDatos.value === "presupuestos") {
    return presupuestos.value.filter(p => p.user_id === userId.value);
  }
  let transaccionesFiltradas = transacciones.value.filter(t => t.user_id === userId.value);
  if (filtroTipo.value) {
    transaccionesFiltradas = transaccionesFiltradas.filter(t => t.tipo === filtroTipo.value);
  }
  if (filtroMonto.value === "mayor") {
    transaccionesFiltradas.sort((a, b) => b.monto - a.monto);
  } else if (filtroMonto.value === "menor") {
    transaccionesFiltradas.sort((a, b) => a.monto - b.monto);
  }
  return transaccionesFiltradas;
});
</script>

<style scoped>
.filters {
  margin-top: 20px;
  margin-bottom: 20px;
}

.summary {
  margin-top: 20px;
  font-weight: bold;
}
</style>

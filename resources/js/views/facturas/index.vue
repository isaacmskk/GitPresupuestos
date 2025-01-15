<template>
  <div>
    <h1>Overview</h1>
    <p>Presupuesto Restante: {{ presupuestoRestante }}</p>
    <p>Total Presupuesto: {{ totalMontoPresupuesto }}</p>
    <p>Total Ingresos: {{ totalTransacciones.ingresos }}</p>
    <p>Total Gastos: {{ totalTransacciones.gastos }}</p>
    <!-- <div>
      <h2>Presupuesto</h2>
      <div v-for="presupuesto in presupuestos" :key="presupuesto.id">
        <p>{{ presupuesto.categoria.nombre }}: {{ presupuesto.monto }}</p>
      </div>
      <p><strong>Total: {{ total }}</strong></p>
      <button @click="showPresupuestoForm = true">Agregar Presupuesto</button>
    </div>

    <div>
      <h2>Transacciones</h2>
      <div v-for="transaccion in transacciones" :key="transaccion.id">
        <p>{{ transaccion.descripcion }} - {{ transaccion.monto }}</p>
      </div>
      <button @click="showTransaccionForm = true">Agregar Transacción</button>
    </div>

    <div v-if="showPresupuestoForm">
      <PresupuestoForm @close="showPresupuestoForm = false" />
    </div>

    <div v-if="showTransaccionForm">
      <TransaccionForm @close="showTransaccionForm = false" />
    </div> -->
  </div>
</template>


<script>
import PresupuestoForm from './PresupuestoForm.vue';
import TransaccionForm from './TransaccionForm.vue';
import axios from 'axios';

export default {
  components: {
    PresupuestoForm,
    TransaccionForm,
  },
  data() {
    return {
      showPresupuestoForm: false,
      showTransaccionForm: false,
      presupuestos: [],
      transacciones: [],
      userId: 1, // Cambiar por el ID del usuario autenticado dinámicamente
    };
  },
  mounted() {
    this.fetchPresupuestos();
    this.fetchTransacciones();
  },
  methods: {
    async fetchPresupuestos() {
      const response = await axios.get('/api/presupuestos');
      this.presupuestos = response.data.presupuestos; // Presupuestos
    },
    async fetchTransacciones() {
      const response = await axios.get('/api/transacciones');
      this.transacciones = response.data; // Transacciones
    },
  },
  computed: {
    // Calcular el monto total del presupuesto del usuario autenticado
    totalMontoPresupuesto() {
      const presupuestosUsuario = this.presupuestos.filter(
        (p) => p.user_id === this.userId
      );
      return presupuestosUsuario.reduce(
        (total, presupuesto) => total + parseFloat(presupuesto.monto),
        0
      );
    },
    // Calcular el total de los ingresos y gastos
    totalTransacciones() {
      const transaccionesUsuario = this.transacciones.filter(
        (t) => t.user_id === this.userId
      );
      const totalIngresos = transaccionesUsuario
        .filter((t) => t.tipo === 'ingreso')
        .reduce((total, t) => total + parseFloat(t.monto), 0);
      const totalGastos = transaccionesUsuario
        .filter((t) => t.tipo === 'gasto')
        .reduce((total, t) => total + parseFloat(t.monto), 0);

      return { ingresos: totalIngresos, gastos: totalGastos };
    },
    // Calcular el presupuesto restante
    presupuestoRestante() {
      return (
        this.totalMontoPresupuesto -
        this.totalTransacciones.gastos +
        this.totalTransacciones.ingresos
      );
    },
  },
};
</script>



<style scoped>
/* Aquí puedes agregar estilos personalizados si es necesario */
</style>
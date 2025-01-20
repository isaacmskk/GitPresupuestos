<template>
  <div>
    <h1>Overview</h1>
    <p>Presupuesto Restante: {{ presupuestoRestante }}</p>
    <p>Total Presupuesto: {{ totalMontoPresupuesto }}</p>
    <p>Total Ingresos: {{ totalTransacciones.ingresos }}</p>
    <p>Total Gastos: {{ totalTransacciones.gastos }}</p>

    <!-- Filtros -->
    <div class="filters">
      <h3>Filtros</h3>

      <!-- Filtro por tipo de datos (Presupuestos/Transacciones) -->
      <div class="form-group">
        <label for="filterTipoDatos">Mostrar:</label>
        <select v-model="filtroTipoDatos">
          <option value="transacciones">Transacciones</option>
          <option value="presupuestos">Presupuestos</option>
        </select>
      </div>

      <!-- Filtro específico para transacciones -->
      <div v-if="filtroTipoDatos === 'transacciones'">
        <div class="form-group">
          <label for="filterTipo">Filtrar por Tipo:</label>
          <select v-model="filtroTipo" @change="aplicarFiltros">
            <option value="">Todos</option>
            <option value="ingreso">Ingreso</option>
            <option value="gasto">Gasto</option>
          </select>
        </div>

        <div class="form-group">
          <label for="filterMonto">Ordenar por Monto:</label>
          <select v-model="filtroMonto" @change="aplicarFiltros">
            <option value="">Sin ordenar</option>
            <option value="mayor">Mayor a menor</option>
            <option value="menor">Menor a mayor</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Tabla -->
    <h3>{{ filtroTipoDatos === 'transacciones' ? 'Transacciones' : 'Presupuestos' }}</h3>
    <table>
      <thead>
        <tr>
          <th>Descripción</th>
          <th>Monto</th>
          <th v-if="filtroTipoDatos === 'transacciones'">Tipo</th>
          <th v-if="filtroTipoDatos === 'presupuestos'">Fecha</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in datosFiltrados" :key="item.id">
          <td>{{ item.descripcion || 'Sin descripción' }}</td>
          <td>{{ item.monto }}</td>
          <td v-if="filtroTipoDatos === 'transacciones'">{{ item.tipo }}</td>
          <td v-if="filtroTipoDatos === 'presupuestos'">{{ item.mes }}</td>

        </tr>
        <tr>
          <td><strong>Total</strong></td>
          <td><strong>{{ filtroTipoDatos === 'transacciones' ? totalTransacciones.ingresos + totalTransacciones.gastos : totalMontoPresupuesto }}</strong></td>
          <td v-if="filtroTipoDatos === 'transacciones'">
            <strong>Ingresos: {{ totalTransacciones.ingresos }} | Gastos: {{ totalTransacciones.gastos }}</strong>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      presupuestos: [],
      transacciones: [],
      userId: 1, // Cambiar por el ID del usuario autenticado dinámicamente
      filtroTipoDatos: "transacciones", // 'transacciones' o 'presupuestos'
      filtroTipo: "",
      filtroMonto: "",
    };
  },
  mounted() {
    this.fetchPresupuestos();
    this.fetchTransacciones();
  },
  methods: {
    async fetchPresupuestos() {
      const response = await axios.get("/api/presupuestos");
      this.presupuestos = response.data.presupuestos;
    },
    async fetchTransacciones() {
      const response = await axios.get("/api/transacciones");
      this.transacciones = response.data;
    },
    aplicarFiltros() {
      // Los filtros se aplican dinámicamente en la propiedad computada
    },
  },
  computed: {
    totalMontoPresupuesto() {
      const presupuestosUsuario = this.presupuestos.filter(
        (p) => p.user_id === this.userId
      );
      return presupuestosUsuario.reduce(
        (total, presupuesto) => total + parseFloat(presupuesto.monto),
        0
      );
    },
    totalTransacciones() {
      const transaccionesUsuario = this.transacciones.filter(
        (t) => t.user_id === this.userId
      );
      const totalIngresos = transaccionesUsuario
        .filter((t) => t.tipo === "ingreso")
        .reduce((total, t) => total + parseFloat(t.monto), 0);
      const totalGastos = transaccionesUsuario
        .filter((t) => t.tipo === "gasto")
        .reduce((total, t) => total + parseFloat(t.monto), 0);

      return { ingresos: totalIngresos, gastos: totalGastos };
    },
    presupuestoRestante() {
      return (
        this.totalMontoPresupuesto -
        this.totalTransacciones.gastos +
        this.totalTransacciones.ingresos
      );
    },
    datosFiltrados() {
      if (this.filtroTipoDatos === "presupuestos") {
        return this.presupuestos.filter((p) => p.user_id === this.userId);
      }

      let transaccionesFiltradas = this.transacciones.filter(
        (t) => t.user_id === this.userId
      );

      if (this.filtroTipo) {
        transaccionesFiltradas = transaccionesFiltradas.filter(
          (t) => t.tipo === this.filtroTipo
        );
      }

      if (this.filtroMonto === "mayor") {
        transaccionesFiltradas.sort((a, b) => b.monto - a.monto);
      } else if (this.filtroMonto === "menor") {
        transaccionesFiltradas.sort((a, b) => a.monto - b.monto);
      }

      return transaccionesFiltradas;
    },
  },
};
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

th,
td {
  padding: 8px;
  text-align: left;
  border: 1px solid #ddd;
}

th {
  background-color: #f4f4f4;
}

.filters {
  margin-top: 20px;
  margin-bottom: 20px;
}
</style>

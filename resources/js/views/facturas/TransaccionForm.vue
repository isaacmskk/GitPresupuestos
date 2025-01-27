<template>
  <div class="card">
    <div class="card-body">
      <!-- Formulario de agregar transacción -->
      <h3>Agregar Transacción</h3>
      <form @submit.prevent="submitTransaccion">
        <!-- Descripción -->
        <div class="form-group mb-2">
          <label for="descripcion">Descripción</label>
          <input type="text" id="descripcion" v-model="newTransaccion.descripcion" required />
        </div>

        <!-- Monto -->
        <div class="form-group mb-2">
          <label for="monto">Monto</label>
          <input type="number" id="monto" v-model="newTransaccion.monto" required />
        </div>

        <!-- Categoría -->
        <div class="form-group mb-2">
          <label for="categoria">Categoría</label>
          <select v-model="newTransaccion.categoria_id" required>
            <option value="" disabled>Seleccione una categoría</option>
            <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
              {{ categoria.nombre }}
            </option>
            <option value="otros">Otros</option>
          </select>
          <input v-if="newTransaccion.categoria_id === 'otros'" v-model="newTransaccion.categoria" type="text"
            placeholder="Escribe la categoría" />
        </div>

        <!-- Tipo (Ingreso/Gasto) -->
        <div class="form-group mb-2">
          <label for="tipo">Tipo</label>
          <select v-model="newTransaccion.tipo" required>
            <option value="ingreso">Ingreso</option>
            <option value="gasto">Gasto</option>
          </select>
        </div>

        <button type="submit">Guardar</button>
      </form>
      

      <!-- Filtros -->
      <div class="filters">
        <h3>Filtros</h3>
        <!-- Filtro por categoría -->
        <div class="form-group">
          <label for="filterCategoria">Filtrar por Categoría:</label>
          <select v-model="filtroCategoria" @change="aplicarFiltros">
            <option value="">Todas las categorías</option>
            <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
              {{ categoria.nombre }}
            </option>
          </select>
        </div>

        <!-- Filtro por tipo -->
        <div class="form-group">
          <label for="filterTipo">Filtrar por Tipo:</label>
          <select v-model="filtroTipo" @change="aplicarFiltros">
            <option value="">Todos</option>
            <option value="ingreso">Ingreso</option>
            <option value="gasto">Gasto</option>
          </select>
        </div>

        <!-- Filtro por monto -->
        <div class="form-group">
          <label for="filterMonto">Ordenar por Monto:</label>
          <select v-model="filtroMonto" @change="aplicarFiltros">
            <option value="">Sin ordenar</option>
            <option value="mayor">Mayor a menor</option>
            <option value="menor">Menor a mayor</option>
          </select>
        </div>
      </div>

      <!-- Lista de transacciones -->
      <h3>Transacciones</h3>
      <table>
        <thead>
          <tr>
            <th>Descripción</th>
            <th>Monto</th>
            <th>Tipo</th>
            <th>Categoría</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="transaccion in transaccionesFiltradas" :key="transaccion.id">
            <td>{{ transaccion.descripcion }}</td>
            <td>{{ transaccion.monto }}</td>
            <td>{{ transaccion.tipo }}</td>
            <td>{{ transaccion.categoria?.nombre || 'Sin categoría' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      newTransaccion: {
        descripcion: '',
        monto: '',
        categoria_id: '',
        tipo: '',
        categoria: ''
      },
      categorias: [],
      transacciones: [],
      filtroCategoria: '',
      filtroTipo: '', // Nuevo filtro para tipo
      filtroMonto: ''
    };
  },
  computed: {
    transaccionesFiltradas() {
      let transaccionesFiltradas = this.transacciones;

      // Filtro por categoría
      if (this.filtroCategoria) {
        transaccionesFiltradas = transaccionesFiltradas.filter(
          (t) => t.categoria?.id == this.filtroCategoria
        );
      }

      // Filtro por tipo
      if (this.filtroTipo) {
        transaccionesFiltradas = transaccionesFiltradas.filter(
          (t) => t.tipo === this.filtroTipo
        );
      }

      return transaccionesFiltradas;
    }
  },
  mounted() {
    this.fetchCategorias();
    this.fetchTransacciones();
    this.cargarFiltros(); // Cargar los filtros al montar el componente
  },
  methods: {
    async generarUsuarioYTransaccion() {
      try {
        const response = await axios.post('/api/random-user');
        console.log('Usuario generado:', response.data);

        // Recargar transacciones para reflejar los cambios
        this.fetchTransacciones();
      } catch (error) {
        console.error('Error al generar usuario y transacción:', error);
      }
    },
    async fetchCategorias() {
      try {
        const response = await axios.get('/api/categorias');
        this.categorias = response.data;
      } catch (error) {
        console.error('Error al cargar las categorías:', error);
      }
    },
    async fetchTransacciones() {
      try {
        const response = await axios.get('/api/transacciones', {
          params: {
            monto: this.filtroMonto,
            tipo: this.filtroTipo
          }
        });
        this.transacciones = response.data;
      } catch (error) {
        console.error('Error al cargar las transacciones:', error);
      }
    },
    async submitTransaccion() {
      try {
        if (this.newTransaccion.categoria_id === 'otros' && this.newTransaccion.categoria) {
          const nuevaCategoria = { nombre: this.newTransaccion.categoria };
          const response = await axios.post('/api/categorias', nuevaCategoria);
          this.newTransaccion.categoria_id = response.data.id;
        }

        const transaccionAEnviar = { ...this.newTransaccion };
        delete transaccionAEnviar.categoria;

        const response = await axios.post('/api/transacciones', transaccionAEnviar);

        console.log('Transacción agregada:', response.data);

        this.fetchTransacciones();
        this.$emit('close');
      } catch (error) {
        console.error('Error al agregar la transacción:', error);
      }
    },
    aplicarFiltros() {
      // Guardar los filtros en localStorage
      localStorage.setItem('filtroCategoria', this.filtroCategoria);
      localStorage.setItem('filtroTipo', this.filtroTipo);
      localStorage.setItem('filtroMonto', this.filtroMonto);

      // Recargar las transacciones con los filtros aplicados
      this.fetchTransacciones();
    },
    cargarFiltros() {
      // Cargar los filtros desde localStorage
      this.filtroCategoria = localStorage.getItem('filtroCategoria') || '';
      this.filtroTipo = localStorage.getItem('filtroTipo') || '';
      this.filtroMonto = localStorage.getItem('filtroMonto') || '';

      this.fetchTransacciones(); // Recargar las transacciones con los filtros guardados
    }
  }
};
</script>

<style scoped>
/* Estilos existentes */
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

form div {
  margin-bottom: 15px;
}

button {
  margin-right: 10px;
}

.filters {
  margin-top: 20px;
  margin-bottom: 20px;
}
</style>

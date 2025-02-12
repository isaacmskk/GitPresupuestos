<template>
  <div class="card">
    <div class="card-body">
      <h3>Agregar Transacción</h3>
      <form @submit.prevent="submitTransaccion">
        <div class="form-group mb-2">
          <label for="descripcion">Descripción</label>
          <input type="text" id="descripcion" v-model="newTransaccion.descripcion" required />
        </div>

        <div class="form-group mb-2">
          <label for="monto">Monto</label>
          <input type="number" id="monto" v-model="newTransaccion.monto" required />
        </div>

        <div class="form-group mb-2">
          <label for="categoria">Categoría</label>
          <select v-model="newTransaccion.categoria_id" required>
            <option value="" disabled>Seleccione una categoría</option>
            <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
              {{ categoria.nombre }}
            </option>
            <option value="otros">Otros</option>
          </select>
          <input v-if="newTransaccion.categoria_id === 'otros'" v-model="newTransaccion.categoria" type="text" placeholder="Escribe la categoría" />
        </div>

        <div class="form-group mb-2">
          <label for="tipo">Tipo</label>
          <select v-model="newTransaccion.tipo" required>
            <option value="ingreso">Ingreso</option>
            <option value="gasto">Gasto</option>
          </select>
        </div>

        <button type="submit">Guardar</button>
      </form>

      <div class="filters">
        <h3>Filtros</h3>
        <div class="form-group">
          <label for="filterCategoria">Filtrar por Categoría:</label>
          <select v-model="filtroCategoria" @change="aplicarFiltros">
            <option value="">Todas las categorías</option>
            <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
              {{ categoria.nombre }}
            </option>
          </select>
        </div>

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

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const newTransaccion = ref({ descripcion: '', monto: '', categoria_id: '', tipo: '', categoria: '' });
const categorias = ref([]);
const transacciones = ref([]);
const filtroCategoria = ref('');
const filtroTipo = ref('');
const filtroMonto = ref('');

const fetchCategorias = async () => {
  try {
    const response = await axios.get('/api/categorias');
    categorias.value = response.data;
  } catch (error) {
    console.error('Error al cargar las categorías:', error);
  }
};

const fetchTransacciones = async () => {
  try {
    const response = await axios.get('/api/transacciones', {
      params: { monto: filtroMonto.value, tipo: filtroTipo.value }
    });
    transacciones.value = response.data;
  } catch (error) {
    console.error('Error al cargar las transacciones:', error);
  }
};

const transaccionesFiltradas = computed(() => {
  let filtradas = transacciones.value;
  if (filtroCategoria.value) {
    filtradas = filtradas.filter(t => t.categoria?.id == filtroCategoria.value);
  }
  if (filtroTipo.value) {
    filtradas = filtradas.filter(t => t.tipo === filtroTipo.value);
  }
  return filtradas;
});

const submitTransaccion = async () => {
  try {
    if (newTransaccion.value.categoria_id === 'otros' && newTransaccion.value.categoria) {
      const nuevaCategoria = { nombre: newTransaccion.value.categoria };
      const response = await axios.post('/api/categorias', nuevaCategoria);
      newTransaccion.value.categoria_id = response.data.id;
    }

    const transaccionAEnviar = { ...newTransaccion.value };
    delete transaccionAEnviar.categoria;

    await axios.post('/api/transacciones', transaccionAEnviar);
    fetchTransacciones();
  } catch (error) {
    console.error('Error al agregar la transacción:', error);
  }
};

const aplicarFiltros = () => {
  localStorage.setItem('filtroCategoria', filtroCategoria.value);
  localStorage.setItem('filtroTipo', filtroTipo.value);
  localStorage.setItem('filtroMonto', filtroMonto.value);
  fetchTransacciones();
};

const cargarFiltros = () => {
  filtroCategoria.value = localStorage.getItem('filtroCategoria') || '';
  filtroTipo.value = localStorage.getItem('filtroTipo') || '';
  filtroMonto.value = localStorage.getItem('filtroMonto') || '';
  fetchTransacciones();
};

onMounted(() => {
  fetchCategorias();
  cargarFiltros();
});
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
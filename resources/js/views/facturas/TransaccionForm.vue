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
          <!-- Si se selecciona 'otros', se muestra el campo para escribir la nueva categoría -->
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
        <button type="button" @click="$emit('close')">Cerrar</button>
      </form>

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
          <tr v-for="transaccion in transacciones" :key="transaccion.id">
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
        categoria: '' // Para la categoría "Otros"
      },
      categorias: [], // Categorías obtenidas desde la API
      transacciones: [] // Transacciones del usuario autenticado
    };
  },
  mounted() {
    this.fetchCategorias();
    this.fetchTransacciones();
  },
  methods: {
    // Obtener las categorías desde la API
    async fetchCategorias() {
      try {
        const response = await axios.get('/api/categorias');
        this.categorias = response.data;
      } catch (error) {
        console.error('Error al cargar las categorías:', error);
      }
    },

    // Obtener las transacciones desde la API
    async fetchTransacciones() {
      try {
        const response = await axios.get('/api/transacciones');
        this.transacciones = response.data;
      } catch (error) {
        console.error('Error al cargar las transacciones:', error);
      }
    },

    // Enviar la transacción a la API
    async submitTransaccion() {
      try {
        // Si se selecciona "otros", crea una nueva categoría
        if (this.newTransaccion.categoria_id === 'otros' && this.newTransaccion.categoria) {
          const nuevaCategoria = { nombre: this.newTransaccion.categoria };
          const response = await axios.post('/api/categorias', nuevaCategoria);
          this.newTransaccion.categoria_id = response.data.id; // Asignar el ID de la nueva categoría
        }

        // Crear una copia de la transacción sin el campo 'categoria'
        const transaccionAEnviar = { ...this.newTransaccion };
        delete transaccionAEnviar.categoria;

        // Enviar la transacción al backend
        const response = await axios.post('/api/transacciones', transaccionAEnviar);

        console.log('Transacción agregada:', response.data);

        // Recargar la lista de transacciones después de agregar una nueva
        this.fetchTransacciones();

        // Cerrar el formulario después de guardar
        this.$emit('close');
      } catch (error) {
        console.error('Error al agregar la transacción:', error);
      }
    }
  }
};
</script>
<style scoped>
/* Estilos para el formulario y la tabla */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

th, td {
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
</style>
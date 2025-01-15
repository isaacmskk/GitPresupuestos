<template>
  <h2>Añadir Presupuesto</h2>
  <div class="card">
    <div class="card-body">
      <!-- Mensajes de éxito y error -->
      <div v-if="strSuccess" class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>{{ strSuccess }}</strong>
      </div>

      <div v-if="strError" class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>{{ strError }}</strong>
      </div>

      <!-- Formulario -->
      <form @submit.prevent="crearPresupuesto">
        <div class="form-group mb-2">
          <label>Categoría</label><span class="text-danger"> *</span>
          <select v-model="presupuesto.categoria_id" class="form-control">
            <option value="" disabled>Seleccione una categoría</option>
            <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
              {{ categoria.nombre }}
            </option>
          </select>
        </div>

        <div class="form-group mb-2">
          <label>Monto</label><span class="text-danger"> *</span>
          <input v-model="presupuesto.monto" type="number" class="form-control" placeholder="Ingrese el monto" />
        </div>

        <div class="form-group mb-2">
          <label>Mes</label><span class="text-danger"> *</span>
          <input v-model="presupuesto.mes" type="date" class="" />
        </div>

        <button type="submit" class="botonGeneral" style="margin-top: 20px;">Añadir</button>
        <button type="button" class="botonGeneral" style="margin-top: 20px;" @click="$emit('close')">Cerrar</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref, reactive, onMounted } from "vue";

// Declarar el evento emitido
defineEmits(["close"]);

// Datos reactivos
const presupuesto = reactive({
  categoria_id: "",
  monto: "",
  mes: ""
});

const categorias = ref([]);
const strError = ref();
const strSuccess = ref();

// Función para obtener las categorías
const fetchCategorias = async () => {
  try {
    const response = await axios.get("/api/categorias");
    categorias.value = response.data;
  } catch (error) {
    console.error("Error al cargar las categorías:", error);
    strError.value = "No se pudieron cargar las categorías.";
  }
};

// Función para crear un presupuesto
const crearPresupuesto = async () => {
  try {
    const response = await axios.post("/api/presupuestos", presupuesto, {
      headers: {
        "Content-Type": "application/json"
      }
    });

    strSuccess.value = "Presupuesto añadido correctamente.";
    strError.value = "";
    console.log("Presupuesto añadido:", response.data);

    // Resetear el formulario
    presupuesto.categoria_id = "";
    presupuesto.monto = "";
    presupuesto.mes = "";
  } catch (error) {
    console.error("Error al añadir el presupuesto:", error);
    strError.value = error.response?.data?.message || "Ocurrió un error al añadir el presupuesto.";
    strSuccess.value = "";
  }
};

// Cargar las categorías al montar el componente
onMounted(fetchCategorias);
</script>

<style>
label {
  color: rgb(171, 168, 168);
}
</style>

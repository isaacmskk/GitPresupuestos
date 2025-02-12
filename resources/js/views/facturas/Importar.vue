<template>
  <div>
    <h2>Importar Transacciones</h2>
    <form @submit.prevent="importCSV">
      <input type="file" @change="handleFileUpload" accept=".csv" />
      <button type="submit">Subir Archivo</button>
    </form>
    <div v-if="message" class="message">{{ message }}</div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";

const csvFile = ref(null);
const message = ref("");

function handleFileUpload(event) {
  csvFile.value = event.target.files[0];
}

async function importCSV() {
  if (!csvFile.value) {
    message.value = "Por favor selecciona un archivo CSV.";
    return;
  }

  const formData = new FormData();
  formData.append("csv_file", csvFile.value);

  try {
    const response = await axios.post(
      "/api/transacciones/import-csv",
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      }
    );
    message.value = response.data.message;
  } catch (error) {
    message.value = error.response?.data?.message || "Error al importar las transacciones.";
  }
}
</script>

<style scoped>
.message {
  margin-top: 10px;
  color: green;
}
</style>

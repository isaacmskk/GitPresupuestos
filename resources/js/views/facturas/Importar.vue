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
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        csvFile: null,
        message: "",
      };
    },
    methods: {
      handleFileUpload(event) {
        this.csvFile = event.target.files[0];
      },
      async importCSV() {
        if (!this.csvFile) {
          this.message = "Por favor selecciona un archivo CSV.";
          return;
        }
  
        const formData = new FormData();
        formData.append("csv_file", this.csvFile);
  
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
          this.message = response.data.message;
        } catch (error) {
          this.message =
            error.response?.data?.message || "Error al importar las transacciones.";
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .message {
    margin-top: 10px;
    color: green;
  }
  </style>
  
<template>
    <div>
        <h3>Generar user y transacciones</h3>
        <button @click="generarUsuarioYTransaccion">Generar Usuario y Transacción</button>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";

const bankAccounts = ref([]);
const bankTransactions = ref([]);

const generarUsuarioYTransaccion = async () => {
    try {
        const response = await axios.post("/api/random-user");
        console.log("Usuario generado:", response.data);
    } catch (error) {
        console.error("Error al generar usuario y transacción:", error);
    }
};
</script>
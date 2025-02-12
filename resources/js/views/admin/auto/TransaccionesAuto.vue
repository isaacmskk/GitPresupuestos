<template>
    <div>
        <h1>Transacciones de Todos los Usuarios</h1>
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="transaccion in transacciones" :key="transaccion.id">
                    <td>{{ transaccion.user ? transaccion.user.name : 'Desconocido' }}</td>
                    <td>{{ transaccion.descripcion }}</td>
                    <td>{{ transaccion.monto }}</td>
                    <td>{{ transaccion.tipo }}</td>
                    <td>{{ transaccion.fecha }}</td>
                </tr>
            </tbody>
        </table>
        <button @click="generarUsuarioYTransaccion">Generar Usuario y Transacción</button>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const transacciones = ref([]);
const usuarios = ref([]);

const fetchTransacciones = async () => {
    try {
        const response = await axios.get("/api/transacciones");
        transacciones.value = response.data;
    } catch (error) {
        console.error("Error al cargar transacciones:", error);
    }
};

const generarUsuarioYTransaccion = async () => {
    try {
        const response = await axios.post("/api/random-user");
        console.log("Usuario generado:", response.data);
        fetchTransacciones();
    } catch (error) {
        console.error("Error al generar usuario y transacción:", error);
    }
};

const fetchUsuarios = async () => {
    try {
        const response = await axios.get("/api/usuarios");
        usuarios.value = response.data;
    } catch (error) {
        console.error("Error al cargar usuarios:", error);
    }
};

const getUsuarioNombre = (userId) => {
    if (!Array.isArray(usuarios.value) || usuarios.value.length === 0) {
        return "Desconocido";
    }
    const usuario = usuarios.value.find((u) => u.id === userId);
    return usuario ? usuario.nombre : "Desconocido";
};

onMounted(async () => {
    try {
        const response = await fetch("/api/transacciones?include_all_users=true");
        transacciones.value = await response.json();
    } catch (error) {
        console.error("Error fetching transacciones:", error);
    }
});
</script>

<style scoped>
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

th,
td {
    border: 1px solid #ddd;
    padding: 8px;
}

th {
    background-color: #f4f4f4;
    text-align: left;
}
</style>

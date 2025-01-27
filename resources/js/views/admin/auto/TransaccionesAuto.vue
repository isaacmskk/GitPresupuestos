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

<script>
import axios from "axios";

export default {
    data() {
        return {
            transacciones: [],
            usuarios: [],
        };
    },
    methods: {
        async fetchTransacciones() {
            try {
                const response = await axios.get("/api/transacciones");
                this.transacciones = response.data;
            } catch (error) {
                console.error("Error al cargar transacciones:", error);
            }
        },
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
        async fetchUsuarios() {
            try {
                const response = await axios.get("/api/usuarios");
                this.usuarios = response.data;
            } catch (error) {
                console.error("Error al cargar usuarios:", error);
            }
        },
        getUsuarioNombre(userId) {
            if (!Array.isArray(this.usuarios) || this.usuarios.length === 0) {
                return "Desconocido";
            }
            const usuario = this.usuarios.find((u) => u.id === userId);
            return usuario ? usuario.nombre : "Desconocido";
        },
    },
    async mounted() {
        try {
            const response = await fetch('/api/transacciones?include_all_users=true');
            this.transacciones = await response.json();
        } catch (error) {
            console.error('Error fetching transacciones:', error);
        }
    }

};
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
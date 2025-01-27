<template>
    <div>
        <h3>Generar user y transacciones</h3>
        <button @click="generarUsuarioYTransaccion">Generar Usuario y Transacción</button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            bankAccounts: [],
            bankTransactions: []
        };
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
        }
    }

};
</script>
<template>
    <div>
        <h3>Conectar Cuenta Bancaria</h3>
        <button @click="fetchBankAccounts">Conectar</button>

        <div v-if="bankAccounts.length">
            <h4>Cuentas Bancarias:</h4>
            <pre>{{ bankAccounts }}</pre> <!-- Ver datos directamente -->
            <ul>
                <li v-for="account in bankAccounts" :key="account.id">
                    {{ account.bank_name }} - {{ account.account_number }}
                    <button @click="fetchTransactions(account.id)">Ver Transacciones</button>
                </li>
            </ul>
        </div>


        <div v-if="bankTransactions.length">
            <h4>Transacciones Bancarias:</h4>
            <table>
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="transaction in bankTransactions" :key="transaction.id">
                        <td>{{ transaction.description }}</td>
                        <td>{{ transaction.amount }}</td>
                        <td>{{ transaction.date }}</td>
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
            bankAccounts: [],
            bankTransactions: []
        };
    },
    methods: {
    async fetchBankAccounts() {
        try {
            const response = await axios.get('/api/bank-accounts');
            this.bankAccounts = response.data.bank_accounts || [];
        } catch (error) {
            alert('Error al obtener cuentas bancarias. Intenta de nuevo.');
            console.error('Error:', error);
        }
    },
    async fetchTransactions(accountId) {
        try {
            const response = await axios.get(`/api/bank-accounts/${accountId}/transactions`);
            this.bankTransactions = response.data.transactions || [];
        } catch (error) {
            alert('Error al obtener transacciones. Intenta de nuevo.');
            console.error('Error:', error);
        }
    }
}

};
</script>
<template>
  <div>
    <h2>Gráficos de Presupuestos y Transacciones por Mes</h2>

    <div>
      <label for="mes">Selecciona un mes:</label>
      <select id="mes" v-model="selectedMonth" @change="fetchDatos">
        <option value="">Todos los meses</option>
        <option v-for="mes in meses" :key="mes" :value="mes">{{ `Mes ${mes}` }}</option>
      </select>
    </div>

    <div class="charts-container">
      <div class="chart">
        <h3>Gráfico de Presupuestos por Mes</h3>
        <canvas ref="presupuestosChart"></canvas>
      </div>

      <div class="chart">
        <h3>Gráfico de Transacciones por Mes</h3>
        <canvas ref="transaccionesChart"></canvas>
      </div>
    </div>
  </div>
</template>

<script>
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);

export default {
  components: {
    Line
  },
  data() {
    return {
      selectedMonth: "",
      meses: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
      presupuestoData: {
        labels: [],
        datasets: [
          {
            label: 'Presupuesto',
            data: [],
            borderColor: 'blue',
            backgroundColor: 'rgba(0, 0, 255, 0.2)',
            fill: true,
          }
        ]
      },
      transaccionesData: {
        labels: [],
        datasets: [
          {
            label: 'Transacciones',
            data: [],
            borderColor: 'green',
            backgroundColor: 'rgba(0, 255, 0, 0.2)',
            fill: true,
          }
        ]
      },
      presupuestoChartInstance: null,
      transaccionesChartInstance: null
    };
  },
  mounted() {
    this.fetchDatos();
  },
  methods: {
    async fetchDatos() {
      try {
        const responsePresupuestos = await axios.get('/api/presupuestos', {
          params: {
            group_by: 'mes',
            mes: this.selectedMonth || undefined
          }
        });

        const responseTransacciones = await axios.get('/api/transacciones', {
          params: {
            group_by: 'mes',
            mes: this.selectedMonth || undefined
          }
        });
        console.log("Presupuestos:", responsePresupuestos.data);
        console.log("Transacciones:", responseTransacciones.data);


        const presupuestos = responsePresupuestos.data.presupuestos;
        const transacciones = responseTransacciones.data;

        const labelsMes = presupuestos.map(p => `Mes ${p.mes}`);
        const presupuestoData = presupuestos.map(p => p.monto);

        const transaccionesData = [];
        const transaccionesLabels = [];
        const mesesTransacciones = transacciones.map(t => t.mes);

        for (let mes = 1; mes <= 12; mes++) {
          const transaccion = transacciones.find(t => t.mes === mes);
          transaccionesLabels.push(`Mes ${mes}`);
          transaccionesData.push(transaccion ? parseFloat(transaccion.total) : 0);
        }

        this.presupuestoData.labels = labelsMes;
        this.presupuestoData.datasets[0].data = presupuestoData;

        this.transaccionesData.labels = transaccionesLabels;
        this.transaccionesData.datasets[0].data = transaccionesData;

        this.renderPresupuestoChart();
        this.renderTransaccionesChart();
      } catch (error) {
        console.error("Error al cargar los datos para los gráficos:", error);
      }
    },

    renderPresupuestoChart() {
      if (this.presupuestoChartInstance) {
        this.presupuestoChartInstance.destroy();
      }

      this.presupuestoChartInstance = new ChartJS(this.$refs.presupuestosChart, {
        type: 'line',
        data: this.presupuestoData,
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: 'Gráfico de Presupuestos por Mes'
            }
          },
          scales: {
            y: {
              min: 0
            }
          }
        }
      });
    },

    renderTransaccionesChart() {
      if (this.transaccionesChartInstance) {
        this.transaccionesChartInstance.destroy();
      }

      this.transaccionesChartInstance = new ChartJS(this.$refs.transaccionesChart, {
        type: 'line',
        data: this.transaccionesData,
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: 'Gráfico de Transacciones por Mes'
            }
          },
          scales: {
            y: {
              min: 0
            }
          }
        }
      });
    }
  },
  renderPresupuestoChart() {
    if (this.$refs.presupuestosChart) {
      new ChartJS(this.$refs.presupuestosChart, {
        type: 'line',
        data: this.presupuestoData,
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: 'Gráfico de Presupuestos por Mes'
            }
          },
          scales: {
            y: {
              min: 0
            }
          }
        }
      });
    }
  },

  renderTransaccionesChart() {
    if (this.$refs.transaccionesChart) {
      new ChartJS(this.$refs.transaccionesChart, {
        type: 'line',
        data: this.transaccionesData,
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: 'Gráfico de Transacciones por Mes'
            }
          },
          scales: {
            y: {
              min: 0
            }
          }
        }
      });
    }
  }
}
</script>

<style scoped>
.charts-container {
  display: flex;
  justify-content: space-around;
  margin-top: 30px;
  flex-wrap: wrap;
}

.chart {
  width: 45%;
  margin-bottom: 20px;
}

canvas {
  max-width: 100%;
  height: 400px;
  margin-top: 20px;
}
</style>
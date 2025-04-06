<template>
  <PieChart v-if="isChartDataReady" :data="chartData" :options="chartOptions" />
</template>

<script>
import { defineComponent } from 'vue';
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement);

export default defineComponent({
  name: 'NutritionChart',
  components: { PieChart: Pie },
  props: {
    nutritionData: {
      type: Object,
      required: true,
    },
  },
  computed: {
    chartData() {
      console.log('nutritionData:', this.nutritionData);
      if (!this.nutritionData) {
        return { labels: [], datasets: [{ data: [] }] };
      }
      const labels = Object.keys(this.nutritionData);
      const data = Object.values(this.nutritionData);
      console.log('chartData:', { labels, datasets: [{ data }] });
      return {
        labels,
        datasets: [{
          data,
          backgroundColor: [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
          ],
        }],
      };
    },
    chartOptions() {
      return {
        responsive: true,
        plugins: {
          legend: { position: 'top' },
          title: { display: false, text: 'Rozdelenie výživových hodnôt' },
        },
      };
    },
    isChartDataReady() {
      return this.chartData && this.chartData.labels && this.chartData.labels.length > 0;
    },
  },
});
</script>
import { createApp } from 'vue';
import NutritionChart from './components/NutritionChart.vue';

console.log('Creating Vue app...');
const app = createApp({});
console.log('Registering component...');
app.component('nutrition-chart', NutritionChart);
console.log('Mounting Vue app...');
try {
  app.mount('#app');
  console.log('Vue app mounted');
} catch (error) {
  console.error('Mount failed:', error);
}
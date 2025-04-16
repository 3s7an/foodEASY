import { createApp } from 'vue';
import * as bootstrap from 'bootstrap';

import NutritionChart from './components/NutritionChart.vue';
import MealPlan from './components/MealPlan.vue';

console.log('Creating Vue app...');

const app = createApp({
    components: {
        NutritionChart,
        MealPlan
    },
    data() {
        return {
            dateFrom: new Date().toISOString().split("T")[0],
            period: '',
            modalTitle: '',
            selectedRecipeId: null
        };
    },
    methods: { 
        modal_recipe_to_list(recipeName, recipeId) {
            this.modalTitle = recipeName || 'Bez názvu'; 
            this.selectedRecipeId = recipeId;
            const modal = new bootstrap.Modal(document.getElementById('recipe_to_list_modal')); 
            modal.show();
        },
        addToList() {
            console.log('Pridané do zoznamu:', this.modalTitle); 
            const modal = bootstrap.Modal.getInstance(document.getElementById('recipeModal')); 
            modal.hide();
        },
        modal_add_recipe() {
            this.modalTitle = 'Vytvorenie receptu';
            const modalElement = document.getElementById('add_recipe_modal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();
        }
    },
    mounted() {
        console.log('Vue je pripojené!');
    }
});

// Globálna registrácia (pre istotu)
app.component('meal-plan', MealPlan);
app.component('nutrition-chart', NutritionChart);

console.log('Mounting Vue app...');
try {
    app.mount('#app');
    console.log('Vue app mounted');
} catch (error) {
    console.error('Mount failed:', error);
}

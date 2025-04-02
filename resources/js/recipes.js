import { createApp } from 'vue'; 
import * as bootstrap from 'bootstrap';

const app = createApp({
    data() {
        return {
            modalTitle: ''
        };
    },
    methods: { 
        openModal(recipeName, recipeId) {
            this.modalTitle = recipeName || 'Bez názvu'; 
            this.selectedRecipeId = recipeId;
            const modal = new bootstrap.Modal(document.getElementById('recipeModal')); 
            modal.show();
        },
        addToList() {
            console.log('Pridané do zoznamu:', this.modalTitle); 
            const modal = bootstrap.Modal.getInstance(document.getElementById('recipeModal')); 
            modal.hide();
        }
    },
    mounted() {
        console.log('Vue je pripojené!');
    }
});


app.mount('#recipe-app');
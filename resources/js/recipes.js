import { createApp } from 'vue'; 
import * as bootstrap from 'bootstrap';

const app = createApp({
    data() {
        return {
            modalTitle: ''
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
        modal_add_recipe(){
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


app.mount('#recipe-app');
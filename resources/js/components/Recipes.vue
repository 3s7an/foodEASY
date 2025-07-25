<template>
  <div class="container mt-4">
    <div class="d-flex align-items-center justify-content-between">
      <h1 class="fw-bold text-center mb-4">Recepty</h1>
      <button class="btn btn-secondary" @click="modal_add_recipe()">
        <i class="fas fa-plus"></i> Nový recept
      </button>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 mt-4">
      <nav class="d-flex gap-2">
        <a href="#" @click.prevent="fetchRecipes()" class="btn btn-outline-secondary btn-sm">
          Všetky recepty
        </a>
        <a href="#" @click.prevent="fetchRecipes('oldest')" class="btn btn-outline-secondary btn-sm">
          Najstašie recepty
        </a>
        <a href="#" @click.prevent="fetchRecipes('newest')" class="btn btn-outline-secondary btn-sm">
          Najnovšie recepty
        </a>
        <a href="#" @click.prevent="fetchRecipes('mine')" class="btn btn-outline-secondary btn-sm">
          Moje recepty
        </a>
      </nav>
    </div>

    <NewRecipeModal v-if="showModal" @close="closeModal" />

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <div class="col" v-for="recipe in recipes" :key="recipe.id">
        <div class="card h-100 shadow-sm border-0 d-flex flex-column">
          <!-- Obrázok hore -->
          <img
            :src="recipe.image_url"
            alt="Obrázok receptu"
            class="card-img-top object-fit-cover"
            style="height: 200px; object-fit: cover;"
          />

          <div class="card-body d-flex flex-column">
            <!-- Názov receptu -->
            <h5 class="card-title mb-3">
              {{ recipe.name || ('Zoznam #' + recipe.id) }}
            </h5>

            <!-- Ikony / tlačidlá dolu -->
            <div class="mt-auto d-flex justify-content-between align-items-center">
              <a
                :href="`/recipes/${recipe.id}`"
                class="btn btn-sm btn-outline-dark"
                title="Zobraziť detail"
              >
                <i class="fas fa-eye"></i>
              </a>

              <!-- Červená ikona koša na mazanie -->
              <button
                class="btn btn-sm btn-outline-danger ms-2"
                title="Zmazať recept"
                @click="deleteRecipe(recipe.id)"
              >
                <i class="fas fa-trash"></i>
              </button>

              <span
                class="badge bg-light text-dark d-flex align-items-center px-2 py-1"
              >
                <i class="fas fa-clock me-1"></i> {{ recipe.id }} min
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import NewRecipeModal from './NewRecipeModal.vue';
import { route } from 'ziggy-js';

export default {
  data() {
    return {
      recipes: [],
      isLoggedIn: window.isLoggedIn === true,
      showModal: false,
    };
  },

  components: {
    NewRecipeModal,
  },

  methods: {
    async fetchRecipes(filter = null) {
      try {
        const response = await axios.get('/recipes', { params: { filter } });
        this.recipes = response.data;
      } catch (error) {
      }
    },

    modal_add_recipe() {
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
    },

    async deleteRecipe(recipeId) {
      if (!confirm("Naozaj chcete zmazať tento recept?")) {
        return;
      }

      try {
        await axios.delete(route('recipes.destroy', recipeId));
        this.recipes = this.recipes.filter(r => r.id !== recipeId);
        alert("Recept bol úspešne zmazaný.");
        window.location.reload();
      } catch (error) {
        console.error("Chyba pri mazani receptu:", error);
        alert("Nepodarilo sa zmazať recept.");
      }
    }
  },

  mounted() {
    this.fetchRecipes();
  },
};
</script>

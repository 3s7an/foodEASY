<template>
  <div class="container mt-5">
    
    <h1 class="fw-bold text-center mb-4">Recepty</h1>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
      
      <nav class="d-flex gap-2">
        <a href="#" @click.prevent="fetchRecipes()" class="btn btn-outline-dark btn-sm">
          Všetky recepty
        </a>
        <a href="#" @click.prevent="fetchRecipes()" class="btn btn-outline-dark btn-sm">
          Najlepšie hodnotené
        </a>
        <a href="#" @click.prevent="fetchRecipes()" class="btn btn-outline-dark btn-sm">
          Rýchle recepty
        </a>
        <a href="#" @click.prevent="fetchRecipes('mine')" class="btn btn-outline-dark btn-sm">
          Moje recepty
        </a>
      </nav>

      <button class="btn btn-dark btn-sm d-flex align-items-center gap-2" @click="modal_add_recipe()">
        <i class="fas fa-plus"></i> Nový recept
      </button>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <div class="col" v-for="recipe in recipes" :key="recipe.id">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title mb-3">
              {{ recipe.name || ('Zoznam #' + recipe.id) }}
            </h5>
            <div class="d-flex justify-content-between align-items-center mt-auto">
              <div class="d-flex gap-2">
                <img :src="recipe.image_url" alt="Obrázok receptu" />
                <a :href="`/recipes/${recipe.id}`" class="btn btn-sm btn-outline-dark" title="Zobraziť detail">
                  <i class="fas fa-eye"></i>
                </a>
                <span class="badge bg-light text-dark d-flex align-items-center px-2 py-1">
                  <i class="fas fa-clock me-1"></i> {{ recipe.id }} min
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> 
  </div>
</template>


<script>
import axios from 'axios';

export default {
  data() {
    return {
      recipes: [],
      isLoggedIn: window.isLoggedIn === true,
    };
  },
  methods: {
    async fetchRecipes(filter = null) {
      try {
        const response = await axios.get('/recipes', { params: { filter } });
        this.recipes = response.data;
        console.log("Prijaté recepty:", this.recipes);
      } catch (error) {
        console.error("Chyba pri načítaní receptov:", error);
      }
    },
    modal_add_recipe() {
    }
  },
  mounted() {
    this.fetchRecipes();
  }
};
</script>

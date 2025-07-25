<template>
  <div class="modal-backdrop" @click.self="$emit('close')">
    <div class="modal-dialog">
      <div class="modal-content p-4">
        <FlashMessage v-if="flash.message" :message="flash.message" :type="flash.type" />

        <h2>Pridaj nový recept</h2>

        <!-- Názov receptu -->
        <div class="mb-4">
          <input v-model="recipe.name" type="text" class="form-control" placeholder="Názov receptu">
        </div>

        <!-- Ingrediencie -->
        <div class="mb-4">
          <h5>Ingrediencie</h5>
          <div class="row g-3 mb-3">
            <div class="col-md-5">
              <input v-model="newItem.name" type="text" class="form-control" placeholder="Názov">
            </div>
            <div class="col-md-3">
              <input v-model.number="newItem.amount" type="number" class="form-control" placeholder="Množstvo">
            </div>
            <div class="col-md-4">
              <select v-model="newItem.unit" class="form-select">
                <option value="g">g</option>
                <option value="ml">ml</option>
              </select>
            </div>
          </div>
          <button class="btn btn-outline-primary btn-sm" @click="sendItemToApi" :disabled="loading">
            {{ loading ? 'Odosielam...' : 'Pridať ingredienciu' }}
          </button>

          <ul class="list-group mt-3" v-if="recipe.items.length">
            <li class="list-group-item" v-for="(item, index) in recipe.items" :key="index">
              <div>
                <strong>{{ item.name }}</strong>
                — {{ item.weight }}g / {{ item.calories }} kcal
              </div>
            </li>
          </ul>
        </div>

        <!-- Postup -->
        <div class="mb-4">
          <h5>Postup</h5>
          <div class="input-group mb-3">
            <input v-model="newStep" type="text" class="form-control" placeholder="Napíš krok...">
            <button class="btn btn-outline-primary" @click="addStep">Pridať krok</button>
          </div>
          <ol class="list-group list-group-numbered" v-if="recipe.steps.length">
            <li class="list-group-item" v-for="(step, index) in recipe.steps" :key="index">
              {{ step }}
            </li>
          </ol>
        </div>

        <!-- Submit -->
        <div class="d-flex justify-content-between">
          <button class="btn btn-secondary" @click="$emit('close')">Zatvoriť</button>
          <button class="btn btn-primary" @click="submitRecipe">Odoslať recept</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import FlashMessage from './FlashMessage.vue'

export default {
  props: {
    recipeId: {
      type: Number,
      required: true
    }
  },
  components: {
    FlashMessage
  },
  data() {
    return {
      recipe: {
        name: '',
        items: [],
        steps: [],
      },
      newItem: {
        name: '',
        amount: '',
        unit: 'g',
      },
      newStep: '',
      loading: false,
      flash: {
        message: '',
        type: '', // 'success' | 'error'
      },
    };
  },
  methods: {
    async sendItemToApi() {
      if (!this.newItem.name || !this.newItem.amount || this.newItem.amount <= 0) {
        this.flash = { message: 'Zadaj platný názov a množstvo.', type: 'error' };
        return;
      }

      this.loading = true;

      try {
        const response = await axios.post(route('recipe.get_nutrients'), {
          name: this.newItem.name,
          amount: this.newItem.amount,
          amount_unit: this.newItem.unit,
        });

        const data = response.data;

        if (data && data.nutrients) {
          const fullItem = {
            name: this.newItem.name,
            amount: this.newItem.amount,
            unit: this.newItem.unit,
            ...data.nutrients,
          };

          this.recipe.items.push(fullItem);
          this.newItem = { name: '', amount: '', unit: 'g' };
          this.flash = { message: 'Ingrediencia pridaná.', type: 'success' };
        } else {
          this.flash = { message: data.message || 'Nepodarilo sa získať výživové údaje.', type: 'error' };
        }
      } catch (error) {
        console.error(error);
        this.flash = { message: 'Chyba pri komunikácii so serverom.', type: 'error' };
      } finally {
        this.loading = false;
      }
    },

    addStep() {
      if (this.newStep.trim() !== '') {
        this.recipe.steps.push(this.newStep.trim());
        this.newStep = '';
      }
    },

    async submitRecipe() {
      try {
        const response = await axios.post(route('recipes.create'), this.recipe);
        this.flash = { message: 'Recept úspešne vytvorený.', type: 'success' };
        setTimeout(() => this.$emit('close'), 1000);
      } catch (error) {
        console.error('Chyba pri ukladaní receptu:', error);
        this.flash = { message: 'Nepodarilo sa uložiť recept.', type: 'error' };
      }
    }
  }
}
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}

.modal-dialog {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 1000px;
  padding: 2rem;
}
</style>

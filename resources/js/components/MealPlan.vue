<template>
  <div>
    <div
      v-for="(day, index) in periodDays"
      :key="index"
      class="row g-3 align-items-center mb-3"
    >
      <div class="col-md-2">
        <span class="text-muted fs-5">{{ formatDate(index) }}</span>
      </div>

      <!-- Zobrazenie ak ide o pokračovanie receptu -->
      <template v-if="isReadonlyDay(index)">
        <div class="col-md-10">
          <input
            type="text"
            class="form-control bg-light"
            :value="getRecipeName(selectedRecipes[index]) + ' (pokračuje)'"
            disabled
          />
        </div>
      </template>

      <!-- Inak zobrazíme výber receptu a dní -->
      <template v-else>
        <div class="col-md-5">
          <select
            class="form-select"
            :name="'recipes[' + index + ']'"
            v-model="selectedRecipes[index]"
          >
            <option value="">Vyber recept</option>
            <option
              v-for="recipe in recipes"
              :key="recipe.id"
              :value="recipe.id"
            >
              {{ recipe.name }}
            </option>
          </select>
        </div>

        <div class="col-md-5" v-if="selectedRecipes[index]">
          <select
            class="form-select"
            :name="'days_count[' + index + ']'"
            v-model.number="daysPerRecipe[index]"
            @change="applyRecipeToDays(index)"
          >
            <option value="">Počet dní</option>
            <option
              v-for="n in getAvailableDays(index)"
              :key="n"
              :value="n"
            >
              {{ n }} {{ n === 1 ? 'deň' : (n < 5 ? 'dni' : 'dní') }}
            </option>
          </select>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    startDate: String,
    period: {
      type: Number,
      required: true
    },
    recipes: Array
  },
  data() {
    return {
      selectedRecipes: [],
      daysPerRecipe: []
    };
  },
  computed: {
    periodDays() {
      return Array.from({ length: this.period || 0 });
    }
  },
  watch: {
    period(newVal) {
      if (typeof newVal === 'number' && newVal > 0) {
        this.selectedRecipes = Array(newVal).fill('');
        this.daysPerRecipe = Array(newVal).fill(0);
      }
    },
    selectedRecipes: {
      deep: true,
      handler(newVal, oldVal) {
        newVal.forEach((recipeId, index) => {
          if (recipeId && this.daysPerRecipe[index]) {
            this.applyRecipeToDays(index);
          }
        });
      }
    }
  },
  methods: {
    formatDate(offset) {
      const date = new Date(this.startDate);
      date.setDate(date.getDate() + offset);
      return date.toLocaleDateString('sk-SK');
    },
    getAvailableDays(index) {
      let available = 0;
      for (let i = index; i < this.period; i++) {
        if (!this.selectedRecipes[i]) {
          available++;
        } else if (i !== index) {
          break;
        }
      }
      return Array.from({ length: available }, (_, i) => i + 1);
    },
    applyRecipeToDays(index) {
      const recipeId = this.selectedRecipes[index];
      const duration = this.daysPerRecipe[index];

      if (!recipeId || !duration) return;

      // Najprv zresetuj pokračovania, ktoré by mohli kolidovať
      for (let i = index + 1; i < index + duration; i++) {
        if (i < this.period) {
          this.selectedRecipes[i] = '';
          this.daysPerRecipe[i] = 0;
        }
      }

      // Potom vyplň pokračujúce dni
      for (let i = 1; i < duration; i++) {
        const nextIndex = index + i;
        if (nextIndex >= this.period) break;

        if (!this.selectedRecipes[nextIndex]) {
          this.selectedRecipes[nextIndex] = recipeId;
          this.daysPerRecipe[nextIndex] = -1; // Označíme ako pokračovanie
        }
      }
    },
    isReadonlyDay(index) {
      return this.daysPerRecipe[index] === -1;
    },
    getRecipeName(id) {
      const recipe = this.recipes.find(r => r.id === id);
      return recipe ? recipe.name : '';
    }
  },
  created() {
    if (typeof this.period === 'number' && this.period > 0) {
      this.selectedRecipes = Array(this.period).fill('');
      this.daysPerRecipe = Array(this.period).fill(0);
    } else {
      console.error('Invalid period:', this.period);
    }
  }
};
</script>

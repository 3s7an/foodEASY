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

      <div class="col-md-5">
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
      // zabezpečíme, že periodDays bude vždy pole s platnou dĺžkou
      return Array.from({ length: this.period || 0 });
    },
    totalUsedDays() {
      return this.daysPerRecipe.reduce((sum, val) => sum + (val || 0), 0);
    }
  },
  watch: {
    period(newVal) {
      if (typeof newVal === "number" && newVal > 0) {
        this.selectedRecipes = Array(newVal).fill('');
        this.daysPerRecipe = Array(newVal).fill(0);
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
      const daysUsedBefore = this.daysPerRecipe.reduce((sum, val, i) => {
        if (i !== index) return sum + (val || 0);
        return sum;
      }, 0);
      const remaining = this.period - daysUsedBefore;
      return Array.from({ length: remaining }, (_, i) => i + 1);
    },
    applyRecipeToDays(index) {
      const recipeId = this.selectedRecipes[index];
      const duration = this.daysPerRecipe[index];

      if (!recipeId || !duration) return;

      for (let i = 1; i < duration; i++) {
        const nextIndex = index + i;
        if (nextIndex >= this.period) break;

        // Ak je nasledujúci deň prázdny, vyplň ho
        if (!this.selectedRecipes[nextIndex]) {
          this.selectedRecipes[nextIndex] = recipeId;
          this.daysPerRecipe[nextIndex] = 0; // deň je súčasťou receptu, ale nemá vlastné trvanie
        }
      }
    }
  },
  created() {
    if (typeof this.period === "number" && this.period > 0) {
      this.selectedRecipes = Array(this.period).fill('');
      this.daysPerRecipe = Array(this.period).fill(0);
    } else {
      console.error('Invalid period:', this.period);
    }
  }
};
</script>
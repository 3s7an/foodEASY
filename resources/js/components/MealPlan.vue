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

      <div class="col-md-2">
        <select
          class="form-select"
          v-model="selectedCategories[index]"
          @change="handleCategoryChange(index)"
        >
          <option value="">Kategória</option>
          <option
            v-for="category in categories"
            :key="category.id"
            :value="category.id"
          >
            {{ category.name }}
          </option>
        </select>
      </div>

      <div class="col-md-4">
        <select
          class="form-select"
          :name="'recipes[' + index + ']'"
          v-model="selectedRecipes[index]"
          @change="handleRecipeChange(index)"
        >
          <option value="">Vyber recept</option>
          <option
            v-for="recipe in filteredRecipes(index)"
            :key="recipe.id"
            :value="recipe.id"
          >
            {{ recipe.name }}
          </option>
        </select>
      </div>

      <div class="col-md-4">
        <select
          class="form-select"
          :name="'days_count[' + index + ']'"
          v-model.number="daysPerRecipe[index]"
          @change="applyRecipeToDays(index)"
        >
          <option value="">Počet dní</option>
          <option v-for="n in getAvailableDays(index)" :key="n" :value="n">
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
    recipes: Array,
    categories: Array
  },
  data() {
    return {
      selectedRecipes: [],
      daysPerRecipe: [],
      selectedCategories: []
    };
  },
  computed: {
    periodDays() {
      return Array.from({ length: this.period || 0 });
    },
    totalUsedDays() {
      return this.daysPerRecipe.reduce((sum, val) => sum + (val || 0), 0);
    }
  },
  methods: {
    formatDate(offset) {
      const date = new Date(this.startDate);
      date.setDate(date.getDate() + offset);
      return date.toLocaleDateString('sk-SK');
    },
    filteredRecipes(index) {
      const selectedCategory = this.selectedCategories[index];
      if (!selectedCategory) return this.recipes;
      return this.recipes.filter(recipe => recipe.category_id === selectedCategory);
    },
    getAvailableDays(index) {
      const daysUsedBefore = this.daysPerRecipe.reduce((sum, val, i) => {
        if (i < index) return sum + (val || 0);
        return sum;
      }, 0);
      const remaining = this.period - daysUsedBefore;
      return Array.from({ length: remaining }, (_, i) => i + 1);
    },
    applyRecipeToDays(index) {
      const recipeId = this.selectedRecipes[index];
      const duration = this.daysPerRecipe[index];
      if (!recipeId || !duration) {
        this.clearFollowingDays(index);
        return;
      }

      for (let i = 1; i < duration; i++) {
        const nextIndex = index + i;
        if (nextIndex >= this.period) break;

        this.selectedRecipes[nextIndex] = recipeId;
        this.daysPerRecipe[nextIndex] = 0;
      }

      this.clearFollowingDays(index + duration);
    },
    handleCategoryChange(index) {
      this.selectedRecipes[index] = ''; 
      this.daysPerRecipe[index] = 0; 
    }
  },
  created() {
    if (typeof this.period === "number" && this.period > 0) {
      this.selectedRecipes = Array(this.period).fill('');
      this.daysPerRecipe = Array(this.period).fill(0);
      this.selectedCategories = Array(this.period).fill('');
    } else {
      console.error('Invalid period:', this.period);
    }
  }
};
</script>

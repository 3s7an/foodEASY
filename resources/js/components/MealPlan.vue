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
          @change="handleRecipeChange(index)"
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

      // Aplikuj recept na nasledujúce dni
      for (let i = 1; i < duration; i++) {
        const nextIndex = index + i;
        if (nextIndex >= this.period) break;

        this.selectedRecipes[nextIndex] = recipeId;
        this.daysPerRecipe[nextIndex] = 0;
      }

      // Vyčisti dni za aktuálnou skupinou
      this.clearFollowingDays(index + duration);
    },
    handleRecipeChange(index) {
      const currentRecipe = this.selectedRecipes[index];

      // Ak je to prvý deň skupiny (má nenulový počet dní)
      if (this.daysPerRecipe[index] > 0) {
        this.applyRecipeToDays(index);
        return;
      }

      // Nájdi začiatok skupiny (prvý predchádzajúci deň s nenulovým počtom dní)
      let groupStartIndex = index;
      while (groupStartIndex > 0 && this.daysPerRecipe[groupStartIndex] === 0) {
        groupStartIndex--;
      }

      // Ak je aktuálny recept iný ako recept skupiny
      if (
        groupStartIndex >= 0 &&
        this.selectedRecipes[groupStartIndex] !== currentRecipe
      ) {
        // Skráť počet dní pre pôvodnú skupinu
        const originalDuration = this.daysPerRecipe[groupStartIndex];
        if (originalDuration > index - groupStartIndex) {
          this.daysPerRecipe[groupStartIndex] = index - groupStartIndex;
        }

        // Aplikuj nový recept od aktuálneho indexu
        this.daysPerRecipe[index] = 1;
        this.applyRecipeToDays(index);
      }
    },
    clearFollowingDays(startIndex) {
      for (let i = startIndex; i < this.period; i++) {
        if (this.daysPerRecipe[i] > 0) {
          this.selectedRecipes[i] = '';
          this.daysPerRecipe[i] = 0;
        } else if (
          this.selectedRecipes[i] &&
          (!this.selectedRecipes[i - 1] || this.selectedRecipes[i] !== this.selectedRecipes[i - 1])
        ) {
          this.selectedRecipes[i] = '';
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
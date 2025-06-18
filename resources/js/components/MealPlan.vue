<template>
  {{ selectedCategories }}
  {{ selectedRecipes }}
  {{ selectedMeals }}
  <h2 class="form-title text-center">Nastavenie jedálničku</h2>
  <!-- Mód generovania -->
  <div class="form-section row">
    <div class="col-md-6">
      <label class="form-label">Režim generovania:</label><br />
      <div class="form-check">
        <input class="form-check-input" type="radio" id="auto" name="generation_mode" value="auto" checked
          v-model="generation_mode" />
        <label class="form-check-label" for="auto">Automatický</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="manual" name="generation_mode" value="manual"
          v-model="generation_mode" />
        <label class="form-check-label" for="manual">Manuálny</label>
      </div>
    </div>
    <div class="col-md-6">
      <label for="start-date" class="form-label">Dátum od:</label>
      <input type="date" class="form-control" id="start-date" name="start_date" v-model="start_date_v" required />
    </div>
  </div>

  <!-- Jedlá za deň -->
  <div class="form-section row">
    <div class="col-md-6 d-flex flex-column">
      <label class="form-label d-block mb-2">Jedlá za deň:</label>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="checkbox-breakfast" name="meals[]" value="breakfast"
          v-model="selectedMeals" @change="updateMealSelections" />
        <label class="form-check-label" for="checkbox-breakfast">Raňajky</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="checkbox-snack1" name="meals[]" value="snack1"
          v-model="selectedMeals" @change="updateMealSelections" />
        <label class="form-check-label" for="checkbox-snack1">Desiata</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="checkbox-lunch" name="meals[]" value="lunch" checked
          v-model="selectedMeals" @change="updateMealSelections" />
        <label class="form-check-label" for="checkbox-lunch">Obed</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="checkbox-snack2" name="meals[]" value="snack2"
          v-model="selectedMeals" @change="updateMealSelections" />
        <label class="form-check-label" for="checkbox-snack2">Olovrant</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="checkbox-dinner" name="meals[]" value="dinner"
          v-model="selectedMeals" @change="updateMealSelections" />
        <label class="form-check-label" for="checkbox-dinner">Večera</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="checkbox-second-dinner" name="meals[]"
          value="second_dinner" v-model="selectedMeals" @change="updateMealSelections" />
        <label class="form-check-label" for="checkbox-second-dinner">Druhá večera</label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="mb-4">
        <label for="days" class="form-label">Počet dní:</label>
        <select class="form-select" id="days" name="days" v-model="selectedPeriod" @change="initializeSelections">
          <option value="3">3 dni</option>
          <option value="5">5 dní</option>
          <option value="7">7 dní</option>
          <option value="14">14 dní</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="no-repeat-days" class="form-label">Neopakovať jedlá počas (dní):</label>
        <input type="number" class="form-control" id="no-repeat-days" name="no_repeat_days" min="0" max="30" step="1"
          value="0" v-model="noRepeatDays" />
      </div>
    </div>
  </div>

  <div v-if="generation_mode === 'manual'" class="mb-4">
    <div v-for="(day, dayIndex) in periodDays" :key="dayIndex" class="mb-4">
      <div class="col-md-2"><span>{{ formatDate(dayIndex) }}</span></div>
      <div v-for="meal in selectedMeals" :key="`${dayIndex}-${meal}`" class="row mb-2">
        <div class="col-md-1">{{ mealLabels[meal] }}</div>
        <div class="col-md-5">
          <select class="form-select" v-model="selectedCategories[dayIndex][meal]"
            @change="handleCategoryChange(dayIndex, meal)">
            <option value="">Kategória</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
        <div class="col-md-5">
          <select class="form-select" v-model="selectedRecipes[dayIndex][meal]"
            @change="handleRecipeChange(dayIndex, meal)">
            <option value="">Vyber recept</option>
            <option v-for="recipe in filteredRecipes(dayIndex, meal)" :key="recipe.id" :value="recipe.id">
              {{ recipe.name }}
            </option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Kalórie -->
  <div class="form-section">
    <label for="calories" class="form-label">
      Denný kalorický limit:
      <span id="calorie-value" class="range-value">{{ calorieValue }}</span> kcal
    </label>
    <input type="range" class="form-range" id="calories" name="calories" min="1200" max="3500" step="50"
      v-model="calorieValue" />
  </div>

  <!-- Percento mäsa -->
  <div class="form-section">
    <label for="meat-percentage" class="form-label">
      Podiel mäsitých jedál:
      <span id="meat-value" class="range-value">{{ meatPercentage }}</span>%
    </label>
    <input type="range" class="form-range" id="meat-percentage" name="meat_percentage" min="0" max="100" step="5"
      v-model="meatPercentage" />
  </div>
</template>

<script>
import { reactive, ref } from 'vue';

export default {
  props: {
    period: {
      type: String,
      required: true
    },
    start_date: String,
    recipes: Array,
    categories: Array
  },
  setup() {
    const selectedPeriod = ref('3');
    const start_date_v = ref('');
    const selectedMeals = ref(['lunch']);
    const generation_mode = ref('auto');
    const calorieValue = ref(2000);
    const meatPercentage = ref(50);
    const noRepeatDays = ref(0);

    const selectedCategories = reactive({});
    const selectedRecipes = reactive({});

    return {
      selectedPeriod,
      start_date_v,
      selectedMeals,
      generation_mode,
      calorieValue,
      meatPercentage,
      noRepeatDays,
      selectedCategories,
      selectedRecipes
    };
  },
  data() {
    return {
      mealLabels: {
        breakfast: 'Raňajky',
        snack1: 'Desiata',
        lunch: 'Obed',
        snack2: 'Olovrant',
        dinner: 'Večera',
        second_dinner: 'Druhá večera'
      }
    };
  },
  computed: {
    periodDays() {
      return Array.from({ length: parseInt(this.selectedPeriod) || 0 });
    }
  },
  methods: {
    initializeSelections() {
      // Clear existing selections
      Object.keys(this.selectedCategories).forEach(key => delete this.selectedCategories[key]);
      Object.keys(this.selectedRecipes).forEach(key => delete this.selectedRecipes[key]);

      // Initialize new structure
      this.periodDays.forEach((_, dayIndex) => {
        this.selectedCategories[dayIndex] = {};
        this.selectedRecipes[dayIndex] = {};
        this.selectedMeals.forEach(meal => {
          this.selectedCategories[dayIndex][meal] = '';
          this.selectedRecipes[dayIndex][meal] = '';
        });
      });
    },
    updateMealSelections() {
      const newCategories = {};
      const newRecipes = {};
      this.periodDays.forEach((_, dayIndex) => {
        newCategories[dayIndex] = {};
        newRecipes[dayIndex] = {};
        this.selectedMeals.forEach(meal => {
          newCategories[dayIndex][meal] = this.selectedCategories[dayIndex]?.[meal] || '';
          newRecipes[dayIndex][meal] = this.selectedRecipes[dayIndex]?.[meal] || '';
        });
      });
      Object.keys(this.selectedCategories).forEach(key => delete this.selectedCategories[key]);
      Object.keys(this.selectedRecipes).forEach(key => delete this.selectedRecipes[key]);
      Object.assign(this.selectedCategories, newCategories);
      Object.assign(this.selectedRecipes, newRecipes);
    },
    formatDate(offset) {
      const date = new Date(this.start_date_v || new Date());
      date.setDate(date.getDate() + offset);
      return date.toLocaleDateString('sk-SK');
    },
    filteredRecipes(dayIndex, meal) {
      const selectedCategory = this.selectedCategories[dayIndex]?.[meal] || '';
      if (!selectedCategory) return this.recipes;
      return this.recipes.filter(recipe => recipe.category_id === selectedCategory);
    },
    handleCategoryChange(dayIndex, meal) {
      this.selectedRecipes[dayIndex][meal] = '';
    },
    handleRecipeChange(dayIndex, meal) {
      console.log(`Selected recipe for ${this.mealLabels[meal]} on day ${dayIndex}: ${this.selectedRecipes[dayIndex][meal]}`);
    }
  },
  created() {
    this.selectedPeriod = this.period || '3';
    this.start_date_v = this.start_date;
    this.initializeSelections();
  },
  watch: {
    selectedPeriod() {
      this.initializeSelections();
    }
  }
};
</script>

<style scoped>
.meal-form {
  max-width: 800px;
  margin: 40px auto;
  background: white;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.form-title {
  font-weight: bold;
  color: #333;
  margin-bottom: 25px;
}

.form-section {
  margin-bottom: 20px;
}

.range-value {
  font-weight: bold;
  color: #0d6efd;
}
</style>
<template>
  <div class="meal-form">
    <h2 class="form-title text-center">Nastavenie jedálničku</h2>
    
    <!-- Mód generovania -->
    <div class="form-section row">
      <div class="col-md-6">
        <label class="form-label">Režim generovania:</label><br />
        <div class="form-check">
          <input class="form-check-input" type="radio" id="auto" name="generation_mode" value="auto" 
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
        <input type="date" class="form-control" id="start-date" name="start_date" 
          v-model="start_date_v" required />
      </div>
    </div>

    <!-- Jedlá za deň -->
    <div class="form-section row">
      <div class="col-md-6 d-flex flex-column">
        <label class="form-label d-block mb-2">Jedlá za deň:</label>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="checkbox-breakfast" value="breakfast" 
            v-model="selectedMeals" @change="updateMealSelections" />
          <label class="form-check-label" for="checkbox-breakfast">Raňajky</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="checkbox-lunch" value="lunch" 
            v-model="selectedMeals" @change="updateMealSelections" />
          <label class="form-check-label" for="checkbox-lunch">Obed</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="checkbox-dinner" value="dinner" 
            v-model="selectedMeals" @change="updateMealSelections" />
          <label class="form-check-label" for="checkbox-dinner">Večera</label>
        </div>
      </div>
      <div class="col-md-6">
        <div class="mb-4">
          <label for="days" class="form-label">Počet dní:</label>
          <select class="form-select" id="days" v-model="selectedPeriod" @change="initializeSelections">
            <option value="3">3 dni</option>
            <option value="5">5 dní</option>
            <option value="7">7 dní</option>
            <option value="14">14 dní</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="no-repeat-days" class="form-label">Neopakovať jedlá počas (dní):</label>
          <input type="number" class="form-control" id="no-repeat-days" min="0" max="30" step="1" 
            v-model.number="noRepeatDays" />
        </div>
      </div>
    </div>

    <!-- Manual selection inputs -->
    <div v-if="generation_mode === 'manual'" class="mb-4">
      <div v-for="(day, dayIndex) in periodDays" :key="dayIndex" class="mb-4">
        <div class="col-md-12"><strong>{{ formatDate(dayIndex) }}</strong></div>
        <div v-for="meal in selectedMeals" :key="`${dayIndex}-${meal}`" class="row mb-2 align-items-center">
          <div class="col-md-2">{{ mealLabels[meal] }}</div>
          <div class="col-md-5">
            <select class="form-select" v-model="selectedCategories[dayIndex][meal]"
              @change="handleCategoryChange(dayIndex, meal)">
              <option value="">Vyber kategóriu</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="col-md-5">
            <select class="form-select" v-model="selectedRecipes[dayIndex][meal]">
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
        <span class="range-value">{{ calorieValue }}</span> kcal
      </label>
      <input type="range" class="form-range" id="calories" min="1200" max="3500" step="50" 
        v-model.number="calorieValue" />
    </div>

    <!-- Percento mäsa -->
    <div class="form-section">
      <label for="meat-percentage" class="form-label">
        Podiel mäsitých jedál:
        <span class="range-value">{{ meatPercentage }}</span>%
      </label>
      <input type="range" class="form-range" id="meat-percentage" min="0" max="100" step="5" 
        v-model.number="meatPercentage" />
    </div>

    <!-- Submit button -->
    <div class="form-section text-center">
      <button class="btn btn-primary" @click="submitForm">Odoslať</button>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';


export default {
  props: {
    period: { type: [String, Number], default: 3 },
    start_date: String,
    recipes: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] }
  },
  setup(props) {
    const selectedPeriod = ref(props.period || 3);
    const start_date_v = ref(props.start_date || new Date().toISOString().split('T')[0]);
    const selectedMeals = ref(['lunch']);
    const generation_mode = ref('auto');
    const calorieValue = ref(2000);
    const meatPercentage = ref(50);
    const noRepeatDays = ref(0);
    
    const selectedCategories = reactive({});
    const selectedRecipes = reactive({});

    const periodDays = computed(() => {
      return Array.from({ length: parseInt(selectedPeriod.value) || 0 });
    });

    const mealLabels = {
      breakfast: 'Raňajky',
      snack1: 'Desiata',
      lunch: 'Obed',
      snack2: 'Olovrant',
      dinner: 'Večera',
      second_dinner: 'Druhá večera'
    };

    function initializeSelections() {
      Object.keys(selectedCategories).forEach(key => delete selectedCategories[key]);
      Object.keys(selectedRecipes).forEach(key => delete selectedRecipes[key]);

      periodDays.value.forEach((_, dayIndex) => {
        selectedCategories[dayIndex] = {};
        selectedRecipes[dayIndex] = {};
        selectedMeals.value.forEach(meal => {
          selectedCategories[dayIndex][meal] = '';
          selectedRecipes[dayIndex][meal] = '';
        });
      });
    }

    function updateMealSelections() {
      const newCategories = {};
      const newRecipes = {};
      periodDays.value.forEach((_, dayIndex) => {
        newCategories[dayIndex] = {};
        newRecipes[dayIndex] = {};
        selectedMeals.value.forEach(meal => {
          newCategories[dayIndex][meal] = selectedCategories[dayIndex]?.[meal] || '';
          newRecipes[dayIndex][meal] = selectedRecipes[dayIndex]?.[meal] || '';
        });
      });
      Object.keys(selectedCategories).forEach(key => delete selectedCategories[key]);
      Object.keys(selectedRecipes).forEach(key => delete selectedRecipes[key]);
      Object.assign(selectedCategories, newCategories);
      Object.assign(selectedRecipes, newRecipes);
    }

    function formatDate(offset) {
      const date = new Date(start_date_v.value || new Date());
      date.setDate(date.getDate() + offset);
      return date.toLocaleDateString('sk-SK');
    }

    function filteredRecipes(dayIndex, meal) {
      const selectedCategory = selectedCategories[dayIndex]?.[meal] || '';
      if (!selectedCategory) return props.recipes;
      return props.recipes.filter(recipe => recipe.category_id === parseInt(selectedCategory));
    }

    function handleCategoryChange(dayIndex, meal) {
      selectedRecipes[dayIndex][meal] = '';
    }

    async function submitForm() {
      const payload = {
        generation_mode: generation_mode.value,
        start_date: start_date_v.value,
        days: parseInt(selectedPeriod.value),
        meals: selectedMeals.value,
        no_repeat_days: noRepeatDays.value,
        calories: calorieValue.value,
        meat_percentage: meatPercentage.value,
        selections: generation_mode.value === 'manual' ? Object.entries(selectedRecipes).reduce((acc, [dayIndex, meals]) => {
          acc[dayIndex] = Object.entries(meals).reduce((mealAcc, [meal, recipeId]) => {
            if (recipeId) {
              mealAcc[meal] = {
                category_id: selectedCategories[dayIndex][meal] || null,
                recipe_id: recipeId
              };
            }
            return mealAcc;
          }, {});
          return acc;
        }, {}) : {}
      };

      try {
        const response = await axios.post(route('plans.store', {}, Ziggy), payload);
        console.log(payload);
        
        alert('Jedálniček bol úspešne odoslaný!');
        console.log('Response:', response.data);
      } catch (error) {
        console.error('Chyba pri odosielaní:', error);
          if (error.response) {
            console.error('Response data:', error.response.data);
            alert('Nastala chyba: ' + JSON.stringify(error.response.data));
          } else {
            alert('Nastala neznáma chyba pri odosielaní jedálnička.');
          }
      }
    }

    initializeSelections();
    watch(selectedPeriod, initializeSelections);

    return {
      selectedPeriod,
      start_date_v,
      selectedMeals,
      generation_mode,
      calorieValue,
      meatPercentage,
      noRepeatDays,
      selectedCategories,
      selectedRecipes,
      periodDays,
      mealLabels,
      initializeSelections,
      updateMealSelections,
      formatDate,
      filteredRecipes,
      handleCategoryChange,
      submitForm
    };
  }
};
</script>

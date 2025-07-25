<template>
  <FlashMessage :message="flash.message" :type="flash.type" />
  <div class="meal-form">
    <!-- Režim + Dátum -->
    <div class="form-section row g-3 mb-4">
      <div class="col-md-6">
        <label class="form-label"><i class="bi bi-gear-fill mt-4"></i>Režim generovania:</label>
        <div class="form-check form-check-custom">
          <input class="form-check-input" type="radio" id="auto" name="generation_mode" value="auto" v-model="generation_mode" />
          <label class="form-check-label" for="auto">Automatický</label>
        </div>
        <div class="form-check form-check-custom">
          <input class="form-check-input" type="radio" id="manual" name="generation_mode" value="manual" v-model="generation_mode" />
          <label class="form-check-label" for="manual">Manuálny</label>
        </div>
      </div>
      <div class="col-md-6">
        <label for="start-date" class="form-label"><i class="bi bi-calendar3 me-2"></i>Dátum od:</label>
        <input type="date" class="form-control" id="start-date" v-model="start_date_v" required />
      </div>
    </div>

    <!-- Jedlá a Dni -->
    <div class="form-section row g-3 mb-4">
      <div class="col-md-6">
        <label class="form-label"><i class="bi bi-egg-fried me-2"></i>Jedlá za deň:</label>
        <div class="form-check-group d-flex flex-column">
          <div class="form-check form-check-inline" v-for="meal in ['breakfast', 'lunch', 'dinner']" :key="meal">
            <input class="form-check-input" type="checkbox" :id="`checkbox-${meal}`" :value="meal" v-model="selectedMeals" @change="updateMealSelections" />
            <label class="form-check-label" :for="`checkbox-${meal}`">{{ mealLabels[meal] }}</label>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <label for="days" class="form-label"><i class="bi bi-clock-history me-2"></i>Počet dní:</label>
        <select class="form-select" id="days" v-model="selectedPeriod" @change="initializeSelections">
          <option value="3">3 dni</option>
          <option value="5">5 dní</option>
          <option value="7">7 dní</option>
          <option value="14">14 dní</option>
        </select>
        <div class="mt-3" v-if="generation_mode === 'auto'">
          <label for="no-repeat-days" class="form-label"><i class="bi bi-arrow-repeat me-2"></i>Neopakovať jedlá počas (dní):</label>
          <input type="number" class="form-control" id="no-repeat-days" min="0" max="30" step="1" v-model.number="noRepeatDays" />
        </div>
      </div>
    </div>

    <!-- Automatický režim -->
    <div class="form-section row g-3" v-if="generation_mode === 'auto'">
      <div class="col-md-5">
        <label class="form-label"><i class="bi bi-tags-fill me-2"></i>Generovať z kategórií:</label>
        <select class="form-select" v-model="categoriesAuto" multiple size="6" style="min-height: 180px;">
          <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
        </select>
        <small class="form-text text-muted">Podrž Ctrl (Cmd) alebo Shift pre výber viacerých kategórií.</small>
      </div>
      <div class="col-md-1"></div>
      <!-- <div class="col-md-6">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="show_calories" v-model="show_calories" />
          <label class="form-check-label" for="show_calories">Nastaviť denný kalorický limit</label>
        </div>
        <div v-if="show_calories" class="mt-3">
          <label for="calories" class="form-label"><i class="bi bi-fire me-2"></i>Denný kalorický limit: <span class="range-value">{{ calorieValue }}</span> kcal</label>
          <input type="range" class="form-range" id="calories" min="1200" max="3500" step="50" v-model.number="calorieValue" />
        </div>
      </div> -->
    </div>

    <!-- Manuálny režim -->
    <div class="form-section mb-4" v-if="generation_mode === 'manual'">
      <div v-for="(day, dayIndex) in periodDays" :key="dayIndex" class="day-section mb-4">
        <h5 class="day-title"><i class="bi bi-calendar-day me-2"></i>{{ formatDate(dayIndex) }}</h5>
        <div v-for="meal in selectedMeals" :key="`${dayIndex}-${meal}`" class="row mb-2 align-items-center">
          <div class="col-md-2 meal-label">{{ mealLabels[meal] }}</div>
          <div class="col-md-5">
            <select
              class="form-select"
              :class="{ 'is-invalid': errors[`selections.${dayIndex}.${meal}.category_id`] }"
              v-model="selectedCategories[dayIndex][meal]"
              @change="handleCategoryChange(dayIndex, meal)"
            >
              <option value="">Vyber kategóriu</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
            </select>
            <p v-if="errors[`selections.${dayIndex}.${meal}.category_id`]" class="text-danger small mt-1">
              {{ errors[`selections.${dayIndex}.${meal}.category_id`][0] }}
            </p>
          </div>
          <div class="col-md-5">
            <select
              class="form-select"
              :class="{ 'is-invalid': errors[`selections.${dayIndex}.${meal}.recipe_id`] }"
              v-model="selectedRecipes[dayIndex][meal]"
            >
              <option value="">Vyber recept</option>
              <option v-for="recipe in filteredRecipes(dayIndex, meal)" :key="recipe.id" :value="recipe.id">{{ recipe.name }}</option>
            </select>
            <p v-if="errors[`selections.${dayIndex}.${meal}.recipe_id`]" class="text-danger small mt-1">
              {{ errors[`selections.${dayIndex}.${meal}.recipe_id`][0] }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Odoslať -->
    <div class="form-section text-center">
      <button class="btn btn-primary btn-lg" @click="submitForm">
        <i class="bi bi-check-circle-fill me-2"></i>Odoslať
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import axios from 'axios'
import { route } from 'ziggy-js'
import FlashMessage from './FlashMessage.vue'

const props = defineProps({
  period: { type: [String, Number], default: 3 },
  start_date: String,
  recipes: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] }
})

const flash = reactive({ message: '', type: '' })
const emit = defineEmits(['closeModal'])

const selectedPeriod = ref(props.period || 3)
const start_date_v = ref(props.start_date || new Date().toISOString().split('T')[0])
const selectedMeals = ref(['lunch'])
const generation_mode = ref('auto')
const calorieValue = ref(2000)
const noRepeatDays = ref(0)
const show_calories = ref(false)
const categoriesAuto = ref([])
const selectedCategories = reactive({})
const selectedRecipes = reactive({})
const errors = ref({}) 

const periodDays = computed(() => Array.from({ length: parseInt(selectedPeriod.value) || 0 }))
const mealLabels = {
  breakfast: 'Raňajky',
  // snack1: 'Desiata',
  lunch: 'Obed',
  snack2: 'Olovrant',
  dinner: 'Večera',
  second_dinner: 'Druhá večera'
}

function initializeSelections() {
  Object.keys(selectedCategories).forEach(key => delete selectedCategories[key])
  Object.keys(selectedRecipes).forEach(key => delete selectedRecipes[key])

  periodDays.value.forEach((_, dayIndex) => {
    selectedCategories[dayIndex] = {}
    selectedRecipes[dayIndex] = {}
    selectedMeals.value.forEach(meal => {
      selectedCategories[dayIndex][meal] = ''
      selectedRecipes[dayIndex][meal] = ''
    })
  })

  errors.value = {} 
}

function updateMealSelections() {
  const newCategories = {}
  const newRecipes = {}
  periodDays.value.forEach((_, dayIndex) => {
    newCategories[dayIndex] = {}
    newRecipes[dayIndex] = {}
    selectedMeals.value.forEach(meal => {
      newCategories[dayIndex][meal] = selectedCategories[dayIndex]?.[meal] || ''
      newRecipes[dayIndex][meal] = selectedRecipes[dayIndex]?.[meal] || ''
    })
  })
  Object.assign(selectedCategories, newCategories)
  Object.assign(selectedRecipes, newRecipes)
}

function formatDate(offset) {
  const date = new Date(start_date_v.value || new Date())
  date.setDate(date.getDate() + offset)
  return date.toLocaleDateString('sk-SK')
}

function filteredRecipes(dayIndex, meal) {
  const selectedCategory = selectedCategories[dayIndex]?.[meal] || ''
  if (!selectedCategory) return props.recipes
  return props.recipes.filter(recipe => recipe.category_id === parseInt(selectedCategory))
}

function handleCategoryChange(dayIndex, meal) {
  selectedRecipes[dayIndex][meal] = ''
}

async function submitForm() {
  errors.value = {}

  const payload = {
    generation_mode: generation_mode.value,
    start_date: start_date_v.value,
    days: parseInt(selectedPeriod.value),
    meals: selectedMeals.value,
    auto_categories: generation_mode.value === 'auto' ? categoriesAuto.value : [],
    calorie_limit: show_calories.value ? calorieValue.value : null,
    no_repeat_days: noRepeatDays.value,
    selections: {}
  }

  if (generation_mode.value === 'manual') {
    let hasError = false
    const newErrors = {}

    periodDays.value.forEach((_, dayIndex) => {
      payload.selections[dayIndex] = {} 

      selectedMeals.value.forEach(meal => {
        const category = selectedCategories[dayIndex]?.[meal]
        const recipe = selectedRecipes[dayIndex]?.[meal]

        if (!category) {
          hasError = true
          newErrors[`selections.${dayIndex}.${meal}.category_id`] = ['Pole je povinné.']
        }

        if (!recipe) {
          hasError = true
          newErrors[`selections.${dayIndex}.${meal}.recipe_id`] = ['Pole je povinné.']
        }

        payload.selections[dayIndex][meal] = {
          category_id: category,
          recipe_id: recipe
        }
      })
    })

    if (hasError) {
      errors.value = newErrors
      flash.message = 'Prosím, vyplň všetky polia v manuálnom režime.'
      flash.type = 'danger'
      return
    }
  }

  try {
    const response = await axios.post(route('plans.store'), payload)
    flash.message = response.data.message
    flash.type = response.data.status
    window.location.reload();
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      flash.message = reponse.data.message
      flash.type = 'danger'
    } else {
      flash.message = response.data.message
      flash.type = 'danger'
    }
  }
}


watch([selectedPeriod, selectedMeals], () => {
  initializeSelections()
}, { immediate: true })

watch(generation_mode, () => {
  errors.value = {}
  initializeSelections()
})
</script>

<style scoped>
.is-invalid {
  border-color: #dc3545 !important;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
.text-danger {
  margin-top: 0.25rem;
  font-size: 0.875em;
}
.meal-label {
  font-weight: 600;
}
.day-title {
  font-weight: 700;
  margin-bottom: 1rem;
  color: #3a3a3a;
}
.form-section {
  border-bottom: 1px solid #e2e2e2;
  padding-bottom: 1rem;
}
</style>

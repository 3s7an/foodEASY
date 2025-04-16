<template>
  <div>
    <div v-for="(day, index) in periodDays" :key="index" class="row g-3 align-items-center mb-3">
      <div class="col-md-2">
        <span class="text-muted fs-5">{{ formatDate(index) }}</span>
      </div>
      <div class="col-md-5">
        <select class="form-select" :name="'recipes[' + index + ']'" v-model="selectedRecipes[index]">
          <option v-for="recipe in recipes" :key="recipe.id" :value="recipe.id">
            {{ recipe.name }}
          </option>
        </select>
      </div>
      <div class="col-md-5">
        <select class="form-select" id="days_count" name="days_count">
          <option>Pocet dni</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    startDate: String,
    period: Number,
    recipes: Array
  },
  data() {
    return {
      selectedRecipes: []
    };
  },
  computed: {
    periodDays() {
      return Array.from({ length: this.period });
    }
  },
  methods: {
    formatDate(offset) {
      const date = new Date(this.startDate);
      date.setDate(date.getDate() + offset);
      return date.toLocaleDateString('sk-SK');
    }
  }
}
</script>

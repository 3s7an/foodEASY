<template>
  <div
    v-if="visible"
    :class="['alert', alertClass, 'alert-dismissible', 'fade', 'show']"
    role="alert"
  >
    {{ message }}
    <button type="button" class="btn-close" @click="visible = false"></button>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
  message: String,
  type: {
    type: String,
    default: 'success', 
  }
})

const visible = ref(!!props.message)

watch(() => props.message, (val) => {
  visible.value = !!val
})

const alertClass = computed(() => {
  switch (props.type) {
    case 'success':
      return 'alert-success' 
    case 'error':
      return 'alert-danger'   
    case 'warning':
      return 'alert-warning' 
    case 'info':
      return 'alert-info'    
    default:
      return 'alert-primary'  
  }
})
</script>

<style scoped>
.alert {
  margin-top: 1rem;
}
</style>

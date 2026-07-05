<template>
  <div>
    <label v-if="label" :for="id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
      {{ label }}
    </label>
    <div class="relative">
      <div v-if="icon" class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
        <Icon :name="icon" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
      </div>
      
      <input 
        :type="inputType" 
        :id="id" 
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        :class="[
          'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
          icon ? 'ps-10' : '',
          isPassword ? 'pe-10' : ''
        ]"
        :placeholder="placeholder" 
        v-bind="$attrs"
      >
      
      <button 
        v-if="isPassword" 
        type="button"
        @click="togglePasswordVisibility"
        class="absolute inset-y-0 end-0 flex items-center pe-3.5 focus:outline-none"
      >
        <Icon 
          :name="showPassword ? 'heroicons:eye-slash' : 'heroicons:eye'" 
          class="w-5 h-5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors cursor-pointer" 
        />
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

defineOptions({
  inheritAttrs: false
})

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  id: {
    type: String,
    default: () => `input-${Math.random().toString(36).substring(2, 9)}`
  }
})

defineEmits(['update:modelValue'])

const showPassword = ref(false)

const isPassword = computed(() => props.type === 'password')
const inputType = computed(() => {
  if (isPassword.value) {
    return showPassword.value ? 'text' : 'password'
  }
  return props.type
})

function togglePasswordVisibility() {
  showPassword.value = !showPassword.value
}
</script>

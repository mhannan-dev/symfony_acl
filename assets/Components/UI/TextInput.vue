<script setup>
import { ref, computed } from 'vue'
import { Eye, EyeOff } from 'lucide-vue-next'

const props = defineProps({
  modelValue: String,
  label: String,
  id: String,
  type: {
    type: String,
    default: 'text'
  },
  placeholder: String,
  error: String,
  required: Boolean
})

defineEmits(['update:modelValue'])

const showPassword = ref(false)

const inputType = computed(() => {
  if (props.type === 'password') {
    return showPassword.value ? 'text' : 'password'
  }
  return props.type
})
</script>

<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium text-slate-700 mb-1.5">
      {{ label }}
    </label>
    <div class="relative">
      <div v-if="$slots.icon" class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
        <slot name="icon"></slot>
      </div>
      
      <input
        :id="id"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :type="inputType"
        :required="required"
        class="block w-full rounded-xl border bg-white py-3 text-slate-900 placeholder-slate-400 focus:ring-4 focus:outline-none transition-all text-sm"
        :class="[
          $slots.icon ? 'pl-10' : 'pl-4',
          type === 'password' ? 'pr-12' : 'pr-4',
          error 
            ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500/10' 
            : 'border-slate-200 focus:border-blue-500 focus:ring-blue-500/10'
        ]"
        :placeholder="placeholder"
      />
      
      <button
        v-if="type === 'password'"
        type="button"
        @click="showPassword = !showPassword"
        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors"
        :aria-label="showPassword ? 'Hide password' : 'Show password'"
      >
        <EyeOff v-if="showPassword" class="w-5 h-5" />
        <Eye v-else class="w-5 h-5" />
      </button>
    </div>
    <p v-if="error" class="mt-1.5 text-xs text-rose-600">{{ error }}</p>
  </div>
</template>

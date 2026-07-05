<template>
  <Teleport to="body">
    <div v-if="modelValue" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 transition-opacity" @click="$emit('update:modelValue', false)"></div>
      
      <!-- Modal -->
      <div class="relative w-full max-w-md bg-white rounded-xl shadow-lg dark:bg-gray-800 overflow-hidden transform transition-all">
        <div class="p-6 text-center">
          <Icon name="heroicons:exclamation-circle" class="mx-auto mb-4 text-gray-400 w-14 h-14 dark:text-gray-200" />
          <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
            <slot>{{ message }}</slot>
          </h3>
          <div class="flex justify-center gap-3">
            <button @click="$emit('confirm')" type="button" class="text-white bg-rose-600 hover:bg-rose-700 focus:ring-4 focus:outline-none focus:ring-rose-300 dark:focus:ring-rose-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors">
              Yes, I'm sure
            </button>
            <button @click="$emit('update:modelValue', false)" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600 transition-colors">
              No, cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
defineProps({
  modelValue: { type: Boolean, required: true },
  message: { type: String, default: 'Are you sure you want to delete this item?' }
})
defineEmits(['update:modelValue', 'confirm'])
</script>

<template>
  <NuxtLink :to="to" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg px-4 py-2 transition-all focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-800 shadow-sm">
    <Icon :name="icon" class="w-5 h-5" />
    <slot />
  </NuxtLink>
</template>

<script setup lang="ts">
defineProps({
  to: { type: String, required: true },
  icon: { type: String, default: 'heroicons:plus' }
})
</script>

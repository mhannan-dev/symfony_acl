<template>
  <NuxtLink v-if="to" :to="to" :title="title" class="p-2 inline-flex items-center justify-center rounded-lg transition-colors focus:ring-2 focus:outline-none" :class="colorClass">
    <Icon :name="icon" class="w-5 h-5" />
    <span class="sr-only">{{ title }}</span>
  </NuxtLink>
  
  <button v-else @click="$emit('click')" :title="title" type="button" class="p-2 inline-flex items-center justify-center rounded-lg transition-colors focus:ring-2 focus:outline-none" :class="colorClass">
    <Icon :name="icon" class="w-5 h-5" />
    <span class="sr-only">{{ title }}</span>
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps({
  to: { type: String, default: undefined },
  icon: { type: String, required: true },
  title: { type: String, required: true },
  color: { type: String, default: 'blue', validator: (v: string) => ['blue', 'red'].includes(v) }
})

defineEmits(['click'])

const colorClass = computed(() => {
  if (props.color === 'blue') return 'text-blue-600 hover:bg-blue-50 focus:ring-blue-300 dark:text-blue-500 dark:hover:bg-gray-700'
  if (props.color === 'red') return 'text-rose-600 hover:bg-rose-50 focus:ring-rose-300 dark:text-rose-500 dark:hover:bg-gray-700'
  return ''
})
</script>

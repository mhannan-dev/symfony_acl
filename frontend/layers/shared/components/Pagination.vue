<template>
  <div class="flex items-center justify-between px-6 py-3 border-t border-slate-200">
    <div class="flex items-center gap-3">
      <div class="flex items-center gap-1.5 text-sm text-slate-500">
        <span>Rows per page:</span>
        <select :value="perPage" @change="$emit('update:perPage', Number(($event.target as HTMLSelectElement).value))"
          class="border border-slate-300 rounded-md px-2 py-1 text-sm text-slate-700 bg-white focus:ring-primary-500 focus:border-primary-500">
          <option v-for="opt in perPageOptions" :key="opt" :value="opt">{{ opt }}</option>
        </select>
      </div>

      <p class="text-sm text-slate-500">
        <span class="font-medium">{{ from }}-{{ to }}</span>
        of <span class="font-medium">{{ total }}</span>
      </p>
    </div>

    <div v-if="lastPage > 1" class="flex items-center gap-1">
      <button @click="go(currentPage - 1)" :disabled="currentPage <= 1"
        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
        :class="currentPage <= 1 ? 'text-slate-300' : 'text-slate-600 hover:bg-slate-100'">
        Previous
      </button>

      <button v-for="p in pages" :key="p" @click="go(p)"
        class="min-w-[2rem] px-2 py-1.5 text-sm font-medium rounded-md transition-colors"
        :class="p === currentPage ? 'bg-primary-600 text-white' : 'text-slate-600 hover:bg-slate-100'">
        {{ p }}
      </button>

      <button @click="go(currentPage + 1)" :disabled="currentPage >= lastPage"
        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
        :class="currentPage >= lastPage ? 'text-slate-300' : 'text-slate-600 hover:bg-slate-100'">
        Next
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  currentPage: number
  lastPage: number
  total: number
  perPage: number
}>()

const emit = defineEmits<{
  page: [page: number]
  'update:perPage': [value: number]
}>()

const perPageOptions = [5, 10, 25, 50, 100]

const from = computed(() => Math.min((props.currentPage - 1) * props.perPage + 1, props.total))
const to = computed(() => Math.min(props.currentPage * props.perPage, props.total))

const pages = computed(() => {
  const { currentPage, lastPage } = props
  if (lastPage <= 7) {
    return Array.from({ length: lastPage }, (_, i) => i + 1)
  }

  const result: (number | '...')[] = [1]
  let start = Math.max(2, currentPage - 1)
  let end = Math.min(lastPage - 1, currentPage + 1)

  if (currentPage <= 3) {
    end = Math.min(5, lastPage - 1)
  }
  if (currentPage >= lastPage - 2) {
    start = Math.max(2, lastPage - 4)
  }

  if (start > 2) result.push('...')
  for (let i = start; i <= end; i++) result.push(i)
  if (end < lastPage - 1) result.push('...')
  result.push(lastPage)

  return result
})

function go(page: number) {
  if (page >= 1 && page <= props.lastPage) {
    emit('page', page)
  }
}
</script>

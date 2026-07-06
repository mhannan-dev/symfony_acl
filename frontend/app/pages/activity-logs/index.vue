<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-900">Activity Logs</h1>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
      <table class="w-full">
        <thead>
          <tr class="border-b border-slate-200 bg-slate-50">
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Time</th>
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">User</th>
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Action</th>
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Object</th>
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Content Type</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="log in logs" :key="log.id" class="hover:bg-slate-50">
            <td class="px-6 py-4 text-sm text-slate-500 whitespace-nowrap">{{ new Date(log.actionTime).toLocaleString() }}</td>
            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ log.user?.name || '&mdash;' }}</td>
            <td class="px-6 py-4 text-sm">
              <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="actionClass(log.actionFlag)">{{ actionLabel(log.actionFlag) }}</span>
            </td>
            <td class="px-6 py-4 text-sm text-slate-500">{{ log.objectRepr }}</td>
            <td class="px-6 py-4 text-sm text-slate-500">{{ log.contentType?.appLabel || '&mdash;' }}</td>
          </tr>
          <tr v-if="!logs.length">
            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400">No activity logs found.</td>
          </tr>
        </tbody>
      </table>
      <Pagination :current-page="page" :last-page="lastPage" :total="total" :per-page="perPage" @page="loadLogs" @update:per-page="onPerPageChange" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'auth',
})

const { get } = useApi()
const logs = ref<any[]>([])
const page = ref(1)
const perPage = ref(10)
const lastPage = ref(1)
const total = ref(0)

onMounted(() => loadLogs())

function onPerPageChange(val: number) {
  perPage.value = val
  page.value = 1
  loadLogs()
}

async function loadLogs(p?: number) {
  if (p) page.value = p
  const { data } = await get<{ logs: any[]; pagination: { currentPage: number; lastPage: number; total: number } }>(`/activity-logs?page=${page.value}&perPage=${perPage.value}`)
  if (data?.logs) logs.value = data.logs
  if (data?.pagination) {
    lastPage.value = data.pagination.lastPage
    total.value = data.pagination.total
  }
}

function actionLabel(flag: number) {
  const labels = { 1: 'Addition', 2: 'Change', 3: 'Deletion' }
  return labels[flag] || `Action ${flag}`
}

function actionClass(flag: number) {
  const classes = {
    1: 'bg-emerald-100 text-emerald-700',
    2: 'bg-amber-100 text-amber-700',
    3: 'bg-rose-100 text-rose-700',
  }
  return classes[flag] || 'bg-slate-100 text-slate-700'
}
</script>

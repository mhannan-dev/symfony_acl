<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>

    <div v-if="loading" class="text-slate-500">Loading...</div>

    <template v-else>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Users</span>
          </div>
          <p class="text-3xl font-bold text-slate-900">{{ stats.users }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Groups</span>
          </div>
          <p class="text-3xl font-bold text-slate-900">{{ stats.groups }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Permissions</span>
          </div>
          <p class="text-3xl font-bold text-slate-900">{{ stats.permissions }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Activity Logs</span>
          </div>
          <p class="text-3xl font-bold text-slate-900">{{ stats.recentActivity }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <h3 class="text-sm font-semibold text-slate-700 mb-4">Users per Group</h3>
          <Pie :data="usersPerGroupChart" :options="chartOptions" />
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <h3 class="text-sm font-semibold text-slate-700 mb-4">User Status</h3>
          <Doughnut :data="userStatusChart" :options="chartOptions" />
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <h3 class="text-sm font-semibold text-slate-700 mb-4">Permissions per Content Type</h3>
          <Pie :data="permsPerTypeChart" :options="chartOptions" />
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <h3 class="text-sm font-semibold text-slate-700 mb-4">Activity (Last 7 Days)</h3>
          <Line :data="activityChart" :options="lineChartOptions" />
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Pie, Doughnut, Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Filler,
} from 'chart.js'

ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement, Filler)

definePageMeta({
  layout: 'admin',
  middleware: 'auth',
})

const { get } = useApi()
const loading = ref(true)
const stats = ref({ users: 0, groups: 0, permissions: 0, recentActivity: 0 })
const charts = ref({
  usersPerGroup: [] as { name: string; count: number }[],
  userStatus: [] as { label: string; count: number }[],
  permissionsPerContentType: [] as { app_label: string; model: string; count: number }[],
  activityLast7Days: [] as { date: string; count: number }[],
})

const colors = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1']

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'bottom' as const, labels: { usePointStyle: true, padding: 16 } },
  },
}

const lineChartOptions = {
  responsive: true,
  plugins: {
    legend: { display: false },
  },
  scales: {
    y: { beginAtZero: true, ticks: { stepSize: 1 } },
    x: { grid: { display: false } },
  },
  elements: {
    line: { tension: 0.35, fill: true },
  },
}

const usersPerGroupChart = computed(() => ({
  labels: charts.value.usersPerGroup.map(g => g.name),
  datasets: [{
    data: charts.value.usersPerGroup.map(g => g.count),
    backgroundColor: colors.slice(0, charts.value.usersPerGroup.length),
    borderWidth: 0,
  }],
}))

const userStatusChart = computed(() => ({
  labels: charts.value.userStatus.map(s => s.label),
  datasets: [{
    data: charts.value.userStatus.map(s => s.count),
    backgroundColor: ['#10b981', '#ef4444'],
    borderWidth: 0,
  }],
}))

const permsPerTypeChart = computed(() => ({
  labels: charts.value.permissionsPerContentType.map(p => `${p.app_label} / ${p.model}`),
  datasets: [{
    data: charts.value.permissionsPerContentType.map(p => p.count),
    backgroundColor: colors.slice(0, charts.value.permissionsPerContentType.length),
    borderWidth: 0,
  }],
}))

const activityChart = computed(() => ({
  labels: charts.value.activityLast7Days.map(a => a.date),
  datasets: [{
    label: 'Actions',
    data: charts.value.activityLast7Days.map(a => a.count),
    borderColor: '#3b82f6',
    backgroundColor: 'rgba(59, 130, 246, 0.1)',
    pointBackgroundColor: '#3b82f6',
    pointRadius: 4,
    borderWidth: 2,
  }],
}))

onMounted(async () => {
  const { data } = await get<{ stats: typeof stats.value; charts: typeof charts.value }>('/dashboard/stats')
  if (data) {
    stats.value = data.stats
    if (data.charts) charts.value = data.charts
  }
  loading.value = false
})
</script>

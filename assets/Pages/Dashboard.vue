<template>
  <Layout>
    <div class="space-y-6">
      <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>

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
    </div>
  </Layout>
</template>

<script setup>
import Layout from './Layout.vue'

defineProps({
  stats: Object,
})
</script>

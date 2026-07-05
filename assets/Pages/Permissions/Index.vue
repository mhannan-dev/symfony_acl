<template>
  <Layout>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Permissions</h1>
        <Link href="/admin/permissions/new" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg px-4 py-2 transition-colors">
          Add Permission
        </Link>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full">
          <thead>
            <tr class="border-b border-slate-200 bg-slate-50">
              <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Name</th>
              <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Codename</th>
              <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Content Type</th>
              <th class="text-right text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="perm in permissions" :key="perm.id" class="hover:bg-slate-50">
              <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ perm.name }}</td>
              <td class="px-6 py-4 text-sm text-slate-500"><code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs">{{ perm.codename }}</code></td>
              <td class="px-6 py-4 text-sm text-slate-500">{{ perm.contentType ? `${perm.contentType.appLabel} — ${perm.contentType.model}` : '—' }}</td>
              <td class="px-6 py-4 text-right">
                <Link :href="`/admin/permissions/${perm.id}/edit`" class="text-sm text-blue-600 hover:text-blue-800 font-medium mr-3">Edit</Link>
                <button @click="deletePermission(perm.id)" class="text-sm text-rose-600 hover:text-rose-800 font-medium">Delete</button>
              </td>
            </tr>
            <tr v-if="!permissions.length">
              <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-400">No permissions found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3'
import Layout from '../Layout.vue'

defineProps({
  permissions: Array,
})

function deletePermission(id) {
  if (confirm('Are you sure you want to delete this permission?')) {
    router.post(`/admin/permissions/${id}/delete`)
  }
}
</script>

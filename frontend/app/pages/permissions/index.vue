<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-slate-900">Permissions</h1>
      <NuxtLink to="/permissions/new" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg px-4 py-2 transition-colors">
        Add Permission
      </NuxtLink>
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
            <td class="px-6 py-4 text-sm text-slate-500">{{ perm.contentType ? `${perm.contentType.appLabel} &mdash; ${perm.contentType.model}` : '&mdash;' }}</td>
            <td class="px-6 py-4 text-right">
              <NuxtLink :to="`/permissions/${perm.id}`" class="text-sm text-blue-600 hover:text-blue-800 font-medium mr-3">Edit</NuxtLink>
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
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'admin',
  middleware: 'auth',
})

const { get, del } = useApi()
const permissions = ref<any[]>([])

onMounted(async () => {
  const { data } = await get<{ permissions: any[] }>('/permissions')
  if (data?.permissions) permissions.value = data.permissions
})

async function deletePermission(id: number) {
  if (confirm('Are you sure you want to delete this permission?')) {
    await del(`/permissions/${id}/delete`)
    permissions.value = permissions.value.filter(p => p.id !== id)
  }
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-slate-900">Content Types</h1>
      <NuxtLink to="/content-types/new" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg px-4 py-2 transition-colors">
        Add Content Type
      </NuxtLink>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
      <table class="w-full">
        <thead>
          <tr class="border-b border-slate-200 bg-slate-50">
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">App Label</th>
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Model</th>
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Permissions</th>
            <th class="text-right text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="ct in contentTypes" :key="ct.id" class="hover:bg-slate-50">
            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ ct.appLabel }}</td>
            <td class="px-6 py-4 text-sm text-slate-500"><code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs">{{ ct.model }}</code></td>
            <td class="px-6 py-4 text-sm text-slate-500">{{ ct.permissions.length }}</td>
            <td class="px-6 py-4 text-right">
              <NuxtLink :to="`/content-types/${ct.id}`" class="text-sm text-blue-600 hover:text-blue-800 font-medium mr-3">Edit</NuxtLink>
              <button @click="deleteContentType(ct.id)" class="text-sm text-rose-600 hover:text-rose-800 font-medium">Delete</button>
            </td>
          </tr>
          <tr v-if="!contentTypes.length">
            <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-400">No content types found.</td>
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
const contentTypes = ref<any[]>([])

onMounted(async () => {
  const { data } = await get<{ contentTypes: any[] }>('/content-types')
  if (data?.contentTypes) contentTypes.value = data.contentTypes
})

async function deleteContentType(id: number) {
  if (confirm('Are you sure you want to delete this content type?')) {
    await del(`/content-types/${id}/delete`)
    contentTypes.value = contentTypes.value.filter(ct => ct.id !== id)
  }
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <h1 class="text-2xl font-bold text-slate-900">Permissions</h1>
      
      <div class="flex items-center gap-3 w-full sm:w-auto">
        <div class="relative w-full sm:w-64">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <Icon name="heroicons:magnifying-glass" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
          </div>
          <input 
            type="text" 
            v-model="searchQuery" 
            class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 shadow-sm transition-colors" 
            placeholder="Search permissions..." 
          />
        </div>
        <CreateButton to="/permissions/new" icon="heroicons:key" class="whitespace-nowrap">
          Add Permission
        </CreateButton>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mt-6">
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
          <tr v-for="perm in filteredPermissions" :key="perm.id" class="hover:bg-slate-50">
            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ perm.name }}</td>
            <td class="px-6 py-4 text-sm text-slate-500"><code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs">{{ perm.codename }}</code></td>
            <td class="px-6 py-4 text-sm text-slate-500">{{ perm.contentType ? `${perm.contentType.appLabel} &mdash; ${perm.contentType.model}` : '&mdash;' }}</td>
            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
              <ActionIconButton :to="`/permissions/${perm.id}`" icon="heroicons:pencil-square" title="Edit" color="blue" />
              <ActionIconButton @click="confirmDelete(perm.id)" icon="heroicons:trash" title="Delete" color="red" />
            </td>
          </tr>
          <tr v-if="!filteredPermissions.length">
            <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
              <div v-if="searchQuery" class="flex flex-col items-center justify-center">
                <Icon name="heroicons:magnifying-glass" class="w-8 h-8 text-slate-300 mb-2" />
                No permissions found matching "{{ searchQuery }}".
              </div>
              <div v-else>No permissions found.</div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <ConfirmModal 
      v-model="isDeleteModalOpen" 
      message="Are you sure you want to delete this permission? This action cannot be undone."
      @confirm="executeDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'auth',
})

const { get, del } = useApi()
const permissions = ref<any[]>([])
const searchQuery = ref('')

const filteredPermissions = computed(() => {
  if (!searchQuery.value) return permissions.value
  
  const query = searchQuery.value.toLowerCase()
  return permissions.value.filter(p => {
    return (
      (p.name && p.name.toLowerCase().includes(query)) ||
      (p.codename && p.codename.toLowerCase().includes(query)) ||
      (p.contentType && p.contentType.model && p.contentType.model.toLowerCase().includes(query)) ||
      (p.contentType && p.contentType.appLabel && p.contentType.appLabel.toLowerCase().includes(query))
    )
  })
})

const isDeleteModalOpen = ref(false)
const itemToDelete = ref<number | null>(null)

onMounted(async () => {
  const { data } = await get<{ permissions: any[] }>('/permissions')
  if (data?.permissions) permissions.value = data.permissions
})

function confirmDelete(id: number) {
  itemToDelete.value = id
  isDeleteModalOpen.value = true
}

async function executeDelete() {
  if (itemToDelete.value) {
    await del(`/permissions/${itemToDelete.value}/delete`)
    permissions.value = permissions.value.filter(p => p.id !== itemToDelete.value)
    isDeleteModalOpen.value = false
    itemToDelete.value = null
  }
}
</script>

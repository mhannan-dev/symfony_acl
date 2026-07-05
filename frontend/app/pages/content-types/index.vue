<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-slate-900">Content Types</h1>
      <CreateButton to="/content-types/new" icon="heroicons:document-text">
        Add Content Type
      </CreateButton>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mt-6">
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
            <td class="px-6 py-4 text-sm text-slate-500">{{ ct.permissions ? ct.permissions.length : 0 }}</td>
            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
              <ActionIconButton :to="`/content-types/${ct.id}`" icon="heroicons:pencil-square" title="Edit" color="blue" />
              <ActionIconButton @click="confirmDelete(ct.id)" icon="heroicons:trash" title="Delete" color="red" />
            </td>
          </tr>
          <tr v-if="!contentTypes.length">
            <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-400">No content types found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <ConfirmModal 
      v-model="isDeleteModalOpen" 
      message="Are you sure you want to delete this content type? This action cannot be undone."
      @confirm="executeDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'auth',
})

const { get, del } = useApi()
const contentTypes = ref<any[]>([])

const isDeleteModalOpen = ref(false)
const itemToDelete = ref<number | null>(null)

onMounted(async () => {
  const { data } = await get<{ contentTypes: any[] }>('/content-types')
  if (data?.contentTypes) contentTypes.value = data.contentTypes
})

function confirmDelete(id: number) {
  itemToDelete.value = id
  isDeleteModalOpen.value = true
}

async function executeDelete() {
  if (itemToDelete.value) {
    await del(`/content-types/${itemToDelete.value}/delete`)
    contentTypes.value = contentTypes.value.filter(ct => ct.id !== itemToDelete.value)
    isDeleteModalOpen.value = false
    itemToDelete.value = null
  }
}
</script>

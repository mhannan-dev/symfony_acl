<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-slate-900">Groups</h1>
      <CreateButton to="/groups/new" icon="heroicons:user-group">
        Add Group
      </CreateButton>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mt-6">
      <table class="w-full">
        <thead>
          <tr class="border-b border-slate-200 bg-slate-50">
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Name</th>
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Permissions</th>
            <th class="text-right text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="group in groups" :key="group.id" class="hover:bg-slate-50">
            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ group.name }}</td>
            <td class="px-6 py-4 text-sm text-slate-500">{{ group.groupPermissions.length }}</td>
            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
              <ActionIconButton :to="`/groups/${group.id}`" icon="heroicons:pencil-square" title="Edit" color="blue" />
              <ActionIconButton @click="confirmDelete(group.id)" icon="heroicons:trash" title="Delete" color="red" />
            </td>
          </tr>
          <tr v-if="!groups.length">
            <td colspan="3" class="px-6 py-12 text-center text-sm text-slate-400">No groups found.</td>
          </tr>
        </tbody>
      </table>
      <Pagination :current-page="page" :last-page="lastPage" :total="total" @page="loadGroups" />
    </div>

    <ConfirmModal 
      v-model="isDeleteModalOpen" 
      message="Are you sure you want to delete this group? This action cannot be undone."
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
const groups = ref<any[]>([])
const page = ref(1)
const lastPage = ref(1)
const total = ref(0)

const isDeleteModalOpen = ref(false)
const itemToDelete = ref<number | null>(null)

onMounted(() => loadGroups())

async function loadGroups(p?: number) {
  if (p) page.value = p
  const { data } = await get<{ groups: any[]; pagination: { currentPage: number; lastPage: number; total: number } }>(`/groups?page=${page.value}&perPage=10`)
  if (data?.groups) groups.value = data.groups
  if (data?.pagination) {
    lastPage.value = data.pagination.lastPage
    total.value = data.pagination.total
  }
}

function confirmDelete(id: number) {
  itemToDelete.value = id
  isDeleteModalOpen.value = true
}

async function executeDelete() {
  if (itemToDelete.value) {
    await del(`/groups/${itemToDelete.value}/delete`)
    groups.value = groups.value.filter(g => g.id !== itemToDelete.value)
    isDeleteModalOpen.value = false
    itemToDelete.value = null
  }
}
</script>

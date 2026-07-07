<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-slate-900">Groups</h1>
      <div class="flex items-center gap-3">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <Icon name="heroicons:magnifying-glass" class="w-5 h-5 text-gray-500" />
          </div>
          <input type="text" v-model="searchQuery" @input="onSearch"
            class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-64 pl-10 p-2 shadow-sm transition-colors"
            placeholder="Search groups..." />
        </div>
        <CreateButton v-if="hasPermission('add_group')" to="/groups/new" icon="heroicons:user-group">
          Add Group
        </CreateButton>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mt-6">
      <table class="w-full">
        <thead>
          <tr class="border-b border-slate-200 bg-slate-50">
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Name</th>
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Status</th>
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Permissions</th>
            <th class="text-right text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="group in groups" :key="group.id" class="hover:bg-slate-50">
            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ group.name }}</td>
            <td class="px-6 py-4">
              <span v-if="group.status" class="bg-green-50 text-green-700 text-xs font-medium px-2.5 py-0.5 rounded-md border border-green-200">Active</span>
              <span v-else class="bg-slate-100 text-slate-600 text-xs font-medium px-2.5 py-0.5 rounded-md border border-slate-200">Inactive</span>
            </td>
            <td class="px-6 py-4 text-sm text-slate-500">{{ group.groupPermissions.length }}</td>
            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
              <ActionIconButton v-if="hasPermission('change_group')" :to="`/groups/${group.id}`" icon="heroicons:pencil-square" title="Edit" color="blue" />
              <ActionIconButton v-if="hasPermission('delete_group')" @click="confirmDelete(group.id)" icon="heroicons:trash" title="Delete" color="red" />
            </td>
          </tr>
          <tr v-if="!groups.length">
            <td colspan="3" class="px-6 py-12 text-center text-sm text-slate-400">No groups found.</td>
          </tr>
        </tbody>
      </table>
      <Pagination :current-page="page" :last-page="lastPage" :total="total" :per-page="perPage" @page="loadGroups" @update:per-page="onPerPageChange" />
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
import { useAcl } from '../../composables/useAcl'

const { hasPermission } = useAcl()

definePageMeta({
  layout: 'admin',
  middleware: ['auth', 'acl'],
  permission: 'view_group',
})

const { get, del } = useApi()
const groups = ref<any[]>([])
const page = ref(1)
const perPage = ref(10)
const lastPage = ref(1)
const total = ref(0)
const searchQuery = ref('')

const isDeleteModalOpen = ref(false)
const itemToDelete = ref<number | null>(null)

onMounted(() => loadGroups())

function onSearch() {
  page.value = 1
  loadGroups()
}

function onPerPageChange(val: number) {
  perPage.value = val
  page.value = 1
  loadGroups()
}

async function loadGroups(p?: number) {
  if (p) page.value = p
  const { data } = await get<{ groups: any[]; pagination: { currentPage: number; lastPage: number; total: number } }>(`/groups?page=${page.value}&perPage=${perPage.value}&search=${encodeURIComponent(searchQuery.value)}`)
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
    const { error } = await del(`/groups/${itemToDelete.value}/delete`)
    if (error) {
      useToast().error(error)
      isDeleteModalOpen.value = false
      itemToDelete.value = null
      return
    }
    groups.value = groups.value.filter(g => g.id !== itemToDelete.value)
    isDeleteModalOpen.value = false
    itemToDelete.value = null
    useToast().success('Group deleted successfully')
  }
}
</script>

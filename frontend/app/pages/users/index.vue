<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-slate-900">Users</h1>
      <div class="flex items-center gap-3">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <Icon name="heroicons:magnifying-glass" class="w-5 h-5 text-gray-500" />
          </div>
          <input type="text" v-model="searchQuery" @input="onSearch"
            class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-64 pl-10 p-2 shadow-sm transition-colors"
            placeholder="Search users..." />
        </div>
        <CreateButton to="/users/new" icon="heroicons:user-plus">
          Add User
        </CreateButton>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mt-6">
      <table class="w-full">
        <thead>
          <tr class="border-b border-slate-200 bg-slate-50">
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Name</th>
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Email</th>
            <th class="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Groups</th>
            <th class="text-right text-xs font-medium text-slate-500 uppercase tracking-wider px-6 py-3">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50">
            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ user.name }}</td>
            <td class="px-6 py-4 text-sm text-slate-500">{{ user.email }}</td>
            <td class="px-6 py-4 text-sm text-slate-500">
              <span v-for="(ug, i) in user.userGroups" :key="ug.id">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">{{ ug.group.name }}</span>{{ i < user.userGroups.length - 1 ? ' ' : '' }}
              </span>
              <span v-if="!user.userGroups.length" class="text-slate-400">&mdash;</span>
            </td>
            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
              <ActionIconButton :to="`/users/${user.id}`" icon="heroicons:pencil-square" title="Edit" color="blue" />
              <ActionIconButton @click="confirmDelete(user.id)" icon="heroicons:trash" title="Delete" color="red" />
            </td>
          </tr>
          <tr v-if="!users.length">
            <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-400">No users found.</td>
          </tr>
        </tbody>
      </table>
      <Pagination :current-page="page" :last-page="lastPage" :total="total" :per-page="perPage" @page="loadUsers" @update:per-page="onPerPageChange" />
    </div>

    <ConfirmModal 
      v-model="isDeleteModalOpen" 
      message="Are you sure you want to delete this user? This action cannot be undone."
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
const users = ref<any[]>([])
const page = ref(1)
const perPage = ref(10)
const lastPage = ref(1)
const total = ref(0)
const searchQuery = ref('')

const isDeleteModalOpen = ref(false)
const itemToDelete = ref<number | null>(null)

onMounted(() => loadUsers())

function onSearch() {
  page.value = 1
  loadUsers()
}

function onPerPageChange(val: number) {
  perPage.value = val
  page.value = 1
  loadUsers()
}

async function loadUsers(p?: number) {
  if (p) page.value = p
  const { data } = await get<{ users: any[]; pagination: { currentPage: number; lastPage: number; total: number } }>(`/users?page=${page.value}&perPage=${perPage.value}&search=${encodeURIComponent(searchQuery.value)}`)
  if (data?.users) users.value = data.users
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
    await del(`/users/${itemToDelete.value}/delete`)
    users.value = users.value.filter(u => u.id !== itemToDelete.value)
    isDeleteModalOpen.value = false
    itemToDelete.value = null
  }
}
</script>

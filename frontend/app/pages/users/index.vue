<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-slate-900">Users</h1>
      <NuxtLink to="/users/new" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg px-4 py-2 transition-colors">
        Add User
      </NuxtLink>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
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
            <td class="px-6 py-4 text-right">
              <NuxtLink :to="`/users/${user.id}`" class="text-sm text-blue-600 hover:text-blue-800 font-medium mr-3">Edit</NuxtLink>
              <button @click="deleteUser(user.id)" class="text-sm text-rose-600 hover:text-rose-800 font-medium">Delete</button>
            </td>
          </tr>
          <tr v-if="!users.length">
            <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-400">No users found.</td>
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
const users = ref<any[]>([])

onMounted(async () => {
  const { data } = await get<{ users: any[] }>('/users')
  if (data?.users) users.value = data.users
})

async function deleteUser(id: number) {
  if (confirm('Are you sure you want to delete this user?')) {
    await del(`/users/${id}/delete`)
    users.value = users.value.filter(u => u.id !== id)
  }
}
</script>

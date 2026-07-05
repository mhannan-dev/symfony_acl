<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-slate-900">Groups</h1>
      <NuxtLink to="/groups/new" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg px-4 py-2 transition-colors">
        Add Group
      </NuxtLink>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
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
            <td class="px-6 py-4 text-right">
              <NuxtLink :to="`/groups/${group.id}`" class="text-sm text-blue-600 hover:text-blue-800 font-medium mr-3">Edit</NuxtLink>
              <button @click="deleteGroup(group.id)" class="text-sm text-rose-600 hover:text-rose-800 font-medium">Delete</button>
            </td>
          </tr>
          <tr v-if="!groups.length">
            <td colspan="3" class="px-6 py-12 text-center text-sm text-slate-400">No groups found.</td>
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
const groups = ref<any[]>([])

onMounted(async () => {
  const { data } = await get<{ groups: any[] }>('/groups')
  if (data?.groups) groups.value = data.groups
})

async function deleteGroup(id: number) {
  if (confirm('Are you sure you want to delete this group?')) {
    await del(`/groups/${id}/delete`)
    groups.value = groups.value.filter(g => g.id !== id)
  }
}
</script>

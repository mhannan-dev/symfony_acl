<template>
  <div class="max-w-2xl space-y-6">
    <div class="flex items-center gap-4">
      <NuxtLink to="/groups" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
      </NuxtLink>
      <h1 class="text-2xl font-bold text-slate-900">Edit Group</h1>
    </div>

    <form @submit.prevent="submit" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
        <input v-model="form.name" type="text" required
          class="block w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all" />
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Permissions</label>
        <div class="space-y-4 max-h-96 overflow-y-auto border border-slate-200 rounded-lg p-4 bg-slate-50">
          <div v-for="(perms, model) in permissionsGroupedByModel" :key="model" class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-100 pb-2 flex items-center gap-2">
              <Icon name="heroicons:folder" class="w-4 h-4" />
              {{ model }}
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <label v-for="perm in perms" :key="perm.id" class="flex items-start gap-3 cursor-pointer p-2 hover:bg-slate-50 rounded-md transition-colors border border-transparent hover:border-slate-100">
                <input type="checkbox" :value="perm.id" v-model="form.permissionIds"
                  class="mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
                <div class="flex flex-col">
                  <span class="text-sm font-medium text-slate-700">{{ perm.name }}</span>
                  <span class="text-xs text-slate-400 font-mono">{{ perm.codename }}</span>
                </div>
              </label>
            </div>
          </div>
          <p v-if="!permissions.length" class="text-sm text-slate-400 text-center py-4">No permissions available.</p>
        </div>
      </div>

      <div class="flex items-center gap-3 pt-2">
        <button type="submit" :disabled="saving"
          class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg px-6 py-2.5 transition-colors disabled:opacity-60">
          {{ saving ? 'Saving...' : 'Save' }}
        </button>
        <NuxtLink to="/groups" class="text-sm text-slate-500 hover:text-slate-700 font-medium">Cancel</NuxtLink>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, reactive, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: ['auth', 'acl'],
  permission: 'change_group',
})

const route = useRoute()
const router = useRouter()
const { get, post } = useApi()

const form = reactive({
  id: null as number | null,
  name: '',
  permissionIds: [] as number[],
})

const permissions = ref<any[]>([])
const saving = ref(false)

const permissionsGroupedByModel = computed(() => {
  const groups: Record<string, any[]> = {}
  permissions.value.forEach(p => {
    const modelName = p.contentType?.model || 'Other'
    if (!groups[modelName]) groups[modelName] = []
    groups[modelName].push(p)
  })
  
  // Sort alphabetically by model name
  return Object.keys(groups).sort().reduce((acc, key) => {
    acc[key] = groups[key]
    return acc
  }, {} as Record<string, any[]>)
})

onMounted(async () => {
  const { data } = await get<{ group: any; permissions: any[]; groupPermissionIds: number[] }>(`/groups/${route.params.id}/edit`)
  if (data) {
    form.id = data.group.id
    form.name = data.group.name
    form.permissionIds = data.groupPermissionIds || []
    permissions.value = data.permissions || []
  }
})

async function submit() {
  saving.value = true
  const { error } = await post('/groups/save', {
    id: form.id,
    name: form.name,
    permissionIds: form.permissionIds,
  })
  saving.value = false
  if (error) {
    useToast().error(error)
    return
  }
  useToast().success('Group saved successfully')
  router.push('/groups')
}
</script>

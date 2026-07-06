<template>
  <div class="max-w-3xl space-y-6">
    <div class="flex items-center gap-4">
      <NuxtLink to="/groups" class="text-slate-400 hover:text-slate-600 transition-colors p-1 -ml-1">
        <Icon name="heroicons:arrow-left" class="w-5 h-5" />
      </NuxtLink>
      <h1 class="text-2xl font-bold text-slate-900">New Group</h1>
    </div>

    <form @submit.prevent="submit" class="bg-white rounded-xl border border-slate-200 p-6 space-y-6">
      <div>
        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Group Name</label>
        <input id="name" v-model="form.name" @input="clearNameError" type="text" required maxlength="255"
          class="block w-full rounded-lg border px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-all"
          :class="nameError ? 'border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10' : 'border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10'"
          placeholder="e.g. Moderator, Editor, Viewer" />
        <p v-if="nameError" class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
          <Icon name="heroicons:exclamation-circle" class="w-3.5 h-3.5" />
          {{ nameError }}
        </p>
      </div>

      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block text-sm font-medium text-slate-700">Permissions</label>
          <span class="text-xs text-slate-400">{{ selectedCount }} of {{ totalCount }} selected</span>
        </div>

        <div v-if="loading" class="space-y-3">
          <div v-for="n in 3" :key="n" class="animate-pulse bg-slate-50 rounded-lg border border-slate-200 p-4">
            <div class="h-4 bg-slate-200 rounded w-24 mb-3"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <div v-for="m in 4" :key="m" class="h-8 bg-slate-100 rounded"></div>
            </div>
          </div>
        </div>

        <div v-else-if="!permissions.length" class="text-sm text-slate-400 text-center py-12 border border-dashed border-slate-200 rounded-lg">
          <Icon name="heroicons:key" class="w-8 h-8 mx-auto mb-2 text-slate-300" />
          No permissions available. Run <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs">app:sync-permissions</code> first.
        </div>

        <div v-else class="space-y-4 max-h-[30rem] overflow-y-auto border border-slate-200 rounded-lg p-4 bg-slate-50/50">
          <div v-for="(perms, model) in permissionsGroupedByModel" :key="model" class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between px-4 py-2.5 border-b border-slate-100">
              <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2">
                <Icon name="heroicons:folder" class="w-4 h-4" />
                {{ model }}
              </h3>
              <button type="button" @click="toggleModel(perms)"
                class="text-xs font-medium text-primary-600 hover:text-primary-700 transition-colors">
                {{ modelAllSelected(perms) ? 'Deselect all' : 'Select all' }}
              </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 p-2">
              <label v-for="perm in perms" :key="perm.id"
                class="flex items-start gap-3 cursor-pointer p-2 rounded-md transition-colors hover:bg-slate-50 border border-transparent hover:border-slate-100"
                :class="{ 'bg-primary-50/50 border-primary-100': form.permissionIds.includes(perm.id) }">
                <input type="checkbox" :value="perm.id" v-model="form.permissionIds"
                  class="mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
                <div class="flex flex-col min-w-0">
                  <span class="text-sm font-medium text-slate-700 truncate">{{ perm.name }}</span>
                  <span class="text-xs text-slate-400 font-mono truncate">{{ perm.codename }}</span>
                </div>
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
        <button type="submit" :disabled="saving || !form.name.trim()"
          class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg px-6 py-2.5 transition-colors disabled:opacity-60 shadow-sm">
          <Icon v-if="saving" name="heroicons:arrow-path" class="w-4 h-4 animate-spin" />
          <Icon v-else name="heroicons:check" class="w-4 h-4" />
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
  permission: 'add_group',
})

const router = useRouter()
const { get, post } = useApi()

const form = reactive({
  name: '',
  permissionIds: [] as number[],
})

const permissions = ref<any[]>([])
const saving = ref(false)
const loading = ref(true)
const nameError = ref('')

const totalCount = computed(() => permissions.value.length)
const selectedCount = computed(() => form.permissionIds.length)

const permissionsGroupedByModel = computed(() => {
  const groups: Record<string, any[]> = {}
  permissions.value.forEach(p => {
    const modelName = p.contentType?.model || 'Other'
    if (!groups[modelName]) groups[modelName] = []
    groups[modelName].push(p)
  })
  return Object.keys(groups).sort().reduce((acc, key) => {
    acc[key] = groups[key]
    return acc
  }, {} as Record<string, any[]>)
})

function modelAllSelected(perms: any[]): boolean {
  return perms.every(p => form.permissionIds.includes(p.id))
}

function toggleModel(perms: any[]) {
  const allSelected = modelAllSelected(perms)
  if (allSelected) {
    form.permissionIds = form.permissionIds.filter(id => !perms.some(p => p.id === id))
  } else {
    const ids = perms.map(p => p.id)
    form.permissionIds = [...new Set([...form.permissionIds, ...ids])]
  }
}

function clearNameError() {
  if (nameError.value) nameError.value = ''
}

onMounted(async () => {
  const { data, error } = await get<{ permissions: any[] }>('/groups/new')
  if (data?.permissions) permissions.value = data.permissions
  if (error) useToast().error(error)
  loading.value = false
})

async function submit() {
  if (!form.name.trim()) {
    nameError.value = 'Group name is required'
    return
  }

  saving.value = true
  const { error } = await post('/groups/save', {
    id: null,
    name: form.name.trim(),
    permissionIds: form.permissionIds,
  })
  saving.value = false

  if (error) {
    useToast().error(error)
    return
  }

  useToast().success('Group created successfully')
  router.push('/groups')
}
</script>

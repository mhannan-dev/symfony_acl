<template>
  <div class="max-w-2xl space-y-6">
    <div class="flex items-center gap-4">
      <NuxtLink to="/permissions" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
      </NuxtLink>
      <h1 class="text-2xl font-bold text-slate-900">Edit Permission</h1>
    </div>

    <form @submit.prevent="submit" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
        <input v-model="form.name" type="text" required
          class="block w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all" />
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Codename</label>
        <input v-model="form.codename" type="text" required
          class="block w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all font-mono" />
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Content Type</label>
        <select v-model="form.contentTypeId" required
          class="block w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all">
          <option value="" disabled>Select content type</option>
          <option v-for="ct in contentTypes" :key="ct.id" :value="ct.id">{{ ct.appLabel }} &mdash; {{ ct.model }}</option>
        </select>
      </div>

      <div class="flex items-center gap-3 pt-2">
        <button type="submit" :disabled="saving"
          class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg px-6 py-2.5 transition-colors disabled:opacity-60">
          {{ saving ? 'Saving...' : 'Save' }}
        </button>
        <NuxtLink to="/permissions" class="text-sm text-slate-500 hover:text-slate-700 font-medium">Cancel</NuxtLink>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'admin',
  middleware: ['auth', 'acl'],
  permission: 'change_permission',
})

const route = useRoute()
const router = useRouter()
const { get, post } = useApi()

const form = reactive({
  id: null as number | null,
  name: '',
  codename: '',
  contentTypeId: '' as string | number,
})

const contentTypes = ref<any[]>([])
const saving = ref(false)

onMounted(async () => {
  const { data } = await get<{ permission: any; contentTypes: any[] }>(`/permissions/${route.params.id}/edit`)
  if (data) {
    form.id = data.permission.id
    form.name = data.permission.name
    form.codename = data.permission.codename
    form.contentTypeId = data.permission.contentType?.id || ''
    contentTypes.value = data.contentTypes || []
  }
})

async function submit() {
  saving.value = true
  const { error } = await post('/permissions/save', {
    id: form.id,
    name: form.name,
    codename: form.codename,
    contentTypeId: form.contentTypeId ? parseInt(form.contentTypeId as string) : null,
  })
  saving.value = false
  if (error) {
    useToast().error(error)
    return
  }
  useToast().success('Permission saved successfully')
  router.push('/permissions')
}
</script>

<template>
  <div class="max-w-2xl space-y-6">
    <div class="flex items-center gap-4">
      <NuxtLink to="/content-types" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
      </NuxtLink>
      <h1 class="text-2xl font-bold text-slate-900">Edit Content Type</h1>
    </div>

    <form @submit.prevent="submit" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">App Label</label>
        <input v-model="form.appLabel" type="text" required
          class="block w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all" />
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Model</label>
        <input v-model="form.model" type="text" required
          class="block w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all font-mono" />
      </div>

      <div class="flex items-center gap-3 pt-2">
        <button type="submit" :disabled="saving"
          class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg px-6 py-2.5 transition-colors disabled:opacity-60">
          {{ saving ? 'Saving...' : 'Save' }}
        </button>
        <NuxtLink to="/content-types" class="text-sm text-slate-500 hover:text-slate-700 font-medium">Cancel</NuxtLink>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'admin',
  middleware: 'auth',
})

const route = useRoute()
const router = useRouter()
const { get, post } = useApi()

const form = reactive({
  id: null as number | null,
  appLabel: '',
  model: '',
})

const saving = ref(false)

onMounted(async () => {
  const { data } = await get<{ contentType: any }>(`/content-types/${route.params.id}/edit`)
  if (data?.contentType) {
    form.id = data.contentType.id
    form.appLabel = data.contentType.appLabel
    form.model = data.contentType.model
  }
})

async function submit() {
  saving.value = true
  await post('/content-types/save', {
    id: form.id,
    appLabel: form.appLabel,
    model: form.model,
  })
  saving.value = false
  router.push('/content-types')
}
</script>

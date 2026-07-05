<template>
  <Layout>
    <div class="max-w-2xl space-y-6">
      <div class="flex items-center gap-4">
        <Link href="/admin/permissions" class="text-slate-400 hover:text-slate-600 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </Link>
        <h1 class="text-2xl font-bold text-slate-900">{{ permission ? 'Edit Permission' : 'New Permission' }}</h1>
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
          <p class="text-xs text-slate-400 mt-1">Convention: <code class="bg-slate-100 px-1 rounded">add_</code>, <code class="bg-slate-100 px-1 rounded">change_</code>, <code class="bg-slate-100 px-1 rounded">delete_</code>, <code class="bg-slate-100 px-1 rounded">view_</code> + model name</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Content Type</label>
          <select v-model="form.contentTypeId" required
            class="block w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all">
            <option value="" disabled>Select content type</option>
            <option v-for="ct in contentTypes" :key="ct.id" :value="ct.id">{{ ct.appLabel }} — {{ ct.model }}</option>
          </select>
        </div>

        <div class="flex items-center gap-3 pt-2">
          <button type="submit" :disabled="form.processing"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg px-6 py-2.5 transition-colors disabled:opacity-60">
            {{ form.processing ? 'Saving...' : 'Save' }}
          </button>
          <Link href="/admin/permissions" class="text-sm text-slate-500 hover:text-slate-700 font-medium">Cancel</Link>
        </div>
      </form>
    </div>
  </Layout>
</template>

<script setup>
import { reactive } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import Layout from '../Layout.vue'

const props = defineProps({
  permission: Object,
  contentTypes: Array,
})

const form = reactive({
  id: props.permission?.id || null,
  name: props.permission?.name || '',
  codename: props.permission?.codename || '',
  contentTypeId: props.permission?.contentType?.id || '',
  processing: false,
})

function submit() {
  form.processing = true
  router.post('/admin/permissions/save', {
    id: form.id,
    name: form.name,
    codename: form.codename,
    contentTypeId: form.contentTypeId,
  }, {
    onError: () => { form.processing = false },
    onFinish: () => { form.processing = false },
  })
}
</script>

<template>
  <Layout>
    <div class="max-w-2xl space-y-6">
      <div class="flex items-center gap-4">
        <Link href="/admin/users" class="text-slate-400 hover:text-slate-600 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </Link>
        <h1 class="text-2xl font-bold text-slate-900">{{ user ? 'Edit User' : 'New User' }}</h1>
      </div>

      <form @submit.prevent="submit" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
          <input v-model="form.name" type="text" required
            class="block w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
          <input v-model="form.email" type="email" required
            class="block w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ user ? 'New Password (leave blank to keep)' : 'Password' }}</label>
          <input v-model="form.password" type="password" :required="!user"
            class="block w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Groups</label>
          <div class="space-y-2 max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-3">
            <label v-for="group in groups" :key="group.id" class="flex items-center gap-3 cursor-pointer">
              <input type="checkbox" :value="group.id" v-model="form.groupIds"
                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
              <span class="text-sm text-slate-700">{{ group.name }}</span>
            </label>
            <p v-if="!groups.length" class="text-sm text-slate-400 text-center py-2">No groups available.</p>
          </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
          <button type="submit" :disabled="form.processing"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg px-6 py-2.5 transition-colors disabled:opacity-60">
            {{ form.processing ? 'Saving...' : 'Save' }}
          </button>
          <Link href="/admin/users" class="text-sm text-slate-500 hover:text-slate-700 font-medium">Cancel</Link>
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
  user: Object,
  groups: Array,
  userGroupIds: Array,
})

const form = reactive({
  id: props.user?.id || null,
  name: props.user?.name || '',
  email: props.user?.email || '',
  password: '',
  groupIds: [...(props.userGroupIds || [])],
  processing: false,
})

function submit() {
  form.processing = true
  router.post('/admin/users/save', {
    id: form.id,
    name: form.name,
    email: form.email,
    password: form.password,
    groupIds: form.groupIds,
  }, {
    onError: () => { form.processing = false },
    onFinish: () => { form.processing = false },
  })
}
</script>

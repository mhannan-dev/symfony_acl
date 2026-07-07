<template>
  <div class="max-w-2xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <h1 class="text-2xl font-bold text-slate-900">My Profile</h1>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mt-6 p-6">
      <form @submit.prevent="saveProfile" class="space-y-6">
        <div>
          <label for="name" class="block text-sm font-medium text-slate-700">Full Name</label>
          <div class="mt-1">
            <input
              type="text"
              id="name"
              v-model="form.name"
              required
              class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 shadow-sm transition-colors"
            />
          </div>
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-slate-700">Email Address</label>
          <div class="mt-1">
            <input
              type="email"
              id="email"
              v-model="form.email"
              required
              class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 shadow-sm transition-colors"
            />
          </div>
        </div>

        <div class="pt-4 border-t border-slate-200">
          <h3 class="text-lg font-medium text-slate-900 mb-4">Change Password</h3>
          <p class="text-sm text-slate-500 mb-4">Leave the password fields blank if you do not want to change your password.</p>
          
          <div class="space-y-4">
            <div>
              <label for="password" class="block text-sm font-medium text-slate-700">New Password</label>
              <div class="mt-1">
                <input
                  type="password"
                  id="password"
                  v-model="form.password"
                  class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 shadow-sm transition-colors"
                  placeholder="••••••••"
                />
              </div>
            </div>

            <div>
              <label for="confirmPassword" class="block text-sm font-medium text-slate-700">Confirm New Password</label>
              <div class="mt-1">
                <input
                  type="password"
                  id="confirmPassword"
                  v-model="form.confirmPassword"
                  class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 shadow-sm transition-colors"
                  placeholder="••••••••"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="pt-4 flex justify-end gap-3">
          <button
            type="submit"
            :disabled="isSaving"
            class="inline-flex justify-center rounded-lg border border-transparent bg-primary-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 transition-colors"
          >
            <span v-if="isSaving">Saving...</span>
            <span v-else>Save Profile</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAuth } from '../../../layers/features/auth/composables/useAuth'

definePageMeta({
  layout: 'admin',
  middleware: ['auth'],
})

const { user, fetchUser } = useAuth()
const { post } = useApi()
const toast = useToast()

const isSaving = ref(false)

const form = ref({
  name: '',
  email: '',
  password: '',
  confirmPassword: ''
})

onMounted(() => {
  if (user.value) {
    form.value.name = user.value.name
    form.value.email = user.value.email
  }
})

async function saveProfile() {
  if (form.value.password !== form.value.confirmPassword) {
    toast.error('Passwords do not match')
    return
  }

  isSaving.value = true
  
  try {
    const payload: Record<string, string> = {
      name: form.value.name,
      email: form.value.email,
    }
    
    if (form.value.password) {
      payload.password = form.value.password
    }
    
    const { data, error } = await post('/profile', payload)
    
    if (error) {
      toast.error(error || 'Failed to update profile')
      return
    }
    
    toast.success('Profile updated successfully')
    form.value.password = ''
    form.value.confirmPassword = ''
    
    // Refresh user data in global state
    await fetchUser()
  } finally {
    isSaving.value = false
  }
}
</script>

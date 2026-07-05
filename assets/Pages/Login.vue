<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 flex">
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 items-center justify-center relative overflow-hidden">
      <div class="absolute inset-0">
        <div class="absolute top-0 -left-40 w-[500px] h-[500px] bg-blue-500/10 rounded-full blur-[150px]" />
        <div class="absolute -bottom-32 -right-32 w-[600px] h-[600px] bg-indigo-500/10 rounded-full blur-[150px]" />
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-cyan-500/5 rounded-full blur-[120px]" />
      </div>
      <div class="relative px-16 text-center">
        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-lg shadow-blue-600/25 ring-1 ring-white/10">
          <Shield class="w-10 h-10 text-white" />
        </div>
        <h2 class="text-4xl font-bold text-white mb-4 tracking-tight">Symfony ACL</h2>
        <p class="text-slate-400 text-lg max-w-sm mx-auto leading-relaxed">
          Enterprise-grade Role-Based Access Control Management System
        </p>
        <div class="mt-12 flex items-center justify-center gap-4">
          <div class="h-px w-16 bg-gradient-to-r from-transparent via-slate-600 to-transparent" />
          <div class="flex -space-x-1">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 ring-2 ring-slate-800" />
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 ring-2 ring-slate-800" />
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600 ring-2 ring-slate-800" />
          </div>
          <div class="h-px w-16 bg-gradient-to-r from-transparent via-slate-600 to-transparent" />
        </div>
        <p class="mt-6 text-slate-500 text-sm">Trusted by security-conscious teams</p>
      </div>
    </div>

    <div class="flex-1 flex items-center justify-center px-4 py-8 relative">
      <div class="w-full max-w-md relative">
        <div class="lg:hidden flex items-center gap-3 mb-10">
          <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-md">
            <Shield class="w-5 h-5 text-white" />
          </div>
          <span class="text-xl font-bold text-slate-900 tracking-tight">Symfony ACL</span>
        </div>

        <div class="bg-white rounded-2xl shadow-[0_2px_40px_-8px_rgba(0,0,0,0.08)] p-8 sm:p-10">
          <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Welcome back</h1>
            <p class="text-slate-500 mt-1.5">Sign in to your account to continue</p>
          </div>

          <form @submit.prevent="submit" class="space-y-5">

            <TextInput
              id="email"
              v-model="form.email"
              type="email"
              label="Email address"
              placeholder="you@example.com"
              :error="form.errors.email"
              
            >
              <template #icon>
                <Mail class="w-5 h-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" />
              </template>
            </TextInput>

            <TextInput
              id="password"
              v-model="form.password"
              type="password"
              label="Password"
              placeholder="Enter your password"
              :error="form.errors.password"
              
            >
              <template #icon>
                <Lock class="w-5 h-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" />
              </template>
            </TextInput>

            <div class="flex items-center justify-between">
              <label class="flex items-center gap-2 cursor-pointer group">
                <div class="relative flex items-center justify-center">
                  <input
                    type="checkbox"
                    v-model="form.remember"
                    class="peer sr-only"
                  />
                  <div class="w-4 h-4 rounded border border-slate-300 bg-white peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-focus:ring-2 peer-focus:ring-blue-500/20 transition-all group-hover:border-slate-400" />
                  <Check class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" />
                </div>
                <span class="text-sm text-slate-600 group-hover:text-slate-700 transition-colors select-none">Remember me</span>
              </label>
              <a
                href="/reset-password"
                class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors"
              >Reset password?</a>
            </div>

            <button
              type="submit"
              :disabled="form.processing"
              class="relative w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl px-4 py-3 text-sm transition-all focus:outline-none focus:ring-4 focus:ring-blue-500/20 disabled:opacity-60 disabled:cursor-not-allowed shadow-lg shadow-blue-600/15 hover:shadow-blue-600/25 active:shadow-none active:translate-y-0.5 overflow-hidden"
            >
              <span class="relative z-10 flex items-center justify-center gap-2">
                <span v-if="form.processing" class="flex items-center gap-2">
                  <Loader2 class="animate-spin h-4 w-4" />
                  Signing in...
                </span>
                <span v-else class="flex items-center gap-2">
                  Sign in
                  <ArrowRight class="w-4 h-4" />
                </span>
              </span>
            </button>
          </form>

          <div class="mt-8 pt-6 border-t border-slate-100">
            <p class="text-xs text-slate-400 text-center leading-relaxed">
              Protected by enterprise-grade security.<br />
              &copy; {{ new Date().getFullYear() }} Symfony ACL. All rights reserved.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { Shield, ShieldAlert, Mail, Lock, Check, Loader2, ArrowRight } from 'lucide-vue-next'
import TextInput from '../Components/UI/TextInput.vue'

const props = defineProps({
  error: String,
  email: String,
})

const form = reactive({
  email: props.email || '',
  password: '',
  remember: false,
  processing: false,
  errors: {},
})

// Map Symfony's global authentication error to the email field validation
watch(() => props.error, (newError) => {
  if (newError) {
    form.errors.email = newError
  }
}, { immediate: true })

function submit() {
  form.processing = true
  form.errors = {}
  
  // Symfony's form_login expects standard form data (multipart/form-data or application/x-www-form-urlencoded)
  // Inertia sends JSON by default, which form_login cannot read. 
  // We use FormData here to force a multipart/form-data request so Symfony can authenticate properly!
  const formData = new FormData()
  formData.append('email', form.email)
  formData.append('password', form.password)
  if (form.remember) {
    formData.append('_remember_me', 'on')
  }

  router.post('/login', formData, {
    onError: (errors) => {
      form.processing = false
      if (errors.email || errors.password) {
        form.errors = errors
      }
    },
    onFinish: () => {
      form.processing = false
    },
  })
}
</script>

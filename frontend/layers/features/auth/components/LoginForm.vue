<template>
  <div class="bg-white rounded-2xl shadow-[0_2px_40px_-8px_rgba(0,0,0,0.08)] p-8 sm:p-10">
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Welcome back</h1>
      <p class="text-slate-500 mt-1.5">Sign in to your account to continue</p>
    </div>

    <form @submit.prevent="submit" class="space-y-5">
      <div
        v-if="error"
        class="flex items-start gap-3 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl px-4 py-3.5 text-sm"
      >
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        <div>
          <p class="font-medium">Authentication failed</p>
          <p class="text-rose-600/80 mt-0.5">{{ error }}</p>
        </div>
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
            <svg class="w-5 h-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <input id="email" v-model="form.email" type="email" required placeholder="you@example.com"
            class="block w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all text-sm" />
        </div>
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
            <svg class="w-5 h-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <input id="password" v-model="form.password" type="password" required placeholder="Enter your password"
            class="block w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all text-sm" />
        </div>
      </div>

      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer group">
          <div class="relative flex items-center justify-center">
            <input type="checkbox" v-model="form.remember" class="peer sr-only" />
            <div class="w-4 h-4 rounded border border-slate-300 bg-white peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-focus:ring-2 peer-focus:ring-blue-500/20 transition-all group-hover:border-slate-400" />
            <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <span class="text-sm text-slate-600 group-hover:text-slate-700 transition-colors select-none">Remember me</span>
        </label>
        <NuxtLink to="/reset-password" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">Reset password?</NuxtLink>
      </div>

      <button type="submit" :disabled="auth.loading.value"
        class="relative w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl px-4 py-3 text-sm transition-all focus:outline-none focus:ring-4 focus:ring-blue-500/20 disabled:opacity-60 disabled:cursor-not-allowed shadow-lg shadow-blue-600/15 hover:shadow-blue-600/25 active:shadow-none active:translate-y-0.5 overflow-hidden">
        <span class="relative z-10 flex items-center justify-center gap-2">
          <span v-if="auth.loading.value" class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            Signing in...
          </span>
          <span v-else class="flex items-center gap-2">
            Sign in
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
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
</template>

<script setup lang="ts">
const auth = useAuth()
const router = useRouter()

const form = reactive({
  email: '',
  password: '',
  remember: false,
})

const error = ref('')

async function submit() {
  error.value = ''
  const result = await auth.login(form.email, form.password)
  if (result.success) {
    router.push('/dashboard')
  } else {
    error.value = result.error || 'Invalid email or password.'
  }
}
</script>

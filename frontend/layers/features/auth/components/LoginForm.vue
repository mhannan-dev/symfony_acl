<template>
  <div class="w-full max-w-md bg-white rounded-lg shadow-xl dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
      <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
        Sign in to your account
      </h1>
      
      <form class="space-y-4 md:space-y-6" @submit.prevent="submit">
        
        <div v-if="error" class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
          <Icon name="heroicons:exclamation-circle-20-solid" class="w-5 h-5 me-3 shrink-0" />
          <span class="sr-only">Info</span>
          <div>
            <span class="font-medium">Error!</span> {{ error }}
          </div>
        </div>

        <BaseInput
          id="email"
          type="email"
          v-model="form.email"
          label="Your email"
          placeholder="name@company.com"
          icon="heroicons:envelope"
          required
        />
        
        <BaseInput
          id="password"
          type="password"
          v-model="form.password"
          label="Password"
          placeholder="••••••••"
          icon="heroicons:lock-closed"
          required
        />
        <div class="flex items-center justify-between">
          <div class="flex items-start">
            <div class="flex items-center h-5">
              <input id="remember" aria-describedby="remember" type="checkbox" v-model="form.remember" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800">
            </div>
            <div class="ml-3 text-sm">
              <label for="remember" class="text-gray-500 dark:text-gray-300">Remember me</label>
            </div>
          </div>
          <NuxtLink to="/reset-password" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">Forgot password?</NuxtLink>
        </div>
        
        <button type="submit" :disabled="isLoginPending" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex items-center justify-center disabled:opacity-50">
          <Icon v-if="isLoginPending" name="heroicons:arrow-path" class="w-5 h-5 me-2 animate-spin" />
          <span v-if="isLoginPending">Signing in...</span>
          <span v-else>Sign in</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import BaseInput from '../../../shared/components/BaseInput.vue'

const { login, isLoginPending } = useAuth()
const router = useRouter()

const form = reactive({
  email: '',
  password: '',
  remember: false,
})

const error = ref('')

async function submit() {
  error.value = ''
  const result = await login(form.email, form.password)
  if (result.success) {
    router.push('/dashboard')
  } else {
    error.value = result.error || 'Invalid email or password.'
  }
}
</script>

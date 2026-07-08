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
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <h2 class="text-4xl font-bold text-white mb-4 tracking-tight">RBAC</h2>
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
      <div class="w-full max-w-lg relative">
        <div class="lg:hidden flex items-center gap-3 mb-10">
          <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-md">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <span class="text-xl font-bold text-slate-900 tracking-tight">RBAC</span>
        </div>

        <LoginForm />

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'default',
})
</script>

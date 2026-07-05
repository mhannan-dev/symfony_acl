<template>
  <div class="min-h-screen bg-slate-50">
    <nav class="bg-slate-900 border-b border-slate-800">
      <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
            <span class="text-lg font-semibold text-slate-100">Symfony ACL</span>
          </div>
          <div class="flex items-center gap-4">
            <Link
              v-if="isAuthenticated"
              href="/dashboard"
              class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg px-4 py-2 transition-colors"
            >
              Dashboard
            </Link>
            <Link
              v-else
              href="/login"
              class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg px-4 py-2 transition-colors"
            >
              Sign in
            </Link>
          </div>
        </div>
      </div>
    </nav>

    <main>
      <section class="max-w-7xl mx-auto px-6 pt-24 pb-20">
        <div class="max-w-3xl">
          <h1 class="text-5xl font-bold text-slate-900 leading-tight mb-6">
            Role-Based Access Control
            <span class="text-blue-600">Management System</span>
          </h1>
          <p class="text-lg text-slate-500 leading-relaxed mb-10 max-w-2xl">
            Enterprise-grade permission management with granular access control, 
            role hierarchy, and comprehensive audit logging. Secure your application 
            with confidence.
          </p>
          <div class="flex items-center gap-4">
            <Link
              href="/login"
              class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg px-6 py-3 transition-colors"
            >
              Get started
            </Link>
            <a
              href="#features"
              class="text-slate-600 hover:text-slate-900 font-medium rounded-lg px-6 py-3 transition-colors"
            >
              Learn more
            </a>
          </div>
        </div>
      </section>

      <section id="features" class="border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-20">
          <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-slate-900 mb-4">Everything you need to manage permissions</h2>
            <p class="text-slate-500 max-w-2xl mx-auto">
              A complete RBAC solution with granular control over users, roles, and permissions.
            </p>
          </div>

          <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
              <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-slate-900 mb-2">User Management</h3>
              <p class="text-sm text-slate-500 leading-relaxed">
                Manage users with detailed profiles, role assignments, and direct permission overrides.
              </p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
              <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-slate-900 mb-2">Role Hierarchy</h3>
              <p class="text-sm text-slate-500 leading-relaxed">
                Define roles with hierarchical permissions. Assign users to multiple groups for flexible access.
              </p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
              <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-slate-900 mb-2">Granular Permissions</h3>
              <p class="text-sm text-slate-500 leading-relaxed">
                Define permissions per content type with codename-based access control and conditional rules.
              </p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
              <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-slate-900 mb-2">Audit Trail</h3>
              <p class="text-sm text-slate-500 leading-relaxed">
                Track every action with detailed activity logs including timestamps, user info, and change history.
              </p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
              <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-slate-900 mb-2">Content Types</h3>
              <p class="text-sm text-slate-500 leading-relaxed">
                Django-style content type framework enabling model-specific permissions like add, edit, and delete.
              </p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
              <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-slate-900 mb-2">Customizable Workflows</h3>
              <p class="text-sm text-slate-500 leading-relaxed">
                Adapt the permission system to your needs with inherited, allowed, and denied access states.
              </p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="border-t border-slate-200">
      <div class="max-w-7xl mx-auto px-6 py-8 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-slate-400">
          <div class="w-5 h-5 bg-blue-600 rounded flex items-center justify-center">
            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          Symfony ACL
        </div>
        <p class="text-xs text-slate-400">&copy; {{ new Date().getFullYear() }} Symfony ACL. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  isAuthenticated: Boolean,
})
</script>

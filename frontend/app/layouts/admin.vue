<template>
  <div class="antialiased bg-gray-50 dark:bg-gray-900 min-h-screen">
    <!-- Top Navbar -->
    <nav class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
      <div class="flex flex-wrap justify-between items-center">
        <div class="flex justify-start items-center">
          <!-- Mobile Sidebar Toggle -->
          <button @click="isSidebarOpen = !isSidebarOpen" class="p-2 mr-2 text-gray-600 rounded-lg cursor-pointer md:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
            <Icon name="heroicons:bars-3" class="w-6 h-6" />
            <span class="sr-only">Toggle sidebar</span>
          </button>
          
          <NuxtLink to="/dashboard" class="flex items-center justify-between mr-4 gap-3">
            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center shrink-0">
              <Icon name="heroicons:shield-check-solid" class="w-5 h-5 text-white" />
            </div>
            <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">RBAC</span>
          </NuxtLink>
        </div>
        
        <div class="flex items-center lg:order-2">
          <!-- User Profile Dropdown Menu -->
          <div class="relative">
            <button @click="isUserMenuOpen = !isUserMenuOpen" type="button" class="flex mx-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all hover:ring-4 hover:ring-gray-200 dark:hover:ring-gray-700">
              <span class="sr-only">Open user menu</span>
              <img class="w-8 h-8 rounded-full" :src="'https://ui-avatars.com/api/?name=' + (user?.name || 'Admin') + '&background=1d4ed8&color=fff'" alt="user photo" />
            </button>
            
            <!-- Dropdown content -->
            <div v-show="isUserMenuOpen" class="absolute right-0 z-50 my-4 text-base list-none bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 transition-all origin-top-right" style="top: 100%; margin-top: 0.5rem; min-width: 12rem;">
              <div class="py-3 px-4">
                <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ user?.name || 'Admin User' }}</span>
                <span class="block text-sm font-light text-gray-500 truncate dark:text-gray-400">{{ user?.email || 'admin@example.com' }}</span>
              </div>
              <ul class="py-1 font-light text-gray-500 dark:text-gray-400">
                <li>
                  <NuxtLink to="/profile" class="flex items-center gap-2 py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white transition-colors">
                    <Icon name="heroicons:user-circle" class="w-4 h-4" />
                    My profile
                  </NuxtLink>
                </li>
              </ul>
              <ul class="py-1 font-light text-gray-500 dark:text-gray-400">
                <li>
                  <button @click="logout()" class="flex w-full text-left items-center gap-2 py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white transition-colors">
                    <Icon name="heroicons:arrow-right-on-rectangle" class="w-4 h-4" />
                    Sign out
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Collapsible Sidebar -->
    <aside :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 transition-transform bg-white border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidenav">
      <div class="overflow-y-auto py-5 px-3 h-full bg-white dark:bg-gray-800 flex flex-col justify-between">
        <ul class="space-y-2">
          <template v-for="item in navItems" :key="item.href">
            <li v-if="!item.permission || hasPermission(item.permission)">
              <NuxtLink
              :to="item.href"
              class="flex items-center p-2 text-base font-medium rounded-lg transition-colors group"
              :class="isActive(item.href) ? 'bg-primary-50 text-primary-700 dark:bg-gray-700 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'"
            >
              <Icon 
                :name="item.icon" 
                class="w-6 h-6 transition duration-75" 
                :class="isActive(item.href) ? 'text-primary-700 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'" 
              />
              <span class="ml-3">{{ item.label }}</span>
              </NuxtLink>
            </li>
          </template>
        </ul>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="p-4 md:ml-64 h-auto pt-20">
      <slot />
    </main>
    
    <!-- Mobile Sidebar Backdrop -->
    <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-30 md:hidden"></div>

    <ToastContainer />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import { useAuth } from '../../layers/features/auth/composables/useAuth'
import { useAcl } from '../composables/useAcl'

const route = useRoute()
const { user, logout } = useAuth()
const { hasPermission } = useAcl()

const isSidebarOpen = ref(false)
const isUserMenuOpen = ref(false)

// Used exclusively @nuxt/icon components instead of raw SVGs
const navItems = [
  { label: 'Dashboard', href: '/dashboard', icon: 'heroicons:home' },
  { label: 'Users', href: '/users', icon: 'heroicons:users', permission: 'view_user' },
  { label: 'Groups', href: '/groups', icon: 'heroicons:user-group', permission: 'view_group' },
  { label: 'Permissions', href: '/permissions', icon: 'heroicons:key', permission: 'view_permission' },
  { label: 'Content Types', href: '/content-types', icon: 'heroicons:document-text', permission: 'view_content_type' },
  { label: 'Activity Logs', href: '/activity-logs', icon: 'heroicons:clock', permission: 'view_activity_log' },
]

function isActive(href: string): boolean {
  return route.path === href || route.path.startsWith(href + '/')
}
</script>

<template>
  <Teleport to="body">
    <div class="fixed bottom-4 right-4 z-[200] flex flex-col gap-2 max-w-sm w-full pointer-events-none">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg border"
          :class="toastClasses(toast.type)"
        >
          <Icon :name="iconName(toast.type)" class="w-5 h-5 shrink-0" />
          <p class="text-sm font-medium">{{ toast.message }}</p>
          <button @click="dismiss(toast.id)" class="ml-auto shrink-0 p-0.5 rounded hover:bg-black/10 transition-colors">
            <Icon name="heroicons:x-mark" class="w-4 h-4" />
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { useToast } from '../composables/useToast'

const { toasts } = useToast()

function toastClasses(type: string) {
  if (type === 'success') return 'bg-emerald-50 border-emerald-200 text-emerald-800'
  if (type === 'error') return 'bg-red-50 border-red-200 text-red-800'
  return 'bg-blue-50 border-blue-200 text-blue-800'
}

function iconName(type: string) {
  if (type === 'success') return 'heroicons:check-circle'
  if (type === 'error') return 'heroicons:exclamation-circle'
  return 'heroicons:information-circle'
}

function dismiss(id: number) {
  toasts.value = toasts.value.filter(t => t.id !== id)
}
</script>

<style scoped>
.toast-enter-active {
  transition: all 0.3s ease-out;
}
.toast-leave-active {
  transition: all 0.2s ease-in;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>

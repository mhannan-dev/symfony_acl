import { useAuth } from '../../layers/features/auth/composables/useAuth'

export function useAcl() {
  const { user } = useAuth()

  const hasPermission = (permission: string): boolean => {
    if (!user.value || !user.value.permissions) {
      return false
    }
    return user.value.permissions.includes(permission)
  }

  const hasAnyPermission = (permissions: string[]): boolean => {
    if (!user.value || !user.value.permissions) {
      return false
    }
    return permissions.some(p => user.value!.permissions.includes(p))
  }

  return { hasPermission, hasAnyPermission }
}

import { useAcl } from '../composables/useAcl'

export default defineNuxtRouteMiddleware((to) => {
  const { hasPermission } = useAcl()
  const requiredPermission = to.meta.permission as string | undefined

  if (requiredPermission && !hasPermission(requiredPermission)) {
    return abortNavigation({
      statusCode: 403,
      message: 'Forbidden: You do not have the required permissions to access this page.'
    })
  }
})

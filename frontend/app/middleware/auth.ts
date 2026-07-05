export default defineNuxtRouteMiddleware(async (to) => {
  if (import.meta.server) return

  const auth = useAuth()

  if (!auth.isAuthenticated.value) {
    const user = await auth.fetchUser()
    if (!user && to.path !== '/login') {
      return navigateTo('/login')
    }
  }
})

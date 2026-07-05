interface User {
  id: number
  name: string
  email: string
  roles: string[]
}

export function useAuth() {
  const user = useState<User | null>('auth-user', () => null)
  const loading = useState<boolean>('auth-loading', () => false)

  async function fetchUser(): Promise<User | null> {
    const { get } = useApi()
    const { data, error } = await get<{ user: User }>('/me')
    if (data?.user) {
      user.value = data.user
      return data.user
    }
    user.value = null
    return null
  }

  async function login(email: string, password: string): Promise<{ success: boolean; error?: string }> {
    loading.value = true
    const { post } = useApi()
    const { data, error } = await post<{ user: User }>('/login', { email, password })
    loading.value = false

    if (data?.user) {
      user.value = data.user
      return { success: true }
    }

    return { success: false, error: error || 'Login failed' }
  }

  async function logout(): Promise<void> {
    const { post } = useApi()
    await post('/logout')
    user.value = null
    navigateTo('/')
  }

  function clear(): void {
    user.value = null
  }

  const isAuthenticated = computed(() => !!user.value)

  return { user, loading, isAuthenticated, fetchUser, login, logout, clear }
}

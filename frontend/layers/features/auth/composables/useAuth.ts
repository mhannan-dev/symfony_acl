import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import { navigateTo } from '#imports'

interface User {
  id: number
  name: string
  email: string
  roles: string[]
}

export function useAuth() {
  const queryClient = useQueryClient()

  const { data: user, isLoading: loading, refetch: fetchUser } = useQuery({
    queryKey: ['auth-user'],
    queryFn: async (): Promise<User | null> => {
      const { get } = useApi()
      const { data } = await get<{ user: User }>('/me')
      return data?.user || null
    },
    staleTime: 1000 * 60 * 5, // 5 minutes
    retry: false,
  })

  const { mutateAsync: loginMutation, isPending: isLoginPending } = useMutation({
    mutationFn: async (credentials: { email: string; password: string }) => {
      const { post } = useApi()
      const { data, error } = await post<{ user: User }>('/login', credentials)
      if (error) throw new Error(error)
      if (!data?.user) throw new Error('Login failed')
      return data.user
    },
    onSuccess: (userData) => {
      queryClient.setQueryData(['auth-user'], userData)
    },
  })

  const { mutateAsync: logoutMutation } = useMutation({
    mutationFn: async () => {
      const { post } = useApi()
      await post('/logout')
    },
    onSuccess: () => {
      queryClient.setQueryData(['auth-user'], null)
      navigateTo('/')
    },
  })

  async function login(email: string, password: string): Promise<{ success: boolean; error?: string }> {
    try {
      await loginMutation({ email, password })
      return { success: true }
    } catch (e: any) {
      return { success: false, error: e.message || 'Login failed' }
    }
  }

  async function logout(): Promise<void> {
    await logoutMutation()
  }

  function clear(): void {
    queryClient.setQueryData(['auth-user'], null)
  }

  const isAuthenticated = computed(() => !!user.value)

  return { user, loading, isLoginPending, isAuthenticated, fetchUser, login, logout, clear }
}

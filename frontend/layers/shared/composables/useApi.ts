const BASE_URL = '/api/v1'

interface ApiResponse<T = unknown> {
  data: T | null
  error: string | null
}

async function request<T = unknown>(
  path: string,
  options: RequestInit = {}
): Promise<ApiResponse<T>> {
  try {
    const res = await fetch(`${BASE_URL}${path}`, {
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...options.headers,
      },
      ...options,
    })

    if (res.status === 401) {
      const auth = useAuth()
      auth.clear()
      if (import.meta.client && !window.location.pathname.includes('/login')) {
        navigateTo('/login')
      }
      return { data: null, error: 'Not authenticated' }
    }

    const text = await res.text()
    if (!text) return { data: null as T, error: null }
    let json;
    try {
      json = JSON.parse(text)
    } catch (e) {
      console.error('API Response is not JSON:', text);
      return { data: null, error: `Server returned non-JSON: ${text.substring(0, 100)}...` }
    }

    if (!res.ok) {
      // Return detailed validation errors if it's a 422
      if (res.status === 422 && json.errors) {
        return { data: null, error: Object.values(json.errors)[0] as string || 'Validation failed' }
      }
      return { data: null, error: json.error || json.message || 'Request failed' }
    }

    return { data: (json.status === 'success' && 'data' in json) ? json.data as T : json as T, error: null }
  } catch (err) {
    return { data: null, error: err instanceof Error ? err.message : 'Network error' }
  }
}

export function useApi() {
  function get<T>(path: string): Promise<ApiResponse<T>> {
    return request<T>(path)
  }

  function post<T>(path: string, body?: unknown): Promise<ApiResponse<T>> {
    return request<T>(path, {
      method: 'POST',
      body: body ? JSON.stringify(body) : undefined,
    })
  }

  function del<T>(path: string): Promise<ApiResponse<T>> {
    return request<T>(path, { method: 'DELETE' })
  }

  function patch<T>(path: string, body?: unknown): Promise<ApiResponse<T>> {
    return request<T>(path, {
      method: 'PATCH',
      body: body ? JSON.stringify(body) : undefined,
    })
  }

  return { get, post, del, patch }
}

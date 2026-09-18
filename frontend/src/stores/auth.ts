import { defineStore } from 'pinia'
import { apiClient } from '../services/api/client'

export interface User {
  id: number
  name: string
  email: string
  role: string
}

interface AuthResponse {
  success: boolean
  message: string
  data: {
    token: string
    user: User
  }
}

interface UserResponse {
  success: boolean
  message: string
  data: User
}

const tokenKey = 'asili_api_token'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem(tokenKey) as string | null,
    user: null as User | null,
    loading: false,
  }),
  getters: {
    isAuthenticated: (state) => Boolean(state.token && state.user),
    isAdmin: (state) => state.user?.role === 'admin' || state.user?.role === 'editor',
  },
  actions: {
    async login(email: string, password: string): Promise<void> {
      this.loading = true

      try {
        const { data } = await apiClient.post<AuthResponse>('/auth/login', { email, password })
        this.token = data.data.token
        this.user = data.data.user
        localStorage.setItem(tokenKey, this.token)
      } finally {
        this.loading = false
      }
    },
    async fetchCurrentUser(): Promise<void> {
      if (!this.token) return

      const { data } = await apiClient.get<UserResponse>('/auth/me')
      this.user = data.data
    },
    async logout(): Promise<void> {
      try {
        if (this.token) await apiClient.post('/auth/logout')
      } finally {
        this.clear()
      }
    },
    clear(): void {
      this.token = null
      this.user = null
      localStorage.removeItem(tokenKey)
    },
  },
})

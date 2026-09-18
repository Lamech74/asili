<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

async function submit(): Promise<void> {
  errorMessage.value = ''

  try {
    await auth.login(email.value, password.value)
    await router.push(typeof route.query.redirect === 'string' ? route.query.redirect : '/admin/products')
  } catch {
    errorMessage.value = 'The credentials could not be verified.'
  }
}
</script>

<template>
  <main class="admin-page">
    <section class="admin-panel">
      <p class="eyebrow eyebrow--green">Asili Naturals / Admin</p>
      <h1>Welcome back.</h1>
      <p class="admin-panel__intro">Sign in to manage the collection and keep the catalogue considered.</p>
      <form class="admin-form" @submit.prevent="submit">
        <label>
          Email address
          <input v-model="email" type="email" autocomplete="email" required />
        </label>
        <label>
          Password
          <input v-model="password" type="password" autocomplete="current-password" required />
        </label>
        <p v-if="errorMessage" class="form-error" role="alert">{{ errorMessage }}</p>
        <button class="button button--dark" type="submit" :disabled="auth.loading">
          {{ auth.loading ? 'Signing in...' : 'Sign in' }}
        </button>
      </form>
      <RouterLink class="text-link" to="/">Return to Asili Naturals <span>↗</span></RouterLink>
    </section>
  </main>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiClient } from '../../services/api/client'
import { useAuthStore } from '../../stores/auth'

interface Testimonial {
  id: number
  name: string
  role: string | null
  quote: string
  avatar_url: string | null
  featured: boolean
  status: string
}

interface TestimonialResponse {
  success: boolean
  message: string
  data: Testimonial[]
}

interface TestimonialFormState {
  name: string
  role: string
  quote: string
  avatar_url: string
  featured: boolean
  status: 'draft' | 'published' | 'archived'
}

const auth = useAuthStore()
const router = useRouter()
const testimonials = ref<Testimonial[]>([])
const loading = ref(true)
const errorMessage = ref('')
const formError = ref('')
const formSuccess = ref('')
const submitting = ref(false)
const editingId = ref<number | null>(null)
const formTitle = computed(() => (editingId.value ? 'Edit testimonial' : 'Add testimonial'))
const emptyForm = (): TestimonialFormState => ({
  name: '',
  role: '',
  quote: '',
  avatar_url: '',
  featured: false,
  status: 'draft',
})
const form = ref<TestimonialFormState>(emptyForm())

async function loadTestimonials(): Promise<void> {
  loading.value = true
  errorMessage.value = ''

  try {
    const { data } = await apiClient.get<TestimonialResponse>('/admin/testimonials')
    testimonials.value = data.data
  } catch {
    errorMessage.value = 'The testimonials could not be loaded.'
  } finally {
    loading.value = false
  }
}

function resetForm(): void {
  form.value = emptyForm()
  editingId.value = null
  formError.value = ''
  formSuccess.value = ''
}

function fillForm(item: Testimonial): void {
  editingId.value = item.id
  form.value = {
    name: item.name,
    role: item.role ?? '',
    quote: item.quote,
    avatar_url: item.avatar_url ?? '',
    featured: Boolean(item.featured),
    status: (item.status as 'draft' | 'published' | 'archived') || 'draft',
  }
  formError.value = ''
  formSuccess.value = ''
}

async function handleSubmit(): Promise<void> {
  formError.value = ''
  formSuccess.value = ''
  submitting.value = true

  try {
    const payload = {
      name: form.value.name,
      role: form.value.role || null,
      quote: form.value.quote,
      avatar_url: form.value.avatar_url || null,
      featured: form.value.featured,
      status: form.value.status,
    }

    if (editingId.value) {
      await apiClient.put(`/admin/testimonials/${editingId.value}`, payload)
      formSuccess.value = 'Testimonial updated successfully.'
    } else {
      await apiClient.post('/admin/testimonials', payload)
      formSuccess.value = 'Testimonial created successfully.'
    }

    await loadTestimonials()
    resetForm()
  } catch (error: any) {
    formError.value = error.response?.data?.message || 'The testimonial could not be saved.'
  } finally {
    submitting.value = false
  }
}

async function deleteTestimonial(itemId: number): Promise<void> {
  const confirmed = window.confirm('Delete this testimonial?')
  if (!confirmed) return

  try {
    await apiClient.delete(`/admin/testimonials/${itemId}`)
    await loadTestimonials()
    if (editingId.value === itemId) resetForm()
  } catch {
    errorMessage.value = 'The testimonial could not be deleted.'
  }
}

async function signOut(): Promise<void> {
  await auth.logout()
  await router.push('/')
}

onMounted(loadTestimonials)
</script>

<template>
  <main class="admin-page admin-page--wide">
    <header class="admin-header">
      <div>
        <p class="eyebrow eyebrow--green">Asili Naturals / Admin</p>
        <h1>Testimonials</h1>
      </div>
      <div class="admin-header__actions">
        <span>{{ auth.user?.name }}</span>
        <button class="text-button" type="button" @click="signOut">Sign out</button>
      </div>
    </header>

    <section class="admin-content">
      <div class="admin-panel admin-panel--compact" style="width: min(760px, 100%); margin: 0 0 40px;">
        <h2 style="margin-bottom: 18px;">{{ formTitle }}</h2>
        <form class="admin-form" @submit.prevent="handleSubmit">
          <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px;">
            <label>
              Customer name
              <input v-model="form.name" type="text" required />
            </label>
            <label>
              Role
              <input v-model="form.role" type="text" />
            </label>
          </div>

          <label>
            Quote
            <textarea v-model="form.quote" rows="4" required style="width: 100%; padding: 14px 12px; border: 1px solid var(--line); background: transparent; color: var(--ink); font: inherit; resize: vertical;"></textarea>
          </label>

          <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px;">
            <label>
              Avatar URL
              <input v-model="form.avatar_url" type="url" />
            </label>
            <label>
              Status
              <select v-model="form.status" style="width: 100%; padding: 14px 12px; border: 1px solid var(--line); background: transparent; color: var(--ink); font: inherit;">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
              </select>
            </label>
          </div>

          <label style="display: flex; align-items: center; gap: 10px; text-transform: none; letter-spacing: 0;">
            <input v-model="form.featured" type="checkbox" />
            Feature this testimonial
          </label>

          <p v-if="formError" class="form-error" role="alert">{{ formError }}</p>
          <p v-if="formSuccess" class="admin-state" style="color: var(--green-soft);">{{ formSuccess }}</p>

          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <button class="button button--dark" type="submit" :disabled="submitting">
              {{ submitting ? 'Saving...' : (editingId ? 'Update testimonial' : 'Create testimonial') }}
            </button>
            <button v-if="editingId" class="button button--outline" type="button" @click="resetForm" style="border-color: var(--line); color: var(--green); background: transparent;">
              Cancel edit
            </button>
          </div>
        </form>
      </div>

      <p v-if="loading" class="admin-state">Loading testimonials...</p>
      <p v-else-if="errorMessage" class="admin-state form-error">{{ errorMessage }}</p>
      <p v-else-if="testimonials.length === 0" class="admin-state">No testimonials have been added yet.</p>
      <div v-else class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Role</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in testimonials" :key="item.id">
              <td><strong>{{ item.name }}</strong></td>
              <td>{{ item.role || 'Customer' }}</td>
              <td><span class="status-pill">{{ item.status }}</span></td>
              <td>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                  <button class="text-button" type="button" @click="fillForm(item)">Edit</button>
                  <button class="text-button" type="button" @click="deleteTestimonial(item.id)" style="color: #9d463a; border-color: #9d463a;">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</template>

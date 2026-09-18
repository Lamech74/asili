<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiClient } from '../../services/api/client'
import { useAuthStore } from '../../stores/auth'

interface Service {
  id: number
  title: string
  slug: string
  summary: string | null
  description: string | null
  icon: string | null
  featured: boolean
  status: string
}

interface ServiceResponse {
  success: boolean
  message: string
  data: Service[]
}

interface ServiceFormState {
  title: string
  slug: string
  summary: string
  description: string
  icon: string
  featured: boolean
  status: 'draft' | 'published' | 'archived'
}

const auth = useAuthStore()
const router = useRouter()
const services = ref<Service[]>([])
const loading = ref(true)
const errorMessage = ref('')
const formError = ref('')
const formSuccess = ref('')
const submitting = ref(false)
const editingId = ref<number | null>(null)
const formTitle = computed(() => (editingId.value ? 'Edit service' : 'Add service'))
const emptyForm = (): ServiceFormState => ({
  title: '',
  slug: '',
  summary: '',
  description: '',
  icon: '',
  featured: false,
  status: 'draft',
})
const form = ref<ServiceFormState>(emptyForm())

async function loadServices(): Promise<void> {
  loading.value = true
  errorMessage.value = ''

  try {
    const { data } = await apiClient.get<ServiceResponse>('/admin/services')
    services.value = data.data
  } catch {
    errorMessage.value = 'The service catalogue could not be loaded.'
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

function fillForm(service: Service): void {
  editingId.value = service.id
  form.value = {
    title: service.title,
    slug: service.slug,
    summary: service.summary ?? '',
    description: service.description ?? '',
    icon: service.icon ?? '',
    featured: Boolean(service.featured),
    status: (service.status as 'draft' | 'published' | 'archived') || 'draft',
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
      title: form.value.title,
      slug: form.value.slug,
      summary: form.value.summary || null,
      description: form.value.description || null,
      icon: form.value.icon || null,
      featured: form.value.featured,
      status: form.value.status,
    }

    if (editingId.value) {
      await apiClient.put(`/admin/services/${editingId.value}`, payload)
      formSuccess.value = 'Service updated successfully.'
    } else {
      await apiClient.post('/admin/services', payload)
      formSuccess.value = 'Service created successfully.'
    }

    await loadServices()
    resetForm()
  } catch (error: any) {
    formError.value = error.response?.data?.message || 'The service could not be saved.'
  } finally {
    submitting.value = false
  }
}

async function deleteService(serviceId: number): Promise<void> {
  const confirmed = window.confirm('Delete this service?')
  if (!confirmed) return

  try {
    await apiClient.delete(`/admin/services/${serviceId}`)
    await loadServices()
    if (editingId.value === serviceId) resetForm()
  } catch {
    errorMessage.value = 'The service could not be deleted.'
  }
}

async function signOut(): Promise<void> {
  await auth.logout()
  await router.push('/')
}

onMounted(loadServices)
</script>

<template>
  <main class="admin-page admin-page--wide">
    <header class="admin-header">
      <div>
        <p class="eyebrow eyebrow--green">Asili Naturals / Admin</p>
        <h1>Services</h1>
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
              Service title
              <input v-model="form.title" type="text" required />
            </label>
            <label>
              Slug
              <input v-model="form.slug" type="text" required />
            </label>
          </div>

          <label>
            Summary
            <input v-model="form.summary" type="text" />
          </label>

          <label>
            Description
            <textarea v-model="form.description" rows="4" style="width: 100%; padding: 14px 12px; border: 1px solid var(--line); background: transparent; color: var(--ink); font: inherit; resize: vertical;"></textarea>
          </label>

          <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px;">
            <label>
              Icon label
              <input v-model="form.icon" type="text" />
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
            Feature this service
          </label>

          <p v-if="formError" class="form-error" role="alert">{{ formError }}</p>
          <p v-if="formSuccess" class="admin-state" style="color: var(--green-soft);">{{ formSuccess }}</p>

          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <button class="button button--dark" type="submit" :disabled="submitting">
              {{ submitting ? 'Saving...' : (editingId ? 'Update service' : 'Create service') }}
            </button>
            <button v-if="editingId" class="button button--outline" type="button" @click="resetForm" style="border-color: var(--line); color: var(--green); background: transparent;">
              Cancel edit
            </button>
          </div>
        </form>
      </div>

      <p v-if="loading" class="admin-state">Loading services...</p>
      <p v-else-if="errorMessage" class="admin-state form-error">{{ errorMessage }}</p>
      <p v-else-if="services.length === 0" class="admin-state">No services have been added yet.</p>
      <div v-else class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Summary</th>
              <th>Status</th>
              <th>Featured</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="service in services" :key="service.id">
              <td>
                <strong>{{ service.title }}</strong>
                <small>{{ service.slug }}</small>
              </td>
              <td>{{ service.summary || '—' }}</td>
              <td><span class="status-pill">{{ service.status }}</span></td>
              <td>{{ service.featured ? 'Yes' : 'No' }}</td>
              <td>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                  <button class="text-button" type="button" @click="fillForm(service)">Edit</button>
                  <button class="text-button" type="button" @click="deleteService(service.id)" style="color: #9d463a; border-color: #9d463a;">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</template>

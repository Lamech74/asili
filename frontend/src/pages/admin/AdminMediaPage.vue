<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiClient } from '../../services/api/client'
import { useAuthStore } from '../../stores/auth'

interface MediaAsset {
  id: number
  title: string
  file_url: string
  mime_type: string | null
  category: string | null
  alt_text: string | null
  featured: boolean
  status: string
}

interface MediaResponse {
  success: boolean
  message: string
  data: MediaAsset[]
}

interface MediaFormState {
  title: string
  file_url: string
  mime_type: string
  category: string
  alt_text: string
  featured: boolean
  status: 'draft' | 'published' | 'archived'
}

const auth = useAuthStore()
const router = useRouter()
const assets = ref<MediaAsset[]>([])
const loading = ref(true)
const errorMessage = ref('')
const formError = ref('')
const formSuccess = ref('')
const submitting = ref(false)
const editingId = ref<number | null>(null)
const formTitle = computed(() => (editingId.value ? 'Edit media asset' : 'Add media asset'))
const emptyForm = (): MediaFormState => ({
  title: '',
  file_url: '',
  mime_type: '',
  category: '',
  alt_text: '',
  featured: false,
  status: 'draft',
})
const form = ref<MediaFormState>(emptyForm())

async function loadMedia(): Promise<void> {
  loading.value = true
  errorMessage.value = ''

  try {
    const { data } = await apiClient.get<MediaResponse>('/admin/media')
    assets.value = data.data
  } catch {
    errorMessage.value = 'The media library could not be loaded.'
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

function fillForm(asset: MediaAsset): void {
  editingId.value = asset.id
  form.value = {
    title: asset.title,
    file_url: asset.file_url,
    mime_type: asset.mime_type ?? '',
    category: asset.category ?? '',
    alt_text: asset.alt_text ?? '',
    featured: Boolean(asset.featured),
    status: (asset.status as 'draft' | 'published' | 'archived') || 'draft',
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
      file_url: form.value.file_url,
      mime_type: form.value.mime_type || null,
      category: form.value.category || null,
      alt_text: form.value.alt_text || null,
      featured: form.value.featured,
      status: form.value.status,
    }

    if (editingId.value) {
      await apiClient.put(`/admin/media/${editingId.value}`, payload)
      formSuccess.value = 'Media asset updated successfully.'
    } else {
      await apiClient.post('/admin/media', payload)
      formSuccess.value = 'Media asset created successfully.'
    }

    await loadMedia()
    resetForm()
  } catch (error: any) {
    formError.value = error.response?.data?.message || 'The media asset could not be saved.'
  } finally {
    submitting.value = false
  }
}

async function deleteAsset(assetId: number): Promise<void> {
  const confirmed = window.confirm('Delete this media asset?')
  if (!confirmed) return

  try {
    await apiClient.delete(`/admin/media/${assetId}`)
    await loadMedia()
    if (editingId.value === assetId) resetForm()
  } catch {
    errorMessage.value = 'The media asset could not be deleted.'
  }
}

async function signOut(): Promise<void> {
  await auth.logout()
  await router.push('/')
}

onMounted(loadMedia)
</script>

<template>
  <main class="admin-page admin-page--wide">
    <header class="admin-header">
      <div>
        <p class="eyebrow eyebrow--green">Asili Naturals / Admin</p>
        <h1>Media library</h1>
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
              Title
              <input v-model="form.title" type="text" required />
            </label>
            <label>
              Category
              <input v-model="form.category" type="text" placeholder="General" />
            </label>
          </div>

          <label>
            File URL
            <input v-model="form.file_url" type="url" required />
          </label>

          <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px;">
            <label>
              MIME type
              <input v-model="form.mime_type" type="text" placeholder="image/jpeg" />
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

          <label>
            Alt text
            <input v-model="form.alt_text" type="text" />
          </label>

          <label style="display: flex; align-items: center; gap: 10px; text-transform: none; letter-spacing: 0;">
            <input v-model="form.featured" type="checkbox" />
            Feature this asset
          </label>

          <p v-if="formError" class="form-error" role="alert">{{ formError }}</p>
          <p v-if="formSuccess" class="admin-state" style="color: var(--green-soft);">{{ formSuccess }}</p>

          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <button class="button button--dark" type="submit" :disabled="submitting">
              {{ submitting ? 'Saving...' : (editingId ? 'Update asset' : 'Create asset') }}
            </button>
            <button v-if="editingId" class="button button--outline" type="button" @click="resetForm" style="border-color: var(--line); color: var(--green); background: transparent;">
              Cancel edit
            </button>
          </div>
        </form>
      </div>

      <p v-if="loading" class="admin-state">Loading media assets...</p>
      <p v-else-if="errorMessage" class="admin-state form-error">{{ errorMessage }}</p>
      <p v-else-if="assets.length === 0" class="admin-state">No media has been uploaded yet.</p>
      <div v-else class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Category</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="asset in assets" :key="asset.id">
              <td>
                <strong>{{ asset.title }}</strong>
                <small>{{ asset.file_url }}</small>
              </td>
              <td>{{ asset.category || 'General' }}</td>
              <td><span class="status-pill">{{ asset.status }}</span></td>
              <td>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                  <button class="text-button" type="button" @click="fillForm(asset)">Edit</button>
                  <button class="text-button" type="button" @click="deleteAsset(asset.id)" style="color: #9d463a; border-color: #9d463a;">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</template>

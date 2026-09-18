<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiClient } from '../../services/api/client'
import { useAuthStore } from '../../stores/auth'

interface SiteSetting {
  id: number
  key: string
  value: string | null
  type: string
  is_public: boolean
}

interface SiteSettingResponse {
  success: boolean
  message: string
  data: SiteSetting[]
}

interface SettingFormState {
  key: string
  value: string
  type: 'text' | 'number' | 'textarea' | 'boolean' | 'json'
  is_public: boolean
}

const auth = useAuthStore()
const router = useRouter()
const settings = ref<SiteSetting[]>([])
const loading = ref(true)
const errorMessage = ref('')
const formError = ref('')
const formSuccess = ref('')
const submitting = ref(false)
const editingId = ref<number | null>(null)
const formTitle = computed(() => (editingId.value ? 'Edit setting' : 'Add setting'))
const emptyForm = (): SettingFormState => ({
  key: '',
  value: '',
  type: 'text',
  is_public: false,
})
const form = ref<SettingFormState>(emptyForm())

async function loadSettings(): Promise<void> {
  loading.value = true
  errorMessage.value = ''

  try {
    const { data } = await apiClient.get<SiteSettingResponse>('/admin/settings')
    settings.value = data.data
  } catch {
    errorMessage.value = 'The site settings could not be loaded.'
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

function fillForm(setting: SiteSetting): void {
  editingId.value = setting.id
  form.value = {
    key: setting.key,
    value: setting.value ?? '',
    type: (setting.type as 'text' | 'number' | 'textarea' | 'boolean' | 'json') || 'text',
    is_public: Boolean(setting.is_public),
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
      key: form.value.key,
      value: form.value.value || null,
      type: form.value.type,
      is_public: form.value.is_public,
    }

    if (editingId.value) {
      await apiClient.put(`/admin/settings/${editingId.value}`, payload)
      formSuccess.value = 'Setting updated successfully.'
    } else {
      await apiClient.post('/admin/settings', payload)
      formSuccess.value = 'Setting created successfully.'
    }

    await loadSettings()
    resetForm()
  } catch (error: any) {
    formError.value = error.response?.data?.message || 'The setting could not be saved.'
  } finally {
    submitting.value = false
  }
}

async function deleteSetting(settingId: number): Promise<void> {
  const confirmed = window.confirm('Delete this setting?')
  if (!confirmed) return

  try {
    await apiClient.delete(`/admin/settings/${settingId}`)
    await loadSettings()
    if (editingId.value === settingId) resetForm()
  } catch {
    errorMessage.value = 'The setting could not be deleted.'
  }
}

async function signOut(): Promise<void> {
  await auth.logout()
  await router.push('/')
}

onMounted(loadSettings)
</script>

<template>
  <main class="admin-page admin-page--wide">
    <header class="admin-header">
      <div>
        <p class="eyebrow eyebrow--green">Asili Naturals / Admin</p>
        <h1>Site settings</h1>
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
              Key
              <input v-model="form.key" type="text" required />
            </label>
            <label>
              Type
              <select v-model="form.type" style="width: 100%; padding: 14px 12px; border: 1px solid var(--line); background: transparent; color: var(--ink); font: inherit;">
                <option value="text">Text</option>
                <option value="number">Number</option>
                <option value="textarea">Textarea</option>
                <option value="boolean">Boolean</option>
                <option value="json">JSON</option>
              </select>
            </label>
          </div>

          <label>
            Value
            <textarea v-model="form.value" rows="4" style="width: 100%; padding: 14px 12px; border: 1px solid var(--line); background: transparent; color: var(--ink); font: inherit; resize: vertical;"></textarea>
          </label>

          <label style="display: flex; align-items: center; gap: 10px; text-transform: none; letter-spacing: 0;">
            <input v-model="form.is_public" type="checkbox" />
            Publicly visible
          </label>

          <p v-if="formError" class="form-error" role="alert">{{ formError }}</p>
          <p v-if="formSuccess" class="admin-state" style="color: var(--green-soft);">{{ formSuccess }}</p>

          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <button class="button button--dark" type="submit" :disabled="submitting">
              {{ submitting ? 'Saving...' : (editingId ? 'Update setting' : 'Create setting') }}
            </button>
            <button v-if="editingId" class="button button--outline" type="button" @click="resetForm" style="border-color: var(--line); color: var(--green); background: transparent;">
              Cancel edit
            </button>
          </div>
        </form>
      </div>

      <p v-if="loading" class="admin-state">Loading settings...</p>
      <p v-else-if="errorMessage" class="admin-state form-error">{{ errorMessage }}</p>
      <p v-else-if="settings.length === 0" class="admin-state">No site settings have been added yet.</p>
      <div v-else class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Key</th>
              <th>Value</th>
              <th>Public</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="setting in settings" :key="setting.id">
              <td><strong>{{ setting.key }}</strong></td>
              <td>{{ setting.value || '—' }}</td>
              <td>{{ setting.is_public ? 'Yes' : 'No' }}</td>
              <td>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                  <button class="text-button" type="button" @click="fillForm(setting)">Edit</button>
                  <button class="text-button" type="button" @click="deleteSetting(setting.id)" style="color: #9d463a; border-color: #9d463a;">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</template>

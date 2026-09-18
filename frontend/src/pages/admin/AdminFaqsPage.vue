<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiClient } from '../../services/api/client'
import { useAuthStore } from '../../stores/auth'

interface Faq {
  id: number
  question: string
  answer: string
  category: string | null
  featured: boolean
  status: string
}

interface FaqResponse {
  success: boolean
  message: string
  data: Faq[]
}

interface FaqFormState {
  question: string
  answer: string
  category: string
  featured: boolean
  status: 'draft' | 'published' | 'archived'
}

const auth = useAuthStore()
const router = useRouter()
const faqs = ref<Faq[]>([])
const loading = ref(true)
const errorMessage = ref('')
const formError = ref('')
const formSuccess = ref('')
const submitting = ref(false)
const editingId = ref<number | null>(null)
const formTitle = computed(() => (editingId.value ? 'Edit FAQ' : 'Add FAQ'))
const emptyForm = (): FaqFormState => ({
  question: '',
  answer: '',
  category: '',
  featured: false,
  status: 'draft',
})
const form = ref<FaqFormState>(emptyForm())

async function loadFaqs(): Promise<void> {
  loading.value = true
  errorMessage.value = ''

  try {
    const { data } = await apiClient.get<FaqResponse>('/admin/faqs')
    faqs.value = data.data
  } catch {
    errorMessage.value = 'The FAQs could not be loaded.'
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

function fillForm(item: Faq): void {
  editingId.value = item.id
  form.value = {
    question: item.question,
    answer: item.answer,
    category: item.category ?? '',
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
      question: form.value.question,
      answer: form.value.answer,
      category: form.value.category || null,
      featured: form.value.featured,
      status: form.value.status,
    }

    if (editingId.value) {
      await apiClient.put(`/admin/faqs/${editingId.value}`, payload)
      formSuccess.value = 'FAQ updated successfully.'
    } else {
      await apiClient.post('/admin/faqs', payload)
      formSuccess.value = 'FAQ created successfully.'
    }

    await loadFaqs()
    resetForm()
  } catch (error: any) {
    formError.value = error.response?.data?.message || 'The FAQ could not be saved.'
  } finally {
    submitting.value = false
  }
}

async function deleteFaq(itemId: number): Promise<void> {
  const confirmed = window.confirm('Delete this FAQ?')
  if (!confirmed) return

  try {
    await apiClient.delete(`/admin/faqs/${itemId}`)
    await loadFaqs()
    if (editingId.value === itemId) resetForm()
  } catch {
    errorMessage.value = 'The FAQ could not be deleted.'
  }
}

async function signOut(): Promise<void> {
  await auth.logout()
  await router.push('/')
}

onMounted(loadFaqs)
</script>

<template>
  <main class="admin-page admin-page--wide">
    <header class="admin-header">
      <div>
        <p class="eyebrow eyebrow--green">Asili Naturals / Admin</p>
        <h1>FAQ library</h1>
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
              Category
              <input v-model="form.category" type="text" placeholder="General" />
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
            Question
            <input v-model="form.question" type="text" required />
          </label>

          <label>
            Answer
            <textarea v-model="form.answer" rows="4" required style="width: 100%; padding: 14px 12px; border: 1px solid var(--line); background: transparent; color: var(--ink); font: inherit; resize: vertical;"></textarea>
          </label>

          <label style="display: flex; align-items: center; gap: 10px; text-transform: none; letter-spacing: 0;">
            <input v-model="form.featured" type="checkbox" />
            Feature this FAQ
          </label>

          <p v-if="formError" class="form-error" role="alert">{{ formError }}</p>
          <p v-if="formSuccess" class="admin-state" style="color: var(--green-soft);">{{ formSuccess }}</p>

          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <button class="button button--dark" type="submit" :disabled="submitting">
              {{ submitting ? 'Saving...' : (editingId ? 'Update FAQ' : 'Create FAQ') }}
            </button>
            <button v-if="editingId" class="button button--outline" type="button" @click="resetForm" style="border-color: var(--line); color: var(--green); background: transparent;">
              Cancel edit
            </button>
          </div>
        </form>
      </div>

      <p v-if="loading" class="admin-state">Loading FAQs...</p>
      <p v-else-if="errorMessage" class="admin-state form-error">{{ errorMessage }}</p>
      <p v-else-if="faqs.length === 0" class="admin-state">No FAQs have been added yet.</p>
      <div v-else class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Question</th>
              <th>Category</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in faqs" :key="item.id">
              <td><strong>{{ item.question }}</strong></td>
              <td>{{ item.category || 'General' }}</td>
              <td><span class="status-pill">{{ item.status }}</span></td>
              <td>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                  <button class="text-button" type="button" @click="fillForm(item)">Edit</button>
                  <button class="text-button" type="button" @click="deleteFaq(item.id)" style="color: #9d463a; border-color: #9d463a;">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</template>

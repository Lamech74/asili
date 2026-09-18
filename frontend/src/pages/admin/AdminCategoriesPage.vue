<script setup lang="ts">
import { computed, nextTick, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiClient } from '../../services/api/client'
import { useAuthStore } from '../../stores/auth'
import type { Category } from '../../services/api/catalog'

interface CategoryResponse { success: boolean; message: string; data: Category[] }
interface CategoryForm { name: string; slug: string; description: string; image_url: string; is_featured: boolean; status: 'draft' | 'published' | 'archived' }

const auth = useAuthStore()
const router = useRouter()
const categories = ref<Category[]>([])
const loading = ref(true)
const submitting = ref(false)
const uploading = ref(false)
const errorMessage = ref('')
const formError = ref('')
const editingId = ref<number | null>(null)
const formPanel = ref<HTMLElement | null>(null)
const nameInput = ref<HTMLInputElement | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)
const emptyForm = (): CategoryForm => ({ name: '', slug: '', description: '', image_url: '', is_featured: false, status: 'draft' })
const form = ref<CategoryForm>(emptyForm())
const formTitle = computed(() => editingId.value ? 'Edit category' : 'Add category')

async function loadCategories(): Promise<void> {
  loading.value = true
  try {
    const { data } = await apiClient.get<CategoryResponse>('/admin/categories')
    categories.value = data.data
  } catch { errorMessage.value = 'Categories could not be loaded.' } finally { loading.value = false }
}

function resetForm(): void { form.value = emptyForm(); editingId.value = null; formError.value = '' }

async function editCategory(category: Category): Promise<void> {
  editingId.value = category.id
  form.value = { name: category.name, slug: category.slug, description: category.description ?? '', image_url: category.image_url ?? '', is_featured: category.is_featured, status: (category.status as CategoryForm['status']) || 'draft' }
  await nextTick()
  formPanel.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
  nameInput.value?.focus({ preventScroll: true })
}

async function uploadImage(event: Event): Promise<void> {
  const input = event.target as HTMLInputElement
  const image = input.files?.[0]
  if (!image) return
  uploading.value = true
  formError.value = ''
  try {
    const payload = new FormData()
    payload.append('image', image)
    const { data } = await apiClient.post<{ data: { url: string } }>('/admin/uploads/image', payload, { headers: { 'Content-Type': 'multipart/form-data' } })
    form.value.image_url = data.data.url
  } catch (error: any) { formError.value = error.response?.data?.message || 'The image could not be uploaded.' } finally { uploading.value = false; input.value = '' }
}

async function submit(): Promise<void> {
  submitting.value = true; formError.value = ''
  try {
    if (editingId.value) await apiClient.put(`/admin/categories/${editingId.value}`, form.value)
    else await apiClient.post('/admin/categories', form.value)
    resetForm(); await loadCategories()
  } catch (error: any) { formError.value = error.response?.data?.message || 'The category could not be saved.' } finally { submitting.value = false }
}

async function deleteCategory(id: number): Promise<void> {
  if (!window.confirm('Delete this category?')) return
  try { await apiClient.delete(`/admin/categories/${id}`); await loadCategories() } catch (error: any) { errorMessage.value = error.response?.data?.message || 'The category could not be deleted.' }
}

async function signOut(): Promise<void> { await auth.logout(); await router.push('/') }
onMounted(loadCategories)
</script>

<template>
  <main class="admin-page admin-page--wide">
    <header class="admin-header">
      <div><p class="eyebrow eyebrow--green">Asili Naturals / Admin</p><h1>Categories</h1></div>
      <div class="admin-header__actions"><span>{{ auth.user?.name }}</span><button class="text-button" type="button" @click="signOut">Sign out</button></div>
    </header>
    <section class="admin-content">
      <div ref="formPanel" class="admin-panel admin-panel--compact" style="width: min(760px, 100%); margin: 0 0 40px;">
        <h2 style="margin-bottom: 18px;">{{ formTitle }}</h2>
        <form class="admin-form" @submit.prevent="submit">
          <div class="admin-form-grid">
            <label>Name<input ref="nameInput" v-model="form.name" type="text" required /></label>
            <label>Slug<input v-model="form.slug" type="text" required /></label>
          </div>
          <label>Description<textarea v-model="form.description" rows="3"></textarea></label>
          <label>Image URL<input v-model="form.image_url" type="url" placeholder="https://..." /></label>
          <div class="upload-row"><input ref="fileInput" type="file" accept="image/jpeg,image/png,image/webp,image/gif" @change="uploadImage" /><span v-if="uploading">Uploading...</span><span v-else>Upload an image or keep using an image URL.</span></div>
          <div class="admin-form-grid">
            <label>Status<select v-model="form.status"><option value="draft">Draft</option><option value="published">Published</option><option value="archived">Archived</option></select></label>
            <label class="checkbox-label"><input v-model="form.is_featured" type="checkbox" /> Featured category</label>
          </div>
          <img v-if="form.image_url" class="admin-image-preview" :src="form.image_url" alt="Category preview" />
          <p v-if="formError" class="form-error" role="alert">{{ formError }}</p>
          <div class="admin-actions"><button class="button button--dark" type="submit" :disabled="submitting || uploading">{{ submitting ? 'Saving...' : (editingId ? 'Update category' : 'Create category') }}</button><button v-if="editingId" class="text-button" type="button" @click="resetForm">Cancel edit</button></div>
        </form>
      </div>
      <p v-if="loading" class="admin-state">Loading categories...</p><p v-else-if="errorMessage" class="admin-state form-error">{{ errorMessage }}</p>
      <div v-else class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Name</th><th>Image</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead><tbody><tr v-for="category in categories" :key="category.id"><td><strong>{{ category.name }}</strong><small>{{ category.slug }}</small></td><td><img v-if="category.image_url" class="admin-table-thumb" :src="category.image_url" :alt="category.name" /><span v-else>None</span></td><td>{{ category.products_count ?? 0 }}</td><td><span class="status-pill">{{ category.status }}</span></td><td><button class="text-button" type="button" @click="editCategory(category)">Edit</button><button class="text-button danger-button" type="button" @click="deleteCategory(category.id)">Delete</button></td></tr></tbody></table></div>
    </section>
  </main>
</template>

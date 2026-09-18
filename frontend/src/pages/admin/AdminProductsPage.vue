<script setup lang="ts">
import { computed, nextTick, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { apiClient } from '../../services/api/client'
import { fetchCategories, type Category, type Product } from '../../services/api/catalog'

interface ProductResponse {
  success: boolean
  message: string
  data: Product[]
}

interface ProductFormState {
  category_id: string
  name: string
  slug: string
  short_description: string
  description: string
  price: string
  compare_at_price: string
  image_url: string
  featured: boolean
  status: 'draft' | 'published' | 'archived'
}

const auth = useAuthStore()
const router = useRouter()
const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const loading = ref(true)
const errorMessage = ref('')
const formError = ref('')
const formSuccess = ref('')
const submitting = ref(false)
const uploading = ref(false)
const editingProductId = ref<number | null>(null)
const productFormPanel = ref<HTMLElement | null>(null)
const productNameInput = ref<HTMLInputElement | null>(null)

const emptyForm = (): ProductFormState => ({
  category_id: '',
  name: '',
  slug: '',
  short_description: '',
  description: '',
  price: '',
  compare_at_price: '',
  image_url: '',
  featured: false,
  status: 'draft',
})

const form = ref<ProductFormState>(emptyForm())

const formTitle = computed(() => (editingProductId.value ? 'Edit product' : 'Add product'))

async function loadProducts(): Promise<void> {
  loading.value = true
  errorMessage.value = ''

  try {
    const { data } = await apiClient.get<ProductResponse>('/admin/products')
    products.value = data.data
  } catch {
    errorMessage.value = 'The catalogue could not be loaded.'
  } finally {
    loading.value = false
  }
}

async function loadCategories(): Promise<void> {
  categories.value = await fetchCategories()
}

function resetForm(): void {
  form.value = emptyForm()
  editingProductId.value = null
  formError.value = ''
  formSuccess.value = ''
}

async function fillForm(product: Product): Promise<void> {
  editingProductId.value = product.id
  form.value = {
    category_id: product.category_id ? String(product.category_id) : '',
    name: product.name,
    slug: product.slug,
    short_description: product.short_description ?? '',
    description: product.description ?? '',
    price: String(product.price ?? ''),
    compare_at_price: product.compare_at_price ? String(product.compare_at_price) : '',
    image_url: product.image_url ?? '',
    featured: Boolean(product.featured),
    status: (product.status as 'draft' | 'published' | 'archived') || 'draft',
  }
  formError.value = ''
  formSuccess.value = ''

  await nextTick()
  productFormPanel.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
  productNameInput.value?.focus({ preventScroll: true })
}

async function handleSubmit(): Promise<void> {
  formError.value = ''
  formSuccess.value = ''
  submitting.value = true

  try {
    const payload = {
      category_id: form.value.category_id ? Number(form.value.category_id) : null,
      name: form.value.name,
      slug: form.value.slug,
      short_description: form.value.short_description || null,
      description: form.value.description || null,
      price: Number(form.value.price),
      compare_at_price: form.value.compare_at_price ? Number(form.value.compare_at_price) : null,
      image_url: form.value.image_url || null,
      featured: form.value.featured,
      status: form.value.status,
    }

    if (editingProductId.value) {
      await apiClient.put(`/admin/products/${editingProductId.value}`, payload)
      formSuccess.value = 'Product updated successfully.'
    } else {
      await apiClient.post('/admin/products', payload)
      formSuccess.value = 'Product created successfully.'
    }

    resetForm()
    await loadProducts()
  } catch (error: any) {
    const message = error.response?.data?.message || 'The product could not be saved.'
    formError.value = message
  } finally {
    submitting.value = false
  }
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
    const { data } = await apiClient.post<{ data: { url: string } }>('/admin/uploads/image', payload, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    form.value.image_url = data.data.url
  } catch (error: any) {
    formError.value = error.response?.data?.message || 'The image could not be uploaded.'
  } finally {
    uploading.value = false
    input.value = ''
  }
}

async function deleteProduct(productId: number): Promise<void> {
  const confirmed = window.confirm('Delete this product from the catalogue?')
  if (!confirmed) return

  try {
    await apiClient.delete(`/admin/products/${productId}`)
    await loadProducts()
    if (editingProductId.value === productId) {
      resetForm()
    }
  } catch {
    errorMessage.value = 'The product could not be deleted.'
  }
}

async function signOut(): Promise<void> {
  await auth.logout()
  await router.push('/')
}

onMounted(async () => {
  await loadCategories()
  await loadProducts()
})
</script>

<template>
  <main class="admin-page admin-page--wide">
    <header class="admin-header">
      <div>
        <p class="eyebrow eyebrow--green">Asili Naturals / Admin</p>
        <h1>Product catalogue</h1>
      </div>
      <div class="admin-header__actions">
        <RouterLink class="text-button" to="/admin/categories">Categories</RouterLink>
        <span>{{ auth.user?.name }}</span>
        <button class="text-button" type="button" @click="signOut">Sign out</button>
      </div>
    </header>

    <section class="admin-content">
      <div ref="productFormPanel" class="admin-panel admin-panel--compact admin-product-form-panel" style="width: min(760px, 100%); margin: 0 0 40px;">
        <h2 style="margin-bottom: 18px;">{{ formTitle }}</h2>
        <form class="admin-form" @submit.prevent="handleSubmit">
          <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px;">
            <label>
              Product name
              <input ref="productNameInput" v-model="form.name" type="text" required />
            </label>
            <label>
              Slug
              <input v-model="form.slug" type="text" required />
            </label>
          </div>

          <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px;">
            <label>
              Category
              <select v-model="form.category_id" style="width: 100%; padding: 14px 12px; border: 1px solid var(--line); background: transparent; color: var(--ink); font: inherit;">
                <option value="">Uncategorised</option>
                <option v-for="category in categories" :key="category.id" :value="String(category.id)">{{ category.name }}</option>
              </select>
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

          <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px;">
            <label>
              Price (KES)
              <input v-model="form.price" type="number" min="0" step="0.01" required />
            </label>
            <label>
              Compare price (KES)
              <input v-model="form.compare_at_price" type="number" min="0" step="0.01" />
            </label>
          </div>

          <label>
            Short description
            <input v-model="form.short_description" type="text" />
          </label>

          <label>
            Description
            <textarea v-model="form.description" rows="4" style="width: 100%; padding: 14px 12px; border: 1px solid var(--line); background: transparent; color: var(--ink); font: inherit; resize: vertical;"></textarea>
          </label>

          <label>
            Image URL
            <input v-model="form.image_url" type="url" />
          </label>
          <div class="upload-row">
            <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" @change="uploadImage" />
            <span>{{ uploading ? 'Uploading...' : 'Upload an image or keep using an image URL.' }}</span>
          </div>
          <img v-if="form.image_url" class="admin-image-preview" :src="form.image_url" alt="Product preview" />

          <label style="display: flex; align-items: center; gap: 10px; text-transform: none; letter-spacing: 0;">
            <input v-model="form.featured" type="checkbox" />
            Feature this product
          </label>

          <p v-if="formError" class="form-error" role="alert">{{ formError }}</p>
          <p v-if="formSuccess" class="admin-state" style="color: var(--green-soft);">{{ formSuccess }}</p>

          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <button class="button button--dark" type="submit" :disabled="submitting || uploading">
              {{ submitting ? 'Saving...' : (editingProductId ? 'Update product' : 'Create product') }}
            </button>
            <button v-if="editingProductId" class="button button--outline" type="button" @click="resetForm" style="border-color: var(--line); color: var(--green); background: transparent;">
              Cancel edit
            </button>
          </div>
        </form>
      </div>

      <p v-if="loading" class="admin-state">Loading the collection...</p>
      <p v-else-if="errorMessage" class="admin-state form-error">{{ errorMessage }}</p>
      <p v-else-if="products.length === 0" class="admin-state">No products have been added yet.</p>
      <div v-else class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr><th>Name</th><th>Category</th><th>Price</th><th>Status</th><th>Featured</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id">
              <td><strong>{{ product.name }}</strong><small>{{ product.slug }}</small></td>
              <td>{{ product.category?.name ?? 'Uncategorised' }}</td>
              <td>KES {{ product.price }}</td>
              <td><span class="status-pill">{{ product.status }}</span></td>
              <td>{{ product.featured ? 'Yes' : 'No' }}</td>
              <td>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                  <button class="text-button" type="button" @click="fillForm(product)">Edit</button>
                  <button class="text-button" type="button" @click="deleteProduct(product.id)" style="color: #9d463a; border-color: #9d463a;">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</template>

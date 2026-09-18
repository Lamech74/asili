<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiClient } from '../../services/api/client'
import { useAuthStore } from '../../stores/auth'

interface BlogPost {
  id: number
  title: string
  slug: string
  excerpt: string | null
  content: string | null
  featured_image: string | null
  author_name: string | null
  published_at: string | null
  featured: boolean
  status: string
}

interface BlogResponse {
  success: boolean
  message: string
  data: BlogPost[]
}

interface BlogFormState {
  title: string
  slug: string
  excerpt: string
  content: string
  featured_image: string
  author_name: string
  published_at: string
  featured: boolean
  status: 'draft' | 'published' | 'archived'
}

const auth = useAuthStore()
const router = useRouter()
const posts = ref<BlogPost[]>([])
const loading = ref(true)
const errorMessage = ref('')
const formError = ref('')
const formSuccess = ref('')
const submitting = ref(false)
const editingId = ref<number | null>(null)
const formTitle = computed(() => (editingId.value ? 'Edit blog post' : 'Add blog post'))
const emptyForm = (): BlogFormState => ({
  title: '',
  slug: '',
  excerpt: '',
  content: '',
  featured_image: '',
  author_name: '',
  published_at: '',
  featured: false,
  status: 'draft',
})
const form = ref<BlogFormState>(emptyForm())

async function loadPosts(): Promise<void> {
  loading.value = true
  errorMessage.value = ''

  try {
    const { data } = await apiClient.get<BlogResponse>('/admin/blog')
    posts.value = data.data
  } catch {
    errorMessage.value = 'The blog posts could not be loaded.'
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

function fillForm(post: BlogPost): void {
  editingId.value = post.id
  form.value = {
    title: post.title,
    slug: post.slug,
    excerpt: post.excerpt ?? '',
    content: post.content ?? '',
    featured_image: post.featured_image ?? '',
    author_name: post.author_name ?? '',
    published_at: post.published_at ? new Date(post.published_at).toISOString().slice(0, 16) : '',
    featured: Boolean(post.featured),
    status: (post.status as 'draft' | 'published' | 'archived') || 'draft',
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
      excerpt: form.value.excerpt || null,
      content: form.value.content || null,
      featured_image: form.value.featured_image || null,
      author_name: form.value.author_name || null,
      published_at: form.value.published_at ? new Date(form.value.published_at).toISOString() : null,
      featured: form.value.featured,
      status: form.value.status,
    }

    if (editingId.value) {
      await apiClient.put(`/admin/blog/${editingId.value}`, payload)
      formSuccess.value = 'Blog post updated successfully.'
    } else {
      await apiClient.post('/admin/blog', payload)
      formSuccess.value = 'Blog post created successfully.'
    }

    await loadPosts()
    resetForm()
  } catch (error: any) {
    formError.value = error.response?.data?.message || 'The blog post could not be saved.'
  } finally {
    submitting.value = false
  }
}

async function deletePost(postId: number): Promise<void> {
  const confirmed = window.confirm('Delete this blog post?')
  if (!confirmed) return

  try {
    await apiClient.delete(`/admin/blog/${postId}`)
    await loadPosts()
    if (editingId.value === postId) resetForm()
  } catch {
    errorMessage.value = 'The blog post could not be deleted.'
  }
}

async function signOut(): Promise<void> {
  await auth.logout()
  await router.push('/')
}

onMounted(loadPosts)
</script>

<template>
  <main class="admin-page admin-page--wide">
    <header class="admin-header">
      <div>
        <p class="eyebrow eyebrow--green">Asili Naturals / Admin</p>
        <h1>Blog posts</h1>
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
              Slug
              <input v-model="form.slug" type="text" required />
            </label>
          </div>

          <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px;">
            <label>
              Author name
              <input v-model="form.author_name" type="text" />
            </label>
            <label>
              Published at
              <input v-model="form.published_at" type="datetime-local" />
            </label>
          </div>

          <label>
            Excerpt
            <input v-model="form.excerpt" type="text" />
          </label>

          <label>
            Content
            <textarea v-model="form.content" rows="5" style="width: 100%; padding: 14px 12px; border: 1px solid var(--line); background: transparent; color: var(--ink); font: inherit; resize: vertical;"></textarea>
          </label>

          <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px;">
            <label>
              Featured image URL
              <input v-model="form.featured_image" type="url" />
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
            Feature this post
          </label>

          <p v-if="formError" class="form-error" role="alert">{{ formError }}</p>
          <p v-if="formSuccess" class="admin-state" style="color: var(--green-soft);">{{ formSuccess }}</p>

          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <button class="button button--dark" type="submit" :disabled="submitting">
              {{ submitting ? 'Saving...' : (editingId ? 'Update post' : 'Create post') }}
            </button>
            <button v-if="editingId" class="button button--outline" type="button" @click="resetForm" style="border-color: var(--line); color: var(--green); background: transparent;">
              Cancel edit
            </button>
          </div>
        </form>
      </div>

      <p v-if="loading" class="admin-state">Loading blog posts...</p>
      <p v-else-if="errorMessage" class="admin-state form-error">{{ errorMessage }}</p>
      <p v-else-if="posts.length === 0" class="admin-state">No blog posts have been added yet.</p>
      <div v-else class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Author</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="post in posts" :key="post.id">
              <td>
                <strong>{{ post.title }}</strong>
                <small>{{ post.slug }}</small>
              </td>
              <td>{{ post.author_name || 'Asili' }}</td>
              <td><span class="status-pill">{{ post.status }}</span></td>
              <td>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                  <button class="text-button" type="button" @click="fillForm(post)">Edit</button>
                  <button class="text-button" type="button" @click="deletePost(post.id)" style="color: #9d463a; border-color: #9d463a;">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</template>

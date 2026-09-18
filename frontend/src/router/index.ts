import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../pages/public/HomePage.vue'),
    },
    {
      path: '/admin/login',
      name: 'admin-login',
      component: () => import('../pages/admin/AdminLoginPage.vue'),
      meta: { guestOnly: true },
    },
    {
      path: '/admin/products',
      name: 'admin-products',
      component: () => import('../pages/admin/AdminProductsPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/categories',
      name: 'admin-categories',
      component: () => import('../pages/admin/AdminCategoriesPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/services',
      name: 'admin-services',
      component: () => import('../pages/admin/AdminServicesPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/blog',
      name: 'admin-blog',
      component: () => import('../pages/admin/AdminBlogPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/testimonials',
      name: 'admin-testimonials',
      component: () => import('../pages/admin/AdminTestimonialsPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/faqs',
      name: 'admin-faqs',
      component: () => import('../pages/admin/AdminFaqsPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/settings',
      name: 'admin-settings',
      component: () => import('../pages/admin/AdminSettingsPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/media',
      name: 'admin-media',
      component: () => import('../pages/admin/AdminMediaPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  if (auth.token && !auth.user) {
    try {
      await auth.fetchCurrentUser()
    } catch {
      auth.clear()
    }
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'admin-login', query: { redirect: to.fullPath } }
  }

  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return { name: 'home' }
  }

  if (to.meta.guestOnly && auth.isAuthenticated) {
    return { name: 'admin-products' }
  }
})

export default router

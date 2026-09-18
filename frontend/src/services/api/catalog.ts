import { apiClient } from './client'

export interface Category {
  id: number
  name: string
  slug: string
  description: string | null
  image_url: string | null
  is_featured: boolean
  status: string
  products_count?: number
}

export interface Product {
  id: number
  category_id: number | null
  name: string
  slug: string
  short_description: string | null
  description: string | null
  price: string
  compare_at_price: string | null
  image_url: string | null
  featured: boolean
  status: string
  category?: Category | null
}

export interface Service {
  id: number
  title: string
  slug: string
  summary: string | null
  description: string | null
  icon: string | null
  featured: boolean
  status: string
}

export interface BlogPost {
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

export interface Testimonial {
  id: number
  name: string
  role: string | null
  quote: string
  avatar_url: string | null
  featured: boolean
  status: string
}

export interface Faq {
  id: number
  question: string
  answer: string
  category: string | null
  featured: boolean
  status: string
}

export interface SiteSetting {
  id: number
  key: string
  value: string | null
  type: string
  is_public: boolean
}

export interface MediaAsset {
  id: number
  title: string
  file_url: string
  mime_type: string | null
  category: string | null
  alt_text: string | null
  featured: boolean
  status: string
}

interface ApiResponse<T> {
  success: boolean
  message: string
  data: T
}

export async function fetchProducts(): Promise<Product[]> {
  const { data } = await apiClient.get<ApiResponse<Product[]>>('/products')
  return data.data
}

export async function fetchServices(): Promise<Service[]> {
  const { data } = await apiClient.get<ApiResponse<Service[]>>('/services')
  return data.data
}

export async function fetchBlogPosts(): Promise<BlogPost[]> {
  const { data } = await apiClient.get<ApiResponse<BlogPost[]>>('/blog')
  return data.data
}

export async function fetchTestimonials(): Promise<Testimonial[]> {
  const { data } = await apiClient.get<ApiResponse<Testimonial[]>>('/testimonials')
  return data.data
}

export async function fetchFaqs(): Promise<Faq[]> {
  const { data } = await apiClient.get<ApiResponse<Faq[]>>('/faqs')
  return data.data
}

export async function fetchSiteSettings(): Promise<SiteSetting[]> {
  const { data } = await apiClient.get<ApiResponse<SiteSetting[]>>('/settings')
  return data.data
}

export async function fetchMediaAssets(): Promise<MediaAsset[]> {
  const { data } = await apiClient.get<ApiResponse<MediaAsset[]>>('/media')
  return data.data
}

export async function fetchCategories(): Promise<Category[]> {
  const { data } = await apiClient.get<ApiResponse<Category[]>>('/categories')
  return data.data
}

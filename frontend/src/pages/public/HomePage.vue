<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { formatProductPrice } from '../../services/currency'
import {
  fetchBlogPosts,
  fetchFaqs,
  fetchProducts,
  fetchServices,
  fetchSiteSettings,
  fetchTestimonials,
  type Product,
  type Service,
} from '../../services/api/catalog'

const products = ref<Product[]>([])
const productsLoading = ref(true)
const productsError = ref(false)
const services = ref<Service[]>([])
const servicesLoading = ref(true)
const servicesError = ref(false)
const blogPosts = ref<any[]>([])
const testimonials = ref<any[]>([])
const faqs = ref<any[]>([])
const settings = ref<any[]>([])

const principles = [
  {
    number: '01',
    title: 'Rooted in nature',
    text: 'Thoughtful wellness products inspired by the richness of African botanicals.',
  },
  {
    number: '02',
    title: 'Made with care',
    text: 'Simple, intentional choices for everyday rituals that feel good to keep.',
  },
  {
    number: '03',
    title: 'Guided by trust',
    text: 'Clear information and responsible language for a more informed wellness journey.',
  },
]

onMounted(async () => {
  try {
    const [productData, serviceData, blogData, testimonialData, faqData, settingData] = await Promise.all([
      fetchProducts(),
      fetchServices(),
      fetchBlogPosts(),
      fetchTestimonials(),
      fetchFaqs(),
      fetchSiteSettings(),
    ])
    products.value = productData
    services.value = serviceData
    blogPosts.value = blogData
    testimonials.value = testimonialData
    faqs.value = faqData
    settings.value = settingData
  } catch {
    productsError.value = true
    servicesError.value = true
  } finally {
    productsLoading.value = false
    servicesLoading.value = false
  }
})
</script>

<template>
  <main>
    <section class="hero">
      <div class="hero__grain"></div>
      <div class="container hero__content">
        <p class="eyebrow"><span></span> Asili Naturals</p>
        <h1>Natural healing,<br /><em>God's provision.</em></h1>
        <p class="hero__intro">
          Thoughtful, botanical wellness essentials for daily rituals that support your body,
          mind, and spirit with clarity and care.
        </p>
        <div class="hero__actions">
          <a class="button button--light" href="#products">Explore products <span>↗</span></a>
          <a class="text-link text-link--light" href="#story">Learn our story</a>
        </div>
      </div>
      <div class="hero__botanical" aria-hidden="true">
        <span class="sun"></span>
        <span class="stem stem--one"></span>
        <span class="stem stem--two"></span>
        <span class="leaf leaf--one"></span>
        <span class="leaf leaf--two"></span>
        <span class="leaf leaf--three"></span>
      </div>
      <div class="hero__note"><span class="hero__line"></span>Rooted in nature</div>
    </section>

    <section id="story" class="section section--paper">
      <div class="container story-grid">
        <div>
          <p class="eyebrow eyebrow--green">A gentler way forward</p>
          <h2>Wellness is a practice, not a promise.</h2>
        </div>
        <div class="story-copy">
          <p>
            We believe nature offers generous starting points for caring for body, mind, and spirit.
            Asili Naturals brings those starting points into considered, everyday experiences.
          </p>
          <a class="text-link" href="#principles">Meet Asili Naturals <span>↗</span></a>
        </div>
      </div>
    </section>

    <section id="principles" class="section section--sand">
      <div class="container">
        <div class="section-heading">
          <p class="eyebrow eyebrow--green">The Asili approach</p>
          <h2>Small rituals.<br /><em>Deep roots.</em></h2>
        </div>
        <div class="principles-grid">
          <article v-for="principle in principles" :key="principle.number" class="principle">
            <span class="principle__number">{{ principle.number }}</span>
            <h3>{{ principle.title }}</h3>
            <p>{{ principle.text }}</p>
          </article>
        </div>
      </div>
    </section>

    <section id="services" class="section section--sand">
      <div class="container">
        <div class="section-heading">
          <div>
            <p class="eyebrow eyebrow--green">What we offer</p>
            <h2>Gentle support<br /><em>for everyday wellness.</em></h2>
          </div>
          <p class="catalog__intro">Natural care designed to help you slow down, restore balance, and move with more intention.</p>
        </div>

        <div v-if="servicesLoading" class="admin-state">Preparing our service menu...</div>
        <div v-else-if="servicesError || services.length === 0" class="admin-state">Support offerings are being refreshed. Please check back soon.</div>
        <div v-else class="catalog-grid">
          <article v-for="service in services" :key="service.id" class="catalog-card">
            <div class="catalog-card__image">
              <span aria-hidden="true">{{ service.icon || 'A' }}</span>
            </div>
            <div class="catalog-card__body">
              <p class="catalog-card__category">Service</p>
              <h3>{{ service.title }}</h3>
              <p>{{ service.summary || service.description }}</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <section v-if="blogPosts.length" id="blog" class="section section--paper">
      <div class="container">
        <div class="section-heading">
          <div>
            <p class="eyebrow eyebrow--green">Journal</p>
            <h2>Stories from the<br /><em>wellness path.</em></h2>
          </div>
        </div>
        <div class="catalog-grid">
          <article v-for="post in blogPosts.slice(0, 3)" :key="post.id" class="catalog-card">
            <div class="catalog-card__image">
              <img v-if="post.featured_image" :src="post.featured_image" :alt="post.title" />
              <span v-else aria-hidden="true">A</span>
            </div>
            <div class="catalog-card__body">
              <p class="catalog-card__category">{{ post.author_name || 'Asili Journal' }}</p>
              <h3>{{ post.title }}</h3>
              <p>{{ post.excerpt || post.content }}</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <section v-if="testimonials.length" id="testimonials" class="section section--sand">
      <div class="container">
        <div class="section-heading">
          <div>
            <p class="eyebrow eyebrow--green">Kind words</p>
            <h2>What our community<br /><em>is saying.</em></h2>
          </div>
        </div>
        <div class="catalog-grid">
          <article v-for="testimonial in testimonials.slice(0, 3)" :key="testimonial.id" class="catalog-card">
            <div class="catalog-card__body">
              <p class="catalog-card__category">{{ testimonial.role || 'Customer' }}</p>
              <h3>{{ testimonial.name }}</h3>
              <p>“{{ testimonial.quote }}”</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <section v-if="faqs.length" id="faqs" class="section section--paper">
      <div class="container">
        <div class="section-heading">
          <div>
            <p class="eyebrow eyebrow--green">FAQs</p>
            <h2>Questions, answered<br /><em>with care.</em></h2>
          </div>
        </div>
        <div class="catalog-grid">
          <article v-for="faq in faqs.slice(0, 3)" :key="faq.id" class="catalog-card">
            <div class="catalog-card__body">
              <p class="catalog-card__category">{{ faq.category || 'General' }}</p>
              <h3>{{ faq.question }}</h3>
              <p>{{ faq.answer }}</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <section id="products" class="section section--green">
      <div class="container product-callout" v-if="productsLoading || productsError || products.length === 0">
        <div>
          <p class="eyebrow eyebrow--cream">Coming into focus</p>
          <h2>Discover your<br /><em>natural rhythm.</em></h2>
        </div>
        <div>
          <p v-if="productsLoading" class="product-callout__copy">Preparing the collection...</p>
          <p v-else-if="productsError" class="product-callout__copy">The collection is resting briefly. Please check back soon.</p>
          <p v-else class="product-callout__copy">Our product collection is being carefully prepared. Soon you will be able to explore ingredient-led essentials made for your own pace of wellness.</p>
          <a class="button button--outline" href="mailto:hello@asilinaturals.co.ke">Stay connected <span>↗</span></a>
        </div>
      </div>
      <div v-else class="container catalog">
        <div class="section-heading">
          <div>
            <p class="eyebrow eyebrow--cream">The collection</p>
            <h2>Made for your<br /><em>natural rhythm.</em></h2>
          </div>
          <p class="catalog__intro">Ingredient-led essentials for small, considered rituals.</p>
        </div>
        <div class="catalog-grid">
          <article v-for="product in products" :key="product.id" class="catalog-card">
            <div class="catalog-card__image">
              <img v-if="product.image_url" :src="product.image_url" :alt="product.name" />
              <span v-else aria-hidden="true">AN</span>
            </div>
            <div class="catalog-card__body">
              <p class="catalog-card__category">{{ product.category?.name ?? 'Asili Naturals' }}</p>
              <h3>{{ product.name }}</h3>
              <p>{{ product.short_description }}</p>
              <strong>{{ formatProductPrice(product.price) }}</strong>
            </div>
          </article>
        </div>
      </div>
    </section>
  </main>
</template>

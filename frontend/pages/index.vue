<template>
  <main class="products-page">
    <div class="header-section">
      <div class="header-container">
        <div class="header-content">
          <h1 class="main-title">Our Products</h1>
          <p class="main-subtitle">Discover our amazing collection</p>
        </div>
      </div>
    </div>

    <div class="main-container">
      <div v-if="pending" class="loading-state">
        <div class="loading-spinner"></div>
        <p class="loading-text">Loading amazing products...</p>
      </div>

      <div v-else-if="error" class="error-state">
        <div class="error-card">
          <div class="error-icon">
            <svg class="error-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="error-title">Oops! Something went wrong</h3>
          <p class="error-message">{{ error.message }}</p>
        </div>
      </div>

      <div v-else-if="products?.length" class="products-section">
        <div class="products-header">
          <p class="products-count">
            <span class="count-number">{{ products.length }}</span> 
            {{ products.length === 1 ? 'product' : 'products' }} found
          </p>
        </div>

        <div class="products-grid">
          <div 
            v-for="product in products" 
            :key="product.id"
            class="product-card"
          >
            <div class="product-image-container">
              <img 
                v-if="product.images?.length" 
                :src="product.images[0].src" 
                :alt="product.title"
                class="product-image"
                loading="lazy"
              />
              <div 
                v-else 
                class="product-image-placeholder"
              >
                <svg class="placeholder-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              
              <div 
                v-if="product.variants?.length" 
                class="variants-badge"
              >
                {{ product.variants.length }} variant{{ product.variants.length !== 1 ? 's' : '' }}
              </div>
            </div>

            <div class="product-info">
              <h3 class="product-title">
                {{ product.title }}
              </h3>
              
              <p v-if="product.description" class="product-description">
                {{ product.description }}
              </p>

              <div class="product-meta">
                <span v-if="product.price" class="product-price">
                  ${{ product.price }}
                </span>
                <span v-if="product.status" class="product-status" :class="'status-' + product.status">
                  {{ product.status }}
                </span>
              </div>

              <button class="product-button">
                View Details
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <div class="empty-content">
          <div class="empty-icon">
            <svg class="empty-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
          </div>
          <h3 class="empty-title">No products yet</h3>
          <p class="empty-description">Start by adding your first product to see it here.</p>
          <button class="empty-button">
            Add Product
          </button>
        </div>
      </div>

    </div>
  </main>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()

const { data, pending, error } = await useAsyncData('products', () =>
  $fetch(`${config.public.apiBase}/products?with=variants,images`)
)

const products = computed(() => (data.value?.data ?? []) as any[])
const raw = computed(() => data.value)
</script>



<style scoped>
* {
  box-sizing: border-box;
}

.products-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
}

.header-section {
  background-color: #ffffff;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  border-bottom: 1px solid #e5e7eb;
}

.header-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 1rem;
}

@media (min-width: 640px) {
  .header-container {
    padding: 0 1.5rem;
  }
}

@media (min-width: 1024px) {
  .header-container {
    padding: 0 2rem;
  }
}

.header-content {
  text-align: center;
  padding: 2rem 0;
}

.main-title {
  font-size: 2.25rem;
  font-weight: 700;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.main-subtitle {
  font-size: 1.125rem;
  color: #6b7280;
  margin: 0;
}

.main-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

@media (min-width: 640px) {
  .main-container {
    padding: 2rem 1.5rem;
  }
}

@media (min-width: 1024px) {
  .main-container {
    padding: 2rem 2rem;
  }
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 0;
}

.loading-spinner {
  width: 4rem;
  height: 4rem;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #2563eb;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-text {
  color: #6b7280;
  font-size: 1.125rem;
  margin: 0;
}

.error-state {
  max-width: 28rem;
  margin: 0 auto;
  text-align: center;
  padding: 4rem 0;
}

.error-card {
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 0.5rem;
  padding: 1.5rem;
}

.error-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 3rem;
  height: 3rem;
  margin: 0 auto 1rem auto;
  background-color: #fee2e2;
  border-radius: 50%;
}

.error-svg {
  width: 1.5rem;
  height: 1.5rem;
  color: #dc2626;
}

.error-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #991b1b;
  margin: 0 0 0.5rem 0;
}

.error-message {
  color: #dc2626;
  margin: 0;
}

.products-section {
  margin-bottom: 1.5rem;
}

.products-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
}

.products-count {
  color: #6b7280;
  margin: 0;
}

.count-number {
  font-weight: 600;
  color: #111827;
}

.products-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

@media (min-width: 640px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1280px) {
  .products-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.product-card {
  background-color: #ffffff;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  transition: all 0.3s ease;
  overflow: hidden;
  border: 1px solid #e5e7eb;
  position: relative;
}

.product-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  border-color: #d1d5db;
  transform: translateY(-2px);
}

.product-image-container {
  position: relative;
  aspect-ratio: 1;
  overflow: hidden;
  background-color: #f3f4f6;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image {
  transform: scale(1.05);
}

.product-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
}

.placeholder-icon {
  width: 4rem;
  height: 4rem;
  color: #9ca3af;
}

.variants-badge {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  background-color: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(4px);
  border-radius: 9999px;
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 500;
  color: #374151;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.product-info {
  padding: 1rem;
}

.product-title {
  font-weight: 600;
  color: #111827;
  font-size: 1.125rem;
  margin: 0 0 0.5rem 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  transition: color 0.2s ease;
}

.product-card:hover .product-title {
  color: #2563eb;
}

.product-description {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0 0 0.75rem 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.5;
}

.product-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.product-price {
  font-weight: 600;
  color: #059669;
}

.product-status {
  padding: 0.125rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-active {
  background-color: #d1fae5;
  color: #065f46;
}

.status-draft {
  background-color: #fef3c7;
  color: #92400e;
}

.status-archived {
  background-color: #fee2e2;
  color: #991b1b;
}

.product-button {
  width: 100%;
  background-color: #2563eb;
  color: #ffffff;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.product-button:hover {
  background-color: #1d4ed8;
  transform: translateY(-1px);
}

.product-button:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.5);
}

.product-button:active {
  transform: translateY(0);
}

.empty-state {
  text-align: center;
  padding: 4rem 0;
}

.empty-content {
  max-width: 28rem;
  margin: 0 auto;
}

.empty-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 4rem;
  height: 4rem;
  margin: 0 auto 1rem auto;
  background-color: #f3f4f6;
  border-radius: 50%;
}

.empty-svg {
  width: 2rem;
  height: 2rem;
  color: #9ca3af;
}

.empty-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.empty-description {
  color: #6b7280;
  margin: 0 0 1.5rem 0;
}

.empty-button {
  background-color: #2563eb;
  color: #ffffff;
  font-weight: 500;
  padding: 0.5rem 1.5rem;
  border-radius: 0.5rem;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.empty-button:hover {
  background-color: #1d4ed8;
}

.debug-section {
  margin-top: 3rem;
  background-color: #f9fafb;
  border-radius: 0.5rem;
  padding: 1rem;
}

.debug-summary {
  cursor: pointer;
  font-weight: 500;
  color: #374151;
  transition: color 0.2s ease;
}

.debug-summary:hover {
  color: #111827;
}

.debug-content {
  margin-top: 1rem;
  font-size: 0.75rem;
  color: #6b7280;
  overflow: auto;
  background-color: #ffffff;
  padding: 1rem;
  border-radius: 0.25rem;
  border: 1px solid #e5e7eb;
  white-space: pre-wrap;
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}

button:focus,
summary:focus {
  outline: 2px solid #2563eb;
  outline-offset: 2px;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>


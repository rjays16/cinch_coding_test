<template>
  <div class="product-detail-page">
    <div class="container">
      <!-- Loading State -->
      <div v-if="isLoading" class="loading-container">
        <div class="loading-spinner">
          <i class="fas fa-spinner fa-spin"></i>
        </div>
        <p>Loading product...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="errorMessage" class="error-container">
        <i class="fas fa-exclamation-circle"></i>
        <h2>Product Not Found</h2>
        <p>{{ errorMessage }}</p>
        <router-link to="/" class="btn-back-to-products">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </router-link>
      </div>

      <!-- Product Detail -->
      <div v-else-if="product" class="product-detail">
        <div class="product-images">
          <div class="main-image">
            <img :src="getProductImage(product)" :alt="product.name" />
          </div>
        </div>

        <div class="product-info">
          <div class="breadcrumb">
            <router-link to="/products">Products</router-link>
            <i class="fas fa-chevron-right"></i>
            <span>{{ product.category }}</span>
            <i class="fas fa-chevron-right"></i>
            <span>{{ product.name }}</span>
          </div>

          <h1 class="product-title">{{ product.name }}</h1>
          
          <div class="product-meta">
            <span class="category-badge">{{ product.category }}</span>
            <span v-if="product.brand" class="brand-badge">{{ product.brand }}</span>
          </div>

          <div class="product-pricing">
            <div class="prices">
              <span class="current-price">₱{{ formatPrice(product.price) }}</span>
              <span v-if="product.compare_price && product.compare_price > product.price" class="compare-price">
                ₱{{ formatPrice(product.compare_price) }}
              </span>
              <span v-if="product.compare_price && product.compare_price > product.price" class="discount-badge">
                Save {{ calculateDiscount(product) }}%
              </span>
            </div>
          </div>

          <div class="stock-info" :class="getStockClass(product.stock)">
            <i :class="getStockIcon(product.stock)"></i>
            <span>{{ getStockText(product.stock) }}</span>
          </div>

          <div class="product-description">
            <h3>Description</h3>
            <p>{{ product.description }}</p>
          </div>

          <div v-if="product.sizes && product.sizes.length > 0" class="product-sizes">
            <h3>Available Sizes</h3>
            <div class="sizes-grid">
              <span v-for="size in product.sizes" :key="size" class="size-badge">
                {{ size }}
              </span>
            </div>
          </div>

          <div class="product-details">
            <h3>Product Details</h3>
            <div class="details-grid">
              <div v-if="product.color" class="detail-item">
                <strong>Color:</strong>
                <span>{{ product.color }}</span>
              </div>
              <div v-if="product.weight" class="detail-item">
                <strong>Weight:</strong>
                <span>{{ product.weight }} kg</span>
              </div>
              <div v-if="product.material" class="detail-item">
                <strong>Material:</strong>
                <span>{{ product.material }}</span>
              </div>
              <div class="detail-item">
                <strong>SKU:</strong>
                <span>{{ product.sku }}</span>
              </div>
            </div>
          </div>

          <div class="action-buttons">
            <button class="btn-add-cart" :disabled="product.stock === 0">
              <i class="fas fa-shopping-cart"></i>
              {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
            </button>
            <router-link to="/" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Back to Home
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { productService } from '@/services/productService'

const route = useRoute()

// State
const product = ref(null)
const isLoading = ref(true)
const errorMessage = ref('')

// Methods
const loadProduct = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const productId = route.params.id
    const response = await productService.getProduct(productId)

    if (response.success) {
      product.value = response.data
    }

    isLoading.value = false
  } catch (error) {
    errorMessage.value = 'Product not found or unavailable'
    isLoading.value = false
  }
}

const getProductImage = (product) => {
  return product.image_url || product.image || 'https://via.placeholder.com/500'
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price)
}

const calculateDiscount = (product) => {
  if (product.compare_price && product.price) {
    const discount = ((product.compare_price - product.price) / product.compare_price) * 100
    return Math.round(discount)
  }
  return 0
}

const getStockClass = (stock) => {
  if (stock === 0) return 'stock-out'
  if (stock < 10) return 'stock-low'
  return 'stock-good'
}

const getStockIcon = (stock) => {
  if (stock === 0) return 'fas fa-times-circle'
  if (stock < 10) return 'fas fa-exclamation-triangle'
  return 'fas fa-check-circle'
}

const getStockText = (stock) => {
  if (stock === 0) return 'Out of Stock'
  if (stock < 10) return `Only ${stock} left in stock`
  return `In Stock (${stock} available)`
}

onMounted(() => {
  loadProduct()
})
</script>

<style scoped>
.product-detail-page {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 2rem 0 4rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Loading & Error States */
.loading-container,
.error-container {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
}

.loading-spinner {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #f8f9ff;
  color: #667eea;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  margin: 0 auto 1.5rem;
}

.error-container i {
  font-size: 3rem;
  color: #e74c3c;
  margin-bottom: 1rem;
}

.btn-back {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1.5rem;
  background: #667eea;
  color: white;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  margin-top: 1.5rem;
  transition: all 0.3s;
}

.btn-back:hover {
  background: #5568d3;
}

/* Product Detail */
.product-detail {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
}

/* Product Images */
.product-images {
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.main-image {
  width: 100%;
  aspect-ratio: 1;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid #e0e0e0;
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Product Info */
.product-info {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  color: #666;
}

.breadcrumb a {
  color: #667eea;
  text-decoration: none;
}

.breadcrumb i {
  font-size: 0.7rem;
}

.product-title {
  font-size: 2rem;
  margin: 0;
  color: #2c3e50;
  line-height: 1.3;
}

.product-meta {
  display: flex;
  gap: 0.75rem;
}

.category-badge,
.brand-badge {
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
}

.category-badge {
  background: #e3f2fd;
  color: #1976d2;
}

.brand-badge {
  background: #f3e5f5;
  color: #7b1fa2;
}

.product-pricing {
  padding: 1.5rem;
  background: #f8f9ff;
  border-radius: 12px;
}

.prices {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.current-price {
  font-size: 2.5rem;
  font-weight: 700;
  color: #667eea;
}

.compare-price {
  font-size: 1.5rem;
  color: #999;
  text-decoration: line-through;
}

.discount-badge {
  padding: 0.5rem 1rem;
  background: #e74c3c;
  color: white;
  border-radius: 8px;
  font-weight: 600;
}

.stock-info {
  padding: 1rem 1.5rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 600;
  font-size: 1.1rem;
}

.stock-info.stock-good {
  background: #d4edda;
  color: #155724;
}

.stock-info.stock-low {
  background: #fff3cd;
  color: #856404;
}

.stock-info.stock-out {
  background: #f8d7da;
  color: #721c24;
}

.product-description h3,
.product-sizes h3,
.product-details h3 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
  font-size: 1.25rem;
}

.product-description p {
  margin: 0;
  color: #666;
  line-height: 1.8;
  font-size: 1rem;
}

.sizes-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.size-badge {
  padding: 0.75rem 1.25rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
}

.details-grid {
  display: grid;
  gap: 1rem;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.detail-item strong {
  color: #2c3e50;
}

.detail-item span {
  color: #666;
}

.action-buttons {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.btn-add-cart,
.btn-back-to-products {
  flex: 1;
  padding: 1rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  border: none;
  text-decoration: none;
}

.btn-add-cart {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-add-cart:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-add-cart:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.btn-back-to-products {
  background: white;
  color: #667eea;
  border: 2px solid #667eea;
}

.btn-back-to-products:hover {
  background: #667eea;
  color: white;
}

/* Responsive */
@media (max-width: 968px) {
  .product-detail {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .product-images {
    position: static;
  }

  .product-title {
    font-size: 1.5rem;
  }

  .current-price {
    font-size: 2rem;
  }

  .action-buttons {
    flex-direction: column;
  }
}
</style>
<template>
  <transition name="modal-fade">
    <div v-if="show && product" class="modal-overlay" @click="handleClose">
      <div class="modal-content product-detail-modal" @click.stop>
        <!-- Modal Header -->
        <div class="modal-header">
          <h2>Product Details</h2>
          <button class="close-button" @click="handleClose">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
          <!-- Product Image -->
          <div class="product-image-section">
            <div class="main-image">
              <img :src="productImage" :alt="product.name" />
            </div>
            <div class="image-badge" :class="productIsActive ? 'badge-active' : 'badge-inactive'">
              {{ productIsActive ? 'Active' : 'Inactive' }}
            </div>
          </div>

          <!-- Product Info -->
          <div class="product-info-section">
            <!-- Basic Info -->
            <div class="info-group">
              <h3 class="product-name">{{ product.name }}</h3>
              <p class="product-sku">SKU: {{ product.sku }}</p>
            </div>

            <!-- Category & Brand -->
            <div class="info-row">
              <div class="info-item">
                <label><i class="fas fa-tag"></i> Category</label>
                <span class="category-badge">{{ product.category }}</span>
              </div>
              <div class="info-item" v-if="product.brand">
                <label><i class="fas fa-certificate"></i> Brand</label>
                <span>{{ product.brand }}</span>
              </div>
            </div>

            <!-- Pricing -->
            <div class="info-group pricing-section">
              <label><i class="fas fa-money-bill-wave"></i> Pricing</label>
              <div class="price-details">
                <div class="price-item">
                  <span class="price-label">Current Price</span>
                  <span class="current-price">₱{{ formatPrice(product.price) }}</span>
                </div>
                <div class="price-item" v-if="productComparePrice">
                  <span class="price-label">Compare Price</span>
                  <span class="compare-price">₱{{ formatPrice(productComparePrice) }}</span>
                </div>
                <div class="discount-info" v-if="calculateDiscount > 0">
                  <i class="fas fa-percent"></i>
                  Save {{ calculateDiscount }}% off!
                </div>
              </div>
            </div>

            <!-- Stock Info -->
            <div class="info-group">
              <label><i class="fas fa-boxes"></i> Inventory</label>
              <div class="stock-info">
                <div class="stock-item">
                  <span class="stock-label">Stock Quantity</span>
                  <span class="stock-badge" :class="getStockClass(product.stock)">
                    {{ product.stock }} units
                  </span>
                </div>
                <div class="stock-status-bar">
                  <div class="status-fill" :class="getStockClass(product.stock)" :style="{ width: getStockPercentage + '%' }"></div>
                </div>
              </div>
            </div>

            <!-- Sizes (if available) -->
            <div class="info-group" v-if="product.sizes && product.sizes.length > 0">
              <label><i class="fas fa-ruler"></i> Available Sizes</label>
              <div class="sizes-list">
                <span v-for="size in product.sizes" :key="size" class="size-badge">
                  {{ size }}
                </span>
              </div>
            </div>

            <!-- Additional Details -->
            <div class="info-row" v-if="product.color || product.weight">
              <div class="info-item" v-if="product.color">
                <label><i class="fas fa-palette"></i> Color</label>
                <span>{{ product.color }}</span>
              </div>
              <div class="info-item" v-if="product.weight">
                <label><i class="fas fa-weight"></i> Weight</label>
                <span>{{ product.weight }} kg</span>
              </div>
            </div>

            <div class="info-group" v-if="product.material">
              <label><i class="fas fa-leaf"></i> Material</label>
              <span>{{ product.material }}</span>
            </div>

            <!-- Description -->
            <div class="info-group" v-if="product.description">
              <label><i class="fas fa-align-left"></i> Description</label>
              <p class="product-description">{{ product.description }}</p>
            </div>

            <!-- Timestamps -->
            <div class="info-group timestamps" v-if="productCreatedAt || productUpdatedAt">
              <div class="timestamp-item" v-if="productCreatedAt">
                <i class="fas fa-calendar-plus"></i>
                <span>Created: {{ formatDate(productCreatedAt) }}</span>
              </div>
              <div class="timestamp-item" v-if="productUpdatedAt">
                <i class="fas fa-calendar-check"></i>
                <span>Updated: {{ formatDate(productUpdatedAt) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
          <button class="btn-secondary" @click="handleClose">
            <i class="fas fa-times"></i>
            Close
          </button>
          <button class="btn-primary" @click="handleEdit">
            <i class="fas fa-edit"></i>
            Edit Product
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { computed, defineProps, defineEmits } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  product: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'edit'])

// Computed properties to handle both API format and component format
const productIsActive = computed(() => {
  if (!props.product) return false
  // Support both is_active (API) and isActive (component)
  return props.product.is_active !== undefined 
    ? props.product.is_active 
    : props.product.isActive
})

const productComparePrice = computed(() => {
  if (!props.product) return null
  // Support both compare_price (API) and comparePrice (component)
  return props.product.compare_price !== undefined 
    ? props.product.compare_price 
    : props.product.comparePrice
})

const productCreatedAt = computed(() => {
  if (!props.product) return null
  // Support both created_at (API) and createdAt (component)
  return props.product.created_at !== undefined 
    ? props.product.created_at 
    : props.product.createdAt
})

const productUpdatedAt = computed(() => {
  if (!props.product) return null
  // Support both updated_at (API) and updatedAt (component)
  return props.product.updated_at !== undefined 
    ? props.product.updated_at 
    : props.product.updatedAt
})

const productImage = computed(() => {
  if (!props.product) return 'https://via.placeholder.com/300'
  // Support image_url (API), image (both), or placeholder
  return props.product.image_url || props.product.image || 'https://via.placeholder.com/300'
})

const calculateDiscount = computed(() => {
  if (productComparePrice.value && props.product?.price) {
    const discount = ((productComparePrice.value - props.product.price) / productComparePrice.value) * 100
    return Math.round(discount)
  }
  return 0
})

const getStockPercentage = computed(() => {
  if (!props.product?.stock) return 0
  const maxStock = 100
  return Math.min((props.product.stock / maxStock) * 100, 100)
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price)
}

const getStockClass = (stock) => {
  if (stock === 0) return 'stock-out'
  if (stock < 10) return 'stock-low'
  return 'stock-good'
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const handleClose = () => {
  emit('close')
}

const handleEdit = () => {
  emit('edit', props.product.id)
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 16px;
  max-width: 900px;
  width: 100%;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    transform: translateY(50px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.modal-header {
  padding: 1.5rem 2rem;
  border-bottom: 2px solid #f0f0f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.5rem;
}

.close-button {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background: #f5f5f5;
  color: #666;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
  font-size: 1.25rem;
}

.close-button:hover {
  background: #e74c3c;
  color: white;
}

.modal-body {
  padding: 2rem;
  overflow-y: auto;
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 2rem;
}

.product-image-section {
  position: relative;
}

.main-image {
  width: 100%;
  aspect-ratio: 1;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid #e0e0e0;
  background: #f8f9fa;
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.85rem;
}

.badge-active {
  background: #d4edda;
  color: #155724;
}

.badge-inactive {
  background: #f8d7da;
  color: #721c24;
}

.product-info-section {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.info-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.info-group label {
  font-weight: 600;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.info-group label i {
  color: #667eea;
}

.product-name {
  font-size: 1.5rem;
  margin: 0;
  color: #2c3e50;
}

.product-sku {
  margin: 0;
  color: #999;
  font-size: 0.9rem;
}

.info-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.info-item label {
  font-weight: 600;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
}

.info-item label i {
  color: #667eea;
}

.category-badge {
  display: inline-block;
  padding: 0.5rem 1rem;
  background: #e3f2fd;
  color: #1976d2;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
  width: fit-content;
}

.pricing-section {
  background: #f8f9ff;
  padding: 1.25rem;
  border-radius: 12px;
  border: 2px solid #e7ecff;
}

.price-details {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.price-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.price-label {
  color: #666;
  font-size: 0.9rem;
}

.current-price {
  font-size: 1.75rem;
  font-weight: 700;
  color: #667eea;
}

.compare-price {
  font-size: 1.25rem;
  color: #999;
  text-decoration: line-through;
}

.discount-info {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
}

.stock-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.stock-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.stock-label {
  color: #666;
  font-size: 0.9rem;
}

.stock-badge {
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
}

.stock-badge.stock-good {
  background: #d4edda;
  color: #155724;
}

.stock-badge.stock-low {
  background: #fff3cd;
  color: #856404;
}

.stock-badge.stock-out {
  background: #f8d7da;
  color: #721c24;
}

.stock-status-bar {
  width: 100%;
  height: 8px;
  background: #e0e0e0;
  border-radius: 4px;
  overflow: hidden;
}

.status-fill {
  height: 100%;
  transition: width 0.3s;
  border-radius: 4px;
}

.status-fill.stock-good {
  background: #28a745;
}

.status-fill.stock-low {
  background: #ff9800;
}

.status-fill.stock-out {
  background: #dc3545;
}

.sizes-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.size-badge {
  padding: 0.5rem 1rem;
  background: #f0f0f0;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
}

.product-description {
  margin: 0;
  color: #666;
  line-height: 1.6;
}

.timestamps {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
  display: flex;
  gap: 1.5rem;
}

.timestamp-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #666;
  font-size: 0.85rem;
}

.timestamp-item i {
  color: #999;
}

.modal-footer {
  padding: 1.5rem 2rem;
  border-top: 2px solid #f0f0f0;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.btn-primary,
.btn-secondary {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  border: none;
  font-size: 0.95rem;
}

.btn-secondary {
  background: white;
  color: #666;
  border: 2px solid #e0e0e0;
}

.btn-secondary:hover {
  border-color: #667eea;
  color: #667eea;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

@media (max-width: 968px) {
  .modal-body {
    grid-template-columns: 1fr;
    padding: 1.5rem;
  }

  .product-image-section {
    max-width: 300px;
    margin: 0 auto;
  }

  .info-row {
    grid-template-columns: 1fr;
  }

  .timestamps {
    flex-direction: column;
    gap: 0.75rem;
  }

  .modal-footer {
    flex-direction: column;
  }

  .btn-primary,
  .btn-secondary {
    width: 100%;
    justify-content: center;
  }
}
</style>
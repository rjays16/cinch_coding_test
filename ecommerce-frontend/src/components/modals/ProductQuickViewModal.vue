<template>
  <transition name="modal-fade">
    <div v-if="show && product" class="modal-overlay" @click="handleClose">
      <div class="modal-content" @click.stop>
        <!-- Close Button -->
        <button class="close-button" @click="handleClose">
          <i class="fas fa-times"></i>
        </button>

        <div class="modal-body">
          <!-- Product Image -->
          <div class="product-image-section">
            <img :src="getProductImage(product)" :alt="product.name" />
          </div>

          <!-- Product Info -->
          <div class="product-info-section">
            <span class="product-category">{{ product.category }}</span>
            <h2 class="product-title">{{ product.name }}</h2>
            
            <div v-if="product.seller" class="seller-info">
              <i class="fas fa-store"></i>
              <span>{{ product.seller.store_name }}</span>
            </div>

            <div class="product-pricing">
              <span class="current-price">₱{{ formatPrice(product.price) }}</span>
              <span v-if="product.compare_price && product.compare_price > product.price" class="compare-price">
                ₱{{ formatPrice(product.compare_price) }}
              </span>
              <span v-if="product.compare_price && product.compare_price > product.price" class="discount-badge">
                -{{ calculateDiscount(product) }}%
              </span>
            </div>

            <div class="stock-status" :class="getStockClass(product.stock)">
              <i :class="getStockIcon(product.stock)"></i>
              <span>{{ getStockText(product.stock) }}</span>
            </div>

            <div class="product-description">
              <p>{{ product.description }}</p>
            </div>

            <div v-if="product.sizes && product.sizes.length > 0" class="product-sizes">
              <strong>Available Sizes:</strong>
              <div class="sizes-list">
                <span v-for="size in product.sizes" :key="size" class="size-badge">
                  {{ size }}
                </span>
              </div>
            </div>

            <div class="action-buttons">
              <button 
                class="btn-add-cart" 
                :disabled="product.stock === 0"
                @click="handleAddToCart"
              >
                <i class="fas fa-shopping-cart"></i>
                {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
              </button>
              <button class="btn-close" @click="handleClose">
                <i class="fas fa-times"></i>
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'ProductQuickViewModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    product: {
      type: Object,
      default: null
    }
  },
  methods: {
    handleClose() {
      this.$emit('close')
    },

    handleViewFull() {
      this.$emit('view-full', this.product.id)
    },

    handleAddToCart() {
      // For now, just show alert (you can integrate with cart later)
      alert(`"${this.product.name}" will be added to cart!\n(Cart functionality coming soon)`)
    },

    getProductImage(product) {
      return product?.image_url || product?.image || 'https://via.placeholder.com/400'
    },

    formatPrice(price) {
      return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(price)
    },

    calculateDiscount(product) {
      if (product.compare_price && product.price) {
        const discount = ((product.compare_price - product.price) / product.compare_price) * 100
        return Math.round(discount)
      }
      return 0
    },

    getStockClass(stock) {
      if (stock === 0) return 'stock-out'
      if (stock < 10) return 'stock-low'
      return 'stock-good'
    },

    getStockIcon(stock) {
      if (stock === 0) return 'fas fa-times-circle'
      if (stock < 10) return 'fas fa-exclamation-triangle'
      return 'fas fa-check-circle'
    },

    getStockText(stock) {
      if (stock === 0) return 'Out of Stock'
      if (stock < 10) return `Only ${stock} left`
      return 'In Stock'
    }
  }
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
  overflow-y: auto;
  position: relative;
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

.close-button {
  position: absolute;
  top: 1rem;
  right: 1rem;
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
  font-size: 1.25rem;
  transition: all 0.3s;
  z-index: 10;
}

.close-button:hover {
  background: #e74c3c;
  color: white;
}

.modal-body {
  padding: 2rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.product-image-section {
  width: 100%;
  aspect-ratio: 1;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid #e0e0e0;
}

.product-image-section img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.product-category {
  display: inline-block;
  padding: 0.5rem 1rem;
  background: #e3f2fd;
  color: #1976d2;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
  width: fit-content;
}

.product-title {
  margin: 0;
  font-size: 1.75rem;
  color: #2c3e50;
  line-height: 1.3;
}

.seller-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  color: #666;
  font-weight: 500;
}

.seller-info i {
  color: #667eea;
}

.product-pricing {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f8f9ff;
  border-radius: 12px;
  flex-wrap: wrap;
}

.current-price {
  font-size: 2rem;
  font-weight: 700;
  color: #667eea;
}

.compare-price {
  font-size: 1.25rem;
  color: #999;
  text-decoration: line-through;
}

.discount-badge {
  padding: 0.5rem 0.75rem;
  background: #e74c3c;
  color: white;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.9rem;
}

.stock-status {
  padding: 0.75rem 1rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
}

.stock-status.stock-good {
  background: #d4edda;
  color: #155724;
}

.stock-status.stock-low {
  background: #fff3cd;
  color: #856404;
}

.stock-status.stock-out {
  background: #f8d7da;
  color: #721c24;
}

.product-description p {
  margin: 0;
  color: #666;
  line-height: 1.6;
}

.product-sizes {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.product-sizes strong {
  color: #2c3e50;
}

.sizes-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.size-badge {
  padding: 0.5rem 0.75rem;
  border: 2px solid #e0e0e0;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.9rem;
}

.action-buttons {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.btn-add-cart,
.btn-close {
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

.btn-close {
  background: white;
  color: #666;
  border: 2px solid #e0e0e0;
}

.btn-close:hover {
  border-color: #667eea;
  color: #667eea;
}

.view-full-link {
  text-align: center;
  padding-top: 0.5rem;
}

.link-view-full {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.3s;
}

.link-view-full:hover {
  color: #5568d3;
  text-decoration: underline;
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

  .product-title {
    font-size: 1.5rem;
  }

  .current-price {
    font-size: 1.75rem;
  }

  .action-buttons {
    flex-direction: column;
  }

  .btn-add-cart,
  .btn-close {
    width: 100%;
  }
}
</style>
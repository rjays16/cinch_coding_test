<template>
  <div class="product-card">
    <div class="product-image">
      <img :src="product.image_url" :alt="product.name" />
      
      <!-- Low Stock Badge -->
      <div v-if="product.stock < 10 && product.stock > 0" class="low-stock-badge">
        Only {{ product.stock }} left!
      </div>
      
      <!-- Out of Stock Badge -->
      <div v-if="product.stock === 0" class="out-of-stock-badge">
        Out of Stock
      </div>
    </div>

    <div class="product-info">
      <h3 class="product-name">{{ product.name }}</h3>
      <p class="product-category">{{ product.category }}</p>
      <p class="product-description">{{ product.description }}</p>
      
      <div class="product-footer">
        <div class="product-price">₱{{ formatPrice(product.price) }}</div>
        
        <!-- Stock Info -->
        <div class="product-stock">
          <i class="fas fa-box"></i>
          <span :class="{ 'low-stock': product.stock < 10 }">
            {{ product.stock }} in stock
          </span>
        </div>
      </div>

      <button 
        @click="handleAddToCart" 
        class="btn-add-to-cart"
        :disabled="product.stock === 0 || isAdding"
      >
        <span v-if="!isAdding">
          <i class="fas fa-shopping-cart"></i>
          {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
        </span>
        <span v-else>
          <i class="fas fa-spinner fa-spin"></i>
          Adding...
        </span>
      </button>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useCart } from '@/composables/useCart'

export default {
  name: 'ProductCard',
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  setup(props) {
    const { addToCart } = useCart()
    const isAdding = ref(false)

    const handleAddToCart = async () => {
      if (props.product.stock === 0) {
        alert('This product is out of stock')
        return
      }

      isAdding.value = true
      try {
        await addToCart(props.product.id)
      } catch (error) {
        console.error('Error adding to cart:', error)
      } finally {
        isAdding.value = false
      }
    }

    const formatPrice = (price) => {
      return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(price)
    }

    return {
      isAdding,
      handleAddToCart,
      formatPrice
    }
  }
}
</script>

<style scoped>
.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.product-image {
  position: relative;
  width: 100%;
  height: 250px;
  overflow: hidden;
  background: #f5f5f5;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Stock Badges */
.low-stock-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: #ff9800;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3);
}

.out-of-stock-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: #e74c3c;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
}

.product-info {
  padding: 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.product-name {
  margin: 0 0 0.5rem 0;
  font-size: 1.25rem;
  color: #2c3e50;
}

.product-category {
  margin: 0 0 0.5rem 0;
  color: #667eea;
  font-size: 0.9rem;
  font-weight: 600;
  text-transform: uppercase;
}

.product-description {
  margin: 0 0 1rem 0;
  color: #666;
  font-size: 0.95rem;
  line-height: 1.5;
  flex: 1;
}

.product-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #f0f0f0;
}

.product-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #667eea;
}

.product-stock {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  color: #666;
}

.product-stock i {
  color: #4caf50;
}

.product-stock .low-stock {
  color: #ff9800;
  font-weight: 600;
}

.btn-add-to-cart {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-add-to-cart:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-add-to-cart:disabled {
  background: #ccc;
  cursor: not-allowed;
  transform: none;
}
</style>
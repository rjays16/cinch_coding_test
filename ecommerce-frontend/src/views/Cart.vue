<template>
  <div class="cart-page">
    <div class="container">
      <h1 class="page-title">Shopping Cart</h1>

      <!-- Loading State -->
      <div v-if="isLoading" class="loading-state">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Loading your cart...</p>
      </div>

      <!-- Empty Cart -->
      <div v-else-if="cartItems.length === 0" class="empty-cart">
        <div class="empty-cart-icon">
          <i class="fas fa-shopping-cart"></i>
        </div>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added any items to your cart yet.</p>
        <router-link to="/" class="btn-shop-now">
          <i class="fas fa-shopping-bag"></i>
          Start Shopping
        </router-link>
      </div>

      <!-- Cart Content -->
      <div v-else class="cart-content">
        <!-- Cart Items -->
        <div class="cart-items">
          <div 
            v-for="item in cartItems" 
            :key="item.id"
            class="cart-item"
          >
            <div class="item-image">
              <img :src="item.product.image_url" :alt="item.product.name" />
            </div>

            <div class="item-details">
              <h3>{{ item.product.name }}</h3>
              <p class="item-category">{{ item.product.category }}</p>
              <p class="item-price">₱{{ formatPrice(item.product.price) }}</p>
              
              <!-- Stock Warnings -->
              <p v-if="item.quantity > item.product.stock" class="stock-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Only {{ item.product.stock }} units available!
              </p>
              <p v-else-if="item.product.stock < 10 && item.product.stock > 0" class="low-stock-warning">
                <i class="fas fa-info-circle"></i>
                Only {{ item.product.stock }} left in stock
              </p>
              <p v-else-if="item.product.stock === 0" class="out-of-stock-warning">
                <i class="fas fa-times-circle"></i>
                Out of stock - Please remove this item
              </p>
            </div>

            <div class="item-actions">
              <div class="quantity-controls">
                <button 
                  @click="updateQuantity(item.id, item.quantity - 1)"
                  :disabled="item.quantity <= 1"
                  class="btn-quantity"
                >
                  <i class="fas fa-minus"></i>
                </button>
                <input 
                  type="number" 
                  :value="item.quantity"
                  @change="handleQuantityInput($event, item.id, item.product.stock)"
                  min="1"
                  :max="item.product.stock"
                  class="quantity-input"
                />
                <button 
                  @click="updateQuantity(item.id, item.quantity + 1)"
                  :disabled="item.quantity >= item.product.stock"
                  class="btn-quantity"
                >
                  <i class="fas fa-plus"></i>
                </button>
              </div>

              <div class="item-subtotal">
                <span class="subtotal-label">Subtotal:</span>
                <span class="subtotal-price">₱{{ formatPrice(item.subtotal) }}</span>
              </div>

              <button 
                @click="handleRemoveItem(item.id)" 
                class="btn-remove"
                title="Remove item"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Cart Summary -->
        <div class="cart-summary">
          <h2>Order Summary</h2>

          <div class="summary-row">
            <span>Subtotal ({{ cartCount }} items)</span>
            <span>₱{{ formatPrice(cartTotal) }}</span>
          </div>

          <div class="summary-row">
            <span>Shipping Fee</span>
            <span class="free-text">FREE</span>
          </div>

          <div class="summary-divider"></div>

          <div class="summary-row summary-total">
            <span>Total</span>
            <span>₱{{ formatPrice(cartTotal) }}</span>
          </div>

          <router-link 
            to="/checkout" 
            class="btn-checkout"
            :class="{ 'disabled': hasStockIssues }"
            @click.prevent="handleCheckout"
          >
            <i class="fas fa-lock"></i>
            Proceed to Checkout
          </router-link>

          <!-- Stock Issue Warning -->
          <div v-if="hasStockIssues" class="checkout-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <p>Please update quantities or remove out-of-stock items before checkout</p>
          </div>

          <router-link to="/" class="btn-continue-shopping">
            <i class="fas fa-arrow-left"></i>
            Continue Shopping
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCart } from '@/composables/useCart'

export default {
  name: 'Cart',
  setup() {
    const router = useRouter()
    const { 
      cartItems, 
      cartCount, 
      cartTotal, 
      isLoading, 
      loadCart, 
      updateCartItem, 
      removeFromCart 
    } = useCart()

    // Check if any items have stock issues
    const hasStockIssues = computed(() => {
      return cartItems.value.some(item => 
        item.quantity > item.product.stock || item.product.stock === 0
      )
    })

    onMounted(async () => {
      await loadCart()
    })

    const updateQuantity = async (itemId, newQuantity) => {
      if (newQuantity < 1) return

      const item = cartItems.value.find(i => i.id === itemId)
      
      // Check if quantity exceeds available stock
      if (newQuantity > item.product.stock) {
        alert(`Sorry, only ${item.product.stock} units available in stock.`)
        return
      }

      try {
        await updateCartItem(itemId, newQuantity)
      } catch (error) {
        console.error('Error updating quantity:', error)
        alert('Failed to update quantity. Please try again.')
      }
    }

    const handleQuantityInput = async (event, itemId, maxStock) => {
      let newQuantity = parseInt(event.target.value)
      
      if (isNaN(newQuantity) || newQuantity < 1) {
        newQuantity = 1
      }
      
      if (newQuantity > maxStock) {
        alert(`Sorry, only ${maxStock} units available in stock.`)
        newQuantity = maxStock
      }
      
      event.target.value = newQuantity
      await updateQuantity(itemId, newQuantity)
    }

    const handleRemoveItem = async (itemId) => {
      if (confirm('Are you sure you want to remove this item from your cart?')) {
        try {
          await removeFromCart(itemId)
        } catch (error) {
          console.error('Error removing item:', error)
          alert('Failed to remove item. Please try again.')
        }
      }
    }

    const handleCheckout = () => {
      if (hasStockIssues.value) {
        alert('Please update quantities or remove out-of-stock items before proceeding to checkout.')
        return
      }
      router.push('/checkout')
    }

    const formatPrice = (price) => {
      return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(price)
    }

    return {
      cartItems,
      cartCount,
      cartTotal,
      isLoading,
      hasStockIssues,
      updateQuantity,
      handleQuantityInput,
      handleRemoveItem,
      handleCheckout,
      formatPrice
    }
  }
}
</script>

<style scoped>
.cart-page {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 2rem 0 4rem 0;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

.page-title {
  font-size: 2rem;
  color: #2c3e50;
  margin-bottom: 2rem;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 4rem 0;
}

.loading-state i {
  font-size: 3rem;
  color: #667eea;
  margin-bottom: 1rem;
}

.loading-state p {
  color: #666;
  font-size: 1.1rem;
}

/* Empty Cart */
.empty-cart {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.empty-cart-icon {
  width: 120px;
  height: 120px;
  margin: 0 auto 2rem;
  background: #f8f9ff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-cart-icon i {
  font-size: 4rem;
  color: #667eea;
}

.empty-cart h2 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
  font-size: 1.75rem;
}

.empty-cart p {
  margin: 0 0 2rem 0;
  color: #666;
  font-size: 1.1rem;
}

.btn-shop-now {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-shop-now:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

/* Cart Content */
.cart-content {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
}

/* Cart Items */
.cart-items {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.cart-item {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: grid;
  grid-template-columns: 120px 1fr auto;
  gap: 1.5rem;
  align-items: start;
}

.item-image {
  width: 120px;
  height: 120px;
  border-radius: 8px;
  overflow: hidden;
  background: #f5f5f5;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  flex: 1;
}

.item-details h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.25rem;
  color: #2c3e50;
}

.item-category {
  margin: 0 0 0.5rem 0;
  color: #667eea;
  font-size: 0.9rem;
  font-weight: 600;
  text-transform: uppercase;
}

.item-price {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  color: #667eea;
}

/* Stock Warnings */
.stock-warning {
  color: #e74c3c;
  font-size: 0.85rem;
  margin: 0.5rem 0 0 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
}

.low-stock-warning {
  color: #ff9800;
  font-size: 0.85rem;
  margin: 0.5rem 0 0 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
}

.out-of-stock-warning {
  color: #e74c3c;
  font-size: 0.85rem;
  margin: 0.5rem 0 0 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  background: #ffebee;
  padding: 0.5rem;
  border-radius: 4px;
}

/* Item Actions */
.item-actions {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 1rem;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-quantity {
  width: 36px;
  height: 36px;
  border: 2px solid #e0e0e0;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
}

.btn-quantity:hover:not(:disabled) {
  border-color: #667eea;
  color: #667eea;
  background: #f8f9ff;
}

.btn-quantity:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.quantity-input {
  width: 60px;
  height: 36px;
  text-align: center;
  border: 2px solid #e0e0e0;
  border-radius: 6px;
  font-weight: 600;
  font-size: 1rem;
}

.quantity-input:focus {
  outline: none;
  border-color: #667eea;
}

.item-subtotal {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.25rem;
}

.subtotal-label {
  font-size: 0.85rem;
  color: #666;
}

.subtotal-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: #2c3e50;
}

.btn-remove {
  padding: 0.5rem 1rem;
  background: #ffebee;
  color: #e74c3c;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 0.95rem;
}

.btn-remove:hover {
  background: #e74c3c;
  color: white;
}

/* Cart Summary */
.cart-summary {
  position: sticky;
  top: 2rem;
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  height: fit-content;
}

.cart-summary h2 {
  margin: 0 0 1.5rem 0;
  font-size: 1.5rem;
  color: #2c3e50;
  padding-bottom: 1rem;
  border-bottom: 2px solid #f0f0f0;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  color: #666;
  font-size: 1rem;
}

.free-text {
  color: #4caf50;
  font-weight: 600;
}

.summary-divider {
  height: 1px;
  background: #e0e0e0;
  margin: 1.5rem 0;
}

.summary-total {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
}

.btn-checkout {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  width: 100%;
  padding: 1.25rem;
  margin-top: 1.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-checkout:hover:not(.disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-checkout.disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

.checkout-warning {
  margin-top: 1rem;
  padding: 1rem;
  background: #fff3cd;
  border-left: 4px solid #ff9800;
  border-radius: 6px;
  display: flex;
  gap: 0.75rem;
  align-items: start;
}

.checkout-warning i {
  color: #ff9800;
  margin-top: 0.125rem;
}

.checkout-warning p {
  margin: 0;
  color: #856404;
  font-size: 0.9rem;
}

.btn-continue-shopping {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  width: 100%;
  padding: 1rem;
  margin-top: 1rem;
  background: white;
  color: #667eea;
  text-decoration: none;
  border: 2px solid #667eea;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-continue-shopping:hover {
  background: #f8f9ff;
}

/* Responsive */
@media (max-width: 1024px) {
  .cart-content {
    grid-template-columns: 1fr;
  }

  .cart-summary {
    position: static;
  }
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }

  .page-title {
    font-size: 1.5rem;
  }

  .cart-item {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .item-image {
    width: 100%;
    height: 200px;
  }

  .item-actions {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
}
</style>
<template>
  <div class="cart-page">
    <div class="container">
      <h1 class="page-title">Shopping Cart</h1>

      <!-- Loading State -->
      <div v-if="isLoading" class="loading-state">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Loading cart...</p>
      </div>

      <!-- Empty Cart -->
      <div v-else-if="cartItems.length === 0" class="empty-cart">
        <i class="fas fa-shopping-cart empty-icon"></i>
        <h2>Your cart is empty</h2>
        <p>Add some products to get started!</p>
        <router-link to="/" class="btn-primary">
          Browse Products
        </router-link>
      </div>

      <!-- Cart Items -->
      <div v-else class="cart-content">
        <div class="cart-items">
          <div
            v-for="item in cartItems"
            :key="item.id"
            class="cart-item"
          >
            <img
              :src="item.product.image_url || '/placeholder.png'"
              :alt="item.product.name"
              class="item-image"
            />
            
            <div class="item-details">
              <h3>{{ item.product.name }}</h3>
              <p class="item-meta">
                <span>Seller: {{ item.product.seller }}</span>
                <span>Category: {{ item.product.category }}</span>
              </p>
              <p class="item-price">₱{{ item.product.price }}</p>
            </div>

            <div class="item-quantity">
              <button
                @click="decreaseQuantity(item)"
                class="qty-btn"
                :disabled="isLoading"
              >
                <i class="fas fa-minus"></i>
              </button>
              <span class="qty-value">{{ item.quantity }}</span>
              <button
                @click="increaseQuantity(item)"
                class="qty-btn"
                :disabled="isLoading || item.quantity >= item.product.stock"
              >
                <i class="fas fa-plus"></i>
              </button>
            </div>

            <div class="item-subtotal">
              <p class="subtotal-label">Subtotal</p>
              <p class="subtotal-amount">₱{{ item.subtotal.toFixed(2) }}</p>
            </div>

            <button
              @click="showRemoveModal(item)"
              class="btn-remove"
              :disabled="isLoading"
            >
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>

        <!-- Cart Summary -->
        <div class="cart-summary">
          <h2>Order Summary</h2>
          
          <div class="summary-row">
            <span>Items ({{ cartCount }})</span>
            <span>₱{{ cartTotal.toFixed(2) }}</span>
          </div>

          <div class="summary-row">
            <span>Shipping</span>
            <span>FREE</span>
          </div>

          <div class="summary-divider"></div>

          <div class="summary-row summary-total">
            <span>Total</span>
            <span>₱{{ cartTotal.toFixed(2) }}</span>
          </div>

          <router-link to="/checkout" class="btn-checkout-link">
            <button class="btn-checkout" :disabled="isLoading">
              <i class="fas fa-lock"></i>
              Proceed to Checkout
            </button>
          </router-link>

          <router-link to="/" class="btn-continue">
            Continue Shopping
          </router-link>
        </div>
      </div>
    </div>

    <!-- Remove Item Confirmation Modal -->
    <RemoveCartItemModal
      :show="showRemoveConfirm"
      :itemName="itemToRemove?.product?.name"
      :isLoading="isRemoving"
      @confirm="confirmRemove"
      @cancel="cancelRemove"
    />
  </div>
</template>

<script>
import { useCart } from '@/composables/useCart'
import { onMounted, ref } from 'vue'
import RemoveCartItemModal from '@/components/modals/RemoveCartItemModal.vue'

export default {
  name: 'Cart',
  components: {
    RemoveCartItemModal
  },
  setup() {
    const {
      cartItems,
      cartCount,
      cartTotal,
      isLoading,
      loadCart,
      updateQuantity,
      removeFromCart
    } = useCart()

    const showRemoveConfirm = ref(false)
    const itemToRemove = ref(null)
    const isRemoving = ref(false)

    onMounted(() => {
      loadCart()
    })

    const increaseQuantity = async (item) => {
      try {
        await updateQuantity(item.id, item.quantity + 1)
      } catch (error) {
        alert(error.response?.data?.message || 'Failed to update quantity')
      }
    }

    const decreaseQuantity = async (item) => {
      try {
        await updateQuantity(item.id, item.quantity - 1)
      } catch (error) {
        alert(error.response?.data?.message || 'Failed to update quantity')
      }
    }

    const showRemoveModal = (item) => {
      itemToRemove.value = item
      showRemoveConfirm.value = true
    }

    const cancelRemove = () => {
      showRemoveConfirm.value = false
      itemToRemove.value = null
    }

    const confirmRemove = async () => {
      if (!itemToRemove.value) return

      try {
        isRemoving.value = true
        await removeFromCart(itemToRemove.value.id)
        
        // Success - close modal
        showRemoveConfirm.value = false
        itemToRemove.value = null
      } catch (error) {
        alert('Failed to remove item')
      } finally {
        isRemoving.value = false
      }
    }

    return {
      cartItems,
      cartCount,
      cartTotal,
      isLoading,
      increaseQuantity,
      decreaseQuantity,
      showRemoveModal,
      showRemoveConfirm,
      itemToRemove,
      isRemoving,
      confirmRemove,
      cancelRemove
    }
  }
}
</script>

<style scoped>
.cart-page {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
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

/* Empty Cart */
.empty-cart {
  text-align: center;
  padding: 4rem 0;
}

.empty-icon {
  font-size: 5rem;
  color: #ddd;
  margin-bottom: 1rem;
}

.empty-cart h2 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.empty-cart p {
  color: #666;
  margin-bottom: 2rem;
}

/* Cart Content */
.cart-content {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 2rem;
}

/* Cart Items */
.cart-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.cart-item {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  display: grid;
  grid-template-columns: 100px 1fr auto auto auto;
  gap: 1.5rem;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.item-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 8px;
}

.item-details h3 {
  font-size: 1.1rem;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.item-meta {
  font-size: 0.875rem;
  color: #666;
  margin-bottom: 0.5rem;
}

.item-meta span {
  margin-right: 1rem;
}

.item-price {
  font-size: 1.25rem;
  font-weight: 600;
  color: #667eea;
}

/* Quantity Controls */
.item-quantity {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.qty-btn {
  width: 32px;
  height: 32px;
  border: 1px solid #ddd;
  background: white;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s;
}

.qty-btn:hover:not(:disabled) {
  background: #f0f0f0;
}

.qty-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.qty-value {
  min-width: 40px;
  text-align: center;
  font-weight: 600;
}

/* Subtotal */
.item-subtotal {
  text-align: right;
}

.subtotal-label {
  font-size: 0.875rem;
  color: #666;
  margin-bottom: 0.25rem;
}

.subtotal-amount {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
}

/* Remove Button */
.btn-remove {
  width: 40px;
  height: 40px;
  border: none;
  background: #fee;
  color: #e74c3c;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-remove:hover:not(:disabled) {
  background: #e74c3c;
  color: white;
}

/* Cart Summary */
.cart-summary {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  height: fit-content;
  position: sticky;
  top: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.cart-summary h2 {
  font-size: 1.5rem;
  color: #2c3e50;
  margin-bottom: 1.5rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  color: #666;
}

.summary-divider {
  height: 1px;
  background: #eee;
  margin: 1.5rem 0;
}

.summary-total {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
}

.btn-checkout {
  width: 100%;
  padding: 1rem;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  margin-top: 1.5rem;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-checkout:hover:not(:disabled) {
  background: #5568d3;
  transform: translateY(-2px);
}

.btn-checkout:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-continue {
  display: block;
  text-align: center;
  color: #667eea;
  margin-top: 1rem;
  text-decoration: none;
  font-weight: 500;
}

.btn-continue:hover {
  text-decoration: underline;
}

.btn-primary {
  display: inline-block;
  padding: 1rem 2rem;
  background: #667eea;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-primary:hover {
  background: #5568d3;
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 968px) {
  .cart-content {
    grid-template-columns: 1fr;
  }

  .cart-summary {
    position: static;
  }

  .cart-item {
    grid-template-columns: 80px 1fr;
    gap: 1rem;
  }

  .item-quantity,
  .item-subtotal {
    grid-column: 2;
  }

  .btn-remove {
    grid-column: 2;
    justify-self: end;
  }
}

.btn-checkout-link {
  display: block;
  text-decoration: none;
  margin-top: 1.5rem;
}

.btn-checkout {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-checkout:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-checkout:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>

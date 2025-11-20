<template>
  <Layout>
    <div class="cart-page">
      <div class="container">
        <h1 class="page-title">Shopping Cart</h1>

        <!-- Empty Cart -->
        <div v-if="cartItems.length === 0" class="empty-cart">
          <i class="fas fa-shopping-cart"></i>
          <h2>Your cart is empty</h2>
          <p>Add some products to get started!</p>
          <router-link to="/" class="btn-shop">
            <i class="fas fa-shopping-bag"></i>
            Continue Shopping
          </router-link>
        </div>

        <!-- Cart Items -->
        <div v-else class="cart-content">
          <div class="cart-items">
            <div v-for="item in cartItems" :key="item.id" class="cart-item">
              <div class="item-image">
                <img :src="item.image" :alt="item.name" />
              </div>

              <div class="item-details">
                <h3>{{ item.name }}</h3>
                <p class="item-category">{{ item.category }}</p>
                <p v-if="item.seller" class="item-seller">
                  <i class="fas fa-store"></i>
                  {{ item.seller.store_name }}
                </p>
              </div>

              <div class="item-price">
                <span class="price">₱{{ formatPrice(item.price) }}</span>
              </div>

              <div class="item-quantity">
                <button @click="decreaseQuantity(item)" class="btn-qty">
                  <i class="fas fa-minus"></i>
                </button>
                <span class="quantity">{{ item.quantity }}</span>
                <button @click="increaseQuantity(item)" class="btn-qty" :disabled="item.quantity >= item.stock">
                  <i class="fas fa-plus"></i>
                </button>
              </div>

              <div class="item-total">
                <span class="total">₱{{ formatPrice(item.price * item.quantity) }}</span>
              </div>

              <button @click="removeItem(item.id)" class="btn-remove">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>

          <!-- Cart Summary -->
          <div class="cart-summary">
            <h2>Order Summary</h2>
            
            <div class="summary-row">
              <span>Subtotal</span>
              <span>₱{{ formatPrice(cartTotal) }}</span>
            </div>
            
            <div class="summary-row">
              <span>Shipping</span>
              <span>Free</span>
            </div>
            
            <div class="summary-total">
              <span>Total</span>
              <span>₱{{ formatPrice(cartTotal) }}</span>
            </div>

            <button class="btn-checkout">
              <i class="fas fa-lock"></i>
              Proceed to Checkout
            </button>

            <router-link to="/" class="btn-continue">
              <i class="fas fa-arrow-left"></i>
              Continue Shopping
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import Layout from '@/components/layout/Layout.vue'
import { useCart } from '@/composables/useCart'

export default {
  name: 'CartView',
  components: {
    Layout
  },
  setup() {
    const { cartItems, cartTotal, updateQuantity, removeFromCart } = useCart()

    return {
      cartItems,
      cartTotal,
      updateQuantity,
      removeFromCart
    }
  },
  methods: {
    formatPrice(price) {
      return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(price)
    },

    increaseQuantity(item) {
      if (item.quantity < item.stock) {
        this.updateQuantity(item.id, item.quantity + 1)
      }
    },

    decreaseQuantity(item) {
      if (item.quantity > 1) {
        this.updateQuantity(item.id, item.quantity - 1)
      }
    },

    removeItem(itemId) {
      if (confirm('Remove this item from cart?')) {
        this.removeFromCart(itemId)
      }
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
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.page-title {
  font-size: 2.5rem;
  margin-bottom: 2rem;
  color: #2c3e50;
}

/* Empty Cart */
.empty-cart {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
}

.empty-cart i {
  font-size: 5rem;
  color: #ccc;
  margin-bottom: 1.5rem;
}

.empty-cart h2 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
}

.empty-cart p {
  margin: 0 0 2rem 0;
  color: #666;
}

.btn-shop {
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

.btn-shop:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

/* Cart Content */
.cart-content {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
}

.cart-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.cart-item {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  display: grid;
  grid-template-columns: 100px 1fr auto auto auto auto;
  gap: 1.5rem;
  align-items: center;
}

.item-image {
  width: 100px;
  height: 100px;
  border-radius: 8px;
  overflow: hidden;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.1rem;
  color: #2c3e50;
}

.item-category {
  margin: 0 0 0.25rem 0;
  font-size: 0.85rem;
  color: #999;
  text-transform: uppercase;
}

.item-seller {
  margin: 0;
  font-size: 0.9rem;
  color: #666;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.item-seller i {
  color: #667eea;
}

.item-price .price {
  font-size: 1.25rem;
  font-weight: 600;
  color: #667eea;
}

.item-quantity {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.btn-qty {
  width: 32px;
  height: 32px;
  border: 2px solid #e0e0e0;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.btn-qty:hover:not(:disabled) {
  border-color: #667eea;
  color: #667eea;
}

.btn-qty:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity {
  font-weight: 600;
  min-width: 30px;
  text-align: center;
}

.item-total .total {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
}

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

.btn-remove:hover {
  background: #e74c3c;
  color: white;
}

/* Cart Summary */
.cart-summary {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  height: fit-content;
  position: sticky;
  top: 2rem;
}

.cart-summary h2 {
  margin: 0 0 1.5rem 0;
  font-size: 1.5rem;
  color: #2c3e50;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f0f0f0;
  color: #666;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  padding: 1.5rem 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
  border-top: 2px solid #e0e0e0;
  margin-top: 1rem;
}

.btn-checkout {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1.1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  margin-top: 1.5rem;
  transition: all 0.3s;
}

.btn-checkout:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-continue {
  width: 100%;
  padding: 0.875rem;
  background: white;
  color: #667eea;
  border: 2px solid #667eea;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  margin-top: 1rem;
  transition: all 0.3s;
}

.btn-continue:hover {
  background: #f8f9ff;
}

/* Responsive */
@media (max-width: 968px) {
  .cart-content {
    grid-template-columns: 1fr;
  }

  .cart-item {
    grid-template-columns: 80px 1fr;
    gap: 1rem;
  }

  .item-price,
  .item-quantity,
  .item-total {
    grid-column: 2;
  }

  .btn-remove {
    grid-column: 2;
    justify-self: end;
  }

  .cart-summary {
    position: static;
  }
}
</style>
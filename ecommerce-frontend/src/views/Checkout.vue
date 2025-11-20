<template>
  <div class="checkout-page">
    <div class="container">
      <div class="page-header">
        <button @click="goBack" class="btn-back">
          <i class="fas fa-arrow-left"></i>
          Back to Cart
        </button>
        <h1 class="page-title">Checkout</h1>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="loading-state">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Loading checkout...</p>
      </div>

      <!-- Checkout Content -->
      <div v-else class="checkout-content">
        <!-- Left Column: Forms -->
        <div class="checkout-forms">
          <!-- Shipping Information -->
          <div class="checkout-section">
            <div class="section-header">
              <i class="fas fa-shipping-fast"></i>
              <h2>Shipping Information</h2>
            </div>
            <div class="form-grid">
              <div class="form-group">
                <label>Full Name *</label>
                <input 
                  type="text" 
                  v-model="shippingInfo.fullName" 
                  placeholder="Juan Dela Cruz"
                  :class="{ 'error': errors.fullName }"
                />
                <span v-if="errors.fullName" class="error-message">{{ errors.fullName }}</span>
              </div>

              <div class="form-group">
                <label>Phone Number *</label>
                <input 
                  type="tel" 
                  v-model="shippingInfo.phone" 
                  placeholder="+63 912 345 6789"
                  :class="{ 'error': errors.phone }"
                />
                <span v-if="errors.phone" class="error-message">{{ errors.phone }}</span>
              </div>

              <div class="form-group full-width">
                <label>Email Address *</label>
                <input 
                  type="email" 
                  v-model="shippingInfo.email" 
                  placeholder="juan@example.com"
                  :class="{ 'error': errors.email }"
                />
                <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
              </div>

              <div class="form-group full-width">
                <label>Street Address *</label>
                <input 
                  type="text" 
                  v-model="shippingInfo.address" 
                  placeholder="House/Unit No., Street Name"
                  :class="{ 'error': errors.address }"
                />
                <span v-if="errors.address" class="error-message">{{ errors.address }}</span>
              </div>

              <div class="form-group">
                <label>City *</label>
                <input 
                  type="text" 
                  v-model="shippingInfo.city" 
                  placeholder="Manila"
                  :class="{ 'error': errors.city }"
                />
                <span v-if="errors.city" class="error-message">{{ errors.city }}</span>
              </div>

              <div class="form-group">
                <label>Province *</label>
                <input 
                  type="text" 
                  v-model="shippingInfo.province" 
                  placeholder="Metro Manila"
                  :class="{ 'error': errors.province }"
                />
                <span v-if="errors.province" class="error-message">{{ errors.province }}</span>
              </div>

              <div class="form-group">
                <label>Postal Code *</label>
                <input 
                  type="text" 
                  v-model="shippingInfo.postalCode" 
                  placeholder="1000"
                  :class="{ 'error': errors.postalCode }"
                />
                <span v-if="errors.postalCode" class="error-message">{{ errors.postalCode }}</span>
              </div>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="checkout-section">
            <div class="section-header">
              <i class="fas fa-credit-card"></i>
              <h2>Payment Method</h2>
            </div>

            <div class="payment-methods">
              <!-- Cash on Delivery -->
              <div 
                class="payment-option"
                :class="{ active: paymentMethod === 'cod' }"
                @click="selectPaymentMethod('cod')"
              >
                <div class="payment-radio">
                  <i class="fas fa-circle" v-if="paymentMethod === 'cod'"></i>
                  <i class="far fa-circle" v-else></i>
                </div>
                <div class="payment-icon cod-icon">
                  <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="payment-info">
                  <h3>Cash on Delivery</h3>
                  <p>Pay when you receive your order</p>
                </div>
              </div>

              <!-- Stripe -->
              <div 
                class="payment-option"
                :class="{ active: paymentMethod === 'stripe' }"
                @click="selectPaymentMethod('stripe')"
              >
                <div class="payment-radio">
                  <i class="fas fa-circle" v-if="paymentMethod === 'stripe'"></i>
                  <i class="far fa-circle" v-else></i>
                </div>
                <div class="payment-icon stripe-icon">
                  <i class="fab fa-cc-stripe"></i>
                </div>
                <div class="payment-info">
                  <h3>Credit/Debit Card (Stripe)</h3>
                  <p>Pay securely with your card</p>
                </div>
              </div>

              <!-- PayPal -->
              <div 
                class="payment-option"
                :class="{ active: paymentMethod === 'paypal' }"
                @click="selectPaymentMethod('paypal')"
              >
                <div class="payment-radio">
                  <i class="fas fa-circle" v-if="paymentMethod === 'paypal'"></i>
                  <i class="far fa-circle" v-else></i>
                </div>
                <div class="payment-icon paypal-icon">
                  <i class="fab fa-paypal"></i>
                </div>
                <div class="payment-info">
                  <h3>PayPal</h3>
                  <p>Pay with your PayPal account</p>
                </div>
              </div>
            </div>

            <!-- Stripe Note -->
            <div v-if="paymentMethod === 'stripe'" class="payment-note stripe-note">
              <i class="fas fa-info-circle"></i>
              <p>You will be redirected to Stripe's secure payment page to complete your transaction.</p>
            </div>

            <!-- PayPal Note -->
            <div v-if="paymentMethod === 'paypal'" class="payment-note paypal-note">
              <i class="fas fa-info-circle"></i>
              <p>You will be redirected to PayPal to complete your payment securely.</p>
            </div>
          </div>

          <!-- Order Notes (Optional) -->
          <div class="checkout-section">
            <div class="section-header">
              <i class="fas fa-sticky-note"></i>
              <h2>Order Notes (Optional)</h2>
            </div>
            <div class="form-group">
              <textarea 
                v-model="orderNotes" 
                placeholder="Any special instructions for your order..."
                rows="4"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Right Column: Order Summary -->
        <div class="order-summary-section">
          <div class="order-summary">
            <h2>Order Summary</h2>

            <!-- Cart Items -->
            <div class="summary-items">
              <div 
                v-for="item in cartItems" 
                :key="item.id"
                class="summary-item"
              >
                <img :src="item.product.image_url" :alt="item.product.name" />
                <div class="summary-item-info">
                  <h4>{{ item.product.name }}</h4>
                  <p>Qty: {{ item.quantity }}</p>
                </div>
                <div class="summary-item-price">
                  ₱{{ formatPrice(item.subtotal) }}
                </div>
              </div>
            </div>

            <!-- Price Breakdown -->
            <div class="summary-breakdown">
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
            </div>

            <!-- Place Order Button -->
            <button 
              class="btn-place-order"
              @click="placeOrder"
              :disabled="isProcessing"
            >
              <span v-if="!isProcessing">
                <i class="fas fa-lock"></i>
                Place Order
              </span>
              <span v-else>
                <i class="fas fa-spinner fa-spin"></i>
                Processing...
              </span>
            </button>

            <!-- Security Badge -->
            <div class="security-badge">
              <i class="fas fa-shield-alt"></i>
              <span>Secure Checkout - Your information is protected</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Success Modal -->
    <OrderSuccessModal
      :show="showSuccessModal"
      :orderNumber="orderNumber"
      @close="handleSuccessClose"
    />
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCart } from '@/composables/useCart'
import { authService } from '@/services/authService'
import { orderService } from '@/services/orderService'
import OrderSuccessModal from '@/components/modals/OrderSuccessModal.vue'

export default {
  name: 'Checkout',
  components: {
    OrderSuccessModal
  },
  setup() {
    const router = useRouter()
    const { cartItems, cartCount, cartTotal, loadCart, clearCart } = useCart()

    const isLoading = ref(true)
    const isProcessing = ref(false)
    const showSuccessModal = ref(false)
    const orderNumber = ref('')

    // Shipping Information
    const shippingInfo = ref({
      fullName: '',
      phone: '',
      email: '',
      address: '',
      city: '',
      province: '',
      postalCode: ''
    })

    // Payment Method
    const paymentMethod = ref('cod')
    const orderNotes = ref('')

    // Validation Errors
    const errors = ref({})

    onMounted(async () => {
      // Check if user is logged in
      if (!authService.isAuthenticated()) {
        router.push('/login')
        return
      }

      // Load cart
      await loadCart()

      // Check if cart is empty
      if (cartItems.value.length === 0) {
        router.push('/cart')
        return
      }

      // Pre-fill user info
      const user = authService.getUser()
      if (user) {
        shippingInfo.value.fullName = user.name || ''
        shippingInfo.value.email = user.email || ''
      }

      isLoading.value = false
    })

    const selectPaymentMethod = (method) => {
      paymentMethod.value = method
    }

    const validateForm = () => {
      errors.value = {}
      let isValid = true

      if (!shippingInfo.value.fullName.trim()) {
        errors.value.fullName = 'Full name is required'
        isValid = false
      }

      if (!shippingInfo.value.phone.trim()) {
        errors.value.phone = 'Phone number is required'
        isValid = false
      }

      if (!shippingInfo.value.email.trim()) {
        errors.value.email = 'Email is required'
        isValid = false
      } else if (!/\S+@\S+\.\S+/.test(shippingInfo.value.email)) {
        errors.value.email = 'Invalid email format'
        isValid = false
      }

      if (!shippingInfo.value.address.trim()) {
        errors.value.address = 'Address is required'
        isValid = false
      }

      if (!shippingInfo.value.city.trim()) {
        errors.value.city = 'City is required'
        isValid = false
      }

      if (!shippingInfo.value.province.trim()) {
        errors.value.province = 'Province is required'
        isValid = false
      }

      if (!shippingInfo.value.postalCode.trim()) {
        errors.value.postalCode = 'Postal code is required'
        isValid = false
      }

      return isValid
    }

    const placeOrder = async () => {
      // Validate form
      if (!validateForm()) {
        // Scroll to first error
        const firstError = document.querySelector('.error')
        if (firstError) {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' })
        }
        return
      }

      isProcessing.value = true

      try {
        // Prepare order data
        const orderData = {
          shipping: shippingInfo.value,
          payment_method: paymentMethod.value,
          order_notes: orderNotes.value
        }

        // Call backend API
        const response = await orderService.createOrder(orderData)

        if (response.success) {
          orderNumber.value = response.data.order.order_number

          // Check if Stripe payment is required
          if (response.data.requires_payment && response.data.stripe_url) {
            
            // Redirect to Stripe checkout page
            window.location.href = response.data.stripe_url
            return
          }

          // For COD and PayPal - show success modal
          await clearCart()
          showSuccessModal.value = true
        }

      } catch (error) {
        console.error('Order error:', error)
        const errorMessage = error.response?.data?.message || 'Failed to place order. Please try again.'
        alert(errorMessage)
      } finally {
        isProcessing.value = false
      }
    }

    const handleSuccessClose = () => {
      showSuccessModal.value = false
      router.push('/')
    }

    const formatPrice = (price) => {
      return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(price)
    }

    const goBack = () => {
      router.push('/cart')
    }

    return {
      isLoading,
      isProcessing,
      cartItems,
      cartCount,
      cartTotal,
      shippingInfo,
      paymentMethod,
      orderNotes,
      errors,
      showSuccessModal,
      orderNumber,
      selectPaymentMethod,
      placeOrder,
      handleSuccessClose,
      formatPrice,
      goBack
    }
  }
}
</script>

<style scoped>
.checkout-page {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 2rem 0 4rem 0;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

.page-header {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 2rem;
}

.btn-back {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: white;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  color: #666;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 0.95rem;
}

.btn-back:hover {
  border-color: #667eea;
  color: #667eea;
  background: #f8f9ff;
  transform: translateX(-4px);
}

.btn-back i {
  font-size: 1rem;
}

.page-title {
  font-size: 2rem;
  color: #2c3e50;
  margin: 0;
  flex: 1;
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

/* Checkout Content */
.checkout-content {
  display: grid;
  grid-template-columns: 1fr 450px;
  gap: 2rem;
}

/* Checkout Forms */
.checkout-forms {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.checkout-section {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #f0f0f0;
}

.section-header i {
  font-size: 1.5rem;
  color: #667eea;
}

.section-header h2 {
  margin: 0;
  font-size: 1.5rem;
  color: #2c3e50;
}

/* Form Grid */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-weight: 600;
  color: #2c3e50;
  font-size: 0.95rem;
}

.form-group input,
.form-group textarea {
  padding: 0.875rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s;
  font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
}

.form-group input.error,
.form-group textarea.error {
  border-color: #e74c3c;
}

.error-message {
  color: #e74c3c;
  font-size: 0.85rem;
  margin-top: -0.25rem;
}

/* Payment Methods */
.payment-methods {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.payment-option {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s;
}

.payment-option:hover {
  border-color: #667eea;
  background: #f8f9ff;
}

.payment-option.active {
  border-color: #667eea;
  background: #f8f9ff;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.payment-radio {
  font-size: 1.25rem;
  color: #667eea;
}

.payment-icon {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.75rem;
}

.cod-icon {
  background: #e8f5e9;
  color: #4caf50;
}

.stripe-icon {
  background: #ede7f6;
  color: #6772e5;
}

.paypal-icon {
  background: #e3f2fd;
  color: #0070ba;
}

.payment-info {
  flex: 1;
}

.payment-info h3 {
  margin: 0 0 0.25rem 0;
  font-size: 1.1rem;
  color: #2c3e50;
}

.payment-info p {
  margin: 0;
  color: #666;
  font-size: 0.9rem;
}

.payment-note {
  margin-top: 1rem;
  padding: 1rem;
  border-radius: 6px;
  display: flex;
  gap: 0.75rem;
  align-items: start;
}

.payment-note.stripe-note {
  background: #f3e5f5;
  border-left: 4px solid #6772e5;
}

.payment-note.stripe-note i {
  color: #6772e5;
}

.payment-note.stripe-note p {
  color: #4a148c;
}

.payment-note.paypal-note {
  background: #e3f2fd;
  border-left: 4px solid #0070ba;
}

.payment-note.paypal-note i {
  color: #0070ba;
}

.payment-note.paypal-note p {
  color: #01579b;
}

.payment-note i {
  margin-top: 0.125rem;
}

.payment-note p {
  margin: 0;
  font-size: 0.9rem;
}

/* Order Summary */
.order-summary-section {
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.order-summary {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.order-summary h2 {
  margin: 0 0 1.5rem 0;
  font-size: 1.5rem;
  color: #2c3e50;
  padding-bottom: 1rem;
  border-bottom: 2px solid #f0f0f0;
}

/* Summary Items */
.summary-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
  max-height: 300px;
  overflow-y: auto;
}

.summary-item {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.summary-item img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
}

.summary-item-info {
  flex: 1;
}

.summary-item-info h4 {
  margin: 0 0 0.25rem 0;
  font-size: 0.95rem;
  color: #2c3e50;
}

.summary-item-info p {
  margin: 0;
  color: #666;
  font-size: 0.85rem;
}

.summary-item-price {
  font-weight: 600;
  color: #667eea;
}

/* Price Breakdown */
.summary-breakdown {
  padding-top: 1rem;
  border-top: 2px solid #f0f0f0;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  color: #666;
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

/* Place Order Button */
.btn-place-order {
  width: 100%;
  padding: 1.25rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

.btn-place-order:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-place-order:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Security Badge */
.security-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 1rem;
  padding: 0.75rem;
  background: #f8f9fa;
  border-radius: 6px;
  color: #666;
  font-size: 0.85rem;
}

.security-badge i {
  color: #4caf50;
}

/* Responsive */
@media (max-width: 1024px) {
  .checkout-content {
    grid-template-columns: 1fr;
  }

  .order-summary-section {
    position: static;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .btn-back {
    width: 100%;
    justify-content: center;
  }

  .page-title {
    font-size: 1.5rem;
    width: 100%;
    text-align: center;
  }

  .checkout-section {
    padding: 1.5rem;
  }

  .section-header h2 {
    font-size: 1.25rem;
  }
}
</style>
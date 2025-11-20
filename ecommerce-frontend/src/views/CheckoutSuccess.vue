<template>
  <div class="checkout-success-page">
    <div class="container">
      <!-- Loading State -->
      <div v-if="isVerifying" class="loading-state">
        <div class="loading-spinner">
          <i class="fas fa-spinner fa-spin"></i>
        </div>
        <h2>Verifying your payment...</h2>
        <p>Please wait while we confirm your order.</p>
      </div>

      <!-- Success State -->
      <div v-else-if="isSuccess" class="success-state">
        <div class="success-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <h1>Payment Successful!</h1>
        <p class="order-number">Order Number: <strong>{{ orderNumber }}</strong></p>
        <p>Thank you for your purchase! Your order has been confirmed.</p>
        <div class="action-buttons">
          <router-link to="/" class="btn-primary">
            <i class="fas fa-home"></i>
            Back to Home
          </router-link>
          <router-link to="/orders" class="btn-secondary">
            <i class="fas fa-shopping-bag"></i>
            View My Orders
          </router-link>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="error-state">
        <div class="error-icon">
          <i class="fas fa-times-circle"></i>
        </div>
        <h1>Payment Verification Failed</h1>
        <p>{{ errorMessage }}</p>
        <div class="action-buttons">
          <router-link to="/cart" class="btn-primary">
            <i class="fas fa-shopping-cart"></i>
            Back to Cart
          </router-link>
          <router-link to="/" class="btn-secondary">
            <i class="fas fa-home"></i>
            Back to Home
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { orderService } from '@/services/orderService'

export default {
  name: 'CheckoutSuccess',
  setup() {
    const route = useRoute()
    const isVerifying = ref(true)
    const isSuccess = ref(false)
    const orderNumber = ref('')
    const errorMessage = ref('')

    onMounted(async () => {
      const sessionId = route.query.session_id

      if (!sessionId) {
        isVerifying.value = false
        errorMessage.value = 'No payment session found.'
        return
      }

      try {
        const response = await orderService.verifyStripePayment(sessionId)

        if (response.success) {
          isSuccess.value = true
          orderNumber.value = response.data.metadata?.order_number || 'N/A'
        } else {
          errorMessage.value = response.message || 'Payment verification failed'
        }

      } catch (error) {
        console.error('Verification error:', error)
        errorMessage.value = error.response?.data?.message || 'Failed to verify payment'
      } finally {
        isVerifying.value = false
      }
    })

    return {
      isVerifying,
      isSuccess,
      orderNumber,
      errorMessage
    }
  }
}
</script>

<style scoped>
.checkout-success-page {
  min-height: 100vh;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.container {
  max-width: 600px;
  width: 100%;
}

.loading-state,
.success-state,
.error-state {
  background: white;
  padding: 3rem 2rem;
  border-radius: 16px;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Loading */
.loading-spinner {
  width: 100px;
  height: 100px;
  background: #f8f9ff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 2rem;
}

.loading-spinner i {
  font-size: 3rem;
  color: #667eea;
}

.loading-state h2 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
}

.loading-state p {
  color: #666;
  margin: 0;
}

/* Success */
.success-icon {
  width: 100px;
  height: 100px;
  background: #d4edda;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 2rem;
}

.success-icon i {
  font-size: 4rem;
  color: #28a745;
}

.success-state h1 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
  font-size: 2rem;
}

.order-number {
  margin: 0 0 1.5rem 0;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  font-size: 1.1rem;
  color: #666;
}

.order-number strong {
  color: #667eea;
  font-weight: 700;
}

.success-state p {
  color: #666;
  margin: 0 0 2rem 0;
  line-height: 1.6;
}

/* Error */
.error-icon {
  width: 100px;
  height: 100px;
  background: #f8d7da;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 2rem;
}

.error-icon i {
  font-size: 4rem;
  color: #e74c3c;
}

.error-state h1 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
  font-size: 2rem;
}

.error-state p {
  color: #666;
  margin: 0 0 2rem 0;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.btn-primary,
.btn-secondary {
  padding: 1rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  transition: all 0.3s;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
  background: white;
  color: #667eea;
  border: 2px solid #667eea;
}

.btn-secondary:hover {
  background: #f8f9ff;
}

@media (max-width: 768px) {
  .checkout-success-page {
    padding: 1rem;
  }

  .loading-state,
  .success-state,
  .error-state {
    padding: 2rem 1.5rem;
  }

  .success-state h1,
  .error-state h1 {
    font-size: 1.5rem;
  }
}
</style>
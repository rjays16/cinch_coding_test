<template>
  <div class="order-detail-page">
    <div class="container">
      <!-- Back Button -->
      <router-link to="/orders" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Orders
      </router-link>

      <!-- Loading State -->
      <div v-if="isLoading" class="loading-state">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Loading order details...</p>
      </div>

      <!-- Order Details -->
      <div v-else-if="order" class="order-detail">
        <!-- Order Header -->
        <div class="detail-header">
          <div class="header-left">
            <h1>Order #{{ order.order_number }}</h1>
            <p class="order-date">
              <i class="fas fa-calendar"></i>
              Placed on {{ formatDate(order.created_at) }}
            </p>
          </div>
          <div class="header-right">
            <span class="status-badge" :class="'status-' + order.status">
              {{ order.status }}
            </span>
            <span class="payment-badge" :class="'payment-' + order.payment_status">
              {{ order.payment_status }}
            </span>
          </div>
        </div>

        <!-- Order Content Grid -->
        <div class="detail-grid">
          <!-- Left Column: Order Items & Summary -->
          <div class="left-column">
            <!-- Order Items -->
            <div class="detail-card">
              <h2>Order Items</h2>
              <div class="items-list">
                <div 
                  v-for="item in order.items" 
                  :key="item.id"
                  class="detail-item"
                >
                  <div class="item-image">
                    <img :src="item.product.image_url" :alt="item.product.name" />
                  </div>
                  <div class="item-info">
                    <h3>{{ item.product.name }}</h3>
                    <p class="item-meta">
                      Quantity: {{ item.quantity }} × ₱{{ formatPrice(item.price) }}
                    </p>
                  </div>
                  <div class="item-total">
                    ₱{{ formatPrice(item.subtotal) }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Order Summary -->
            <div class="detail-card summary-card">
              <h2>Order Summary</h2>
              <div class="summary-rows">
                <div class="summary-row">
                  <span>Subtotal:</span>
                  <span>₱{{ formatPrice(order.subtotal) }}</span>
                </div>
                <div class="summary-row">
                  <span>Shipping Fee:</span>
                  <span v-if="order.shipping_fee > 0">₱{{ formatPrice(order.shipping_fee) }}</span>
                  <span v-else class="free-text">FREE</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row total-row">
                  <span>Total:</span>
                  <span>₱{{ formatPrice(order.total) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: Shipping & Payment Info -->
          <div class="right-column">
            <!-- Shipping Information -->
            <div class="detail-card">
              <h2>
                <i class="fas fa-shipping-fast"></i>
                Shipping Information
              </h2>
              <div class="info-rows">
                <div class="info-row">
                  <span class="info-label">Full Name:</span>
                  <span class="info-value">{{ order.shipping_full_name }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Email:</span>
                  <span class="info-value">{{ order.shipping_email }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Phone:</span>
                  <span class="info-value">{{ order.shipping_phone }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Address:</span>
                  <span class="info-value">
                    {{ order.shipping_address }}<br>
                    {{ order.shipping_city }}, {{ order.shipping_province }}<br>
                    {{ order.shipping_postal_code }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Payment Information -->
            <div class="detail-card">
              <h2>
                <i class="fas fa-credit-card"></i>
                Payment Information
              </h2>
              <div class="info-rows">
                <div class="info-row">
                  <span class="info-label">Payment Method:</span>
                  <span class="info-value payment-method">
                    {{ formatPaymentMethod(order.payment_method) }}
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Payment Status:</span>
                  <span class="payment-badge" :class="'payment-' + order.payment_status">
                    {{ order.payment_status }}
                  </span>
                </div>
                <div v-if="order.stripe_payment_intent" class="info-row">
                  <span class="info-label">Transaction ID:</span>
                  <span class="info-value transaction-id">{{ order.stripe_payment_intent }}</span>
                </div>
              </div>
            </div>

            <!-- Order Notes -->
            <div v-if="order.order_notes" class="detail-card">
              <h2>
                <i class="fas fa-sticky-note"></i>
                Order Notes
              </h2>
              <p class="order-notes">{{ order.order_notes }}</p>
            </div>

            <!-- Order Timeline -->
            <div class="detail-card">
              <h2>
                <i class="fas fa-clock"></i>
                Order Timeline
              </h2>
              <div class="timeline">
                <div class="timeline-item active">
                  <div class="timeline-dot"></div>
                  <div class="timeline-content">
                    <h4>Order Placed</h4>
                    <p>{{ formatDateTime(order.created_at) }}</p>
                  </div>
                </div>
                <div class="timeline-item" :class="{ active: isStatusActive('processing') }">
                  <div class="timeline-dot"></div>
                  <div class="timeline-content">
                    <h4>Processing</h4>
                    <p v-if="isStatusActive('processing')">Your order is being prepared</p>
                    <p v-else class="pending-text">Pending</p>
                  </div>
                </div>
                <div class="timeline-item" :class="{ active: isStatusActive('shipped') }">
                  <div class="timeline-dot"></div>
                  <div class="timeline-content">
                    <h4>Shipped</h4>
                    <p v-if="isStatusActive('shipped')">Your order is on the way</p>
                    <p v-else class="pending-text">Pending</p>
                  </div>
                </div>
                <div class="timeline-item" :class="{ active: isStatusActive('delivered') }">
                  <div class="timeline-dot"></div>
                  <div class="timeline-content">
                    <h4>Delivered</h4>
                    <p v-if="isStatusActive('delivered')">Order completed</p>
                    <p v-else class="pending-text">Pending</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="error-state">
        <i class="fas fa-exclamation-circle"></i>
        <h2>Order Not Found</h2>
        <p>The order you're looking for doesn't exist or has been removed.</p>
        <router-link to="/orders" class="btn-back">
          Back to Orders
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

export default {
  name: 'OrderDetail',
  setup() {
    const route = useRoute()
    const order = ref(null)
    const isLoading = ref(false)

    const loadOrder = async () => {
      try {
        isLoading.value = true
        const response = await api.get(`/buyer/orders/${route.params.id}`)
        
        if (response.data.success) {
          order.value = response.data.data
        }
      } catch (error) {
        console.error('Error loading order:', error)
      } finally {
        isLoading.value = false
      }
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const formatDateTime = (date) => {
      return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const formatPrice = (price) => {
      return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(price)
    }

    const formatPaymentMethod = (method) => {
      const methods = {
        'cod': 'Cash on Delivery',
        'stripe': 'Credit/Debit Card (Stripe)',
        'paypal': 'PayPal'
      }
      return methods[method] || method
    }

    const isStatusActive = (status) => {
      if (!order.value) return false
      
      const statusOrder = ['pending', 'processing', 'shipped', 'delivered']
      const currentIndex = statusOrder.indexOf(order.value.status)
      const targetIndex = statusOrder.indexOf(status)
      
      return currentIndex >= targetIndex
    }

    onMounted(() => {
      loadOrder()
    })

    return {
      order,
      isLoading,
      formatDate,
      formatDateTime,
      formatPrice,
      formatPaymentMethod,
      isStatusActive
    }
  }
}
</script>

<style scoped>
.order-detail-page {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 2rem 0 4rem 0;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Back Button */
.back-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: white;
  color: #667eea;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  margin-bottom: 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: all 0.3s;
}

.back-button:hover {
  background: #667eea;
  color: white;
  transform: translateX(-5px);
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

/* Error State */
.error-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.error-state i {
  font-size: 4rem;
  color: #e74c3c;
  margin-bottom: 1rem;
}

.error-state h2 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
}

.error-state p {
  margin: 0 0 2rem 0;
  color: #666;
}

.btn-back {
  display: inline-block;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
}

/* Order Detail */
.detail-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.detail-header h1 {
  margin: 0 0 0.5rem 0;
  font-size: 2rem;
  color: #2c3e50;
}

.order-date {
  margin: 0;
  color: #6b7280;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.header-right {
  display: flex;
  gap: 0.5rem;
}

.status-badge,
.payment-badge {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-processing { background: #cfe2ff; color: #084298; }
.status-shipped { background: #cff4fc; color: #055160; }
.status-delivered { background: #d1e7dd; color: #0f5132; }
.status-cancelled { background: #f8d7da; color: #842029; }

.payment-pending { background: #fff3cd; color: #856404; }
.payment-paid { background: #d1e7dd; color: #0f5132; }
.payment-failed { background: #f8d7da; color: #842029; }
.payment-refunded { background: #e7e7e7; color: #333; }

/* Detail Grid */
.detail-grid {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
}

/* Cards */
.detail-card {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 1.5rem;
}

.detail-card:last-child {
  margin-bottom: 0;
}

.detail-card h2 {
  margin: 0 0 1.5rem 0;
  font-size: 1.25rem;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e5e7eb;
}

/* Order Items */
.items-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.detail-item {
  display: flex;
  gap: 1rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.detail-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.item-image {
  width: 100px;
  height: 100px;
  border-radius: 8px;
  overflow: hidden;
  background: #f5f5f5;
  flex-shrink: 0;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-info {
  flex: 1;
}

.item-info h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.1rem;
  color: #2c3e50;
}

.item-meta {
  margin: 0;
  color: #6b7280;
  font-size: 0.95rem;
}

.item-total {
  font-size: 1.25rem;
  font-weight: 700;
  color: #667eea;
  align-self: center;
}

/* Summary */
.summary-rows {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  font-size: 1rem;
  color: #666;
}

.summary-divider {
  height: 1px;
  background: #e5e7eb;
  margin: 0.5rem 0;
}

.total-row {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
}

.free-text {
  color: #10b981;
  font-weight: 600;
}

/* Info Rows */
.info-rows {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.info-row {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.info-label {
  font-size: 0.85rem;
  color: #6b7280;
  font-weight: 600;
}

.info-value {
  font-size: 1rem;
  color: #2c3e50;
  word-break: break-word;
}

.payment-method {
  font-weight: 600;
  color: #667eea;
}

.transaction-id {
  font-family: monospace;
  font-size: 0.9rem;
  color: #6b7280;
}

/* Order Notes */
.order-notes {
  margin: 0;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
  color: #666;
  line-height: 1.6;
}

/* Timeline */
.timeline {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.timeline-item {
  display: flex;
  gap: 1rem;
  position: relative;
  padding-left: 2rem;
}

.timeline-item::before {
  content: '';
  position: absolute;
  left: 0.625rem;
  top: 2rem;
  width: 2px;
  height: calc(100% + 1.5rem);
  background: #e5e7eb;
}

.timeline-item:last-child::before {
  display: none;
}

.timeline-dot {
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 50%;
  background: #e5e7eb;
  border: 3px solid white;
  box-shadow: 0 0 0 2px #e5e7eb;
  flex-shrink: 0;
  position: absolute;
  left: 0;
}

.timeline-item.active .timeline-dot {
  background: #667eea;
  box-shadow: 0 0 0 2px #667eea;
}

.timeline-content h4 {
  margin: 0 0 0.25rem 0;
  color: #2c3e50;
  font-size: 1rem;
}

.timeline-content p {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
}

.pending-text {
  color: #9ca3af;
}

/* Responsive */
@media (max-width: 1024px) {
  .detail-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }

  .detail-header {
    flex-direction: column;
    gap: 1rem;
  }

  .detail-header h1 {
    font-size: 1.5rem;
  }

  .detail-item {
    flex-direction: column;
  }

  .item-image {
    width: 100%;
    height: 200px;
  }

  .item-total {
    align-self: flex-start;
  }
}
</style>
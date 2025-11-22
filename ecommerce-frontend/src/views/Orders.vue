<template>
  <div class="orders-page">
    <div class="container">
      <h1 class="page-title">My Orders</h1>

      <!-- Loading State -->
      <div v-if="isLoading" class="loading-state">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Loading your orders...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="orders.length === 0" class="empty-state">
        <div class="empty-icon">
          <i class="fas fa-shopping-bag"></i>
        </div>
        <h2>No Orders Yet</h2>
        <p>You haven't placed any orders yet.</p>
        <router-link to="/" class="btn-shop-now">
          <i class="fas fa-shopping-bag"></i>
          Start Shopping
        </router-link>
      </div>

      <!-- Orders List -->
      <div v-else class="orders-list">
        <div 
          v-for="order in orders" 
          :key="order.id"
          class="order-card"
        >
          <!-- Order Header -->
          <div class="order-header">
            <div class="order-info">
              <h3>Order #{{ order.order_number }}</h3>
              <p class="order-date">
                <i class="fas fa-calendar"></i>
                {{ formatDate(order.created_at) }}
              </p>
            </div>
            <div class="order-status">
              <span class="status-badge" :class="'status-' + order.status">
                {{ order.status }}
              </span>
              <span class="payment-badge" :class="'payment-' + order.payment_status">
                {{ order.payment_status }}
              </span>
            </div>
          </div>

          <!-- Order Items -->
          <div class="order-items">
            <div 
              v-for="item in order.items" 
              :key="item.id"
              class="order-item"
            >
              <div class="item-image">
                <img :src="item.product.image_url" :alt="item.product.name" />
              </div>
              <div class="item-details">
                <h4>{{ item.product.name }}</h4>
                <p class="item-meta">Qty: {{ item.quantity }} × ₱{{ formatPrice(item.price) }}</p>
              </div>
              <div class="item-price">
                ₱{{ formatPrice(item.subtotal) }}
              </div>
            </div>
          </div>

          <!-- Order Footer -->
          <div class="order-footer">
            <div class="order-total">
              <span>Total:</span>
              <span class="total-amount">₱{{ formatPrice(order.total) }}</span>
            </div>
            <router-link 
              :to="`/orders/${order.id}`" 
              class="btn-view-details"
            >
              View Details
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

export default {
  name: 'Orders',
  setup() {
    const orders = ref([])
    const isLoading = ref(false)

    const loadOrders = async () => {
      try {
        isLoading.value = true
        const response = await api.get('/buyer/orders')
        
        if (response.data.success) {
          orders.value = response.data.data
        }
      } catch (error) {
        console.error('Error loading orders:', error)
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

    const formatPrice = (price) => {
      return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(price)
    }

    onMounted(() => {
      loadOrders()
    })

    return {
      orders,
      isLoading,
      formatDate,
      formatPrice
    }
  }
}
</script>

<style scoped>
.orders-page {
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

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.empty-icon {
  width: 120px;
  height: 120px;
  margin: 0 auto 2rem;
  background: #f8f9ff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-icon i {
  font-size: 4rem;
  color: #667eea;
}

.empty-state h2 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
  font-size: 1.75rem;
}

.empty-state p {
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

/* Orders List */
.orders-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.order-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: all 0.3s;
}

.order-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

/* Order Header */
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  background: #f9fafb;
}

.order-info h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.25rem;
  color: #2c3e50;
}

.order-date {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.order-status {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.status-badge,
.payment-badge {
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
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

/* Order Items */
.order-items {
  padding: 1.5rem;
}

.order-item {
  display: flex;
  gap: 1rem;
  padding: 1rem 0;
  border-bottom: 1px solid #e5e7eb;
}

.order-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.item-image {
  width: 80px;
  height: 80px;
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

.item-details {
  flex: 1;
}

.item-details h4 {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
  color: #2c3e50;
}

.item-meta {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
}

.item-price {
  font-weight: 700;
  color: #667eea;
  font-size: 1.1rem;
  align-self: center;
}

/* Order Footer */
.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  background: #f9fafb;
  border-top: 1px solid #e5e7eb;
}

.order-total {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.order-total span:first-child {
  color: #6b7280;
  font-size: 0.9rem;
}

.total-amount {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
}

.btn-view-details {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-view-details:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }

  .page-title {
    font-size: 1.5rem;
  }

  .order-header {
    flex-direction: column;
    gap: 1rem;
  }

  .order-item {
    flex-direction: column;
  }

  .item-image {
    width: 100%;
    height: 150px;
  }

  .item-price {
    align-self: flex-start;
  }

  .order-footer {
    flex-direction: column;
    gap: 1rem;
  }

  .btn-view-details {
    width: 100%;
    text-align: center;
  }
}
</style>

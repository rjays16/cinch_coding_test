<template>
  <div v-if="show" class="modal-overlay">
    <div class="modal-content">
      <div class="success-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      
      <h2>Order Placed Successfully!</h2>
      
      <p class="order-number">Order Number: <strong>{{ orderNumber }}</strong></p>
      
      <p class="success-message">
        Thank you for your order! We've sent a confirmation email with your order details.
      </p>

      <div class="action-buttons">
        <button @click="handleClose" class="btn-primary">
          <i class="fas fa-shopping-bag"></i>
          View My Orders
        </button>
        <button @click="continueShopping" class="btn-secondary">
          <i class="fas fa-home"></i>
          Continue Shopping
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'OrderSuccessModal',
  props: {
    show: {
      type: Boolean,
      required: true
    },
    orderNumber: {
      type: String,
      default: ''
    }
  },
  methods: {
    handleClose() {
      this.$emit('close')
    },
    continueShopping() {
      this.$router.push('/')
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
  z-index: 10000;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 16px;
  max-width: 500px;
  width: 100%;
  padding: 3rem 2rem;
  text-align: center;
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

.modal-content h2 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
  font-size: 1.75rem;
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

.success-message {
  margin: 0 0 2rem 0;
  color: #666;
  line-height: 1.6;
}

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
  cursor: pointer;
  transition: all 0.3s;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  font-size: 1rem;
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
  .modal-content {
    padding: 2rem 1.5rem;
  }

  .modal-content h2 {
    font-size: 1.5rem;
  }

  .success-icon {
    width: 80px;
    height: 80px;
  }

  .success-icon i {
    font-size: 3rem;
  }
}
</style>
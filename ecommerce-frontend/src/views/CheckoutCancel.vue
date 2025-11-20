<template>
  <div class="checkout-cancel-page">
    <div class="container">
      <div class="cancel-state">
        <div class="cancel-icon">
          <i class="fas fa-times-circle"></i>
        </div>
        <h1>Payment Cancelled</h1>
        <p>Your payment was cancelled. Your cart items are still saved.</p>
        <div class="action-buttons">
          <router-link to="/checkout" class="btn-primary">
            <i class="fas fa-arrow-left"></i>
            Back to Checkout
          </router-link>
          <router-link to="/cart" class="btn-secondary">
            <i class="fas fa-shopping-cart"></i>
            View Cart
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CheckoutCancel'
}
</script>

<style scoped>
.checkout-cancel-page {
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

.cancel-state {
  background: white;
  padding: 3rem 2rem;
  border-radius: 16px;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.cancel-icon {
  width: 100px;
  height: 100px;
  background: #fff3cd;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 2rem;
}

.cancel-icon i {
  font-size: 4rem;
  color: #ff9800;
}

.cancel-state h1 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
  font-size: 2rem;
}

.cancel-state p {
  color: #666;
  margin: 0 0 2rem 0;
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
</style>
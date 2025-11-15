<template>
  <div class="dashboard">
    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon products">
          <i class="fas fa-box"></i>
        </div>
        <div class="stat-info">
          <h3>{{ stats.totalProducts }}</h3>
          <p>Total Products</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon orders">
          <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-info">
          <h3>{{ stats.totalOrders }}</h3>
          <p>Total Orders</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon revenue">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
          <h3>${{ stats.totalRevenue }}</h3>
          <p>Total Revenue</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon customers">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
          <h3>{{ stats.totalCustomers }}</h3>
          <p>Customers</p>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
      <h2>Quick Actions</h2>
      <div class="actions-grid">
        <router-link to="/products/add" class="action-card">
          <i class="fas fa-plus-circle"></i>
          <span>Add Product</span>
        </router-link>

        <router-link to="/orders" class="action-card">
          <i class="fas fa-list"></i>
          <span>View Orders</span>
        </router-link>

        <router-link to="/categories" class="action-card">
          <i class="fas fa-tags"></i>
          <span>Manage Categories</span>
        </router-link>

        <router-link to="/profile" class="action-card">
          <i class="fas fa-user-circle"></i>
          <span>Edit Profile</span>
        </router-link>
      </div>
    </div>

    <!-- Recent Products -->
    <div class="recent-section">
      <div class="section-header">
        <h2>Recent Products</h2>
        <router-link to="/products" class="view-all">View All</router-link>
      </div>
      <div class="products-table">
        <table>
          <thead>
            <tr>
              <th>Product</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="recentProducts.length === 0">
              <td colspan="5" style="text-align: center; padding: 40px;">
                No products yet. <router-link to="/products/add" style="color: #667eea; font-weight: 600;">Add your first product</router-link>
              </td>
            </tr>
            <tr v-for="product in recentProducts" :key="product.id">
              <td>{{ product.name }}</td>
              <td>{{ product.category }}</td>
              <td>${{ product.price }}</td>
              <td>{{ product.stock }}</td>
              <td>
                <span class="status" :class="product.status">
                  {{ product.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({
  totalProducts: 0,
  totalOrders: 0,
  totalRevenue: 0,
  totalCustomers: 0
})

const recentProducts = ref([])

onMounted(() => {
  // Static data for now - you can replace with API calls later
  stats.value = {
    totalProducts: 0,
    totalOrders: 0,
    totalRevenue: 0,
    totalCustomers: 0
  }

  recentProducts.value = []
})
</script>

<style scoped>
.dashboard {
  max-width: 1400px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.stat-card {
  background: white;
  padding: 25px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: white;
}

.stat-icon.products { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-icon.orders { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-icon.revenue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-icon.customers { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

.stat-info h3 {
  font-size: 32px;
  font-weight: 700;
  margin: 0 0 5px 0;
  color: #333;
}

.stat-info p {
  font-size: 14px;
  color: #666;
  margin: 0;
}

.quick-actions {
  background: white;
  padding: 30px;
  border-radius: 12px;
  margin-bottom: 40px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.quick-actions h2 {
  font-size: 20px;
  font-weight: 600;
  margin: 0 0 20px 0;
  color: #333;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.action-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 25px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  border-radius: 12px;
  transition: transform 0.3s, box-shadow 0.3s;
  font-weight: 500;
}

.action-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.action-card i {
  font-size: 32px;
}

.recent-section {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h2 {
  font-size: 20px;
  font-weight: 600;
  margin: 0;
  color: #333;
}

.view-all {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}

.view-all:hover {
  color: #764ba2;
}

.products-table {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  text-align: left;
  padding: 12px;
  background: #f8f9fa;
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #e9ecef;
}

td {
  padding: 15px 12px;
  border-bottom: 1px solid #e9ecef;
  color: #666;
}

.status {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
}

.status.active {
  background: #d4edda;
  color: #155724;
}

.status.out-of-stock {
  background: #f8d7da;
  color: #721c24;
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .actions-grid {
    grid-template-columns: 1fr 1fr;
  }
}
</style>
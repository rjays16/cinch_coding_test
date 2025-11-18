<template>
  <div class="dashboard-page">
    <!-- Dashboard Header -->
    <div class="page-header">
      <div class="header-content">
        <div>
          <h1>Dashboard</h1>
          <p class="subtitle">Welcome back! Here's what's happening with your store.</p>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon total-products">
          <i class="fas fa-box"></i>
        </div>
        <div class="stat-content">
          <h3>{{ totalProducts }}</h3>
          <p>Total Products</p>
          <span class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 12% from last month
          </span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon total-orders">
          <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-content">
          <h3>156</h3>
          <p>Total Orders</p>
          <span class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 8% from last month
          </span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon revenue">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
          <h3>₱{{ formatPrice(totalRevenue) }}</h3>
          <p>Total Revenue</p>
          <span class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 15% from last month
          </span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon low-stock">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
          <h3>{{ lowStockCount }}</h3>
          <p>Low Stock Items</p>
          <span class="stat-change negative">
            <i class="fas fa-arrow-down"></i> Needs attention
          </span>
        </div>
      </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="dashboard-section">
      <div class="section-header">
        <h2>
          <i class="fas fa-bolt"></i>
          Quick Actions
        </h2>
      </div>

      <div class="quick-actions-grid">
        <router-link to="/products/add" class="action-card">
          <div class="action-icon add">
            <i class="fas fa-plus"></i>
          </div>
          <h4>Add Product</h4>
          <p>Add a new product to your store</p>
        </router-link>

        <router-link to="/products" class="action-card">
          <div class="action-icon manage">
            <i class="fas fa-boxes"></i>
          </div>
          <h4>Manage Products</h4>
          <p>View and edit your products</p>
        </router-link>

        <router-link to="/orders" class="action-card">
          <div class="action-icon orders">
            <i class="fas fa-shopping-bag"></i>
          </div>
          <h4>View Orders</h4>
          <p>Check recent customer orders</p>
        </router-link>

        <router-link to="/categories" class="action-card">
          <div class="action-icon categories">
            <i class="fas fa-tags"></i>
          </div>
          <h4>Categories</h4>
          <p>Organize your products</p>
        </router-link>
      </div>
    </div>

    <!-- Recent Products Section (SECOND!) -->
    <div class="dashboard-section">
      <div class="section-header">
        <div>
          <h2>
            <i class="fas fa-clock"></i>
            Recent Products
          </h2>
          <p class="section-subtitle">Your latest added products</p>
        </div>
        <router-link to="/products" class="view-all-link">
          View All
          <i class="fas fa-arrow-right"></i>
        </router-link>
      </div>

      <!-- Recent Products List -->
      <div class="recent-products" v-if="recentProducts.length > 0">
        <div 
          v-for="product in recentProducts" 
          :key="product.id" 
          class="product-card"
        >
          <div class="product-image">
            <img :src="product.image" :alt="product.name" />
            <span 
              class="product-status" 
              :class="product.isActive ? 'status-active' : 'status-inactive'"
            >
              {{ product.isActive ? 'Active' : 'Inactive' }}
            </span>
          </div>

          <div class="product-info">
            <h4>{{ product.name }}</h4>
            <p class="product-category">{{ product.category }}</p>
            <div class="product-details">
              <span class="product-price">₱{{ formatPrice(product.price) }}</span>
              <span 
                class="product-stock" 
                :class="getStockClass(product.stock)"
              >
                {{ product.stock }} in stock
              </span>
            </div>
            <p class="product-date">
              <i class="fas fa-calendar"></i>
              Added {{ formatDate(product.createdAt) }}
            </p>
          </div>

          <div class="product-actions">
            <button 
              class="btn-action btn-view" 
              @click="viewProduct(product.id)"
              title="View Details"
            >
              <i class="fas fa-eye"></i>
            </button>
            <button 
              class="btn-action btn-edit" 
              @click="editProduct(product.id)"
              title="Edit Product"
            >
              <i class="fas fa-edit"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-icon">
          <i class="fas fa-box-open"></i>
        </div>
        <h3>No Products Yet</h3>
        <p>Start by adding your first product to your store</p>
        <router-link to="/products/add" class="btn-add-product">
          <i class="fas fa-plus"></i>
          Add Your First Product
        </router-link>
      </div>
    </div>

    <!-- Product Detail Modal -->
    <ProductDetailModal
      :show="showDetailModal"
      :product="selectedProduct"
      @close="closeDetailModal"
      @edit="editFromDetail"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ProductDetailModal from '@/components/modals/ProductDetailModal.vue'

const router = useRouter()

// Modal states
const showDetailModal = ref(false)
const selectedProduct = ref(null)

// Get products from localStorage
const getStoredProducts = () => {
  const stored = localStorage.getItem('products')
  if (stored) {
    return JSON.parse(stored)
  }
  return []
}

const products = ref(getStoredProducts())

// Computed properties
const totalProducts = computed(() => products.value.length)

const totalRevenue = computed(() => {
  // Calculate total revenue (example calculation)
  return products.value.reduce((sum, product) => {
    return sum + (product.price * 10) // Assuming 10 sales per product
  }, 0)
})

const lowStockCount = computed(() => {
  return products.value.filter(p => p.stock < 10 && p.stock > 0).length
})

// Get 6 most recent products
const recentProducts = computed(() => {
  return [...products.value]
    .sort((a, b) => {
      // Sort by ID (assuming higher ID = more recent)
      return b.id - a.id
    })
    .slice(0, 6)
})

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price)
}

const formatDate = (dateString) => {
  if (!dateString) return 'Recently'
  
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays === 0) return 'Today'
  if (diffDays === 1) return 'Yesterday'
  if (diffDays < 7) return `${diffDays} days ago`
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`
  
  return date.toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getStockClass = (stock) => {
  if (stock === 0) return 'stock-out'
  if (stock < 10) return 'stock-low'
  return 'stock-good'
}

const viewProduct = (productId) => {
  selectedProduct.value = products.value.find(p => p.id === productId)
  showDetailModal.value = true
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedProduct.value = null
}

const editProduct = (productId) => {
  router.push(`/products/${productId}/edit`)
}

const editFromDetail = (productId) => {
  closeDetailModal()
  editProduct(productId)
}

const reloadProducts = () => {
  products.value = getStoredProducts()
}

onMounted(() => {
  console.log('Dashboard loaded')
  reloadProducts()
})
</script>

<style scoped>
.dashboard-page {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.75rem;
  color: #2c3e50;
  margin: 0;
}

.subtitle {
  color: #666;
  margin: 0.5rem 0 0 0;
  font-size: 0.95rem;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1.5rem;
  transition: all 0.3s;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.stat-icon.total-products {
  background: #e3f2fd;
  color: #1976d2;
}

.stat-icon.total-orders {
  background: #f3e5f5;
  color: #7b1fa2;
}

.stat-icon.revenue {
  background: #e8f5e9;
  color: #388e3c;
}

.stat-icon.low-stock {
  background: #fff3e0;
  color: #f57c00;
}

.stat-content {
  flex: 1;
}

.stat-content h3 {
  font-size: 1.75rem;
  margin: 0;
  color: #2c3e50;
}

.stat-content p {
  margin: 0.25rem 0;
  color: #666;
  font-size: 0.9rem;
}

.stat-change {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.85rem;
  font-weight: 600;
  margin-top: 0.5rem;
}

.stat-change.positive {
  color: #28a745;
}

.stat-change.negative {
  color: #dc3545;
}

/* Dashboard Section */
.dashboard-section {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  margin-bottom: 2rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-header h2 {
  font-size: 1.25rem;
  color: #2c3e50;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.section-header h2 i {
  color: #667eea;
}

.section-subtitle {
  color: #999;
  font-size: 0.85rem;
  margin: 0.25rem 0 0 0;
}

.view-all-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s;
}

.view-all-link:hover {
  gap: 0.75rem;
}

/* Quick Actions */
.quick-actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}

.action-card {
  padding: 1.5rem;
  border: 2px solid #f0f0f0;
  border-radius: 12px;
  text-decoration: none;
  color: inherit;
  transition: all 0.3s;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 1rem;
}

.action-card:hover {
  border-color: #667eea;
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.action-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.action-icon.add {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.action-icon.manage {
  background: #e3f2fd;
  color: #1976d2;
}

.action-icon.orders {
  background: #f3e5f5;
  color: #7b1fa2;
}

.action-icon.categories {
  background: #fff3e0;
  color: #f57c00;
}

.action-card h4 {
  margin: 0;
  color: #2c3e50;
  font-size: 1rem;
}

.action-card p {
  margin: 0;
  color: #999;
  font-size: 0.85rem;
}

/* Recent Products */
.recent-products {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

.product-card {
  border: 2px solid #f0f0f0;
  border-radius: 12px;
  padding: 1rem;
  display: flex;
  gap: 1rem;
  transition: all 0.3s;
}

.product-card:hover {
  border-color: #667eea;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.product-image {
  position: relative;
  width: 100px;
  height: 100px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-status {
  position: absolute;
  top: 0.5rem;
  left: 0.5rem;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.7rem;
  font-weight: 600;
}

.product-status.status-active {
  background: #d4edda;
  color: #155724;
}

.product-status.status-inactive {
  background: #f8d7da;
  color: #721c24;
}

.product-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.product-info h4 {
  margin: 0;
  font-size: 1rem;
  color: #2c3e50;
}

.product-category {
  margin: 0;
  font-size: 0.85rem;
  color: #999;
}

.product-details {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-top: 0.25rem;
}

.product-price {
  font-weight: 600;
  color: #667eea;
  font-size: 1.1rem;
}

.product-stock {
  font-size: 0.85rem;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-weight: 600;
}

.product-stock.stock-good {
  background: #d4edda;
  color: #155724;
}

.product-stock.stock-low {
  background: #fff3cd;
  color: #856404;
}

.product-stock.stock-out {
  background: #f8d7da;
  color: #721c24;
}

.product-date {
  margin: 0;
  font-size: 0.8rem;
  color: #999;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.product-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.btn-action {
  width: 36px;
  height: 36px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.btn-view {
  background: #e3f2fd;
  color: #1976d2;
}

.btn-view:hover {
  background: #1976d2;
  color: white;
}

.btn-edit {
  background: #fff3cd;
  color: #ff9800;
}

.btn-edit:hover {
  background: #ff9800;
  color: white;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem 2rem;
}

.empty-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #f8f9fa;
  color: #999;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  margin: 0 auto 1.5rem;
}

.empty-state h3 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
}

.empty-state p {
  margin: 0 0 1.5rem 0;
  color: #666;
}

.btn-add-product {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-add-product:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

/* Responsive */
@media (max-width: 968px) {
  .dashboard-page {
    padding: 1rem;
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .recent-products {
    grid-template-columns: 1fr;
  }

  .quick-actions-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 576px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .quick-actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>
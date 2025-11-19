<template>
  <div class="product-list-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <h1>Products</h1>
          <p class="subtitle">Manage your product inventory</p>
        </div>
        <div class="header-right">
          <router-link to="/products/add" class="btn-add-product">
            <i class="fas fa-plus"></i>
            Add Product
          </router-link>
        </div>
      </div>
    </div>

    <!-- Error Alert -->
    <div v-if="errorMessage" class="error-alert">
      <i class="fas fa-exclamation-circle"></i>
      <div class="error-content">
        <strong>Error</strong>
        <p>{{ errorMessage }}</p>
      </div>
      <button @click="errorMessage = ''" class="close-error">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="loading-container">
      <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
      </div>
      <p>Loading products...</p>
    </div>

    <!-- Content (shown when not loading) -->
    <div v-if="!isLoading">
      <!-- Filters and Search -->
      <div class="filters-section">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search products..."
            @input="handleSearch"
          />
        </div>

        <div class="filter-controls">
          <select v-model="filterCategory" @change="handleFilter" class="filter-select">
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.name">
              {{ cat.name }}
            </option>
          </select>

          <select v-model="filterStatus" @change="handleFilter" class="filter-select">
            <option value="">All Status</option>
            <option value="true">Active</option>
            <option value="false">Inactive</option>
          </select>

          <select v-model="sortBy" @change="handleSort" class="filter-select">
            <option value="created_at">Newest First</option>
            <option value="name">Name (A-Z)</option>
            <option value="price">Price (Low to High)</option>
            <option value="stock">Stock (Low to High)</option>
          </select>
        </div>
      </div>

      <!-- Products Stats -->
      <div class="stats-section">
        <div class="stat-card">
          <div class="stat-icon total">
            <i class="fas fa-box"></i>
          </div>
          <div class="stat-content">
            <h3>{{ totalProducts }}</h3>
            <p>Total Products</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon active">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-content">
            <h3>{{ activeProducts }}</h3>
            <p>Active</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon inactive">
            <i class="fas fa-times-circle"></i>
          </div>
          <div class="stat-content">
            <h3>{{ inactiveProducts }}</h3>
            <p>Inactive</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon low-stock">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="stat-content">
            <h3>{{ lowStockProducts }}</h3>
            <p>Low Stock</p>
          </div>
        </div>
      </div>

      <!-- Products Table -->
      <div class="products-table-container">
        <table class="products-table" v-if="products.length > 0">
          <thead>
            <tr>
              <th>Product</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id">
              <td>
                <div class="product-cell">
                  <div class="product-image">
                    <img :src="product.image_url || product.image || 'https://via.placeholder.com/50'" :alt="product.name" />
                  </div>
                  <div class="product-info">
                    <h4>{{ product.name }}</h4>
                    <p>{{ product.sku }}</p>
                  </div>
                </div>
              </td>
              <td>
                <span class="category-badge">{{ product.category }}</span>
              </td>
              <td>
                <div class="price-cell">
                  <span class="current-price">₱{{ formatPrice(product.price) }}</span>
                  <span v-if="product.compare_price" class="compare-price">
                    ₱{{ formatPrice(product.compare_price) }}
                  </span>
                </div>
              </td>
              <td>
                <span class="stock-badge" :class="getStockClass(product.stock)">
                  {{ product.stock }} units
                </span>
              </td>
              <td>
                <span class="status-badge" :class="product.is_active ? 'status-active' : 'status-inactive'">
                  {{ product.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td>
                <div class="action-buttons">
                  <button class="btn-action btn-view" @click="viewProduct(product.id)" title="View">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button class="btn-action btn-edit" @click="editProduct(product.id)" title="Edit">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn-action btn-delete" @click="confirmDelete(product)" title="Delete">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Empty State -->
        <div v-else class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-box-open"></i>
          </div>
          <h3>No Products Found</h3>
          <p v-if="searchQuery || filterCategory || filterStatus">
            Try adjusting your search or filters
          </p>
          <p v-else>
            You haven't added any products yet
          </p>
          <router-link to="/products/add" class="btn-add-first">
            <i class="fas fa-plus"></i>
            Add Your First Product
          </router-link>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination" v-if="products.length > 0 && totalPages > 1">
        <button class="pagination-btn" :disabled="currentPage === 1" @click="previousPage">
          <i class="fas fa-chevron-left"></i>
          Previous
        </button>
        <div class="pagination-info">
          Page {{ currentPage }} of {{ totalPages }}
        </div>
        <button class="pagination-btn" :disabled="currentPage === totalPages" @click="nextPage">
          Next
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Delete Product?"
      :message="`Are you sure you want to delete '${productToDelete?.name}'? This action cannot be undone.`"
      confirm-text="Delete"
      cancel-text="Cancel"
      icon-class="fas fa-trash"
      :is-loading="isDeleting"
      loading-text="Deleting..."
      @close="cancelDelete"
      @confirm="deleteProduct"
    />

    <!-- Success Modal -->
    <SuccessModal
      :show="showSuccessModal"
      :title="successTitle"
      :message="successMessage"
      primary-text="OK"
      @close="closeSuccessModal"
      @primary="closeSuccessModal"
    />

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
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import SuccessModal from '@/components/modals/SuccessModal.vue'
import ProductDetailModal from '@/components/modals/ProductDetailModal.vue'
import { productService } from '@/services/productService'

const router = useRouter()

// Categories
const categories = ref([
  { id: 1, name: 'Fashion' },
  { id: 2, name: 'Footwear' },
  { id: 3, name: 'Electronics' },
  { id: 4, name: 'Sports & Outdoors' },
  { id: 5, name: 'Home & Living' },
  { id: 6, name: 'Beauty & Personal Care' },
  { id: 7, name: 'Toys & Games' },
  { id: 8, name: 'Books & Stationery' },
  { id: 9, name: 'Accessories' },
  { id: 10, name: 'Food & Beverages' }
])

// State
const products = ref([])
const isLoading = ref(false)
const errorMessage = ref('')

// Filter states
const searchQuery = ref('')
const filterCategory = ref('')
const filterStatus = ref('')
const sortBy = ref('created_at')
const sortOrder = ref('desc')

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(10)
const totalProducts = ref(0)

// Modal states
const showDeleteModal = ref(false)
const showSuccessModal = ref(false)
const showDetailModal = ref(false)
const selectedProduct = ref(null)
const productToDelete = ref(null)
const isDeleting = ref(false)
const successTitle = ref('')
const successMessage = ref('')

// Computed properties
const activeProducts = computed(() => products.value.filter(p => p.is_active).length)
const inactiveProducts = computed(() => products.value.filter(p => !p.is_active).length)
const lowStockProducts = computed(() => products.value.filter(p => p.stock < 10 && p.stock > 0).length)

const totalPages = computed(() => {
  return Math.ceil(totalProducts.value / itemsPerPage.value)
})

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price)
}

const getStockClass = (stock) => {
  if (stock === 0) return 'stock-out'
  if (stock < 10) return 'stock-low'
  return 'stock-good'
}

const loadProducts = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const params = {
      search: searchQuery.value,
      category: filterCategory.value,
      is_active: filterStatus.value,
      sort_by: sortBy.value,
      sort_order: sortOrder.value,
      per_page: itemsPerPage.value,
      page: currentPage.value
    }

    // Remove empty params
    Object.keys(params).forEach(key => {
      if (params[key] === '' || params[key] === null) {
        delete params[key]
      }
    })

    console.log('📥 Loading products with params:', params)

    const response = await productService.getAllProducts(params)
    
    console.log('✅ Products loaded:', response)

    if (response.success) {
      if (response.data.data) {
        // Paginated response
        products.value = response.data.data
        totalProducts.value = response.data.total
        currentPage.value = response.data.current_page
      } else {
        // Non-paginated response
        products.value = response.data
        totalProducts.value = response.data.length
      }
    }

    isLoading.value = false
  } catch (error) {
    console.error('❌ Error loading products:', error)
    errorMessage.value = error.message || 'Failed to load products'
    isLoading.value = false
  }
}

const handleSearch = () => {
  currentPage.value = 1
  loadProducts()
}

const handleFilter = () => {
  currentPage.value = 1
  loadProducts()
}

const handleSort = () => {
  currentPage.value = 1
  loadProducts()
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    loadProducts()
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    loadProducts()
  }
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

const confirmDelete = (product) => {
  productToDelete.value = product
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  productToDelete.value = null
}

const deleteProduct = async () => {
  isDeleting.value = true

  try {
    console.log('🗑️ Deleting product:', productToDelete.value.id)

    const response = await productService.deleteProduct(productToDelete.value.id)
    
    console.log('✅ Product deleted:', response)

    isDeleting.value = false
    showDeleteModal.value = false

    // Show success message
    successTitle.value = 'Product Deleted'
    successMessage.value = `${productToDelete.value.name} has been deleted successfully.`
    showSuccessModal.value = true

    productToDelete.value = null

    // Reload products
    loadProducts()
  } catch (error) {
    console.error('❌ Error deleting product:', error)
    errorMessage.value = error.message || 'Failed to delete product'
    isDeleting.value = false
    showDeleteModal.value = false
  }
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
}

onMounted(() => {
  console.log('✅ ProductList mounted')
  
  // Check if user is authenticated
  const token = localStorage.getItem('seller_token')
  if (!token) {
    console.log('❌ No token found, redirecting to login')
    router.push('/login')
    return
  }

  // Load products from API
  loadProducts()
})
</script>

<style scoped>
.product-list-page {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left h1 {
  font-size: 1.75rem;
  color: #2c3e50;
  margin: 0;
}

.subtitle {
  color: #666;
  margin: 0.25rem 0 0 0;
  font-size: 0.9rem;
}

.btn-add-product {
  padding: 0.875rem 1.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
}

.btn-add-product:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

/* Error Alert */
.error-alert {
  background: #fee;
  border: 2px solid #fcc;
  color: #c33;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  animation: slideDown 0.3s ease;
}

.error-alert i.fa-exclamation-circle {
  font-size: 1.5rem;
  flex-shrink: 0;
}

.error-content {
  flex: 1;
}

.error-content strong {
  display: block;
  margin-bottom: 0.25rem;
  font-size: 1rem;
}

.error-content p {
  margin: 0;
  font-size: 0.9rem;
}

.close-error {
  margin-left: auto;
  background: none;
  border: none;
  color: #c33;
  cursor: pointer;
  font-size: 1.25rem;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.3s;
  flex-shrink: 0;
}

.close-error:hover {
  background: #fcc;
  color: #a00;
}

/* Loading State */
.loading-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 4rem 2rem;
  text-align: center;
}

.loading-spinner {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #f8f9ff;
  color: #667eea;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  margin: 0 auto 1.5rem;
}

.loading-container p {
  color: #666;
  font-size: 1.1rem;
  margin: 0;
}

@keyframes slideDown {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.filters-section {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.search-box {
  flex: 1;
  min-width: 250px;
  position: relative;
}

.search-box i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #999;
}

.search-box input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 3rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.3s;
}

.search-box input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-controls {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.filter-select {
  padding: 0.75rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s;
  background: white;
}

.filter-select:focus {
  outline: none;
  border-color: #667eea;
}

.stats-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
  gap: 1rem;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.stat-icon.total {
  background: #e3f2fd;
  color: #1976d2;
}

.stat-icon.active {
  background: #d4edda;
  color: #28a745;
}

.stat-icon.inactive {
  background: #f8d7da;
  color: #dc3545;
}

.stat-icon.low-stock {
  background: #fff3cd;
  color: #ff9800;
}

.stat-content h3 {
  font-size: 1.75rem;
  margin: 0;
  color: #2c3e50;
}

.stat-content p {
  margin: 0.25rem 0 0 0;
  color: #666;
  font-size: 0.9rem;
}

.products-table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  margin-bottom: 2rem;
}

.products-table {
  width: 100%;
  border-collapse: collapse;
}

.products-table thead {
  background: #f8f9fa;
}

.products-table th {
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #2c3e50;
  font-size: 0.9rem;
  border-bottom: 2px solid #e0e0e0;
}

.products-table td {
  padding: 1rem;
  border-bottom: 1px solid #f0f0f0;
}

.products-table tbody tr:hover {
  background: #f8f9ff;
}

.product-cell {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.product-image {
  width: 50px;
  height: 50px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info h4 {
  margin: 0;
  font-size: 0.95rem;
  color: #2c3e50;
}

.product-info p {
  margin: 0.25rem 0 0 0;
  font-size: 0.85rem;
  color: #999;
}

.category-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background: #e3f2fd;
  color: #1976d2;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
}

.price-cell {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.current-price {
  font-weight: 600;
  color: #2c3e50;
}

.compare-price {
  font-size: 0.85rem;
  color: #999;
  text-decoration: line-through;
}

.stock-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
}

.stock-badge.stock-good {
  background: #d4edda;
  color: #155724;
}

.stock-badge.stock-low {
  background: #fff3cd;
  color: #856404;
}

.stock-badge.stock-out {
  background: #f8d7da;
  color: #721c24;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
}

.status-badge.status-active {
  background: #d4edda;
  color: #155724;
}

.status-badge.status-inactive {
  background: #f8d7da;
  color: #721c24;
}

.action-buttons {
  display: flex;
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
  font-size: 0.9rem;
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

.btn-delete {
  background: #f8d7da;
  color: #dc3545;
}

.btn-delete:hover {
  background: #dc3545;
  color: white;
}

.empty-state {
  padding: 4rem 2rem;
  text-align: center;
}

.empty-icon {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: #f8f9fa;
  color: #999;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  margin: 0 auto 2rem;
}

.empty-state h3 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
}

.empty-state p {
  margin: 0 0 2rem 0;
  color: #666;
}

.btn-add-first {
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

.btn-add-first:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2rem;
}

.pagination-btn {
  padding: 0.75rem 1.5rem;
  background: white;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s;
}

.pagination-btn:hover:not(:disabled) {
  border-color: #667eea;
  color: #667eea;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-info {
  color: #666;
  font-weight: 600;
}

@media (max-width: 968px) {
  .product-list-page {
    padding: 1rem;
  }

  .header-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .filters-section {
    flex-direction: column;
  }

  .filter-controls {
    width: 100%;
  }

  .filter-select {
    flex: 1;
  }

  .stats-section {
    grid-template-columns: repeat(2, 1fr);
  }

  .products-table-container {
    overflow-x: auto;
  }

  .products-table {
    min-width: 800px;
  }
}
</style>
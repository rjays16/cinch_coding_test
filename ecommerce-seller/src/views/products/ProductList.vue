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
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>

        <select v-model="filterStatus" @change="handleFilter" class="filter-select">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>

        <select v-model="sortBy" @change="handleSort" class="filter-select">
          <option value="newest">Newest First</option>
          <option value="oldest">Oldest First</option>
          <option value="name-asc">Name (A-Z)</option>
          <option value="name-desc">Name (Z-A)</option>
          <option value="price-asc">Price (Low to High)</option>
          <option value="price-desc">Price (High to Low)</option>
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
      <table class="products-table" v-if="filteredProducts.length > 0">
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
          <tr v-for="product in filteredProducts" :key="product.id">
            <td>
              <div class="product-cell">
                <div class="product-image">
                  <img :src="product.image" :alt="product.name" />
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
                <span v-if="product.comparePrice" class="compare-price">
                  ₱{{ formatPrice(product.comparePrice) }}
                </span>
              </div>
            </td>
            <td>
              <span class="stock-badge" :class="getStockClass(product.stock)">
                {{ product.stock }} units
              </span>
            </td>
            <td>
              <span class="status-badge" :class="product.isActive ? 'status-active' : 'status-inactive'">
                {{ product.isActive ? 'Active' : 'Inactive' }}
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
        <p>{{ searchQuery ? 'Try adjusting your search or filters' : 'Start by adding your first product' }}</p>
        <router-link to="/products/add" class="btn-add-first" v-if="!searchQuery">
          <i class="fas fa-plus"></i>
          Add Your First Product
        </router-link>
      </div>
    </div>

    <!-- Pagination -->
    <div class="pagination" v-if="filteredProducts.length > 0">
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
  </div>

     <!-- Product Detail Modal -->
    <ProductDetailModal
      :show="showDetailModal"
      :product="selectedProduct"
      @close="closeDetailModal"
      @edit="editFromDetail"
    />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import SuccessModal from '@/components/modals/SuccessModal.vue'
import ProductDetailModal from '@/components/modals/ProductDetailModal.vue'

const router = useRouter()

// Categories (same as AddProduct)
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

// Get products from localStorage or use static data
const getStoredProducts = () => {
  const stored = localStorage.getItem('products')
  if (stored) {
    return JSON.parse(stored)
  }
  
  // Default static products
  const defaultProducts = [
    {
      id: 1,
      name: 'Premium Cotton T-Shirt',
      description: 'Soft and comfortable premium cotton t-shirt. Perfect for everyday wear with a modern fit and breathable fabric.',
      sku: 'PROD-001',
      category: 'Fashion',
      brand: 'StyleCo',
      price: 599,
      comparePrice: 799,
      stock: 50,
      isActive: true,
      sizes: ['S', 'M', 'L', 'XL'],
      color: 'Navy Blue',
      weight: 0.2,
      material: 'Cotton',
      image: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=200&h=200&fit=crop',
      createdAt: '2024-01-15',
      updatedAt: '2024-11-15'
    },
    {
      id: 2,
      name: 'Wireless Bluetooth Headphones',
      description: 'High-quality wireless headphones with noise cancellation, 30-hour battery life, and premium sound quality.',
      sku: 'PROD-002',
      category: 'Electronics',
      brand: 'AudioPro',
      price: 2499,
      comparePrice: 3499,
      stock: 25,
      isActive: true,
      color: 'Black',
      weight: 0.3,
      material: 'Plastic, Metal',
      image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=200&h=200&fit=crop',
      createdAt: '2024-02-10',
      updatedAt: '2024-11-18'
    },
    {
      id: 3,
      name: 'Running Shoes',
      description: 'Professional running shoes with advanced cushioning and breathable mesh upper. Ideal for marathon training.',
      sku: 'PROD-003',
      category: 'Footwear',
      brand: 'RunFast',
      price: 3999,
      comparePrice: null,
      stock: 8,
      isActive: true,
      sizes: ['39', '40', '41', '42', '43'],
      color: 'White/Red',
      weight: 0.5,
      material: 'Mesh, Rubber',
      image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&h=200&fit=crop',
      createdAt: '2024-03-05',
      updatedAt: '2024-11-10'
    },
    {
      id: 4,
      name: 'Yoga Mat',
      description: 'Eco-friendly yoga mat with excellent grip and cushioning. Perfect for yoga, pilates, and stretching exercises.',
      sku: 'PROD-004',
      category: 'Sports & Outdoors',
      brand: 'ZenFit',
      price: 899,
      comparePrice: 1299,
      stock: 0,
      isActive: false,
      color: 'Purple',
      weight: 1.2,
      material: 'TPE',
      image: 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=200&h=200&fit=crop',
      createdAt: '2024-04-20',
      updatedAt: '2024-10-30'
    },
    {
      id: 5,
      name: 'Smart Watch',
      description: 'Feature-packed smart watch with heart rate monitor, GPS, and 5-day battery life. Water resistant up to 50m.',
      sku: 'PROD-005',
      category: 'Electronics',
      brand: 'TechTime',
      price: 4999,
      comparePrice: 6999,
      stock: 15,
      isActive: true,
      color: 'Silver',
      weight: 0.15,
      material: 'Aluminum, Silicone',
      image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200&h=200&fit=crop',
      createdAt: '2024-05-12',
      updatedAt: '2024-11-19'
    },
    {
      id: 6,
      name: 'Leather Wallet',
      description: 'Handcrafted genuine leather wallet with RFID protection. Multiple card slots and bill compartments.',
      sku: 'PROD-006',
      category: 'Accessories',
      brand: 'LeatherCraft',
      price: 799,
      comparePrice: null,
      stock: 30,
      isActive: true,
      color: 'Brown',
      weight: 0.1,
      material: 'Genuine Leather',
      image: 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=200&h=200&fit=crop',
      createdAt: '2024-06-08',
      updatedAt: '2024-11-12'
    },
    {
      id: 7,
      name: 'Organic Face Cream',
      description: 'Natural organic face cream with vitamin E and hyaluronic acid. Suitable for all skin types.',
      sku: 'PROD-007',
      category: 'Beauty & Personal Care',
      brand: 'NaturalGlow',
      price: 1299,
      comparePrice: 1599,
      stock: 5,
      isActive: true,
      color: 'White',
      weight: 0.05,
      material: 'Organic Ingredients',
      image: 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=200&h=200&fit=crop',
      createdAt: '2024-07-15',
      updatedAt: '2024-11-14'
    },
    {
      id: 8,
      name: 'Gaming Mouse',
      description: 'Professional gaming mouse with RGB lighting, 16000 DPI, and programmable buttons. Ultra-precise sensor.',
      sku: 'PROD-008',
      category: 'Electronics',
      brand: 'GamePro',
      price: 1899,
      comparePrice: 2499,
      stock: 20,
      isActive: true,
      color: 'Black/RGB',
      weight: 0.12,
      material: 'Plastic, Metal',
      image: 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=200&h=200&fit=crop',
      createdAt: '2024-08-22',
      updatedAt: '2024-11-16'
    }
  ]
  
  // Save default products to localStorage
  localStorage.setItem('products', JSON.stringify(defaultProducts))
  return defaultProducts
}

// Load products from storage
const products = ref(getStoredProducts())

// Filter states
const searchQuery = ref('')
const filterCategory = ref('')
const filterStatus = ref('')
const sortBy = ref('newest')

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(10)

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
const totalProducts = computed(() => products.value.length)
const activeProducts = computed(() => products.value.filter(p => p.isActive).length)
const inactiveProducts = computed(() => products.value.filter(p => !p.isActive).length)
const lowStockProducts = computed(() => products.value.filter(p => p.stock < 10 && p.stock > 0).length)

const filteredProducts = computed(() => {
  let filtered = [...products.value]

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(p => 
      p.name.toLowerCase().includes(query) ||
      p.sku.toLowerCase().includes(query) ||
      p.category.toLowerCase().includes(query)
    )
  }

  // Category filter
  if (filterCategory.value) {
    filtered = filtered.filter(p => p.category === getCategoryName(filterCategory.value))
  }

  // Status filter
  if (filterStatus.value) {
    filtered = filtered.filter(p => 
      filterStatus.value === 'active' ? p.isActive : !p.isActive
    )
  }

  // Sorting
  switch (sortBy.value) {
    case 'newest':
      filtered.sort((a, b) => b.id - a.id)
      break
    case 'oldest':
      filtered.sort((a, b) => a.id - b.id)
      break
    case 'name-asc':
      filtered.sort((a, b) => a.name.localeCompare(b.name))
      break
    case 'name-desc':
      filtered.sort((a, b) => b.name.localeCompare(a.name))
      break
    case 'price-asc':
      filtered.sort((a, b) => a.price - b.price)
      break
    case 'price-desc':
      filtered.sort((a, b) => b.price - a.price)
      break
  }

  return filtered
})

const totalPages = computed(() => {
  return Math.ceil(filteredProducts.value.length / itemsPerPage.value)
})

// Methods
const getCategoryName = (categoryId) => {
  const cat = categories.value.find(c => c.id === parseInt(categoryId))
  return cat ? cat.name : ''
}

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

const handleSearch = () => {
  currentPage.value = 1
}

const handleFilter = () => {
  currentPage.value = 1
}

const handleSort = () => {
  currentPage.value = 1
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const reloadProducts = () => {
  products.value = getStoredProducts()
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

const deleteProduct = () => {
  isDeleting.value = true

  // Simulate API call
  setTimeout(() => {
    const index = products.value.findIndex(p => p.id === productToDelete.value.id)
    if (index !== -1) {
      products.value.splice(index, 1)
      // Update localStorage
      localStorage.setItem('products', JSON.stringify(products.value))
    }

    isDeleting.value = false
    showDeleteModal.value = false

    // Show success message
    successTitle.value = 'Product Deleted'
    successMessage.value = `${productToDelete.value.name} has been deleted successfully.`
    showSuccessModal.value = true

    productToDelete.value = null
  }, 1500)
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
}

onMounted(() => {
  console.log('Product List loaded')
  // Reload products when returning to page
  reloadProducts()
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
<template>
  <div class="products-page">
    <!-- Page Header with Image Overlay -->
    <div class="page-header">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <h1>Our Products</h1>
        <p class="subtitle">Discover amazing products from our sellers</p>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
      <div class="container">
        <div class="filters-bar">
          <!-- Search -->
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search products..."
              @input="handleSearch"
            />
          </div>

          <!-- Category Filter -->
          <select v-model="filterCategory" @change="handleFilter" class="filter-select">
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat" :value="cat">
              {{ cat }}
            </option>
          </select>

          <!-- Sort -->
          <select v-model="sortBy" @change="handleSort" class="filter-select">
            <option value="created_at">Newest</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
            <option value="name">Name: A-Z</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="products-section">
      <div class="container">
        <!-- Loading State -->
        <div v-if="isLoading" class="loading-container">
          <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
          </div>
          <p>Loading products...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="errorMessage" class="error-container">
          <i class="fas fa-exclamation-circle"></i>
          <p>{{ errorMessage }}</p>
          <button @click="loadProducts" class="btn-retry">Try Again</button>
        </div>

        <!-- Products Grid -->
        <div v-else-if="products.length > 0" class="products-grid">
          <div 
            v-for="product in products" 
            :key="product.id" 
            class="product-card"
            @click="viewProduct(product.id)"
          >
            <div class="product-image">
              <img :src="getProductImage(product)" :alt="product.name" />
              <div class="product-badges">
                <span v-if="product.compare_price && product.compare_price > product.price" class="badge-discount">
                  -{{ calculateDiscount(product) }}%
                </span>
                <span v-if="product.stock < 10 && product.stock > 0" class="badge-stock">
                  Low Stock
                </span>
                <span v-if="product.stock === 0" class="badge-out">
                  Out of Stock
                </span>
              </div>
            </div>

            <div class="product-info">
              <span class="product-category">{{ product.category }}</span>
              <h3 class="product-name">{{ product.name }}</h3>
              <p class="product-description">{{ truncateText(product.description, 80) }}</p>
              
              <div class="product-pricing">
                <div class="prices">
                  <span class="current-price">₱{{ formatPrice(product.price) }}</span>
                  <span v-if="product.compare_price && product.compare_price > product.price" class="compare-price">
                    ₱{{ formatPrice(product.compare_price) }}
                  </span>
                </div>
              </div>

              <button class="btn-view-product">
                <i class="fas fa-eye"></i>
                View Details
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state">
          <i class="fas fa-box-open"></i>
          <h3>No Products Found</h3>
          <p v-if="searchQuery || filterCategory">Try adjusting your filters</p>
          <p v-else>No products available at the moment</p>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="pagination">
          <button 
            class="pagination-btn" 
            :disabled="currentPage === 1" 
            @click="previousPage"
          >
            <i class="fas fa-chevron-left"></i>
            Previous
          </button>
          <span class="pagination-info">Page {{ currentPage }} of {{ totalPages }}</span>
          <button 
            class="pagination-btn" 
            :disabled="currentPage === totalPages" 
            @click="nextPage"
          >
            Next
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { productService } from '@/services/productService'

const router = useRouter()

// State
const products = ref([])
const categories = ref([])
const isLoading = ref(false)
const errorMessage = ref('')

// Filters
const searchQuery = ref('')
const filterCategory = ref('')
const sortBy = ref('created_at')

// Pagination
const currentPage = ref(1)
const totalPages = ref(1)
const totalProducts = ref(0)
const perPage = 12

// Methods
const loadProducts = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const params = {
      search: searchQuery.value,
      category: filterCategory.value,
      per_page: perPage,
      page: currentPage.value
    }

    // Handle sorting
    if (sortBy.value === 'price_asc') {
      params.sort_by = 'price'
      params.sort_order = 'asc'
    } else if (sortBy.value === 'price_desc') {
      params.sort_by = 'price'
      params.sort_order = 'desc'
    } else if (sortBy.value === 'name') {
      params.sort_by = 'name'
      params.sort_order = 'asc'
    } else {
      params.sort_by = 'created_at'
      params.sort_order = 'desc'
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
        totalPages.value = response.data.last_page
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

const loadCategories = async () => {
  try {
    const response = await productService.getAllProducts({ per_page: 'all' })
    if (response.success) {
      const uniqueCategories = [...new Set(response.data.map(p => p.category))]
      categories.value = uniqueCategories.sort()
    }
  } catch (error) {
    console.error('❌ Error loading categories:', error)
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
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    loadProducts()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const viewProduct = (productId) => {
  router.push(`/products/${productId}`)
}

const getProductImage = (product) => {
  return product.image_url || product.image || 'https://via.placeholder.com/300'
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price)
}

const calculateDiscount = (product) => {
  if (product.compare_price && product.price) {
    const discount = ((product.compare_price - product.price) / product.compare_price) * 100
    return Math.round(discount)
  }
  return 0
}

const truncateText = (text, length) => {
  if (!text) return ''
  if (text.length <= length) return text
  return text.substring(0, length) + '...'
}

onMounted(() => {
  console.log('✅ Products page mounted')
  loadProducts()
  loadCategories()
})
</script>

<style scoped>
.products-page {
  min-height: 100vh;
  background: #f8f9fa;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Page Header - Image Overlay (Same as Home.vue) */
.page-header {
  position: relative;
  height: 400px;
  background-image: url('@/assets/images/photo-overlay.jpg');
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-align: center;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
}

.hero-content {
  position: relative;
  z-index: 1;
}

.page-header h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
  font-weight: bold;
}

.subtitle {
  font-size: 1.25rem;
  margin: 0;
  opacity: 0.95;
}

/* Filters Section */
.filters-section {
  background: white;
  border-bottom: 1px solid #e0e0e0;
  padding: 1.5rem 0;
  position: sticky;
  top: 0;
  z-index: 100;
}

.filters-bar {
  display: flex;
  gap: 1rem;
  align-items: center;
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
}

.filter-select {
  padding: 0.75rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s;
  background: white;
  min-width: 180px;
}

.filter-select:focus {
  outline: none;
  border-color: #667eea;
}

/* Products Section */
.products-section {
  padding: 2rem 0 4rem 0;
}

.loading-container,
.error-container {
  text-align: center;
  padding: 4rem 2rem;
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

.loading-container p,
.error-container p {
  color: #666;
  font-size: 1.1rem;
  margin: 0;
}

.error-container i {
  font-size: 3rem;
  color: #e74c3c;
  margin-bottom: 1rem;
}

.btn-retry {
  margin-top: 1rem;
  padding: 0.75rem 1.5rem;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-retry:hover {
  background: #5568d3;
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.product-image {
  position: relative;
  width: 100%;
  height: 280px;
  overflow: hidden;
  background: #f8f9fa;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.product-card:hover .product-image img {
  transform: scale(1.1);
}

.product-badges {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.badge-discount,
.badge-stock,
.badge-out {
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
}

.badge-discount {
  background: #e74c3c;
  color: white;
}

.badge-stock {
  background: #ff9800;
  color: white;
}

.badge-out {
  background: #666;
  color: white;
}

.product-info {
  padding: 1.5rem;
}

.product-category {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background: #e3f2fd;
  color: #1976d2;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.product-name {
  margin: 1rem 0 0.5rem 0;
  font-size: 1.1rem;
  color: #2c3e50;
  line-height: 1.4;
}

.product-description {
  margin: 0 0 1rem 0;
  color: #666;
  font-size: 0.9rem;
  line-height: 1.5;
}

.product-pricing {
  margin: 1rem 0;
}

.prices {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.current-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #667eea;
}

.compare-price {
  font-size: 1rem;
  color: #999;
  text-decoration: line-through;
}

.btn-view-product {
  width: 100%;
  padding: 0.875rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-view-product:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-state i {
  font-size: 4rem;
  color: #999;
  margin-bottom: 1.5rem;
}

.empty-state h3 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
}

.empty-state p {
  margin: 0;
  color: #666;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2rem;
  margin-top: 3rem;
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

/* Responsive */
@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }

  .page-header {
    height: 300px;
  }

  .page-header h1 {
    font-size: 2rem;
  }

  .subtitle {
    font-size: 1rem;
  }

  .filters-bar {
    flex-direction: column;
  }

  .search-box {
    width: 100%;
  }

  .filter-select {
    width: 100%;
  }

  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.5rem;
  }
}
</style>
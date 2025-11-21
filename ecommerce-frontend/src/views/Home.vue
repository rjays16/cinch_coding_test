<template>
  <Layout>
    <div class="home">
      <!-- Hero Section -->
      <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
          <h1>Discover Amazing Products</h1>
          <p>Shop the latest trends from our verified sellers</p>
          <button class="btn-hero" @click="scrollToProducts">Shop Now</button>
        </div>
      </section>

      <!-- Products Section -->
      <section class="products-section" ref="productsSection">
        <div class="container">
          <h2 class="section-title">Trending Products</h2>
          
          <!-- Search and Filter Bar -->
          <div class="filters-container">
            <!-- Search Box -->
            <div class="search-box">
              <i class="fas fa-search search-icon"></i>
              <input 
                type="text" 
                v-model="searchQuery"
                @input="handleSearch"
                placeholder="Search products..." 
                class="search-input"
              />
              <button 
                v-if="searchQuery" 
                @click="clearSearch" 
                class="btn-clear-search"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>

            <!-- Category Filter -->
            <div class="category-filter">
              <button 
                @click="filterByCategory('all')"
                :class="['category-btn', { active: selectedCategory === 'all' }]"
              >
                All
              </button>
              <button 
                v-for="category in categories"
                :key="category"
                @click="filterByCategory(category)"
                :class="['category-btn', { active: selectedCategory === category }]"
              >
                {{ category }}
              </button>
            </div>

            <!-- Results Count -->
            <div class="results-info">
              <p v-if="searchQuery || selectedCategory !== 'all'">
                Showing {{ filteredProducts.length }} of {{ allProducts.length }} products
                <span v-if="searchQuery"> for "<strong>{{ searchQuery }}</strong>"</span>
                <span v-if="selectedCategory !== 'all'"> in <strong>{{ selectedCategory }}</strong></span>
              </p>
            </div>
          </div>
          
          <!-- Loading State -->
          <div v-if="isLoading" class="loading-container">
            <div class="loading-spinner">
              <i class="fas fa-spinner fa-spin"></i>
            </div>
            <p>Loading products...</p>
          </div>

          <!-- Products Grid -->
          <div v-else-if="displayedProducts.length > 0" class="products-grid">
            <div 
              v-for="product in displayedProducts" 
              :key="product.id" 
              class="product-card"
              @click="viewProductDetail(product)"
            >
              <!-- Product Image -->
              <div class="product-image-wrapper">
                <img :src="getProductImage(product)" :alt="product.name" class="product-image" />
                <div class="product-badges">
                  <span v-if="product.compare_price && product.compare_price > product.price" class="badge-discount">
                    -{{ calculateDiscount(product) }}%
                  </span>
                  <span v-if="product.stock === 0" class="badge-out">
                    Out of Stock
                  </span>
                </div>
                <button 
                  class="btn-quick-view" 
                  @click.stop="quickView(product)"
                  :disabled="product.stock === 0"
                >
                  <i class="fas fa-eye"></i>
                  Quick View
                </button>
              </div>

              <!-- Product Info -->
              <div class="product-details">
                <p class="product-category">{{ product.category }}</p>
                <h3 class="product-name">{{ product.name }}</h3>
                <div class="product-pricing">
                  <span class="product-price">₱{{ formatPrice(product.price) }}</span>
                  <span v-if="product.compare_price && product.compare_price > product.price" class="product-compare-price">
                    ₱{{ formatPrice(product.compare_price) }}
                  </span>
                </div>
                <div v-if="product.seller" class="product-seller">
                  <i class="fas fa-store"></i>
                  {{ product.seller.store_name }}
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>No Products Found</h3>
            <p v-if="searchQuery || selectedCategory !== 'all'">
              Try adjusting your search or filter
            </p>
            <p v-else>
              Check back later for new products
            </p>
          </div>

          <!-- Load More Button -->
          <div v-if="showLoadMore" class="view-all-container">
            <button @click="loadMoreProducts" class="btn-load-more">
              <i class="fas fa-sync-alt"></i>
              Load More Products
              <span class="products-count">({{ filteredProducts.length - displayedProducts.length }} remaining)</span>
            </button>
          </div>
        </div>
      </section>

      <!-- Features Section -->
      <section class="features-section">
        <div class="container">
          <div class="features-grid">
            <div class="feature-box">
              <div class="feature-icon">🚚</div>
              <h3>Fast Delivery</h3>
              <p>Quick shipping to your doorstep</p>
            </div>
            <div class="feature-box">
              <div class="feature-icon">💳</div>
              <h3>Secure Payment</h3>
              <p>100% secure checkout</p>
            </div>
            <div class="feature-box">
              <div class="feature-icon">↩️</div>
              <h3>Easy Returns</h3>
              <p>Hassle-free return policy</p>
            </div>
            <div class="feature-box">
              <div class="feature-icon">⭐</div>
              <h3>Quality Products</h3>
              <p>From verified sellers</p>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Product Quick View Modal -->
    <ProductQuickViewModal
      :show="showQuickView"
      :product="selectedProduct"
      @close="closeQuickView"
    />
  </Layout>
</template>

<script>
import Layout from '@/components/layout/Layout.vue'
import ProductQuickViewModal from '@/components/modals/ProductQuickViewModal.vue'
import { productService } from '@/services/productService'

export default {
  name: 'HomeView',
  components: {
    Layout,
    ProductQuickViewModal
  },
  data() {
    return {
      allProducts: [],
      displayedProducts: [],
      isLoading: false,
      showQuickView: false,
      selectedProduct: null,
      displayCount: 9,
      loadMoreCount: 6,
      searchQuery: '',
      selectedCategory: 'all',
      categories: []
    }
  },
  computed: {
    filteredProducts() {
      let filtered = [...this.allProducts]

      // Filter by category
      if (this.selectedCategory !== 'all') {
        filtered = filtered.filter(p => p.category === this.selectedCategory)
      }

      // Filter by search query
      if (this.searchQuery.trim()) {
        const query = this.searchQuery.toLowerCase().trim()
        filtered = filtered.filter(product => 
          product.name.toLowerCase().includes(query) ||
          product.category.toLowerCase().includes(query) ||
          (product.description && product.description.toLowerCase().includes(query)) ||
          (product.brand && product.brand.toLowerCase().includes(query))
        )
      }

      return filtered
    },
    hasMoreProducts() {
      return this.displayedProducts.length < this.filteredProducts.length
    },
    showLoadMore() {
      return this.filteredProducts.length > 6 && this.hasMoreProducts
    }
  },
  watch: {
    filteredProducts: {
      handler(newFiltered) {
        this.displayedProducts = newFiltered.slice(0, this.displayCount)
      },
      immediate: true
    }
  },
  methods: {
    async loadProducts() {
      this.isLoading = true

      try {
        console.log('📥 Loading featured products...')

        const response = await productService.getAllProducts({
          per_page: 'all',
          sort_by: 'created_at',
          sort_order: 'desc'
        })
        
        console.log('✅ Products loaded:', response)

        if (response.success) {
          this.allProducts = response.data
          this.extractCategories()
          this.displayedProducts = this.filteredProducts.slice(0, this.displayCount)
        }

        this.isLoading = false
      } catch (error) {
        console.error('Error loading products:', error)
        this.isLoading = false
      }
    },

    extractCategories() {
      const uniqueCategories = [...new Set(this.allProducts.map(p => p.category))]
      this.categories = uniqueCategories.filter(c => c).sort()
    },

    handleSearch() {
      this.displayCount = 9
    },

    clearSearch() {
      this.searchQuery = ''
      this.displayCount = 9
    },

    filterByCategory(category) {
      this.selectedCategory = category
      this.displayCount = 9
      this.$nextTick(() => {
        this.scrollToProducts()
      })
    },

    resetFilters() {
      this.searchQuery = ''
      this.selectedCategory = 'all'
      this.displayCount = 9
    },

    loadMoreProducts() {
      this.displayCount += this.loadMoreCount
      this.displayedProducts = this.filteredProducts.slice(0, this.displayCount)
      
      this.$nextTick(() => {
        const newProducts = document.querySelectorAll('.product-card')
        const scrollIndex = this.displayCount - this.loadMoreCount
        if (newProducts[scrollIndex]) {
          newProducts[scrollIndex].scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
          })
        }
      })
    },

    getProductImage(product) {
      return product.image_url || product.image || 'https://via.placeholder.com/400'
    },

    formatPrice(price) {
      return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(price)
    },

    calculateDiscount(product) {
      if (product.compare_price && product.price) {
        const discount = ((product.compare_price - product.price) / product.compare_price) * 100
        return Math.round(discount)
      }
      return 0
    },

    quickView(product) {
      this.selectedProduct = product
      this.showQuickView = true
    },

    closeQuickView() {
      this.showQuickView = false
    },

    viewProductDetail(product) {
      this.$router.push(`/products/${product.id}`)
    },

    scrollToProducts() {
      this.$refs.productsSection?.scrollIntoView({ behavior: 'smooth' })
    }
  },
  mounted() {
    console.log('✅ Home page mounted')
    this.loadProducts()
  }
}
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Hero Section */
.hero {
  position: relative;
  height: 500px;
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

.hero h1 {
  font-size: 3.5rem;
  margin-bottom: 1rem;
  font-weight: bold;
}

.hero p {
  font-size: 1.5rem;
  margin-bottom: 2rem;
  opacity: 0.95;
}

.btn-hero {
  background-color: white;
  color: #667eea;
  border: none;
  padding: 1rem 3rem;
  font-size: 1.2rem;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-hero:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

/* Products Section */
.products-section {
  padding: 4rem 0;
  background-color: #f9f9f9;
}

.section-title {
  text-align: center;
  font-size: 2.5rem;
  margin-bottom: 2rem;
  color: #2c3e50;
  font-weight: bold;
}

/* Filters Container */
.filters-container {
  margin-bottom: 2rem;
}

/* Search Box */
.search-box {
  position: relative;
  max-width: 600px;
  margin: 0 auto 1.5rem;
}

.search-icon {
  position: absolute;
  left: 1.25rem;
  top: 50%;
  transform: translateY(-50%);
  color: #999;
  font-size: 1.1rem;
}

.search-input {
  width: 100%;
  padding: 1rem 3.5rem 1rem 3.5rem;
  border: 2px solid #e0e0e0;
  border-radius: 50px;
  font-size: 1rem;
  transition: all 0.3s;
}

.search-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.btn-clear-search {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: #f0f0f0;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.btn-clear-search:hover {
  background: #e0e0e0;
}

/* Category Filter */
.category-filter {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.category-btn {
  padding: 0.75rem 1.5rem;
  border: 2px solid #e0e0e0;
  background: white;
  border-radius: 50px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  color: #666;
}

.category-btn:hover {
  border-color: #667eea;
  color: #667eea;
  transform: translateY(-2px);
}

.category-btn.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-color: transparent;
}

/* Results Info */
.results-info {
  text-align: center;
  color: #666;
  font-size: 0.95rem;
  margin-top: 1rem;
}

.results-info strong {
  color: #667eea;
  font-weight: 600;
}

/* Loading State */
.loading-container {
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

.loading-container p {
  color: #666;
  font-size: 1.1rem;
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
  margin: 0 0 1.5rem 0;
  color: #666;
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

/* Product Card */
.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.product-image-wrapper {
  position: relative;
  overflow: hidden;
  height: 300px;
  background: #f8f9fa;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.product-card:hover .product-image {
  transform: scale(1.1);
}

.product-badges {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  z-index: 2;
}

.badge-discount,
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

.badge-out {
  background: #666;
  color: white;
}

.btn-quick-view {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 1rem;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transform: translateY(100%);
  transition: transform 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  z-index: 1;
}

.product-card:hover .btn-quick-view {
  transform: translateY(0);
}

.btn-quick-view:hover {
  background: linear-gradient(135deg, #5568d3 0%, #6a3d8f 100%);
}

.btn-quick-view:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.product-details {
  padding: 1.5rem;
}

.product-category {
  color: #999;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 0.5rem;
}

.product-name {
  font-size: 1.2rem;
  margin: 0 0 0.75rem 0;
  color: #2c3e50;
  font-weight: 600;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-pricing {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
}

.product-price {
  font-size: 1.5rem;
  font-weight: bold;
  color: #667eea;
}

.product-compare-price {
  font-size: 1rem;
  color: #999;
  text-decoration: line-through;
}

.product-seller {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #666;
  font-size: 0.9rem;
  padding-top: 0.75rem;
  border-top: 1px solid #f0f0f0;
}

.product-seller i {
  color: #667eea;
}

/* Load More Container */
.view-all-container {
  text-align: center;
  margin-top: 3rem;
}

.btn-load-more {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #42b983 0%, #38a169 100%);
  color: white;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1.1rem;
  transition: all 0.3s;
  border: none;
  cursor: pointer;
}

.btn-load-more:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(66, 185, 131, 0.3);
}

.products-count {
  font-size: 0.9rem;
  opacity: 0.9;
  font-weight: 500;
}

/* Features Section */
.features-section {
  padding: 3rem 0;
  background-color: white;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.feature-box {
  text-align: center;
  padding: 2rem;
  transition: transform 0.3s;
}

.feature-box:hover {
  transform: translateY(-5px);
}

.feature-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.feature-box h3 {
  font-size: 1.3rem;
  margin-bottom: 0.5rem;
  color: #2c3e50;
}

.feature-box p {
  color: #666;
}

/* Responsive */
@media (max-width: 768px) {
  .hero h1 {
    font-size: 2rem;
  }

  .hero p {
    font-size: 1rem;
  }

  .section-title {
    font-size: 2rem;
  }

  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.5rem;
  }

  .product-image-wrapper {
    height: 250px;
  }

  .search-input {
    font-size: 0.95rem;
    padding: 0.875rem 3rem 0.875rem 3rem;
  }

  .category-btn {
    padding: 0.625rem 1.25rem;
    font-size: 0.9rem;
  }
}
</style>

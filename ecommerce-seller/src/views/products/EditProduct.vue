<template>
  <div class="edit-product-page">
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <router-link to="/products" class="back-button">
            <i class="fas fa-arrow-left"></i>
          </router-link>
          <div>
            <h1>Edit Product</h1>
            <p class="subtitle">Update product information</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="loading-container">
      <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
      </div>
      <p>Loading product...</p>
    </div>

    <!-- Product Not Found -->
    <div v-else-if="!product" class="not-found-container">
      <div class="not-found-icon">
        <i class="fas fa-box-open"></i>
      </div>
      <h3>Product Not Found</h3>
      <p>The product you're looking for doesn't exist.</p>
      <router-link to="/products" class="btn-back">
        <i class="fas fa-arrow-left"></i>
        Back to Products
      </router-link>
    </div>

    <!-- Edit Form -->
    <div v-else class="form-container">
      <form @submit.prevent="handleSubmit" class="product-form">
        <!-- Basic Information Section -->
        <div class="form-section">
          <h2 class="section-title">
            <i class="fas fa-info-circle"></i>
            Basic Information
          </h2>

          <div class="form-row">
            <div class="form-group full-width">
              <label for="productName">Product Name <span class="required">*</span></label>
              <input
                type="text"
                id="productName"
                v-model="product.name"
                placeholder="Enter product name"
                required
              />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group full-width">
              <label for="description">Description <span class="required">*</span></label>
              <textarea
                id="description"
                v-model="product.description"
                placeholder="Describe your product..."
                rows="5"
                required
              ></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="category">Category <span class="required">*</span></label>
              <select id="category" v-model="product.category" required @change="handleCategoryChange">
                <option value="">Select a category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.name">
                  {{ cat.name }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label for="brand">Brand</label>
              <input
                type="text"
                id="brand"
                v-model="product.brand"
                placeholder="Enter brand name"
              />
            </div>
          </div>
        </div>

        <!-- Pricing Section -->
        <div class="form-section">
          <h2 class="section-title">
            <i class="fas fa-tag"></i>
            Pricing
          </h2>

          <div class="form-row">
            <div class="form-group">
              <label for="price">Price <span class="required">*</span></label>
              <div class="input-with-icon">
                <span class="input-icon">₱</span>
                <input
                  type="number"
                  id="price"
                  v-model.number="product.price"
                  placeholder="0.00"
                  step="0.01"
                  min="0"
                  required
                />
              </div>
            </div>

            <div class="form-group">
              <label for="comparePrice">Compare at Price</label>
              <div class="input-with-icon">
                <span class="input-icon">₱</span>
                <input
                  type="number"
                  id="comparePrice"
                  v-model.number="product.comparePrice"
                  placeholder="0.00"
                  step="0.01"
                  min="0"
                />
              </div>
              <small class="helper-text">Original price to show discount</small>
            </div>
          </div>

          <div class="form-row" v-if="product.comparePrice > product.price">
            <div class="discount-badge">
              <i class="fas fa-percent"></i>
              Save {{ calculateDiscount }}% off!
            </div>
          </div>
        </div>

        <!-- Inventory Section -->
        <div class="form-section">
          <h2 class="section-title">
            <i class="fas fa-boxes"></i>
            Inventory
          </h2>

          <!-- Size Options (Show only if category requires sizes) -->
          <div v-if="requiresSizes" class="form-row">
            <div class="form-group full-width">
              <label>Available Sizes <span class="required">*</span></label>
              <div class="size-options">
                <label
                  v-for="size in availableSizes"
                  :key="size"
                  class="size-checkbox"
                  :class="{ active: product.sizes && product.sizes.includes(size) }"
                >
                  <input
                    type="checkbox"
                    :value="size"
                    v-model="product.sizes"
                  />
                  <span class="size-label">{{ size }}</span>
                </label>
              </div>
              <small class="helper-text">Select all sizes available for this product</small>
            </div>
          </div>

          <!-- Stock Quantity -->
          <div class="form-row">
            <div class="form-group">
              <label for="stock">Stock Quantity <span class="required">*</span></label>
              <input
                type="number"
                id="stock"
                v-model.number="product.stock"
                placeholder="0"
                min="0"
                required
              />
            </div>

            <div class="form-group">
              <label for="sku">SKU (Stock Keeping Unit)</label>
              <input
                type="text"
                id="sku"
                v-model="product.sku"
                placeholder="e.g., PROD-001"
              />
            </div>
          </div>

          <!-- Stock Status Indicator -->
          <div class="form-row">
            <div class="stock-status" :class="stockStatusClass">
              <i :class="stockStatusIcon"></i>
              <span>{{ stockStatusText }}</span>
            </div>
          </div>
        </div>

        <!-- Images Section -->
        <div class="form-section">
          <h2 class="section-title">
            <i class="fas fa-images"></i>
            Product Images
          </h2>

          <div class="form-row">
            <div class="form-group full-width">
              <!-- Current Image -->
              <div v-if="product.image" class="current-image-section">
                <label>Current Image</label>
                <div class="current-image">
                  <img :src="product.image" alt="Current product image" />
                </div>
              </div>

              <label>Upload New Image (Optional)</label>
              <div class="image-upload-area">
                <div class="upload-placeholder">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <p>Click to upload or drag and drop</p>
                  <small>PNG, JPG, GIF up to 10MB</small>
                </div>
                <input
                  type="file"
                  accept="image/*"
                  @change="handleImageUpload"
                  class="file-input"
                />
              </div>

              <!-- New Image Preview -->
              <div v-if="newImagePreview" class="new-image-preview">
                <label>New Image Preview</label>
                <div class="preview-container">
                  <img :src="newImagePreview" alt="New product image" />
                  <button type="button" class="remove-preview" @click="removeNewImage">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Details Section -->
        <div class="form-section">
          <h2 class="section-title">
            <i class="fas fa-list"></i>
            Additional Details
          </h2>

          <div class="form-row">
            <div class="form-group">
              <label for="color">Color</label>
              <input
                type="text"
                id="color"
                v-model="product.color"
                placeholder="e.g., Red, Blue, Black"
              />
            </div>

            <div class="form-group">
              <label for="weight">Weight (kg)</label>
              <input
                type="number"
                id="weight"
                v-model.number="product.weight"
                placeholder="0.00"
                step="0.01"
                min="0"
              />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="material">Material</label>
              <input
                type="text"
                id="material"
                v-model="product.material"
                placeholder="e.g., Cotton, Leather, Plastic"
              />
            </div>

            <div class="form-group">
              <label>Product Status</label>
              <div class="toggle-switch">
                <input
                  type="checkbox"
                  id="status"
                  v-model="product.isActive"
                />
                <label for="status" class="toggle-label">
                  <span class="toggle-slider"></span>
                </label>
                <span class="toggle-text">{{ product.isActive ? 'Active' : 'Inactive' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="handleCancel">
            <i class="fas fa-times"></i>
            Cancel
          </button>
          <button type="submit" class="btn-submit" :disabled="isSaving">
            <span v-if="!isSaving">
              <i class="fas fa-save"></i>
              Save Changes
            </span>
            <span v-else>
              <i class="fas fa-spinner fa-spin"></i>
              Saving...
            </span>
          </button>
        </div>
      </form>
    </div>

    <!-- Success Modal -->
    <SuccessModal
      :show="showSuccessModal"
      title="Product Updated Successfully!"
      message="Your product changes have been saved."
      primary-text="Back to Products"
      primary-icon="fas fa-list"
      @close="backToProducts"
      @primary="backToProducts"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import SuccessModal from '@/components/modals/SuccessModal.vue'

const router = useRouter()
const route = useRoute()

const isLoading = ref(true)
const isSaving = ref(false)
const showSuccessModal = ref(false)
const newImagePreview = ref(null)
const newImageFile = ref(null)

// Static categories data
const categories = ref([
  { id: 1, name: 'Fashion', requiresSizes: true, sizes: ['XS', 'S', 'M', 'L', 'XL', 'XXL'] },
  { id: 2, name: 'Footwear', requiresSizes: true, sizes: ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'] },
  { id: 3, name: 'Electronics', requiresSizes: false, sizes: [] },
  { id: 4, name: 'Sports & Outdoors', requiresSizes: true, sizes: ['S', 'M', 'L', 'XL'] },
  { id: 5, name: 'Home & Living', requiresSizes: false, sizes: [] },
  { id: 6, name: 'Beauty & Personal Care', requiresSizes: false, sizes: [] },
  { id: 7, name: 'Toys & Games', requiresSizes: false, sizes: [] },
  { id: 8, name: 'Books & Stationery', requiresSizes: false, sizes: [] },
  { id: 9, name: 'Accessories', requiresSizes: false, sizes: [] },
  { id: 10, name: 'Food & Beverages', requiresSizes: false, sizes: [] }
])

// Get products from localStorage or use static data
const getStoredProducts = () => {
  const stored = localStorage.getItem('products')
  if (stored) {
    return JSON.parse(stored)
  }
  
  // Default static products if none in localStorage
  return [
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
      image: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=400&fit=crop'
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
      image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop'
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
      image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop'
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
      image: 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=400&h=400&fit=crop'
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
      image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop'
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
      image: 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=400&h=400&fit=crop'
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
      image: 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=400&h=400&fit=crop'
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
      image: 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=400&h=400&fit=crop'
    }
  ]
}

const product = ref(null)

// Computed properties
const selectedCategory = computed(() => {
  return categories.value.find(cat => cat.name === product.value?.category)
})

const requiresSizes = computed(() => {
  return selectedCategory.value?.requiresSizes || false
})

const availableSizes = computed(() => {
  return selectedCategory.value?.sizes || []
})

const calculateDiscount = computed(() => {
  if (product.value?.comparePrice && product.value?.price) {
    const discount = ((product.value.comparePrice - product.value.price) / product.value.comparePrice) * 100
    return Math.round(discount)
  }
  return 0
})

const stockStatusClass = computed(() => {
  if (!product.value) return 'stock-unknown'
  if (product.value.stock === null || product.value.stock === '') return 'stock-unknown'
  if (product.value.stock === 0) return 'stock-out'
  if (product.value.stock < 10) return 'stock-low'
  return 'stock-good'
})

const stockStatusIcon = computed(() => {
  if (!product.value) return 'fas fa-question-circle'
  if (product.value.stock === null || product.value.stock === '') return 'fas fa-question-circle'
  if (product.value.stock === 0) return 'fas fa-times-circle'
  if (product.value.stock < 10) return 'fas fa-exclamation-triangle'
  return 'fas fa-check-circle'
})

const stockStatusText = computed(() => {
  if (!product.value) return 'Stock not set'
  if (product.value.stock === null || product.value.stock === '') return 'Stock not set'
  if (product.value.stock === 0) return 'Out of stock'
  if (product.value.stock < 10) return `Low stock (${product.value.stock} items)`
  return `In stock (${product.value.stock} items)`
})

// Methods
const loadProduct = () => {
  const productId = parseInt(route.params.id)
  
  // Simulate API call with slight delay for better UX
  setTimeout(() => {
    const products = getStoredProducts()
    const foundProduct = products.find(p => p.id === productId)
    
    if (foundProduct) {
      // Deep clone to avoid reference issues
      product.value = JSON.parse(JSON.stringify(foundProduct))
      console.log('✅ Product loaded:', product.value)
    } else {
      console.log('❌ Product not found with ID:', productId)
    }
    
    isLoading.value = false
  }, 300) // Reduced delay for better UX
}

const handleCategoryChange = () => {
  // Clear sizes when category changes
  if (product.value) {
    product.value.sizes = []
  }
}

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  
  if (file) {
    newImageFile.value = file
    const reader = new FileReader()
    
    reader.onload = (e) => {
      newImagePreview.value = e.target.result
    }
    
    reader.readAsDataURL(file)
  }
}

const removeNewImage = () => {
  newImagePreview.value = null
  newImageFile.value = null
}

const handleSubmit = () => {
  console.log('💾 Saving Product Data:', product.value)
  console.log('🖼️ New Image:', newImageFile.value)
  
  isSaving.value = true
  
  // Simulate API call
  setTimeout(() => {
    // Update product in localStorage
    const products = getStoredProducts()
    const index = products.findIndex(p => p.id === product.value.id)
    
    if (index !== -1) {
      // If new image was uploaded, use the preview
      if (newImagePreview.value) {
        product.value.image = newImagePreview.value
      }
      
      products[index] = { ...product.value }
      localStorage.setItem('products', JSON.stringify(products))
      console.log('✅ Product updated in localStorage')
    }
    
    isSaving.value = false
    showSuccessModal.value = true
  }, 1500)
}

const handleCancel = () => {
  if (confirm('Are you sure you want to cancel? All unsaved changes will be lost.')) {
    router.push('/products')
  }
}

const backToProducts = () => {
  showSuccessModal.value = false
  router.push('/products')
}

onMounted(() => {
  console.log('📝 EditProduct mounted, loading product ID:', route.params.id)
  loadProduct()
})
</script>

<style scoped>
/* Same styles as AddProduct.vue */
.edit-product-page {
  padding: 2rem;
  max-width: 1200px;
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

.header-left {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.back-button {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: white;
  border: 2px solid #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  text-decoration: none;
  transition: all 0.3s;
}

.back-button:hover {
  border-color: #667eea;
  color: #667eea;
  transform: translateX(-4px);
}

.page-header h1 {
  font-size: 1.75rem;
  color: #2c3e50;
  margin: 0;
}

.subtitle {
  color: #666;
  margin: 0.25rem 0 0 0;
  font-size: 0.9rem;
}

.loading-container,
.not-found-container {
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

.not-found-icon {
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

.not-found-container h3 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
}

.not-found-container p {
  margin: 0 0 2rem 0;
  color: #666;
}

.btn-back {
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

.btn-back:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.form-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 2rem;
}

.product-form {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.form-section {
  padding-bottom: 2rem;
  border-bottom: 2px solid #f5f5f5;
}

.form-section:last-of-type {
  border-bottom: none;
}

.section-title {
  font-size: 1.25rem;
  color: #2c3e50;
  margin: 0 0 1.5rem 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.section-title i {
  color: #667eea;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.form-row:last-child {
  margin-bottom: 0;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.required {
  color: #e74c3c;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 0.75rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.3s;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
}

.input-with-icon {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
  font-weight: 600;
}

.input-with-icon input {
  padding-left: 2.5rem;
}

.helper-text {
  color: #999;
  font-size: 0.8rem;
  margin-top: 0.25rem;
}

.discount-badge {
  grid-column: 1 / -1;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
}

.size-options {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.size-checkbox {
  position: relative;
  cursor: pointer;
}

.size-checkbox input {
  position: absolute;
  opacity: 0;
}

.size-label {
  display: block;
  padding: 0.5rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  background: white;
  transition: all 0.3s;
  font-weight: 600;
  min-width: 50px;
  text-align: center;
}

.size-checkbox:hover .size-label {
  border-color: #667eea;
}

.size-checkbox.active .size-label {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-color: transparent;
}

.stock-status {
  grid-column: 1 / -1;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
}

.stock-status.stock-good {
  background: #d4edda;
  color: #155724;
}

.stock-status.stock-low {
  background: #fff3cd;
  color: #856404;
}

.stock-status.stock-out {
  background: #f8d7da;
  color: #721c24;
}

.stock-status.stock-unknown {
  background: #e7f3ff;
  color: #004085;
}

.current-image-section {
  margin-bottom: 1.5rem;
}

.current-image {
  width: 200px;
  height: 200px;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid #e0e0e0;
  margin-top: 0.5rem;
}

.current-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-upload-area {
  position: relative;
  border: 2px dashed #e0e0e0;
  border-radius: 8px;
  padding: 2rem;
  text-align: center;
  transition: all 0.3s;
  cursor: pointer;
  margin-top: 0.5rem;
}

.image-upload-area:hover {
  border-color: #667eea;
  background: #f8f9ff;
}

.upload-placeholder i {
  font-size: 3rem;
  color: #667eea;
  margin-bottom: 1rem;
}

.upload-placeholder p {
  margin: 0.5rem 0;
  color: #2c3e50;
  font-weight: 600;
}

.upload-placeholder small {
  color: #999;
}

.file-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.new-image-preview {
  margin-top: 1.5rem;
}

.preview-container {
  position: relative;
  width: 200px;
  height: 200px;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid #667eea;
  margin-top: 0.5rem;
}

.preview-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-preview {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(231, 76, 60, 0.9);
  color: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.remove-preview:hover {
  background: #e74c3c;
  transform: scale(1.1);
}

.toggle-switch {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.toggle-switch input {
  display: none;
}

.toggle-label {
  position: relative;
  width: 50px;
  height: 28px;
  background: #ccc;
  border-radius: 14px;
  cursor: pointer;
  transition: all 0.3s;
}

.toggle-slider {
  position: absolute;
  top: 3px;
  left: 3px;
  width: 22px;
  height: 22px;
  background: white;
  border-radius: 50%;
  transition: all 0.3s;
}

.toggle-switch input:checked + .toggle-label {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.toggle-switch input:checked + .toggle-label .toggle-slider {
  transform: translateX(22px);
}

.toggle-text {
  font-weight: 600;
  color: #2c3e50;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding-top: 2rem;
}

.btn-cancel,
.btn-submit {
  padding: 0.875rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  border: none;
}

.btn-cancel {
  background: white;
  color: #666;
  border: 2px solid #e0e0e0;
}

.btn-cancel:hover {
  border-color: #dc3545;
  color: #dc3545;
}

.btn-submit {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* Responsive */
@media (max-width: 768px) {
  .edit-product-page {
    padding: 1rem;
  }

  .form-container {
    padding: 1.5rem;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .form-actions {
    flex-direction: column;
  }

  .btn-cancel,
  .btn-submit {
    width: 100%;
    justify-content: center;
  }
}
</style>
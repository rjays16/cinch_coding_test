<template>
  <div class="add-product-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <router-link to="/products" class="back-button">
            <i class="fas fa-arrow-left"></i>
          </router-link>
          <div>
            <h1>Add New Product</h1>
            <p class="subtitle">Create a new product for your store</p>
          </div>
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

    <!-- Form Container -->
    <div class="form-container">
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
                :class="{ 'input-error': errors.name }"
                placeholder="Enter product name"
                required
              />
              <span v-if="errors.name" class="error-text">{{ errors.name[0] }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group full-width">
              <label for="description">Description <span class="required">*</span></label>
              <textarea
                id="description"
                v-model="product.description"
                :class="{ 'input-error': errors.description }"
                placeholder="Describe your product..."
                rows="5"
                required
              ></textarea>
              <span v-if="errors.description" class="error-text">{{ errors.description[0] }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="category">Category <span class="required">*</span></label>
              <select 
                id="category" 
                v-model="product.category" 
                :class="{ 'input-error': errors.category }"
                required 
                @change="handleCategoryChange"
              >
                <option value="">Select a category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.name">
                  {{ cat.name }}
                </option>
              </select>
              <span v-if="errors.category" class="error-text">{{ errors.category[0] }}</span>
            </div>

            <div class="form-group">
              <label for="brand">Brand</label>
              <input
                type="text"
                id="brand"
                v-model="product.brand"
                :class="{ 'input-error': errors.brand }"
                placeholder="Enter brand name"
              />
              <span v-if="errors.brand" class="error-text">{{ errors.brand[0] }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="sku">SKU (Stock Keeping Unit) <span class="required">*</span></label>
              <input
                type="text"
                id="sku"
                v-model="product.sku"
                :class="{ 'input-error': errors.sku }"
                placeholder="e.g., PROD-001"
                required
              />
              <small class="helper-text">Unique identifier for this product</small>
              <span v-if="errors.sku" class="error-text">{{ errors.sku[0] }}</span>
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
                  :class="{ 'input-error': errors.price }"
                  placeholder="0.00"
                  step="0.01"
                  min="0"
                  required
                />
              </div>
              <span v-if="errors.price" class="error-text">{{ errors.price[0] }}</span>
            </div>

            <div class="form-group">
              <label for="comparePrice">Compare at Price</label>
              <div class="input-with-icon">
                <span class="input-icon">₱</span>
                <input
                  type="number"
                  id="comparePrice"
                  v-model.number="product.comparePrice"
                  :class="{ 'input-error': errors.compare_price }"
                  placeholder="0.00"
                  step="0.01"
                  min="0"
                />
              </div>
              <small class="helper-text">Original price to show discount</small>
              <span v-if="errors.compare_price" class="error-text">{{ errors.compare_price[0] }}</span>
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
                  :class="{ active: product.sizes.includes(size) }"
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
              <span v-if="errors.sizes" class="error-text">{{ errors.sizes[0] }}</span>
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
                :class="{ 'input-error': errors.stock }"
                placeholder="0"
                min="0"
                required
              />
              <span v-if="errors.stock" class="error-text">{{ errors.stock[0] }}</span>
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
              <label>Upload Product Image</label>
              <div class="image-upload-area" @click="$refs.fileInput.click()">
                <div v-if="!imagePreview" class="upload-placeholder">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <p>Click to upload or drag and drop</p>
                  <small>PNG, JPG, GIF, WEBP up to 5MB</small>
                </div>
                <div v-else class="image-preview">
                  <img :src="imagePreview" alt="Product preview" />
                  <button type="button" class="remove-image" @click.stop="removeImage">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                @change="handleImageUpload"
                class="file-input"
                hidden
              />
              <span v-if="errors.image" class="error-text">{{ errors.image[0] }}</span>
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
                :class="{ 'input-error': errors.color }"
                placeholder="e.g., Red, Blue, Black"
              />
              <span v-if="errors.color" class="error-text">{{ errors.color[0] }}</span>
            </div>

            <div class="form-group">
              <label for="weight">Weight (kg)</label>
              <input
                type="number"
                id="weight"
                v-model.number="product.weight"
                :class="{ 'input-error': errors.weight }"
                placeholder="0.00"
                step="0.01"
                min="0"
              />
              <span v-if="errors.weight" class="error-text">{{ errors.weight[0] }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="material">Material</label>
              <input
                type="text"
                id="material"
                v-model="product.material"
                :class="{ 'input-error': errors.material }"
                placeholder="e.g., Cotton, Leather, Plastic"
              />
              <span v-if="errors.material" class="error-text">{{ errors.material[0] }}</span>
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
              <i class="fas fa-plus"></i>
              Add Product
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
      title="Product Added Successfully!"
      message="Your product has been added to your store."
      primary-text="Go to Products"
      primary-icon="fas fa-list"
      secondary-text="Add Another"
      secondary-icon="fas fa-plus"
      @close="backToProducts"
      @primary="backToProducts"
      @secondary="addAnother"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import SuccessModal from '@/components/modals/SuccessModal.vue'
import { productService } from '@/services/productService'

const router = useRouter()

// Form state
const product = ref({
  name: '',
  description: '',
  sku: '',
  category: '',
  brand: '',
  price: null,
  comparePrice: null,
  stock: null,
  isActive: true,
  sizes: [],
  color: '',
  weight: null,
  material: '',
  image: null
})

const imagePreview = ref(null)
const isSaving = ref(false)
const showSuccessModal = ref(false)
const errorMessage = ref('')
const errors = ref({})

// Categories
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

// Computed properties
const selectedCategory = computed(() => {
  return categories.value.find(cat => cat.name === product.value.category)
})

const requiresSizes = computed(() => {
  return selectedCategory.value?.requiresSizes || false
})

const availableSizes = computed(() => {
  return selectedCategory.value?.sizes || []
})

const calculateDiscount = computed(() => {
  if (product.value.comparePrice && product.value.price) {
    const discount = ((product.value.comparePrice - product.value.price) / product.value.comparePrice) * 100
    return Math.round(discount)
  }
  return 0
})

const stockStatusClass = computed(() => {
  if (product.value.stock === null || product.value.stock === '') return 'stock-unknown'
  if (product.value.stock === 0) return 'stock-out'
  if (product.value.stock < 10) return 'stock-low'
  return 'stock-good'
})

const stockStatusIcon = computed(() => {
  if (product.value.stock === null || product.value.stock === '') return 'fas fa-question-circle'
  if (product.value.stock === 0) return 'fas fa-times-circle'
  if (product.value.stock < 10) return 'fas fa-exclamation-triangle'
  return 'fas fa-check-circle'
})

const stockStatusText = computed(() => {
  if (product.value.stock === null || product.value.stock === '') return 'Stock not set'
  if (product.value.stock === 0) return 'Out of stock'
  if (product.value.stock < 10) return `Low stock (${product.value.stock} items)`
  return `In stock (${product.value.stock} items)`
})

// Methods
const handleCategoryChange = () => {
  product.value.sizes = []
}

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  
  if (file) {
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
      errorMessage.value = 'Image size must be less than 5MB'
      return
    }

    // Validate file type
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']
    if (!validTypes.includes(file.type)) {
      errorMessage.value = 'Image must be JPEG, PNG, JPG, GIF, or WEBP'
      return
    }

    product.value.image = file
    
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
    
    errorMessage.value = ''
  }
}

const removeImage = () => {
  product.value.image = null
  imagePreview.value = null
}

const handleSubmit = async () => {
  console.log('📤 Submitting product:', product.value)
  
  // Clear previous errors
  errors.value = {}
  errorMessage.value = ''
  isSaving.value = true

  try {
    // Prepare data for API
    const productData = {
      name: product.value.name,
      description: product.value.description,
      sku: product.value.sku,
      category: product.value.category,
      brand: product.value.brand,
      price: product.value.price,
      compare_price: product.value.comparePrice,
      stock: product.value.stock,
      is_active: product.value.isActive ? 1 : 0,
      sizes: product.value.sizes,
      color: product.value.color,
      weight: product.value.weight,
      material: product.value.material,
      image: product.value.image
    }

    // Call API
    const response = await productService.createProduct(productData)
    
    console.log('✅ Product created successfully:', response)
    
    // Show success modal
    isSaving.value = false
    showSuccessModal.value = true
    
  } catch (error) {
    console.error('Error creating product:', error)
    
    if (error.errors) {
      // Laravel validation errors
      errors.value = error.errors
      errorMessage.value = error.message || 'Please fix the validation errors'
    } else if (error.message) {
      errorMessage.value = error.message
    } else {
      errorMessage.value = 'Failed to create product. Please try again.'
    }
    
    isSaving.value = false
  }
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

const addAnother = () => {
  showSuccessModal.value = false
  
  // Reset form
  product.value = {
    name: '',
    description: '',
    sku: '',
    category: '',
    brand: '',
    price: null,
    comparePrice: null,
    stock: null,
    isActive: true,
    sizes: [],
    color: '',
    weight: null,
    material: '',
    image: null
  }
  
  imagePreview.value = null
  errors.value = {}
  errorMessage.value = ''
  
  // Scroll to top
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
  console.log('✅ AddProduct component mounted')
  
  // Check if user is authenticated
  const token = localStorage.getItem('seller_token')
  if (!token) {
    console.log('❌ No token found, redirecting to login')
    router.push('/login')
  }
})
</script>

<style scoped>
.add-product-page {
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

/* Input Error State */
.input-error {
  border-color: #e74c3c !important;
  background: #fff5f5 !important;
}

.error-text {
  color: #e74c3c;
  font-size: 0.85rem;
  margin-top: 0.25rem;
  display: block;
  font-weight: 500;
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
  display: none;
}

.image-preview {
  position: relative;
  width: 200px;
  height: 200px;
  margin: 0 auto;
  border-radius: 12px;
  overflow: hidden;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-image {
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

.remove-image:hover {
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
  .add-product-page {
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
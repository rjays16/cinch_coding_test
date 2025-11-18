<template>
  <div class="add-product-page">
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <router-link to="/products" class="back-button">
            <i class="fas fa-arrow-left"></i>
          </router-link>
          <div>
            <h1>Add New Product</h1>
            <p class="subtitle">Create a new product listing for your store</p>
          </div>
        </div>
      </div>
    </div>

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
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
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
              <label>Upload Images</label>
              <div class="image-upload-area">
                <div class="upload-placeholder">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <p>Click to upload or drag and drop</p>
                  <small>PNG, JPG, GIF up to 10MB</small>
                </div>
                <input
                  type="file"
                  accept="image/*"
                  multiple
                  @change="handleImageUpload"
                  class="file-input"
                />
              </div>

              <!-- Image Preview -->
              <div v-if="product.images.length > 0" class="image-preview-grid">
                <div v-for="(image, index) in product.images" :key="index" class="image-preview-item">
                  <img :src="image.preview" :alt="`Product ${index + 1}`" />
                  <button type="button" class="remove-image" @click="removeImage(index)">
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
          <button type="submit" class="btn-submit">
            <i class="fas fa-plus-circle"></i>
            Add Product
          </button>
        </div>
      </form>
    </div>

    <!-- Success Modal -->
    <SuccessModal
      :show="showSuccessModal"
      title="Product Added Successfully!"
      message="Your product has been added to the store."
      primary-text="Add Another"
      primary-icon="fas fa-plus"
      secondary-text="View Products"
      secondary-icon="fas fa-list"
      :show-secondary-button="true"
      @close="closeSuccessModal"
      @primary="addAnother"
      @secondary="viewProducts"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import SuccessModal from '@/components/modals/SuccessModal.vue'

const router = useRouter()

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

// Product form data
const product = ref({
  name: '',
  description: '',
  category: '',
  brand: '',
  price: null,
  comparePrice: null,
  stock: null,
  sku: '',
  sizes: [],
  color: '',
  weight: null,
  material: '',
  isActive: true,
  images: []
})

const showSuccessModal = ref(false)

// Computed properties
const selectedCategory = computed(() => {
  return categories.value.find(cat => cat.id === product.value.category)
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
  // Clear sizes when category changes
  product.value.sizes = []
}

const handleImageUpload = (event) => {
  const files = event.target.files
  
  for (let i = 0; i < files.length; i++) {
    const file = files[i]
    const reader = new FileReader()
    
    reader.onload = (e) => {
      product.value.images.push({
        file: file,
        preview: e.target.result
      })
    }
    
    reader.readAsDataURL(file)
  }
}

const removeImage = (index) => {
  product.value.images.splice(index, 1)
}

const handleSubmit = () => {
  console.log('Product Data:', product.value)
  
  // Show success modal
  showSuccessModal.value = true
}

const handleCancel = () => {
  if (confirm('Are you sure you want to cancel? All unsaved changes will be lost.')) {
    router.push('/products')
  }
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
  router.push('/products')
}

const addAnother = () => {
  showSuccessModal.value = false
  
  // Reset form
  product.value = {
    name: '',
    description: '',
    category: '',
    brand: '',
    price: null,
    comparePrice: null,
    stock: null,
    sku: '',
    sizes: [],
    color: '',
    weight: null,
    material: '',
    isActive: true,
    images: []
  }
}

const viewProducts = () => {
  closeSuccessModal()
}
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

.image-preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.image-preview-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 8px;
  overflow: hidden;
  border: 2px solid #e0e0e0;
}

.image-preview-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-image {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  width: 28px;
  height: 28px;
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

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
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
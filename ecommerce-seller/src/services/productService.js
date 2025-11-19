import api from './api'

export const productService = {
  // Get all products for the authenticated seller
  async getAllProducts(params = {}) {
    try {
      const response = await api.get('/seller/products', { params })
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  // Get single product
  async getProduct(id) {
    try {
      const response = await api.get(`/seller/products/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  // Create new product
  async createProduct(productData) {
    try {
      const formData = new FormData()
      
      // Append all product data to FormData
      Object.keys(productData).forEach(key => {
        if (key === 'sizes') {
          // Only append sizes if array has items
          if (Array.isArray(productData[key]) && productData[key].length > 0) {
            formData.append('sizes', JSON.stringify(productData[key]))
          }
          // Skip if empty array - don't send it
        } else if (key === 'image' && productData[key]) {
          // Append image file
          formData.append('image', productData[key])
        } else if (productData[key] !== null && productData[key] !== undefined && productData[key] !== '') {
          formData.append(key, productData[key])
        }
      })

      const response = await api.post('/seller/products', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  // Update existing product
  async updateProduct(id, productData) {
    try {
      const formData = new FormData()
      
      // Laravel doesn't support PUT with FormData, so we use POST with _method
      formData.append('_method', 'PUT')
      
      // Append all product data
      Object.keys(productData).forEach(key => {
        if (key === 'sizes') {
          // Only append sizes if array has items
          if (Array.isArray(productData[key]) && productData[key].length > 0) {
            formData.append('sizes', JSON.stringify(productData[key]))
          }
          // Skip if empty array
        } else if (key === 'image' && productData[key] instanceof File) {
          // Only append if it's a new file
          formData.append('image', productData[key])
        } else if (productData[key] !== null && productData[key] !== undefined && productData[key] !== '') {
          formData.append(key, productData[key])
        }
      })

      const response = await api.post(`/seller/products/${id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  // Delete product
  async deleteProduct(id) {
    try {
      const response = await api.delete(`/seller/products/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  // Get product statistics
  async getStats() {
    try {
      const response = await api.get('/seller/products-stats')
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }
}
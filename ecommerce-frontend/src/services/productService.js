import axios from 'axios'

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

export const productService = {
  // Get all public products (for buyers)
  async getAllProducts(params = {}) {
    try {
      const response = await api.get('/products', { params })
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  // Get single product
  async getProduct(id) {
    try {
      const response = await api.get(`/products/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  // Search products
  async searchProducts(query) {
    try {
      const response = await api.get('/products', {
        params: { search: query }
      })
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }
}
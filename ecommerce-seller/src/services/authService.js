import api from './api'

export const authService = {
  // Register new seller
  async register(data) {
    const response = await api.post('/seller/register', {
      first_name: data.firstName,
      last_name: data.lastName,
      store_name: data.storeName,
      email: data.email,
      phone: data.phone,
      password: data.password,
      password_confirmation: data.confirmPassword,
      terms_accepted: data.agreeTerms
    })
    
    return {
      token: response.data.token,
      seller: response.data.seller
    }
  },

  // Login seller
  async login(email, password) {
    const response = await api.post('/seller/login', {
      email,
      password
    })
    
    return {
      token: response.data.token,
      seller: response.data.seller
    }
  },

  // Logout seller
  async logout() {
    const response = await api.post('/seller/logout')
    return response.data
  },

  // Get current user profile
  async getCurrentUser() {
    const response = await api.get('/seller/profile')
    return response.data.data
  },

  // Validate token (check if token is still valid)
  async validateToken() {
    try {
      const response = await api.get('/seller/profile')
      return {
        valid: true,
        user: response.data.data
      }
    } catch (error) {
      return {
        valid: false,
        error: error.response?.data?.message || 'Token is invalid'
      }
    }
  },

  // Check if user is authenticated
  isAuthenticated() {
    const token = localStorage.getItem('seller_token')
    const user = localStorage.getItem('seller_user')
    return !!(token && user)
  },

  // Clear authentication data
  clearAuth() {
    localStorage.removeItem('seller_token')
    localStorage.removeItem('seller_user')
  }
}
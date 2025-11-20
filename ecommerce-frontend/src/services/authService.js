import api from './api'

export const authService = {
  // Register new buyer
  async register(name, email, password, confirmPassword) {
    const response = await api.post('/buyer/register', {
      name,
      email,
      password,
      password_confirmation: confirmPassword
    })
    
    return {
      token: response.data.token,
      user: response.data.user
    }
  },

  // Login buyer
  async login(email, password) {
    const response = await api.post('/buyer/login', {
      email,
      password
    })
    
    return {
      token: response.data.token,
      user: response.data.user
    }
  },

  // Logout buyer
  async logout() {
    try {
      await api.post('/buyer/logout')
    } catch (error) {
      console.log('Logout API call failed, but clearing local data anyway')
    }
    
    // Clear all authentication data
    localStorage.removeItem('buyer_token')
    localStorage.removeItem('buyer_user')
    localStorage.removeItem('cart') // Clear cart too
    
    // Dispatch storage event to update other components
    window.dispatchEvent(new Event('storage'))
    
    console.log('✅ Logged out successfully')
  },

  // Get current user
  async getCurrentUser() {
    const response = await api.get('/buyer/profile')
    return response.data.data
  },

  // Check if authenticated
  isAuthenticated() {
    const token = localStorage.getItem('buyer_token')
    const user = localStorage.getItem('buyer_user')
    return !!(token && user)
  },

  // Get user from localStorage
  getUser() {
    const user = localStorage.getItem('buyer_user')
    return user ? JSON.parse(user) : null
  },

  // Clear auth data
  clearAuth() {
    localStorage.removeItem('buyer_token')
    localStorage.removeItem('buyer_user')
    localStorage.removeItem('cart')
    
    // Dispatch storage event
    window.dispatchEvent(new Event('storage'))
  }
}
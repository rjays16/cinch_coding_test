import axios from 'axios'

// Get API URL from environment variable
const API_BASE_URL = process.env.VUE_APP_API_BASE_URL

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Add token to requests
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('buyer_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Handle response errors
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    // Token expired or invalid
    if (error.response?.status === 401) {
      localStorage.removeItem('buyer_token')
      localStorage.removeItem('buyer_user')
      
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }
    
    return Promise.reject(error)
  }
)

export default api
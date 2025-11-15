<template>
  <div class="login-page">
    <!-- Left Side - Login Form -->
    <div class="form-section">
      <div class="form-container">
        <div class="logo-section">
          <h1>Seller Portal</h1>
          <p class="subtitle">Welcome back! Login to manage your store</p>
        </div>

        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group" :class="{ 'has-error': errors.email }">
            <label for="email">Email Address</label>
            <input
              type="email"
              id="email"
              v-model="formData.email"
              placeholder="Enter your email"
              @input="clearError('email')"
              :disabled="isLoading"
            />
            <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
          </div>

          <div class="form-group" :class="{ 'has-error': errors.password }">
            <label for="password">Password</label>
            <input
              type="password"
              id="password"
              v-model="formData.password"
              placeholder="Enter your password"
              @input="clearError('password')"
              :disabled="isLoading"
            />
            <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
          </div>

          <div class="form-options">
            <label class="remember-me">
              <input type="checkbox" v-model="formData.rememberMe" :disabled="isLoading" />
              <span>Remember me</span>
            </label>
            <a href="#" class="forgot-link">Forgot password?</a>
          </div>

          <button type="submit" class="btn-login" :disabled="isLoading">
            <span v-if="!isLoading">Login to Dashboard</span>
            <span v-else>
              <i class="fas fa-spinner fa-spin"></i> Logging in...
            </span>
          </button>

          <div class="divider">
            <span>or</span>
          </div>

          <div class="register-link">
            Don't have a seller account?
            <router-link to="/register">Create an account</router-link>
          </div>
        </form>
      </div>
    </div>

    <!-- Right Side - Use RightSection Component -->
    <RightSection />

    <!-- Error Modal -->
    <ErrorModal
      :show="showErrorModal"
      :title="errorTitle"
      :message="errorMessage"
      @close="closeErrorModal"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import RightSection from '@/components/RightSection.vue'
import ErrorModal from '@/components/ErrorModal.vue'
import { authService } from '@/services/authService'

const router = useRouter()
const isLoading = ref(false)

// Modal state
const showErrorModal = ref(false)
const errorTitle = ref('Login Failed')
const errorMessage = ref('')

const formData = ref({
  email: '',
  password: '',
  rememberMe: false
})

const errors = ref({
  email: '',
  password: ''
})

const clearError = (field) => {
  errors.value[field] = ''
}

const closeErrorModal = () => {
  showErrorModal.value = false
}

const validateForm = () => {
  let isValid = true
  
  errors.value = {
    email: '',
    password: ''
  }

  if (!formData.value.email) {
    errors.value.email = 'Email is required'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
    errors.value.email = 'Please enter a valid email address'
    isValid = false
  }

  if (!formData.value.password) {
    errors.value.password = 'Password is required'
    isValid = false
  } else if (formData.value.password.length < 6) {
    errors.value.password = 'Password must be at least 6 characters'
    isValid = false
  }

  return isValid
}

const handleLogin = async () => {
  // Clear previous errors
  errors.value = {
    email: '',
    password: ''
  }

  // Validate form
  if (!validateForm()) {
    return
  }

  isLoading.value = true

  try {
    // Call API to login
    const response = await authService.login(
      formData.value.email,
      formData.value.password
    )

    // Check if response contains token and seller data
    if (!response.token || !response.seller) {
      throw new Error('Invalid response from server')
    }

    // Save token and seller data
    localStorage.setItem('seller_token', response.token)
    localStorage.setItem('seller_user', JSON.stringify(response.seller))

    // Redirect to dashboard
    router.push('/dashboard')

  } catch (error) {
    isLoading.value = false

    console.error('Login error:', error)

    // Handle different error types
    if (error.response) {
      const status = error.response.status
      const errorData = error.response.data

      if (status === 422) {
        // Validation errors - show below fields (not in modal)
        const backendErrors = errorData.errors
        
        if (backendErrors.email) {
          errors.value.email = backendErrors.email[0]
        }
        if (backendErrors.password) {
          errors.value.password = backendErrors.password[0]
        }
      } else if (status === 401) {
        // Invalid password - show in modal
        errorTitle.value = 'Invalid Credentials'
        errorMessage.value = errorData.message || 'Invalid email or password. Please check your credentials and try again.'
        showErrorModal.value = true
      } else if (status === 404) {
        // Seller not found - show in modal
        errorTitle.value = 'Seller Not Found'
        errorMessage.value = errorData.message || 'No seller account found with this email address. Please register as a seller first.'
        showErrorModal.value = true
      } else if (status === 403) {
        // Forbidden - show in modal
        errorTitle.value = 'Access Denied'
        errorMessage.value = errorData.message || 'This account is not registered as a seller account.'
        showErrorModal.value = true
      } else {
        // Other errors - show in modal
        errorTitle.value = 'Login Failed'
        errorMessage.value = errorData.message || 'Unable to login. Please try again later.'
        showErrorModal.value = true
      }
    } else if (error.request) {
      // Network error - show in modal
      errorTitle.value = 'Connection Error'
      errorMessage.value = 'Cannot connect to server. Please check your internet connection and try again.'
      showErrorModal.value = true
    } else {
      // Other errors - show in modal
      errorTitle.value = 'Error'
      errorMessage.value = error.message || 'An unexpected error occurred. Please try again.'
      showErrorModal.value = true
    }
  }
}
</script>

<style scoped>
.login-page {
  display: flex;
  min-height: 100vh;
  max-height: 100vh;
  overflow: hidden;
}

/* Left Side - Form Section */
.form-section {
  flex: 0 0 40%;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 2rem;
  background-color: #ffffff;
  overflow-y: auto;
  height: 100vh;
}

.form-container {
  width: 100%;
  max-width: 480px;
}

.logo-section {
  margin-bottom: 2.5rem;
}

.logo-section h1 {
  font-size: 2.5rem;
  color: #2c3e50;
  margin: 0 0 0.5rem 0;
  font-weight: 700;
}

.subtitle {
  color: #666;
  font-size: 1rem;
  margin: 0;
  line-height: 1.5;
}

.login-form {
  margin-top: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #2c3e50;
  font-weight: 600;
  font-size: 0.9rem;
}

.form-group input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s;
  font-family: inherit;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group input:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
  opacity: 0.6;
}

.form-group.has-error input {
  border-color: #dc3545;
}

.form-group.has-error input:focus {
  border-color: #dc3545;
  box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

.error-message {
  display: block;
  color: #dc3545;
  font-size: 0.85rem;
  margin-top: 0.5rem;
  font-weight: 500;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #666;
  cursor: pointer;
}

.remember-me input[type="checkbox"] {
  cursor: pointer;
  width: 16px;
  height: 16px;
}

.remember-me input[type="checkbox"]:disabled {
  cursor: not-allowed;
}

.forgot-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.forgot-link:hover {
  text-decoration: underline;
}

.btn-login {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-login:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-login:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.divider {
  text-align: center;
  margin: 2rem 0;
  position: relative;
}

.divider::before,
.divider::after {
  content: '';
  position: absolute;
  top: 50%;
  width: 45%;
  height: 1px;
  background-color: #e0e0e0;
}

.divider::before {
  left: 0;
}

.divider::after {
  right: 0;
}

.divider span {
  background-color: white;
  padding: 0 1rem;
  color: #999;
  font-size: 0.9rem;
}

.register-link {
  text-align: center;
  color: #666;
  font-size: 0.95rem;
}

.register-link a {
  color: #667eea;
  font-weight: 600;
  text-decoration: none;
  margin-left: 0.25rem;
}

.register-link a:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 968px) {
  .login-page {
    flex-direction: column;
  }

  .form-section {
    flex: 1;
  }
}

@media (max-width: 768px) {
  .form-section {
    padding: 1.5rem;
  }

  .logo-section h1 {
    font-size: 2rem;
  }
}
</style>
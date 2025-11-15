<template>
  <div class="register-page">
    <!-- Left Side - Registration Form -->
    <div class="form-section">
      <div class="form-container">
        <div class="logo-section">
          <h1>Seller Portal</h1>
          <p class="subtitle">Create your seller account and start selling</p>
        </div>

        <form @submit.prevent="handleRegister" class="register-form">
          <div class="form-row">
            <div class="form-group" :class="{ 'has-error': errors.firstName }">
              <label for="firstName">First Name</label>
              <input
                type="text"
                id="firstName"
                v-model="formData.firstName"
                placeholder="John"
                @input="clearError('firstName')"
                :disabled="isLoading"
              />
              <span v-if="errors.firstName" class="error-message">{{ errors.firstName }}</span>
            </div>

            <div class="form-group" :class="{ 'has-error': errors.lastName }">
              <label for="lastName">Last Name</label>
              <input
                type="text"
                id="lastName"
                v-model="formData.lastName"
                placeholder="Doe"
                @input="clearError('lastName')"
                :disabled="isLoading"
              />
              <span v-if="errors.lastName" class="error-message">{{ errors.lastName }}</span>
            </div>
          </div>

          <div class="form-group" :class="{ 'has-error': errors.storeName }">
            <label for="storeName">Store Name</label>
            <input
              type="text"
              id="storeName"
              v-model="formData.storeName"
              placeholder="My Awesome Store"
              @input="clearError('storeName')"
              :disabled="isLoading"
            />
            <span v-if="errors.storeName" class="error-message">{{ errors.storeName }}</span>
          </div>

          <div class="form-group" :class="{ 'has-error': errors.email }">
            <label for="email">Email Address</label>
            <input
              type="email"
              id="email"
              v-model="formData.email"
              placeholder="john@example.com"
              @input="clearError('email')"
              :disabled="isLoading"
            />
            <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
          </div>

          <div class="form-group" :class="{ 'has-error': errors.phone }">
            <label for="phone">Phone Number</label>
            <input
              type="tel"
              id="phone"
              v-model="formData.phone"
              placeholder="+63 912 345 6789"
              @input="clearError('phone')"
              :disabled="isLoading"
            />
            <span v-if="errors.phone" class="error-message">{{ errors.phone }}</span>
          </div>

          <div class="form-row">
            <div class="form-group" :class="{ 'has-error': errors.password }">
              <label for="password">Password</label>
              <input
                type="password"
                id="password"
                v-model="formData.password"
                placeholder="••••••••"
                @input="clearError('password')"
                :disabled="isLoading"
              />
              <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
            </div>

            <div class="form-group" :class="{ 'has-error': errors.confirmPassword }">
              <label for="confirmPassword">Confirm Password</label>
              <input
                type="password"
                id="confirmPassword"
                v-model="formData.confirmPassword"
                placeholder="••••••••"
                @input="clearError('confirmPassword')"
                :disabled="isLoading"
              />
              <span v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</span>
            </div>
          </div>

          <div class="form-group checkbox-group" :class="{ 'has-error': errors.agreeTerms }">
            <label class="checkbox-label">
              <input 
                type="checkbox" 
                v-model="formData.agreeTerms" 
                @change="clearError('agreeTerms')"
                :disabled="isLoading"
              />
              <span> I agree to the Terms & Conditions and Privacy Policy</span>
            </label>
            <span v-if="errors.agreeTerms" class="error-message">{{ errors.agreeTerms }}</span>
          </div>

          <!-- Success Message -->
          <div v-if="successMessage" class="success-message">
            <i class="fas fa-check-circle"></i>
            {{ successMessage }}
          </div>

          <!-- General Error Message -->
          <div v-if="generalError" class="general-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ generalError }}
          </div>

          <button type="submit" class="btn-register" :disabled="isLoading">
            <span v-if="!isLoading">Create Seller Account</span>
            <span v-else>
              <i class="fas fa-spinner fa-spin"></i> Creating Account...
            </span>
          </button>

          <div class="divider">
            <span>or</span>
          </div>

          <div class="login-link">
            Already have an account?
            <router-link to="/login">Login here</router-link>
          </div>
        </form>
      </div>
    </div>

    <RightSection />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import RightSection from '@/components/RightSection.vue'
import { authService } from '@/services/authService'

const router = useRouter()
const isLoading = ref(false)
const successMessage = ref('')
const generalError = ref('')

const formData = ref({
  firstName: '',
  lastName: '',
  storeName: '',
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
  agreeTerms: false
})

const errors = ref({
  firstName: '',
  lastName: '',
  storeName: '',
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
  agreeTerms: ''
})

const clearError = (field) => {
  errors.value[field] = ''
  generalError.value = ''
}

const validateForm = () => {
  let isValid = true
  
  // Reset errors
  errors.value = {
    firstName: '',
    lastName: '',
    storeName: '',
    email: '',
    phone: '',
    password: '',
    confirmPassword: '',
    agreeTerms: ''
  }

  // First Name validation
  if (!formData.value.firstName.trim()) {
    errors.value.firstName = 'First name is required'
    isValid = false
  }

  // Last Name validation
  if (!formData.value.lastName.trim()) {
    errors.value.lastName = 'Last name is required'
    isValid = false
  }

  // Store Name validation
  if (!formData.value.storeName.trim()) {
    errors.value.storeName = 'Store name is required'
    isValid = false
  }

  // Email validation
  if (!formData.value.email) {
    errors.value.email = 'Email is required'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
    errors.value.email = 'Please enter a valid email address'
    isValid = false
  }

  // Phone validation
  if (!formData.value.phone) {
    errors.value.phone = 'Phone number is required'
    isValid = false
  } else if (formData.value.phone.length < 10) {
    errors.value.phone = 'Please enter a valid phone number'
    isValid = false
  }

  // Password validation
  if (!formData.value.password) {
    errors.value.password = 'Password is required'
    isValid = false
  } else if (formData.value.password.length < 6) {
    errors.value.password = 'Password must be at least 6 characters'
    isValid = false
  }

  // Confirm Password validation
  if (!formData.value.confirmPassword) {
    errors.value.confirmPassword = 'Please confirm your password'
    isValid = false
  } else if (formData.value.password !== formData.value.confirmPassword) {
    errors.value.confirmPassword = 'Passwords do not match'
    isValid = false
  }

  // Terms validation
  if (!formData.value.agreeTerms) {
    errors.value.agreeTerms = 'You must agree to the terms and conditions'
    isValid = false
  }

  return isValid
}

const handleRegister = async () => {
  // Clear previous messages
  successMessage.value = ''
  generalError.value = ''

  // Validate form
  if (!validateForm()) {
    return
  }

  isLoading.value = true

  try {

    // Call API to register
    const response = await authService.register(formData.value)

    // Check if response contains token and seller data
    if (!response.token || !response.seller) {
      throw new Error('Invalid response from server')
    }

    // Save token and seller data
    localStorage.setItem('seller_token', response.token)
    localStorage.setItem('seller_user', JSON.stringify(response.seller))

    // Show success message
    successMessage.value = 'Seller account created successfully! Redirecting to dashboard...'

    // Redirect to dashboard after 1.5 seconds
    setTimeout(() => {
      router.push('/dashboard')
    }, 1500)

  } catch (error) {
    isLoading.value = false

    // Handle validation errors from Laravel
    if (error.response?.status === 422) {
      const backendErrors = error.response.data.errors
      
      // Map Laravel validation errors to form errors
      if (backendErrors.first_name) {
        errors.value.firstName = backendErrors.first_name[0]
      }
      if (backendErrors.last_name) {
        errors.value.lastName = backendErrors.last_name[0]
      }
      if (backendErrors.store_name) {
        errors.value.storeName = backendErrors.store_name[0]
      }
      if (backendErrors.email) {
        errors.value.email = backendErrors.email[0]
      }
      if (backendErrors.phone) {
        errors.value.phone = backendErrors.phone[0]
      }
      if (backendErrors.password) {
        errors.value.password = backendErrors.password[0]
      }
    } else {
      // General error
      generalError.value = error.response?.data?.message || 'Registration failed. Please try again.'
    }
  }
}
</script>

<style scoped>
.register-page {
  display: flex;
  min-height: 100vh;
  max-height: 100vh;
  overflow: hidden;
}

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
  max-width: 550px;
}

.logo-section {
  margin-bottom: 2rem;
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

.register-form {
  margin-top: 2rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  margin-bottom: 1.25rem;
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

.success-message {
  background: #d4edda;
  color: #155724;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
}

.success-message i {
  font-size: 18px;
}

.general-error {
  background: #f8d7da;
  color: #721c24;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
}

.general-error i {
  font-size: 18px;
}

.checkbox-group {
  margin: 1.25rem 0;
}

.checkbox-label {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  color: #666;
  cursor: pointer;
  font-size: 0.9rem;
  line-height: 1.5;
}

.checkbox-label input[type="checkbox"] {
  margin-top: 0.2rem;
  cursor: pointer;
  width: 16px;
  height: 16px;
  flex-shrink: 0;
}

.checkbox-label input[type="checkbox"]:disabled {
  cursor: not-allowed;
}

.btn-register {
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

.btn-register:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-register:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.divider {
  text-align: center;
  margin: 1.5rem 0;
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

.login-link {
  text-align: center;
  color: #666;
  font-size: 0.95rem;
}

.login-link a {
  color: #667eea;
  font-weight: 600;
  text-decoration: none;
  margin-left: 0.25rem;
}

.login-link a:hover {
  text-decoration: underline;
}

@media (max-width: 968px) {
  .register-page {
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

  .form-container {
    max-width: 100%;
  }

  .logo-section h1 {
    font-size: 2rem;
  }

  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>

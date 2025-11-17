<template>
  <div class="forgot-password-page">
    <!-- Left Side - Form -->
    <div class="form-section">
      <div class="form-container">
        <div class="logo-section">
          <h1>Forgot Password?</h1>
          <p class="subtitle">Enter your email address and we'll send you a link to reset your password</p>
        </div>

        <form @submit.prevent="handleForgotPassword" class="forgot-password-form">
          <div class="form-group" :class="{ 'has-error': errors.email }">
            <label for="email">Email Address</label>
            <input
              type="email"
              id="email"
              v-model="email"
              placeholder="Enter your email"
              @input="clearError"
              :disabled="isLoading"
            />
            <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
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

          <button type="submit" class="btn-submit" :disabled="isLoading">
            <span v-if="!isLoading">Send Reset Link</span>
            <span v-else>
              <i class="fas fa-spinner fa-spin"></i> Sending...
            </span>
          </button>

          <div class="divider">
            <span>or</span>
          </div>

          <div class="back-to-login">
            <router-link to="/login" class="back-link">
              <i class="fas fa-arrow-left"></i> Back to Login
            </router-link>
          </div>
        </form>
      </div>
    </div>

    <!-- Right Side - Use RightSection Component -->
    <RightSection />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import RightSection from '@/components/RightSection.vue'
import axios from 'axios'

const email = ref('')
const isLoading = ref(false)
const successMessage = ref('')
const generalError = ref('')
const errors = ref({
  email: ''
})

const clearError = () => {
  errors.value.email = ''
  generalError.value = ''
}

const validateForm = () => {
  errors.value.email = ''

  if (!email.value) {
    errors.value.email = 'Email is required'
    return false
  }

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    errors.value.email = 'Please enter a valid email address'
    return false
  }

  return true
}

const handleForgotPassword = async () => {
  successMessage.value = ''
  generalError.value = ''

  if (!validateForm()) {
    return
  }

  isLoading.value = true

  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_BASE_URL}/seller/forgot-password`,
      { email: email.value }
    )

    successMessage.value = response.data.message
    email.value = ''
    isLoading.value = false

  } catch (error) {
    isLoading.value = false

    if (error.response?.status === 422) {
      const backendErrors = error.response.data.errors
      
      if (backendErrors.email) {
        errors.value.email = backendErrors.email[0]
      }
    } else if (error.response?.status === 403) {
      generalError.value = error.response.data.message
    } else {
      generalError.value = error.response?.data?.message || 'Failed to send reset link. Please try again.'
    }
  }
}
</script>

<style scoped>
.forgot-password-page {
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

.forgot-password-form {
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

.success-message {
  background: #d4edda;
  border: 1px solid #c3e6cb;
  color: #155724;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
  font-size: 0.9rem;
}

.success-message i {
  font-size: 18px;
}

.general-error {
  background: #f8d7da;
  border: 1px solid #f5c6cb;
  color: #721c24;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
  font-size: 0.9rem;
}

.general-error i {
  font-size: 18px;
}

.btn-submit {
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

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-submit:disabled {
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

.back-to-login {
  text-align: center;
}

.back-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s;
  font-size: 0.95rem;
}

.back-link:hover {
  color: #764ba2;
  gap: 12px;
}

/* Responsive */
@media (max-width: 968px) {
  .forgot-password-page {
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
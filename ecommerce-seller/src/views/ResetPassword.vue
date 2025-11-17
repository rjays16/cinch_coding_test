<template>
  <div class="reset-password-page">
    <!-- Left Side - Form -->
    <div class="form-section">
      <div class="form-container">
        <div class="logo-section">
          <h1>Reset Password</h1>
          <p class="subtitle">Enter your new password</p>
        </div>

        <form @submit.prevent="handleResetPassword" class="reset-password-form">
          <div class="form-group" :class="{ 'has-error': errors.password }">
            <label for="password">New Password</label>
            <input
              type="password"
              id="password"
              v-model="password"
              placeholder="Enter new password"
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
              v-model="confirmPassword"
              placeholder="Confirm new password"
              @input="clearError('confirmPassword')"
              :disabled="isLoading"
            />
            <span v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</span>
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
            <span v-if="!isLoading">Reset Password</span>
            <span v-else>
              <i class="fas fa-spinner fa-spin"></i> Resetting...
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
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import RightSection from '@/components/RightSection.vue'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const token = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const isLoading = ref(false)
const successMessage = ref('')
const generalError = ref('')
const errors = ref({
  password: '',
  confirmPassword: ''
})

onMounted(() => {
  token.value = route.query.token || ''
  email.value = route.query.email || ''

  if (!token.value || !email.value) {
    generalError.value = 'Invalid reset link. Please request a new password reset.'
  }
})

const clearError = (field) => {
  errors.value[field] = ''
  generalError.value = ''
}

const validateForm = () => {
  let isValid = true
  errors.value = {
    password: '',
    confirmPassword: ''
  }

  if (!password.value) {
    errors.value.password = 'Password is required'
    isValid = false
  } else if (password.value.length < 6) {
    errors.value.password = 'Password must be at least 6 characters'
    isValid = false
  }

  if (!confirmPassword.value) {
    errors.value.confirmPassword = 'Please confirm your password'
    isValid = false
  } else if (password.value !== confirmPassword.value) {
    errors.value.confirmPassword = 'Passwords do not match'
    isValid = false
  }

  return isValid
}

const handleResetPassword = async () => {
  successMessage.value = ''
  generalError.value = ''

  if (!validateForm()) {
    return
  }

  isLoading.value = true

  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_BASE_URL}/reset-password`,
      {
        token: token.value,
        email: email.value,
        password: password.value,
        password_confirmation: confirmPassword.value
      }
    )

    successMessage.value = response.data.message

    setTimeout(() => {
      router.push('/login')
    }, 2000)

  } catch (error) {
    isLoading.value = false

    if (error.response?.status === 422) {
      const backendErrors = error.response.data.errors
      
      if (backendErrors.password) {
        errors.value.password = backendErrors.password[0]
      }
    } else {
      generalError.value = error.response?.data?.message || 'Failed to reset password. Please try again.'
    }
  }
}
</script>

<style scoped>
/* Same styles as ForgotPassword.vue */
.reset-password-page {
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

.reset-password-form {
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

@media (max-width: 968px) {
  .reset-password-page {
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
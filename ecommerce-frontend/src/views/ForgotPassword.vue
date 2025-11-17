<template>
  <Layout>
    <div class="forgot-password-page">
      <div class="container">
        <div class="forgot-password-card">
          <h1>Forgot Password?</h1>
          <p class="subtitle">Enter your email address and we'll send you a link to reset your password</p>

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
          </form>

          <div class="back-to-login">
            <router-link to="/login">← Back to Login</router-link>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import Layout from '@/components/layout/Layout.vue'
import axios from 'axios'

export default {
  name: 'ForgotPasswordView',
  components: {
    Layout
  },
  data() {
    return {
      email: '',
      isLoading: false,
      successMessage: '',
      generalError: '',
      errors: {
        email: ''
      }
    }
  },
  methods: {
    clearError() {
      this.errors.email = ''
      this.generalError = ''
    },

    validateForm() {
      this.errors.email = ''

      if (!this.email) {
        this.errors.email = 'Email is required'
        return false
      }

      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
        this.errors.email = 'Please enter a valid email address'
        return false
      }

      return true
    },

    async handleForgotPassword() {
      this.successMessage = ''
      this.generalError = ''

      if (!this.validateForm()) {
        return
      }

      this.isLoading = true

      try {
        // CHANGED: Use buyer-specific endpoint
        const response = await axios.post(
          process.env.VUE_APP_API_BASE_URL + '/buyer/forgot-password',
          { email: this.email }
        )

        this.successMessage = response.data.message
        this.email = ''
        this.isLoading = false

      } catch (error) {
        this.isLoading = false

        if (error.response?.status === 422) {
          const backendErrors = error.response.data.errors
          
          if (backendErrors.email) {
            this.errors.email = backendErrors.email[0]
          }
        } else if (error.response?.status === 403) {
          // User is not a buyer
          this.generalError = error.response.data.message
        } else {
          this.generalError = error.response?.data?.message || 'Failed to send reset link. Please try again.'
        }
      }
    }
  }
}
</script>

<style scoped>
.forgot-password-page {
  min-height: 70vh;
  display: flex;
  align-items: center;
  background-color: #f8f9fa;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.forgot-password-card {
  max-width: 450px;
  margin: 0 auto;
  background: white;
  padding: 3rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.forgot-password-card h1 {
  text-align: center;
  color: #2c3e50;
  margin: 0 0 0.5rem 0;
}

.subtitle {
  text-align: center;
  color: #666;
  margin: 0 0 2rem 0;
  font-size: 0.95rem;
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
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: all 0.3s;
}

.form-group input:focus {
  outline: none;
  border-color: #42b983;
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
  border-radius: 4px;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
  font-size: 0.9rem;
}

.success-message i {
  font-size: 16px;
}

.general-error {
  background: #f8d7da;
  color: #721c24;
  padding: 12px 16px;
  border-radius: 4px;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
  font-size: 0.9rem;
}

.general-error i {
  font-size: 16px;
}

.btn-submit {
  width: 100%;
  padding: 1rem;
  background-color: #42b983;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-submit:hover:not(:disabled) {
  background-color: #359268;
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.back-to-login {
  text-align: center;
  margin-top: 2rem;
}

.back-to-login a {
  color: #42b983;
  font-weight: 500;
  text-decoration: none;
}

.back-to-login a:hover {
  text-decoration: underline;
}

@media (max-width: 768px) {
  .forgot-password-card {
    padding: 2rem 1.5rem;
  }
}
</style>
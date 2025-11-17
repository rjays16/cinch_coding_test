<template>
  <Layout>
    <div class="reset-password-page">
      <div class="container">
        <div class="reset-password-card">
          <h1>Reset Password</h1>
          <p class="subtitle">Enter your new password</p>

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
  name: 'ResetPasswordView',
  components: {
    Layout
  },
  data() {
    return {
      token: '',
      email: '',
      password: '',
      confirmPassword: '',
      isLoading: false,
      successMessage: '',
      generalError: '',
      errors: {
        password: '',
        confirmPassword: ''
      }
    }
  },
  mounted() {
    this.token = this.$route.query.token || ''
    this.email = this.$route.query.email || ''

    if (!this.token || !this.email) {
      this.generalError = 'Invalid reset link. Please request a new password reset.'
    }
  },
  methods: {
    clearError(field) {
      this.errors[field] = ''
      this.generalError = ''
    },

    validateForm() {
      let isValid = true
      this.errors = {
        password: '',
        confirmPassword: ''
      }

      if (!this.password) {
        this.errors.password = 'Password is required'
        isValid = false
      } else if (this.password.length < 6) {
        this.errors.password = 'Password must be at least 6 characters'
        isValid = false
      }

      if (!this.confirmPassword) {
        this.errors.confirmPassword = 'Please confirm your password'
        isValid = false
      } else if (this.password !== this.confirmPassword) {
        this.errors.confirmPassword = 'Passwords do not match'
        isValid = false
      }

      return isValid
    },

    async handleResetPassword() {
      this.successMessage = ''
      this.generalError = ''

      if (!this.validateForm()) {
        return
      }

      this.isLoading = true

      try {
        // IMPORTANT: Laravel expects 'password_confirmation', not 'confirmPassword'
        const response = await axios.post(
          process.env.VUE_APP_API_BASE_URL + '/reset-password',
          {
            token: this.token,
            email: this.email,
            password: this.password,
            password_confirmation: this.confirmPassword  // ← Changed from confirmPassword
          }
        )

        this.successMessage = response.data.message
        
        // Clear form
        this.password = ''
        this.confirmPassword = ''

        // Redirect to login after 2 seconds
        setTimeout(() => {
          this.$router.push('/login')
        }, 2000)

      } catch (error) {
        this.isLoading = false

        if (error.response?.status === 422) {
          const backendErrors = error.response.data.errors
          
          if (backendErrors.password) {
            this.errors.password = backendErrors.password[0]
          }
          if (backendErrors.password_confirmation) {
            this.errors.confirmPassword = backendErrors.password_confirmation[0]
          }
        } else {
          this.generalError = error.response?.data?.message || 'Failed to reset password. Please try again.'
        }
      }
    }
  }
}
</script>

<style scoped>
.reset-password-page {
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

.reset-password-card {
  max-width: 450px;
  margin: 0 auto;
  background: white;
  padding: 3rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.reset-password-card h1 {
  text-align: center;
  color: #2c3e50;
  margin: 0 0 0.5rem 0;
}

.subtitle {
  text-align: center;
  color: #666;
  margin: 0 0 2rem 0;
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
  .reset-password-card {
    padding: 2rem 1.5rem;
  }
}
</style>
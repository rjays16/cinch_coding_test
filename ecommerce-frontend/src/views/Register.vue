<template>
  <Layout>
    <div class="register-page">
      <div class="container">
        <div class="register-card">
          <h1>Create Account</h1>
          <p class="subtitle">Join us and start shopping today!</p>

          <form @submit.prevent="handleRegister" class="register-form">
            <div class="form-group" :class="{ 'has-error': errors.name }">
              <label for="name">Full Name</label>
              <input
                type="text"
                id="name"
                v-model="name"
                placeholder="Enter your full name"
                @input="clearError('name')"
                :disabled="isLoading"
              />
              <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
            </div>

            <div class="form-group" :class="{ 'has-error': errors.email }">
              <label for="email">Email Address</label>
              <input
                type="email"
                id="email"
                v-model="email"
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
                v-model="password"
                placeholder="Create a password"
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
                placeholder="Confirm your password"
                @input="clearError('confirmPassword')"
                :disabled="isLoading"
              />
              <span v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</span>
            </div>

            <div class="form-group checkbox-group" :class="{ 'has-error': errors.agreeTerms }">
              <label class="checkbox-label">
                <input 
                  type="checkbox" 
                  v-model="agreeTerms" 
                  @change="clearError('agreeTerms')"
                  :disabled="isLoading"
                />
                I agree to the Terms & Conditions and Privacy Policy
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
              <span v-if="!isLoading">Create Account</span>
              <span v-else>
                <i class="fas fa-spinner fa-spin"></i> Creating Account...
              </span>
            </button>
          </form>

          <div class="login-link">
            Already have an account?
            <router-link to="/login">Login here</router-link>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import Layout from '@/components/layout/Layout.vue'
import { authService } from '@/services/authService'

export default {
  name: 'RegisterView',
  components: {
    Layout
  },
  data() {
    return {
      name: '',
      email: '',
      password: '',
      confirmPassword: '',
      agreeTerms: false,
      isLoading: false,
      successMessage: '',
      generalError: '',
      errors: {
        name: '',
        email: '',
        password: '',
        confirmPassword: '',
        agreeTerms: ''
      }
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
        name: '',
        email: '',
        password: '',
        confirmPassword: '',
        agreeTerms: ''
      }

      if (!this.name.trim()) {
        this.errors.name = 'Full name is required'
        isValid = false
      }

      if (!this.email) {
        this.errors.email = 'Email is required'
        isValid = false
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
        this.errors.email = 'Please enter a valid email address'
        isValid = false
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

      if (!this.agreeTerms) {
        this.errors.agreeTerms = 'You must agree to the terms and conditions'
        isValid = false
      }

      return isValid
    },

    async handleRegister() {
      this.successMessage = ''
      this.generalError = ''

      if (!this.validateForm()) {
        return
      }

      this.isLoading = true

      try {
        const response = await authService.register(
          this.name,
          this.email,
          this.password,
          this.confirmPassword
        )

        // Save token and user data
        localStorage.setItem('buyer_token', response.token)
        localStorage.setItem('buyer_user', JSON.stringify(response.user))

        this.successMessage = 'Account created successfully! Redirecting...'

        setTimeout(() => {
          this.$router.push('/')
        }, 1500)

      } catch (error) {
        this.isLoading = false

        if (error.response?.status === 422) {
          const backendErrors = error.response.data.errors
          
          if (backendErrors.name) {
            this.errors.name = backendErrors.name[0]
          }
          if (backendErrors.email) {
            this.errors.email = backendErrors.email[0]
          }
          if (backendErrors.password) {
            this.errors.password = backendErrors.password[0]
          }
        } else {
          this.generalError = error.response?.data?.message || 'Registration failed. Please try again.'
        }
      }
    }
  }
}
</script>

<style scoped>
.register-page {
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

.register-card {
  max-width: 450px;
  margin: 0 auto;
  background: white;
  padding: 3rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.register-card h1 {
  text-align: center;
  color: #2c3e50;
  margin: 0 0 0.5rem 0;
}

.subtitle {
  text-align: center;
  color: #666;
  margin: 0 0 2rem 0;
}

.register-form {
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

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
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

/* Error state */
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

.checkbox-group {
  margin: 1.5rem 0;
}

.checkbox-label {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  color: #666;
  cursor: pointer;
  font-size: 0.9rem;
}

.checkbox-label input[type="checkbox"] {
  margin-top: 0.2rem;
}

.checkbox-label input[type="checkbox"]:disabled {
  cursor: not-allowed;
}

.btn-register {
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

.btn-register:hover:not(:disabled) {
  background-color: #359268;
}

.btn-register:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.login-link {
  text-align: center;
  margin-top: 2rem;
  color: #666;
}

.login-link a {
  color: #42b983;
  font-weight: bold;
  text-decoration: none;
}

.login-link a:hover {
  text-decoration: underline;
}

@media (max-width: 768px) {
  .register-card {
    padding: 2rem 1.5rem;
  }
}
</style>
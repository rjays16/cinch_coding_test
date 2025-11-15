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
            />
            <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
          </div>

          <div class="form-options">
            <label class="remember-me">
              <input type="checkbox" v-model="formData.rememberMe" />
              <span>Remember me</span>
            </label>
            <a href="#" class="forgot-link">Forgot password?</a>
          </div>

          <button type="submit" class="btn-login">Login to Dashboard</button>

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
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import RightSection from '@/components/RightSection.vue'

const router = useRouter()

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

const validateForm = () => {
  let isValid = true
  
  // Reset errors
  errors.value = {
    email: '',
    password: ''
  }

  // Email validation
  if (!formData.value.email) {
    errors.value.email = 'Email is required'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
    errors.value.email = 'Please enter a valid email address'
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

  return isValid
}

const handleLogin = () => {
  if (!validateForm()) {
    return
  }

  // Save dummy seller data to localStorage
  localStorage.setItem('seller_token', 'dummy-token-12345')
  localStorage.setItem('seller_user', JSON.stringify({
    id: 1,
    first_name: 'John',
    last_name: 'Doe',
    full_name: 'John Doe',
    store_name: 'My Awesome Store',
    email: formData.value.email,
    role: 'seller'
  }))

  // Navigate to dashboard
  router.push('/dashboard')
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

.btn-login:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
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
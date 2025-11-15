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
            />
            <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
          </div>

          <div class="form-group" :class="{ 'has-error': errors.phone }">
            <label for="phone">Phone Number</label>
            <input
              type="tel"
              id="phone"
              v-model="formData.phone"
              placeholder="+1 234 567 8900"
              @input="clearError('phone')"
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
              />
              <span> I agree to the Terms & Conditions and Privacy Policy</span>
            </label>
            <span v-if="errors.agreeTerms" class="error-message">{{ errors.agreeTerms }}</span>
          </div>

          <button type="submit" class="btn-register">Create Seller Account</button>

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

const handleRegister = () => {
  if (!validateForm()) {
    return
  }

  // Save to localStorage and redirect
  localStorage.setItem('seller_token', 'dummy-token-12345')
  localStorage.setItem('seller_user', JSON.stringify({
    id: 1,
    first_name: formData.value.firstName,
    last_name: formData.value.lastName,
    full_name: `${formData.value.firstName} ${formData.value.lastName}`,
    store_name: formData.value.storeName,
    email: formData.value.email,
    phone: formData.value.phone,
    role: 'seller'
  }))

  router.push('/dashboard')
}
</script>

<style scoped>
.register-page {
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

.btn-register:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
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

/* Responsive */
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
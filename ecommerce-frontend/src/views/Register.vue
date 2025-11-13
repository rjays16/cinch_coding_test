<template>
  <Layout>
    <div class="register-page">
      <div class="container">
        <div class="register-card">
          <h1>Create Account</h1>
          <p class="subtitle">Join us and start shopping today!</p>

          <form @submit.prevent="handleRegister" class="register-form">
            <div class="form-group">
              <label for="name">Full Name</label>
              <input
                type="text"
                id="name"
                v-model="name"
                placeholder="Enter your full name"
                required
              />
            </div>

            <div class="form-group">
              <label for="email">Email Address</label>
              <input
                type="email"
                id="email"
                v-model="email"
                placeholder="Enter your email"
                required
              />
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <input
                type="password"
                id="password"
                v-model="password"
                placeholder="Create a password"
                required
              />
            </div>

            <div class="form-group">
              <label for="confirmPassword">Confirm Password</label>
              <input
                type="password"
                id="confirmPassword"
                v-model="confirmPassword"
                placeholder="Confirm your password"
                required
              />
            </div>

            <div class="form-group checkbox-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="agreeTerms" required />
                I agree to the Terms & Conditions and Privacy Policy
              </label>
            </div>

            <button type="submit" class="btn-register">Create Account</button>
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
      agreeTerms: false
    }
  },
  methods: {
    handleRegister() {
      // Validate passwords match
      if (this.password !== this.confirmPassword) {
        alert('Passwords do not match!')
        return
      }

      // For now, just show alert (will connect to API later)
      alert(`Register with: ${this.name}, ${this.email}`)
      // Will add API call later
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
  transition: border-color 0.3s;
}

.form-group input:focus {
  outline: none;
  border-color: #42b983;
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

.btn-register:hover {
  background-color: #359268;
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

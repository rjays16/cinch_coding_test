<template>
  <Layout>
    <div class="login-page">
      <div class="container">
        <div class="login-card">
          <h1>Login</h1>
          <p class="subtitle">Welcome back! Please login to your account</p>

          <form @submit.prevent="handleLogin" class="login-form">
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
                placeholder="Enter your password"
                required
              />
            </div>

            <div class="form-options">
              <label class="checkbox-label">
                <input type="checkbox" v-model="rememberMe" />
                Remember me
              </label>
              <a href="#" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-login">Login</button>
          </form>

          <div class="register-link">
            Don't have an account?
            <router-link to="/register">Register here</router-link>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import Layout from '@/components/layout/Layout.vue'

export default {
  name: 'LoginView',
  components: {
    Layout
  },
  data() {
    return {
      email: '',
      password: '',
      rememberMe: false
    }
  },
  methods: {
    handleLogin() {
      // For now, just show alert (will connect to API later)
      alert(`Login with: ${this.email}`)
      // Will add API call later
    }
  }
}
</script>

<style scoped>
.login-page {
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

.login-card {
  max-width: 450px;
  margin: 0 auto;
  background: white;
  padding: 3rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.login-card h1 {
  text-align: center;
  color: #2c3e50;
  margin: 0 0 0.5rem 0;
}

.subtitle {
  text-align: center;
  color: #666;
  margin: 0 0 2rem 0;
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
  font-weight: 500;
}

.form-group input {
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

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #666;
  cursor: pointer;
}

.forgot-password {
  color: #42b983;
  text-decoration: none;
  font-size: 0.9rem;
}

.forgot-password:hover {
  text-decoration: underline;
}

.btn-login {
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

.btn-login:hover {
  background-color: #359268;
}

.register-link {
  text-align: center;
  margin-top: 2rem;
  color: #666;
}

.register-link a {
  color: #42b983;
  font-weight: bold;
  text-decoration: none;
}

.register-link a:hover {
  text-decoration: underline;
}

@media (max-width: 768px) {
  .login-card {
    padding: 2rem 1.5rem;
  }
}
</style>
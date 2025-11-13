<template>
  <header class="header">
    <div class="container">
      <div class="header-content">
        <!-- Logo -->
        <router-link to="/" class="logo">
          <h1>E-Commerce</h1>
        </router-link>

        <!-- Navigation Menu -->
        <nav class="nav-menu">
          <router-link to="/" class="nav-link">Home</router-link>
          <router-link to="/seller" class="nav-link">Be A Seller</router-link>
          <router-link to="/about" class="nav-link">About</router-link>
          <router-link to="/contact" class="nav-link">Contact</router-link>
        </nav>

        <!-- Auth Links -->
        <div class="auth-links">
          <router-link to="/login" class="btn-login">Login</router-link>
          <router-link to="/register" class="btn-register">Register</router-link>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'Header'
}
</script>

<style scoped>
.header {
  background-color: #2c3e50;
  color: white;
  padding: 1rem 0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  text-decoration: none;
  color: white;
}

.logo h1 {
  margin: 0;
  font-size: 1.8rem;
  font-weight: bold;
}

.nav-menu {
  display: flex;
  gap: 2rem;
  align-items: center;
}

.nav-link {
  color: white;
  text-decoration: none;
  font-size: 1rem;
  font-weight: 500;
  transition: color 0.3s;
}

.nav-link:hover,
.nav-link.router-link-active {
  color: #42b983;
}

.auth-links {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.btn-login,
.btn-register {
  padding: 0.5rem 1.5rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s;
}

.btn-login {
  color: white;
  border: 1px solid white;
}

.btn-login:hover {
  background-color: white;
  color: #2c3e50;
}

.btn-register {
  background-color: #42b983;
  color: white;
}

.btn-register:hover {
  background-color: #359268;
}

/* Responsive */
@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 1rem;
  }

  .nav-menu {
    gap: 1rem;
  }
}
</style>
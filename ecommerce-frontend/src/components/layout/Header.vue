<template>
  <header class="header">
    <div class="container">
      <div class="header-content">
        <div class="logo">
          <router-link to="/">E-Commerce</router-link>
        </div>

        <nav class="nav-links">
          <router-link to="/">Home</router-link>
          <a :href="sellerPortalUrl" target="_blank" class="seller-link">Be A Seller</a>
          <router-link to="/about">About</router-link>
          <router-link to="/contact">Contact</router-link>
        </nav>

        <div class="header-actions">
          <!-- Show if NOT logged in -->
          <template v-if="!isAuthenticated">
            <router-link to="/login" class="btn-login">Login</router-link>
            <router-link to="/register" class="btn-register">Register</router-link>
          </template>

          <!-- Show if logged in -->
          <template v-else>
            <router-link to="/cart" class="cart-icon">
              🛒
              <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
            </router-link>
            <div class="user-menu">
              <span class="user-name">{{ userName }}</span>
              <button @click="showLogoutModal = true" class="btn-logout">Logout</button>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <LogoutModal 
      :show="showLogoutModal"
      :isLoggingOut="isLoggingOut"
      @close="showLogoutModal = false"
      @confirm="handleLogout"
    />
  </header>
</template>

<script>
import { authService } from '@/services/authService'
import LogoutModal from '@/components/LogoutModal.vue'

export default {
  name: 'Header',
  components: {
    LogoutModal
  },
  data() {
    return {
      isAuthenticated: false,
      userName: '',
      cartCount: 0,
      showLogoutModal: false,
      isLoggingOut: false,
      sellerPortalUrl: process.env.VUE_APP_SELLER_PORTAL_URL || 'http://localhost:5173'
    }
  },
  mounted() {
    this.checkAuth()
  },
  methods: {
    checkAuth() {
      this.isAuthenticated = authService.isAuthenticated()
      if (this.isAuthenticated) {
        const user = authService.getUser()
        this.userName = user?.name || 'User'
        // TODO: Get cart count from API or store
        this.cartCount = 0
      }
    },

    async handleLogout() {
      this.isLoggingOut = true

      try {
        await authService.logout()
        authService.clearAuth()
        this.isAuthenticated = false
        this.showLogoutModal = false
        this.isLoggingOut = false
        this.$router.push('/login')
      } catch (error) {
        console.error('Logout error:', error)
        // Force logout even if API fails
        authService.clearAuth()
        this.isAuthenticated = false
        this.showLogoutModal = false
        this.isLoggingOut = false
        this.$router.push('/login')
      }
    }
  },
  watch: {
    '$route'() {
      this.checkAuth()
    }
  }
}
</script>

<style scoped>
.header {
  background-color: #2c3e50;
  color: white;
  padding: 1rem 0;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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

.logo a {
  color: white;
  text-decoration: none;
  font-size: 1.8rem;
  font-weight: bold;
}

.nav-links {
  display: flex;
  gap: 2rem;
}

.nav-links a {
  color: white;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}

.nav-links a:hover,
.nav-links a.router-link-active {
  color: #42b983;
}

.seller-link {
  color: #42b983 !important;
  font-weight: bold !important;
  position: relative;
}

.seller-link:hover {
  color: #5dcea4 !important;
  transform: translateY(-2px);
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.btn-login {
  color: white;
  background-color: transparent;
  border: 1px solid white;
  padding: 0.5rem 1.5rem;
  border-radius: 4px;
  text-decoration: none;
  transition: all 0.3s;
}

.btn-login:hover {
  background-color: white;
  color: #2c3e50;
}

.btn-register {
  background-color: #42b983;
  color: white;
  padding: 0.5rem 1.5rem;
  border-radius: 4px;
  text-decoration: none;
  transition: all 0.3s;
}

.btn-register:hover {
  background-color: #359268;
}

.cart-icon {
  position: relative;
  font-size: 1.5rem;
  text-decoration: none;
  color: white;
  transition: transform 0.3s;
}

.cart-icon:hover {
  transform: scale(1.1);
}

.cart-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background-color: #ff4444;
  color: white;
  font-size: 0.75rem;
  font-weight: bold;
  padding: 2px 6px;
  border-radius: 50%;
  min-width: 18px;
  text-align: center;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-name {
  color: white;
  font-weight: 500;
}

.btn-logout {
  background-color: #ff4444;
  color: white;
  border: none;
  padding: 0.5rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s;
}

.btn-logout:hover {
  background-color: #cc0000;
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 1rem;
  }

  .nav-links {
    gap: 1rem;
  }
}
</style>
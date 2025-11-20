<template>
  <header class="header">
    <div class="header-container">
      <!-- Logo (Text Only) -->
      <router-link to="/" class="logo">
        E-Commerce
      </router-link>

      <!-- Navigation -->
      <nav class="nav-menu">
        <router-link to="/" class="nav-link">Home</router-link>
        <router-link to="/products" class="nav-link">Products</router-link>
        <router-link to="/about" class="nav-link">About</router-link>
        <router-link to="/contact" class="nav-link">Contact</router-link>
      </nav>

      <!-- Actions -->
      <div class="header-actions">
        <!-- Be A Seller Link -->
        <a href="http://localhost:5173" target="_blank" class="btn-seller">
          <i class="fas fa-store"></i>
          <span>Be A Seller</span>
        </a>

        <!-- Cart Icon (Only show when logged in) -->
        <button v-if="isAuthenticated" @click="goToCart" class="btn-cart">
          <i class="fas fa-shopping-cart"></i>
          <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
        </button>

        <!-- User Menu (When logged in) -->
        <div v-if="isAuthenticated" class="user-menu">
          <button @click="toggleDropdown" class="btn-user">
            <i class="fas fa-user-circle"></i>
            <span>{{ userName }}</span>
          </button>
          
          <div v-if="showDropdown" class="dropdown-menu">
            <router-link to="/profile" class="dropdown-item" @click="showDropdown = false">
              <i class="fas fa-user"></i>
              Profile
            </router-link>
            <router-link to="/orders" class="dropdown-item" @click="showDropdown = false">
              <i class="fas fa-shopping-bag"></i>
              My Orders
            </router-link>
            <button @click="handleLogoutClick" class="dropdown-item">
              <i class="fas fa-sign-out-alt"></i>
              Logout
            </button>
          </div>
        </div>

        <!-- Login & Sign Up Buttons (When not logged in) -->
        <div v-else class="auth-buttons">
          <router-link to="/login" class="btn-login">
            Login
          </router-link>
          <router-link to="/register" class="btn-signup">
            Sign Up
          </router-link>
        </div>
      </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <LogoutModal
      :show="showLogoutConfirm"
      :isLoading="isLoggingOut"
      @confirm="confirmLogout"
      @cancel="cancelLogout"
    />
  </header>
</template>

<script>
import { authService } from '@/services/authService'
import { useCart } from '@/composables/useCart'
import LogoutModal from '@/components/modals/LogoutModal.vue'

export default {
  name: 'Header',
  components: {
    LogoutModal
  },
  data() {
    return {
      showDropdown: false,
      isAuthenticated: false,
      userName: '',
      showLogoutConfirm: false,
      isLoggingOut: false
    }
  },
  setup() {
    const { cartCount, loadCart, clearCart } = useCart()
    
    return {
      cartCount,
      loadCart,
      clearCart
    }
  },
  methods: {
    async checkAuth() {
      this.isAuthenticated = authService.isAuthenticated()
      if (this.isAuthenticated) {
        const user = authService.getUser()
        this.userName = user?.name || 'User'
        // Load cart when authenticated
        await this.loadCart()
      } else {
        this.userName = ''
        this.clearCart()
      }
    },
    
    toggleDropdown() {
      this.showDropdown = !this.showDropdown
    },
    
    // STEP 1: When user clicks "Logout" in dropdown
    handleLogoutClick() {
      this.showLogoutConfirm = true
      this.showDropdown = false
    },
    
    // STEP 2: When user clicks "Cancel" in modal
    cancelLogout() {
      this.showLogoutConfirm = false
    },
    
    // STEP 3: When user clicks "Confirm Logout" in modal
    async confirmLogout() {
      if (this.isLoggingOut) return

      try {
        this.isLoggingOut = true
        
        await new Promise(resolve => setTimeout(resolve, 500))
        
        await authService.logout()
        
        this.clearCart()
        
        this.checkAuth()
        
        this.showLogoutConfirm = false
        this.isLoggingOut = false
        
        this.$router.push('/login')
      } catch (error) {
        this.isLoggingOut = false
        this.showLogoutConfirm = false
        this.$router.push('/login')
      }
    },
    
    goToCart() {
      this.$router.push('/cart')
    }
  },
  
  async mounted() {
    
    // Check auth on mount
    await this.checkAuth()

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.user-menu')) {
        this.showDropdown = false
      }
    })

    // Listen for storage changes
    window.addEventListener('storage', (e) => {
      if (e.key === 'buyer_token' || e.key === 'buyer_user' || e.key === null) {
        this.checkAuth()
      }
    })

    // Listen for cart update events
    window.addEventListener('cart-updated', async () => {
      await this.loadCart()
    })
  },
  
  watch: {
    '$route'() {
      this.checkAuth()
    },
    cartCount(newVal) {
      console.log('🛒 Cart count changed to:', newVal)
    }
  }
}
</script>

<style scoped>
.header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 1rem 2rem;
  display: flex;
  align-items: center;
  gap: 2rem;
}

.logo {
  font-size: 1.5rem;
  font-weight: bold;
  color: white;
  text-decoration: none;
  transition: opacity 0.3s;
  flex-shrink: 0;
  width: 180px;
}

.logo:hover {
  opacity: 0.9;
}

.nav-menu {
  display: flex;
  gap: 2rem;
  justify-content: center;
  align-items: center;
  flex: 1;
}

.nav-link {
  color: white;
  text-decoration: none;
  font-weight: 500;
  transition: opacity 0.3s;
  padding: 0.5rem 0;
  position: relative;
  white-space: nowrap;
}

.nav-link:hover {
  opacity: 0.8;
}

.nav-link.router-link-active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: white;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-shrink: 0;
  width: 180px;
  justify-content: flex-end;
}

/* Be A Seller Button */
.btn-seller {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.5rem 1rem;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.875rem;
  transition: all 0.3s;
  border: 2px solid rgba(255, 255, 255, 0.3);
  white-space: nowrap;
}

.btn-seller:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
}

.btn-seller i {
  font-size: 0.875rem;
}

/* Cart Button */
.btn-cart {
  position: relative;
  background: rgba(255, 255, 255, 0.2);
  border: none;
  font-size: 1.25rem;
  color: white;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: background 0.3s;
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-cart:hover {
  background: rgba(255, 255, 255, 0.3);
}

.cart-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #e74c3c;
  color: white;
  font-size: 0.7rem;
  font-weight: bold;
  padding: 0.15rem 0.4rem;
  border-radius: 10px;
  min-width: 18px;
  text-align: center;
  line-height: 1;
}

/* User Menu */
.user-menu {
  position: relative;
}

.btn-user {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: white;
  font-size: 0.875rem;
  cursor: pointer;
  padding: 0.5rem 0.875rem;
  border-radius: 6px;
  transition: background 0.3s;
  white-space: nowrap;
}

.btn-user:hover {
  background: rgba(255, 255, 255, 0.3);
}

.btn-user i {
  font-size: 1.1rem;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 0.5rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  min-width: 180px;
  overflow: hidden;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  color: #2c3e50;
  text-decoration: none;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  transition: background 0.3s;
  font-size: 0.9rem;
}

.dropdown-item:hover {
  background: #f8f9fa;
}

.dropdown-item i {
  font-size: 0.95rem;
  width: 16px;
}

/* Auth Buttons */
.auth-buttons {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.btn-login,
.btn-signup {
  padding: 0.5rem 1rem;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.875rem;
  transition: all 0.3s;
  display: inline-block;
  white-space: nowrap;
}

.btn-login {
  color: white;
  background: transparent;
  border: 2px solid white;
}

.btn-login:hover {
  background: rgba(255, 255, 255, 0.2);
}

.btn-signup {
  background: white;
  color: #667eea;
  border: 2px solid white;
}

.btn-signup:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Responsive */
@media (max-width: 1200px) {
  .logo {
    width: 150px;
  }

  .header-actions {
    width: 150px;
  }

  .nav-menu {
    gap: 1.5rem;
  }

  .nav-link {
    font-size: 0.9rem;
  }
}

@media (max-width: 968px) {
  .header-container {
    flex-wrap: wrap;
    padding: 1rem;
    gap: 1rem;
  }

  .logo {
    width: auto;
    flex: 0 0 auto;
  }

  .nav-menu {
    flex: 1;
    gap: 1.25rem;
    justify-content: center;
  }

  .header-actions {
    width: auto;
    flex: 0 0 auto;
  }

  .logo {
    font-size: 1.25rem;
  }

  .nav-link {
    font-size: 0.85rem;
  }

  .btn-seller {
    padding: 0.45rem 0.875rem;
    font-size: 0.8rem;
  }

  .btn-login,
  .btn-signup {
    padding: 0.45rem 0.875rem;
    font-size: 0.8rem;
  }

  .header-actions {
    gap: 0.5rem;
  }
}

@media (max-width: 768px) {
  .header-container {
    flex-direction: column;
    text-align: center;
  }

  .logo {
    width: 100%;
    justify-content: center;
  }

  .nav-menu {
    order: 3;
    width: 100%;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
    gap: 1rem;
  }

  .nav-link {
    font-size: 0.9rem;
  }

  .header-actions {
    order: 2;
    width: 100%;
    justify-content: center;
    flex-wrap: wrap;
  }

  .btn-seller {
    font-size: 0.75rem;
    padding: 0.45rem 0.75rem;
  }

  .btn-login,
  .btn-signup {
    font-size: 0.8rem;
    padding: 0.45rem 0.875rem;
  }
}

@media (max-width: 576px) {
  .header-container {
    padding: 0.75rem;
  }

  .logo {
    font-size: 1.1rem;
  }

  .nav-menu {
    gap: 0.75rem;
  }

  .nav-link {
    font-size: 0.8rem;
  }

  .btn-seller span {
    display: none;
  }

  .btn-seller {
    padding: 0.5rem;
    width: 38px;
    height: 38px;
    justify-content: center;
  }

  .btn-login,
  .btn-signup {
    font-size: 0.8rem;
    padding: 0.45rem 0.75rem;
  }

  .auth-buttons {
    gap: 0.5rem;
  }

  .btn-user {
    font-size: 0.8rem;
    padding: 0.45rem 0.75rem;
  }
}
</style>
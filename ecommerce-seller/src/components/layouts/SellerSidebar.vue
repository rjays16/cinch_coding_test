<template>
  <aside class="sidebar" :class="{ closed: !isOpen }">
    <div class="sidebar-header">
      <h2>Seller Dashboard</h2>
      <button class="close-btn" @click="$emit('toggle')">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <nav class="sidebar-nav">
      <router-link to="/dashboard" class="nav-item">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
      </router-link>

      <router-link to="/products" class="nav-item">
        <i class="fas fa-box"></i>
        <span>Products</span>
      </router-link>

      <router-link to="/categories" class="nav-item">
        <i class="fas fa-tags"></i>
        <span>Categories</span>
      </router-link>

      <router-link to="/orders" class="nav-item">
        <i class="fas fa-shopping-cart"></i>
        <span>Orders</span>
      </router-link>

      <router-link to="/profile" class="nav-item">
        <i class="fas fa-user"></i>
        <span>Profile</span>
      </router-link>

      <router-link to="/settings" class="nav-item">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </router-link>

      <button @click="handleLogout" class="nav-item logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </button>
    </nav>

    <div class="sidebar-footer">
      <div class="store-info">
        <p class="store-name">{{ storeName }}</p>
        <p class="user-email">{{ userEmail }}</p>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

defineProps({
  isOpen: {
    type: Boolean,
    default: true
  }
})

defineEmits(['toggle'])

const router = useRouter()
const storeName = ref('')
const userEmail = ref('')

onMounted(() => {
  const user = JSON.parse(localStorage.getItem('seller_user') || '{}')
  storeName.value = user.store_name || 'My Store'
  userEmail.value = user.email || ''
})

const handleLogout = () => {
  localStorage.removeItem('seller_token')
  localStorage.removeItem('seller_user')
  router.push('/login')
}
</script>

<style scoped>
.sidebar {
  width: 260px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease;
  z-index: 1000;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar.closed {
  transform: translateX(-260px);
}

.sidebar-header {
  padding: 25px 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.sidebar-header h2 {
  font-size: 20px;
  font-weight: 600;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 20px;
  cursor: pointer;
  padding: 5px;
  display: none;
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
  overflow-y: auto;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 15px 25px;
  color: white;
  text-decoration: none;
  transition: all 0.3s ease;
  border-left: 3px solid transparent;
  cursor: pointer;
  background: none;
  border-right: none;
  border-top: none;
  border-bottom: none;
  width: 100%;
  text-align: left;
  font-size: 15px;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1);
  border-left-color: white;
}

.nav-item.router-link-active {
  background: rgba(255, 255, 255, 0.15);
  border-left-color: white;
  font-weight: 600;
}

.nav-item i {
  width: 20px;
  margin-right: 15px;
  font-size: 18px;
}

.logout-btn {
  margin-top: 10px;
  opacity: 0.9;
}

.logout-btn:hover {
  opacity: 1;
  background: rgba(255, 255, 255, 0.15);
}

.sidebar-footer {
  padding: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.store-info {
  background: rgba(255, 255, 255, 0.1);
  padding: 15px;
  border-radius: 8px;
}

.store-name {
  font-weight: 600;
  font-size: 14px;
  margin: 0 0 5px 0;
}

.user-email {
  font-size: 12px;
  opacity: 0.8;
  margin: 0;
  word-break: break-word;
}

@media (max-width: 768px) {
  .close-btn {
    display: block;
  }

  .sidebar {
    width: 280px;
  }
  
  .sidebar.closed {
    transform: translateX(-280px);
  }
}
</style>
<template>
  <header class="seller-header">
    <div class="header-left">
      <button class="menu-toggle" @click="$emit('toggleSidebar')">
        <i class="fas fa-bars"></i>
      </button>
      <h1 class="page-title">{{ pageTitle }}</h1>
    </div>

    <div class="header-right">
      <!-- Notification Bell with Dropdown -->
      <div class="notifications">
        <button class="icon-btn" @click.stop="toggleNotifications">
          <i class="fas fa-bell"></i>
          <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
        </button>

        <!-- Notification Dropdown -->
        <transition name="fade">
          <div v-if="showNotifications" class="notification-dropdown" @click.stop>
            <div class="notification-header">
              <h3>Notifications</h3>
              <span class="count">{{ unreadCount }} new</span>
            </div>

            <div class="notification-list">
              <div 
                v-for="notification in notifications" 
                :key="notification.id"
                class="notification-item"
                :class="{ unread: !notification.read }"
                @click="markAsRead(notification.id)"
              >
                <div class="notification-icon" :class="notification.type">
                  <i :class="notification.icon"></i>
                </div>
                <div class="notification-content">
                  <p class="notification-title">{{ notification.title }}</p>
                  <p class="notification-message">{{ notification.message }}</p>
                  <span class="notification-time">{{ notification.time }}</span>
                </div>
              </div>

              <div v-if="notifications.length === 0" class="no-notifications">
                <i class="fas fa-bell-slash"></i>
                <p>No notifications yet</p>
              </div>
            </div>

            <div class="notification-footer">
              <button @click="viewAllNotifications">View All</button>
            </div>
          </div>
        </transition>
      </div>

      <div class="user-menu">
        <div class="user-avatar">
          <img :src="userAvatar" alt="User" />
        </div>
        <span class="user-name">{{ userName }}</span>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRoute, useRouter } from 'vue-router'

defineEmits(['toggleSidebar'])

const route = useRoute()
const router = useRouter()
const userName = ref('')
const userAvatar = ref('https://ui-avatars.com/api/?name=User&background=667eea&color=fff')
const showNotifications = ref(false)

// Two notifications - New Order + Low Stock Alert
const notifications = ref([
  {
    id: 1,
    type: 'order',
    icon: 'fas fa-shopping-cart',
    title: 'New Order Received',
    message: 'Order #12345 - $89.99',
    time: '2 minutes ago',
    read: false
  },
  {
    id: 2,
    type: 'product',
    icon: 'fas fa-box',
    title: 'Low Stock Alert',
    message: 'Blue T-Shirt - Only 5 left',
    time: '1 hour ago',
    read: false
  }
])

const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.read).length
})

const pageTitle = computed(() => {
  const titles = {
    '/dashboard': 'Dashboard',
    '/products': 'Products',
    '/products/add': 'Add Product',
    '/categories': 'Categories',
    '/orders': 'Orders',
    '/profile': 'Profile',
    '/settings': 'Settings'
  }
  return titles[route.path] || 'Dashboard'
})

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
}

const markAsRead = (id) => {
  const notification = notifications.value.find(n => n.id === id)
  if (notification) {
    notification.read = true
  }
}

const viewAllNotifications = () => {
  showNotifications.value = false
  router.push('/notifications')
}

// Close dropdown when clicking outside
const closeNotifications = () => {
  showNotifications.value = false
}

onMounted(() => {
  const user = JSON.parse(localStorage.getItem('seller_user') || '{}')
  userName.value = user.full_name || user.first_name || 'User'
  if (user.first_name) {
    userAvatar.value = `https://ui-avatars.com/api/?name=${user.first_name}+${user.last_name}&background=667eea&color=fff`
  }

  // Add click listener to close notifications when clicking outside
  document.addEventListener('click', closeNotifications)
})

onBeforeUnmount(() => {
  // Clean up event listener
  document.removeEventListener('click', closeNotifications)
})
</script>

<style scoped>
.seller-header {
  height: 70px;
  background: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 30px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 20px;
}

.menu-toggle {
  background: none;
  border: none;
  font-size: 20px;
  color: #333;
  cursor: pointer;
  padding: 8px;
  border-radius: 4px;
  transition: background 0.3s;
}

.menu-toggle:hover {
  background: #f0f0f0;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

.notifications {
  position: relative;
}

.icon-btn {
  background: none;
  border: none;
  font-size: 20px;
  color: #666;
  cursor: pointer;
  padding: 8px;
  position: relative;
  border-radius: 4px;
  transition: background 0.3s;
}

.icon-btn:hover {
  background: #f0f0f0;
}

.badge {
  position: absolute;
  top: 5px;
  right: 5px;
  background: #ff4757;
  color: white;
  font-size: 10px;
  padding: 2px 6px;
  border-radius: 10px;
  font-weight: 600;
  min-width: 16px;
  text-align: center;
}

.notification-dropdown {
  position: absolute;
  top: 50px;
  right: 0;
  width: 380px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  overflow: hidden;
}

.notification-header {
  padding: 16px 20px;
  border-bottom: 1px solid #f0f0f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8f9fa;
}

.notification-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #333;
}

.count {
  font-size: 12px;
  color: #667eea;
  font-weight: 600;
  background: #e8eeff;
  padding: 4px 10px;
  border-radius: 12px;
}

.notification-list {
  max-height: 400px;
  overflow-y: auto;
}

.notification-item {
  padding: 16px 20px;
  display: flex;
  gap: 14px;
  cursor: pointer;
  transition: background 0.2s;
  border-bottom: 1px solid #f0f0f0;
}

.notification-item:last-child {
  border-bottom: none;
}

.notification-item:hover {
  background: #f8f9fa;
}

.notification-item.unread {
  background: #f0f4ff;
}

.notification-item.unread:hover {
  background: #e8eeff;
}

.notification-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: white;
  font-size: 20px;
}

.notification-icon.order { 
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.notification-icon.product { 
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.notification-icon.review { 
  background: linear-gradient(135deg, #ffd93d 0%, #ffaf40 100%);
}

.notification-content {
  flex: 1;
  min-width: 0;
}

.notification-title {
  font-weight: 600;
  font-size: 14px;
  color: #333;
  margin: 0 0 4px 0;
}

.notification-message {
  font-size: 13px;
  color: #666;
  margin: 0 0 6px 0;
  line-height: 1.4;
}

.notification-time {
  font-size: 12px;
  color: #999;
}

.no-notifications {
  text-align: center;
  padding: 50px 20px;
  color: #999;
}

.no-notifications i {
  font-size: 48px;
  margin-bottom: 12px;
  opacity: 0.3;
  display: block;
}

.no-notifications p {
  margin: 0;
  font-size: 14px;
}

.notification-footer {
  padding: 12px 20px;
  border-top: 1px solid #f0f0f0;
  text-align: center;
  background: #f8f9fa;
}

.notification-footer button {
  background: none;
  border: none;
  color: #667eea;
  font-weight: 600;
  cursor: pointer;
  font-size: 14px;
  padding: 6px 12px;
  border-radius: 6px;
  transition: background 0.2s;
}

.notification-footer button:hover {
  background: #e8eeff;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  padding: 8px 12px;
  border-radius: 8px;
  transition: background 0.3s;
}

.user-menu:hover {
  background: #f0f0f0;
}

.user-avatar img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.user-name {
  font-weight: 500;
  color: #333;
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Scrollbar styling */
.notification-list::-webkit-scrollbar {
  width: 6px;
}

.notification-list::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.notification-list::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.notification-list::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

@media (max-width: 768px) {
  .page-title {
    font-size: 18px;
  }

  .user-name {
    display: none;
  }

  .notification-dropdown {
    width: 320px;
    right: -20px;
  }
}
</style>
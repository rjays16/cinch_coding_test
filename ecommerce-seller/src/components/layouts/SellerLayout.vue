<template>
  <div class="seller-layout">
    <!-- Sidebar -->
    <SellerSidebar :isOpen="sidebarOpen" @toggle="toggleSidebar" />

    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'sidebar-closed': !sidebarOpen }">
      <!-- Header -->
      <SellerHeader @toggleSidebar="toggleSidebar" />

      <!-- Page Content -->
      <div class="content-wrapper">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import SellerSidebar from './SellerSidebar.vue'
import SellerHeader from './SellerHeader.vue'

const sidebarOpen = ref(true) // ← Make sure this is true

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}
</script>

<style scoped>
.seller-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.main-content {
  flex: 1;
  margin-left: 260px;
  transition: margin-left 0.3s ease;
}

.main-content.sidebar-closed {
  margin-left: 0;
}

.content-wrapper {
  padding: 30px;
  min-height: calc(100vh - 70px);
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
  }
}
</style>
<template>
  <router-view />
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { authService } from '@/services/authService'

const router = useRouter()

onMounted(async () => {
  // Check if user is authenticated
  if (authService.isAuthenticated()) {
    
    try {
      // Validate token by calling profile endpoint
      const result = await authService.validateToken()
      
      if (!result.valid) {
        // Token is invalid, clear auth and redirect to login
        authService.clearAuth()
        router.push('/login')
      }
    } catch (error) {
      // Error validating token, clear auth
      authService.clearAuth()
      router.push('/login')
    }
  }
})
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
    'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
    sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
</style>
import { ref, computed } from 'vue'
import api from '@/services/api'

// Cart state (shared across components)
const cartItems = ref([])
const isLoading = ref(false)

export function useCart() {
  // Computed properties for cart count and total
  const cartCount = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.quantity, 0)
  })

  const cartTotal = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.subtotal, 0)
  })

  // Load cart from API
  const loadCart = async () => {
    const token = localStorage.getItem('buyer_token')
    if (!token) {
      cartItems.value = []
      return
    }

    try {
      isLoading.value = true
      const response = await api.get('/buyer/cart')
      
      if (response.data.success) {
        // Handle nested structure from backend
        if (response.data.data.items) {
          cartItems.value = response.data.data.items
        } else if (Array.isArray(response.data.data)) {
          cartItems.value = response.data.data
        } else {
          cartItems.value = []
        }
      }
    } catch (error) {
      console.error('Error loading cart:', error)
      cartItems.value = []
    } finally {
      isLoading.value = false
    }
  }

  // Add item to cart
  const addToCart = async (productOrId, quantity = 1) => {
    const token = localStorage.getItem('buyer_token')
    if (!token) {
      throw new Error('Please login to add items to cart')
    }

    try {
      isLoading.value = true
      
      // Handle both product object and product ID
      const productId = typeof productOrId === 'object' ? productOrId.id : productOrId
      
      const response = await api.post('/buyer/cart', {
        product_id: productId,
        quantity: quantity
      })

      if (response.data.success) {
        // Reload cart to get updated data
        await loadCart()
        
        // Emit event to notify other components
        window.dispatchEvent(new Event('cart-updated'))
        
        return true
      }
      return false
    } catch (error) {
      console.error('Error adding to cart:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  // Update item quantity
  const updateCartItem = async (cartItemId, quantity) => {
    if (quantity <= 0) {
      return await removeFromCart(cartItemId)
    }

    try {
      isLoading.value = true
      
      const response = await api.put(`/buyer/cart/${cartItemId}`, {
        quantity: parseInt(quantity)
      })

      if (response.data.success) {
        await loadCart()
        window.dispatchEvent(new Event('cart-updated'))
        return true
      }
      return false
    } catch (error) {
      console.error('Error updating cart item:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  // Remove item from cart
  const removeFromCart = async (cartItemId) => {
    try {
      isLoading.value = true
      const response = await api.delete(`/buyer/cart/${cartItemId}`)

      if (response.data.success) {
        await loadCart()
        window.dispatchEvent(new Event('cart-updated'))
        return true
      }
      return false
    } catch (error) {
      console.error('Error removing from cart:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  // Clear entire cart
  const clearCart = async () => {
    const token = localStorage.getItem('buyer_token')
    if (!token) {
      cartItems.value = []
      return
    }

    try {
      isLoading.value = true
      await api.delete('/buyer/cart')
      cartItems.value = []
      window.dispatchEvent(new Event('cart-updated'))
    } catch (error) {
      console.error('Error clearing cart:', error)
      cartItems.value = []
    } finally {
      isLoading.value = false
    }
  }

  // Check if product is in cart
  const isInCart = (productId) => {
    return cartItems.value.some(item => item.product_id === productId)
  }

  // Get cart item by product ID
  const getCartItem = (productId) => {
    return cartItems.value.find(item => item.product_id === productId)
  }

  return {
    // State
    cartItems,
    cartCount,
    cartTotal,
    isLoading,
    
    // Methods
    loadCart,
    addToCart,
    updateCartItem,
    removeFromCart,
    clearCart,
    isInCart,
    getCartItem
  }
}
import { ref } from 'vue'
import api from '@/services/api'

// Cart state (shared across components)
const cartItems = ref([])
const cartCount = ref(0)
const cartTotal = ref(0)
const isLoading = ref(false)

export function useCart() {
  // Load cart from API
  const loadCart = async () => {
    const token = localStorage.getItem('buyer_token')
    if (!token) {
      cartItems.value = []
      cartCount.value = 0
      cartTotal.value = 0
      return
    }

    try {
      isLoading.value = true
      const response = await api.get('/buyer/cart')
      
      if (response.data.success) {
        cartItems.value = response.data.data.items
        cartCount.value = response.data.data.count
        cartTotal.value = response.data.data.total
      }
    } catch (error) {
      console.error('Error loading cart:', error)
      cartItems.value = []
      cartCount.value = 0
      cartTotal.value = 0
    } finally {
      isLoading.value = false
    }
  }

  // Add item to cart
  const addToCart = async (product, quantity = 1) => {
    const token = localStorage.getItem('buyer_token')
    if (!token) {
      throw new Error('Please login to add items to cart')
    }

    try {
      isLoading.value = true
      const response = await api.post('/buyer/cart', {
        product_id: product.id,
        quantity: quantity
      })

      if (response.data.success) {
        
        // Reload cart to get updated data
        await loadCart()
        
        // Emit event to notify other components
        window.dispatchEvent(new Event('cart-updated'))
        
        return true
      }
    } catch (error) {
      console.error('Error adding to cart:', error)
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
    } catch (error) {
      console.error('Error removing from cart:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  // Update item quantity
  const updateQuantity = async (cartItemId, quantity) => {
    if (quantity <= 0) {
      return await removeFromCart(cartItemId)
    }

    try {
      isLoading.value = true
      const response = await api.put(`/buyer/cart/${cartItemId}`, {
        quantity: quantity
      })

      if (response.data.success) {
        await loadCart()
        window.dispatchEvent(new Event('cart-updated'))
        return true
      }
    } catch (error) {
      console.error('Error updating quantity:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  // Clear cart
  const clearCart = async () => {
    const token = localStorage.getItem('buyer_token')
    if (!token) {
      cartItems.value = []
      cartCount.value = 0
      cartTotal.value = 0
      return
    }

    try {
      isLoading.value = true
      await api.delete('/buyer/cart')
      cartItems.value = []
      cartCount.value = 0
      cartTotal.value = 0
      window.dispatchEvent(new Event('cart-updated'))
    } catch (error) {
      console.error('Error clearing cart:', error)
      cartItems.value = []
      cartCount.value = 0
      cartTotal.value = 0
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
    cartItems,
    cartCount,
    cartTotal,
    isLoading,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    loadCart,
    isInCart,
    getCartItem
  }
}
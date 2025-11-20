import { ref, computed } from 'vue'

// Cart state (shared across components)
const cartItems = ref([])
const cartCount = ref(0)

export function useCart() {
  // Load cart from localStorage
  const loadCart = () => {
    const token = localStorage.getItem('buyer_token')
    if (!token) {
      cartItems.value = []
      cartCount.value = 0
      return
    }

    const stored = localStorage.getItem('cart')
    if (stored) {
      cartItems.value = JSON.parse(stored)
      cartCount.value = cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
    }
  }

  // Save cart to localStorage
  const saveCart = () => {
    localStorage.setItem('cart', JSON.stringify(cartItems.value))
    cartCount.value = cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
  }

  // Add item to cart
  const addToCart = (product, quantity = 1) => {
    const existingItem = cartItems.value.find(item => item.id === product.id)

    if (existingItem) {
      // Increase quantity if already in cart
      existingItem.quantity += quantity
    } else {
      // Add new item to cart
      cartItems.value.push({
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.image_url || product.image,
        seller: product.seller,
        category: product.category,
        stock: product.stock,
        quantity: quantity
      })
    }

    saveCart()
    return true
  }

  // Remove item from cart
  const removeFromCart = (productId) => {
    cartItems.value = cartItems.value.filter(item => item.id !== productId)
    saveCart()
  }

  // Update item quantity
  const updateQuantity = (productId, quantity) => {
    const item = cartItems.value.find(item => item.id === productId)
    if (item) {
      item.quantity = quantity
      if (quantity <= 0) {
        removeFromCart(productId)
      } else {
        saveCart()
      }
    }
  }

  // Clear cart
  const clearCart = () => {
    cartItems.value = []
    cartCount.value = 0
    localStorage.removeItem('cart')
  }

  // Get cart total
  const cartTotal = computed(() => {
    return cartItems.value.reduce((sum, item) => {
      return sum + (item.price * item.quantity)
    }, 0)
  })

  // Check if product is in cart
  const isInCart = (productId) => {
    return cartItems.value.some(item => item.id === productId)
  }

  // Initialize cart
  loadCart()

  return {
    cartItems,
    cartCount,
    cartTotal,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    loadCart,
    isInCart
  }
}
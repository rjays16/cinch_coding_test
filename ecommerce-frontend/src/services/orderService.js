import api from './api'

export const orderService = {
  /**
   * Create a new order
   */
  async createOrder(orderData) {
    try {
      const response = await api.post('/buyer/orders', orderData)
      return response.data
    } catch (error) {
      console.error('Create order error:', error)
      throw error
    }
  },

  /**
   * Get all user orders
   */
  async getOrders() {
    try {
      const response = await api.get('/buyer/orders')
      return response.data
    } catch (error) {
      console.error('Get orders error:', error)
      throw error
    }
  },

  /**
   * Get single order
   */
  async getOrder(orderId) {
    try {
      const response = await api.get(`/buyer/orders/${orderId}`)
      return response.data
    } catch (error) {
      console.error('Get order error:', error)
      throw error
    }
  },

  /**
   * Verify Stripe payment
   */
  async verifyStripePayment(sessionId) {
    try {
      const response = await api.post('/buyer/orders/verify-stripe', {
        session_id: sessionId
      })
      return response.data
    } catch (error) {
      console.error('Verify payment error:', error)
      throw error
    }
  }
}
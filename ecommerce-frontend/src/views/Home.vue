<template>
  <Layout>
    <div class="home">
      <!-- Hero Section -->
      <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
          <h1>Discover Amazing Products</h1>
          <p>Shop the latest trends at unbeatable prices</p>
          <button class="btn-hero" @click="scrollToProducts">Shop Now</button>
        </div>
      </section>

      <!-- Products Section -->
      <section class="products-section" ref="productsSection">
        <div class="container">
          <h2 class="section-title">Trending Products</h2>
          
          <div class="products-grid">
            <div v-for="product in products" :key="product.id" class="product-card">
              <!-- Product Image -->
              <div class="product-image-wrapper">
                <img :src="product.image" :alt="product.name" class="product-image" />
                <button class="btn-quick-add" @click="addToCart(product)">
                  Add to cart
                </button>
              </div>

              <!-- Product Info -->
              <div class="product-details">
                <p class="product-category">{{ product.category }}</p>
                <h3 class="product-name">{{ product.name }}</h3>
                <div class="product-rating">
                  <span class="stars">★★★★★</span>
                </div>
                <p class="product-price">${{ product.price.toFixed(2) }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Features Section -->
      <section class="features-section">
        <div class="container">
          <div class="features-grid">
            <div class="feature-box">
              <div class="feature-icon">🚚</div>
              <h3>Free Shipping</h3>
              <p>On orders over $50</p>
            </div>
            <div class="feature-box">
              <div class="feature-icon">💳</div>
              <h3>Secure Payment</h3>
              <p>100% secure checkout</p>
            </div>
            <div class="feature-box">
              <div class="feature-icon">↩️</div>
              <h3>Easy Returns</h3>
              <p>30-day return policy</p>
            </div>
            <div class="feature-box">
              <div class="feature-icon">⭐</div>
              <h3>Best Quality</h3>
              <p>Premium products</p>
            </div>
          </div>
        </div>
      </section>
    </div>
  </Layout>
</template>

<script>
import Layout from '@/components/layout/Layout.vue'

export default {
  name: 'HomeView',
  components: {
    Layout
  },
  data() {
    return {
      products: [
        {
          id: 1,
          name: 'Wireless Headphones',
          category: 'Electronics',
          price: 89.99,
          image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop'
        },
        {
          id: 2,
          name: 'Smart Watch',
          category: 'Electronics',
          price: 199.99,
          image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop'
        },
        {
          id: 3,
          name: 'Leather Backpack',
          category: 'Accessories',
          price: 79.99,
          image: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&h=400&fit=crop'
        },
        {
          id: 4,
          name: 'Bluetooth Speaker',
          category: 'Electronics',
          price: 59.99,
          image: 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&h=400&fit=crop'
        },
        {
          id: 5,
          name: 'Running Shoes',
          category: 'Fashion',
          price: 129.99,
          image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop'
        },
        {
          id: 6,
          name: 'Coffee Maker',
          category: 'Home',
          price: 149.99,
          image: 'https://images.unsplash.com/photo-1517668808822-9ebb02f2a0e6?w=400&h=400&fit=crop'
        },
        {
          id: 7,
          name: 'Sunglasses',
          category: 'Accessories',
          price: 49.99,
          image: 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&h=400&fit=crop'
        },
        {
          id: 8,
          name: 'Desk Lamp',
          category: 'Home',
          price: 39.99,
          image: 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400&h=400&fit=crop'
        },
        {
          id: 9,
          name: 'Yoga Mat',
          category: 'Sports',
          price: 34.99,
          image: 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=400&h=400&fit=crop'
        }
      ]
    }
  },
  methods: {
    addToCart(product) {
      alert(`Added "${product.name}" to cart!`)
    },
    scrollToProducts() {
      this.$refs.productsSection.scrollIntoView({ behavior: 'smooth' })
    }
  }
}
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Hero Section */
.hero {
  position: relative;
  height: 500px;
  background-image: url('@/assets/images/photo-overlay.jpg');
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-align: center;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
}

.hero-content {
  position: relative;
  z-index: 1;
}

.hero h1 {
  font-size: 3.5rem;
  margin-bottom: 1rem;
  font-weight: bold;
}

.hero p {
  font-size: 1.5rem;
  margin-bottom: 2rem;
}

.btn-hero {
  background-color: #42b983;
  color: white;
  border: none;
  padding: 1rem 3rem;
  font-size: 1.2rem;
  font-weight: bold;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-hero:hover {
  background-color: #359268;
  transform: translateY(-2px);
}

/* Products Section */
.products-section {
  padding: 4rem 0;
  background-color: #f9f9f9;
}

.section-title {
  text-align: center;
  font-size: 2.5rem;
  margin-bottom: 3rem;
  color: #2c3e50;
  font-weight: bold;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

/* Product Card */
.product-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.product-image-wrapper {
  position: relative;
  overflow: hidden;
  height: 300px;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.product-card:hover .product-image {
  transform: scale(1.05);
}

.btn-quick-add {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: rgba(0, 0, 0, 0.8);
  color: white;
  border: none;
  padding: 1rem;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transform: translateY(100%);
  transition: transform 0.3s;
}

.product-card:hover .btn-quick-add {
  transform: translateY(0);
}

.btn-quick-add:hover {
  background-color: #42b983;
}

.product-details {
  padding: 1.5rem;
}

.product-category {
  color: #999;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 0.5rem;
}

.product-name {
  font-size: 1.2rem;
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
  font-weight: 600;
}

.product-rating {
  margin-bottom: 0.5rem;
}

.stars {
  color: #ffa500;
  font-size: 0.9rem;
}

.product-price {
  font-size: 1.5rem;
  font-weight: bold;
  color: #42b983;
  margin: 0;
}

/* Features Section */
.features-section {
  padding: 3rem 0;
  background-color: white;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.feature-box {
  text-align: center;
  padding: 2rem;
}

.feature-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.feature-box h3 {
  font-size: 1.3rem;
  margin-bottom: 0.5rem;
  color: #2c3e50;
}

.feature-box p {
  color: #666;
}

/* Responsive */
@media (max-width: 768px) {
  .hero h1 {
    font-size: 2rem;
  }

  .hero p {
    font-size: 1rem;
  }

  .section-title {
    font-size: 2rem;
  }

  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
  }

  .product-image-wrapper {
    height: 200px;
  }
}
</style>
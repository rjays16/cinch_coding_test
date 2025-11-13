<template>
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <!-- Company Info -->
        <div class="footer-section">
          <h3>E-Commerce</h3>
          <p>Your trusted online shopping destination.</p>
          <p>&copy; 2024 E-Commerce. All rights reserved.</p>
        </div>

        <!-- Quick Links -->
        <div class="footer-section">
          <h4>Quick Links</h4>
          <ul>
            <li><router-link to="/">Home</router-link></li>
            <li><router-link to="/products">Products</router-link></li>
            <li><router-link to="/about">About Us</router-link></li>
            <li><router-link to="/contact">Contact</router-link></li>
          </ul>
        </div>

        <!-- Customer Service -->
        <div class="footer-section">
          <h4>Customer Service</h4>
          <ul>
            <li><a href="#">FAQ</a></li>
            <li><a href="#">Shipping Info</a></li>
            <li><a href="#">Returns</a></li>
            <li><a href="#">Privacy Policy</a></li>
          </ul>
        </div>

        <!-- Contact -->
        <div class="footer-section">
          <h4>Contact Us</h4>
          <ul>
            <li>Email: support@ecommerce.com</li>
            <li>Phone: +1 234 567 890</li>
            <li>Address: 123 Shopping St, City</li>
          </ul>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'Footer'
}
</script>

<style scoped>
.footer {
  background-color: #2c3e50;
  color: white;
  padding: 3rem 0 1rem;
  margin-top: auto;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.footer-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.footer-section h3 {
  margin-bottom: 1rem;
  font-size: 1.5rem;
}

.footer-section h4 {
  margin-bottom: 1rem;
  font-size: 1.2rem;
  color: #42b983;
}

.footer-section p {
  margin: 0.5rem 0;
  color: #ddd;
}

.footer-section ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-section ul li {
  margin: 0.5rem 0;
}

.footer-section ul li a {
  color: #ddd;
  text-decoration: none;
  transition: color 0.3s;
}

.footer-section ul li a:hover {
  color: #42b983;
}

@media (max-width: 768px) {
  .footer-content {
    grid-template-columns: 1fr;
  }
}
</style>
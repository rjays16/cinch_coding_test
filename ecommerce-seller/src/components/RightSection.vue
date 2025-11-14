<template>
  <div class="right-section"></div>
</template>

<script setup>
// Reusable right section component for login/register pages
</script>

<style scoped>
.right-section {
  flex: 0 0 60%;
  height: 100vh;
  position: sticky;
  top: 0;
  background: url('https://images.unsplash.com/photo-1516762689617-e1cffcef479d?w=1600&h=1200&fit=crop&q=80');
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
}

/* Responsive */
@media (max-width: 968px) {
  .right-section {
    flex: 1;
    min-height: 300px;
    height: auto;
    position: relative;
    order: -1;
  }
}

@media (max-width: 768px) {
  .right-section {
    min-height: 250px;
  }
}
</style>
<template>
  <div class="layout">
    <Header />
    <main class="main-content">
      <slot></slot>
    </main>
    <Footer />
  </div>
</template>

<script>
import Header from './Header.vue'
import Footer from './Footer.vue'

export default {
  name: 'Layout',
  components: {
    Header,
    Footer
  }
}
</script>

<style scoped>
.layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  padding: 2rem 0;
}
</style>
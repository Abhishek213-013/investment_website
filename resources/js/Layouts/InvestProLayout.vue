<template>
  <div class="frontend-layout">
    <InvestProHeader />
    <main>
      <slot />
    </main>
    <InvestProFooter />
  </div>
</template>

<script setup>
import InvestProHeader from './InvestProHeader.vue'
import InvestProFooter from './InvestProFooter.vue'
</script>

<style scoped>
.frontend-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}
</style>
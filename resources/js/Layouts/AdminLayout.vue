<template>
  <div class="min-h-screen bg-gray-50">
    <AdminSidebar />
    <div class="ml-48">
      <AdminNavbar :title="title" />
      <main>
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import AdminSidebar from './AdminSidebar.vue'
import AdminNavbar from './AdminNavbar.vue'

defineProps({
  title: {
    type: String,
    default: 'Dashboard'
  }
})
</script>
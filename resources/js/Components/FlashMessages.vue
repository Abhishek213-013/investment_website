<template>
  <div>
    <!-- Success Message -->
    <div v-if="flash?.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
      <div class="flex items-center">
        <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-green-700">{{ flash.success }}</span>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="flash?.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
      <div class="flex items-center">
        <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-red-700">{{ flash.error }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  flash: {
    type: Object,
    default: () => ({})
  }
})
</script>
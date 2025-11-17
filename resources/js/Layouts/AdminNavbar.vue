<template>
  <nav class="bg-white border-b border-gray-200 px-6 py-4">
    <div class="flex justify-between items-center">
      <!-- Page Title -->
      <div>
        <h1 class="text-xl font-semibold text-gray-800">{{ pageTitle }}</h1>
      </div>

      <!-- Right Side: Search & User Menu -->
      <div class="flex items-center space-x-4">
        <!-- Search Bar -->
        <div class="relative">
          <input 
            type="text" 
            :value="searchQuery"
            @input="$emit('search', $event.target.value)"
            placeholder="Search..." 
            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64"
          >
          <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>

        <!-- User Menu -->
        <div class="relative">
          <button 
            @click="toggleUserMenu"
            class="flex items-center space-x-3 focus:outline-none"
          >
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
              <span class="text-white text-sm font-semibold">
                {{ getUserInitials }}
              </span>
            </div>
            <div class="text-left">
              <p class="text-sm font-medium text-gray-700">{{ user?.name }}</p>
              <p class="text-xs text-gray-500 capitalize">{{ user?.user_type?.replace('_', ' ') }}</p>
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>

          <!-- Dropdown Menu -->
          <div 
            v-show="showUserMenu"
            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
          >
            <a href="/admin/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
            <a href="/admin/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
            <div class="border-t border-gray-200 my-1"></div>
            <form method="POST" action="/admin/logout" class="block w-full text-left">
              <button type="submit" class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left">
                Sign Out
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

defineProps({
  pageTitle: {
    type: String,
    default: 'Dashboard'
  },
  searchQuery: {
    type: String,
    default: ''
  }
})

defineEmits(['search'])

const showUserMenu = ref(false)
const page = usePage()

const user = computed(() => page.props.auth?.user)

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const getUserInitials = computed(() => {
  if (!user.value?.name) return 'U'
  return user.value.name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2)
})

// Close dropdown when clicking outside
const closeUserMenu = (event) => {
  if (!event.target.closest('.relative')) {
    showUserMenu.value = false
  }
}

// Add click event listener to close dropdown
if (typeof window !== 'undefined') {
  document.addEventListener('click', closeUserMenu)
}
</script>

<style scoped>
:deep(*) {
  font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}
</style>
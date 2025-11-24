<template>
  <aside class="w-64 bg-white border-r border-gray-200 fixed h-screen overflow-y-auto flex flex-col justify-between px-4 py-4 sidebar-font">
    <!-- Top: Logo + Navigation -->
    <div class="logo w-[100px] h-[15px]">
      <a href="/admin">
        <div class="logo-container  w-[150px]">
          <img src="/assets/logo.png"  alt="InvestPro" class="logo-image  w-full  ">
        </div>
      </a>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="space-y-1 mt-10 ">
      <!-- Dashboard -->
      <div>
        <button 
          @click="toggleMenu('dashboard')"
          class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors sidebar-font"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="sidebar-text">Dashboard</span>
          </div>
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': activeMenu === 'dashboard' }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div v-show="activeMenu === 'dashboard'" class="ml-8 mt-1 space-y-0.5">
          <a href="/admin" class="submenu-link sidebar-text">Overview</a>
          <a href="/admin/analytics" class="submenu-link sidebar-text">Analytics</a>
        </div>
      </div>

      <p class="sidebar-label px-2 mt-4 mb-1">
        User Management
      </p>

      <!-- User Management -->
      <div>
        <button 
          @click="toggleMenu('users')"
          class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors sidebar-font"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="sidebar-text">Manage Users</span>
          </div>
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': activeMenu === 'users' }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div v-show="activeMenu === 'users'" class="ml-8 mt-1 space-y-0.5">
          <a href="/admin/users/super-admins" class="submenu-link sidebar-text">Super Admin</a>
          <a href="/admin/users/admins" class="submenu-link sidebar-text">Admin</a>
          <a href="/admin/users/investors" class="submenu-link sidebar-text">Investors</a>
        </div>
      </div>

      <!-- Pages Section (Replaced Investments) -->
      <div>
        <button 
          @click="toggleMenu('pages')"
          class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors sidebar-font"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="sidebar-text">Pages</span>
          </div>
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': activeMenu === 'pages' }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div v-show="activeMenu === 'pages'" class="ml-8 mt-1 space-y-0.5">
          <a href="/admin/pages/site/1" class="submenu-link sidebar-text">Add New Page</a>
          <a href="/admin/pages/site/2" class="submenu-link sidebar-text">Update Existing Page</a>
          <a href="/admin/home-info" class="submenu-link sidebar-text">Create and Update Home Page</a>
        </div>
      </div>

      <!-- Documents Section -->
      <div>
        <button 
          @click="toggleMenu('documents')"
          class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors sidebar-font"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="sidebar-text">Documents</span>
          </div>
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': activeMenu === 'documents' }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div v-show="activeMenu === 'documents'" class="ml-8 mt-1 space-y-0.5">
          <a href="/admin/documents/type" class="submenu-link sidebar-text">Document Type</a>
          <a href="/admin/documents/site/1" class="submenu-link sidebar-text">Services</a>
          <a href="/admin/documents/site/2" class="submenu-link sidebar-text">Download</a>
          <a href="/admin/documents/site/3" class="submenu-link sidebar-text">News</a>
          <a href="/admin/documents/site/4" class="submenu-link sidebar-text">FAQ</a>
          <a href="/admin/documents/site/5" class="submenu-link sidebar-text">Contact</a>
        </div>
      </div>

      <!-- Committee Section (Replaced Reports) -->
      <div>
        <button 
          @click="toggleMenu('committee')"
          class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors sidebar-font"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="sidebar-text">Committee</span>
          </div>
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': activeMenu === 'committee' }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div v-show="activeMenu === 'committee'" class="ml-8 mt-1 space-y-0.5">
          <a href="/admin/committee/site/1" class="submenu-link sidebar-text">Create New Committee</a>
          <a href="/admin/committee/site/2" class="submenu-link sidebar-text">Update Committee</a>
          <a href="/admin/committee/site/3" class="submenu-link sidebar-text">Committee List</a>
        </div>
      </div>

      <!-- Gallery Section (Replaced Settings) -->
      <div>
        <button 
          @click="toggleMenu('gallery')"
          class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors sidebar-font"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="sidebar-text">Gallery</span>
          </div>
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': activeMenu === 'gallery' }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div v-show="activeMenu === 'gallery'" class="ml-8 mt-1 space-y-0.5">
          <a href="/admin/gallery/site/1" class="submenu-link sidebar-text">Create New Album</a>
          <a href="/admin/gallery/site/2" class="submenu-link sidebar-text">Update Albums</a>
        </div>
      </div>
    </nav>

    <!-- Bottom: Version Info -->
    <div class="sidebar-collapsed-text px-2 mt-4">
      <p class="text-xs text-gray-500">InvestPro v1.0</p>
    </div>
  </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue'

// Reactive data
const activeMenu = ref('dashboard')

// Methods
const toggleMenu = (menu) => {
  activeMenu.value = activeMenu.value === menu ? null : menu
}

// Initialize on mount
onMounted(() => {
  console.log('✅ Admin Sidebar mounted successfully')
})
</script>

<style scoped>
/* Font family for the entire sidebar */
.sidebar-font {
  font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}

/* Main navigation text */
.sidebar-text {
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}

/* Section labels */
.sidebar-label {
  font-size: 11px;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}

/* Collapsed view text */
.sidebar-collapsed-text {
  font-size: 12px;
  font-weight: 600;
  color: #4f46e5;
  font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}

.rotate-180 {
  transform: rotate(180deg);
}

.submenu-link {
  display: block;
  padding: 0.35rem 0.75rem;
  color: #4b5563;
  border-radius: 0.375rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
  text-decoration: none;
  font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
  text-decoration: none;
}

.logo-image {
  object-fit: cover;
  height: 40px;
}
</style>
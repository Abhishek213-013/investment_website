<template>
  <InvestProLayout>
    <div class="bg-white min-h-screen">
      <!-- Gallery Header -->
      <div class="bg-gray-50 py-12">
        <div class="container mx-auto px-6">
          <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Gallery</h1>
            <p class="text-xl text-gray-600">Explore our photo collections and events</p>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="container mx-auto px-6 py-12 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Loading gallery...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="container mx-auto px-6 py-12 text-center">
        <svg class="mx-auto h-24 w-24 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Unable to load gallery</h3>
        <p class="mt-2 text-gray-500">{{ error }}</p>
        <button 
          @click="fetchAlbums"
          class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
        >
          Try Again
        </button>
      </div>

      <!-- Gallery Content -->
      <div v-else class="container mx-auto px-6 py-12">
        <!-- Albums Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="album in processedAlbums"
            :key="album.id"
            class="group cursor-pointer bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 album-card"
            @click="viewAlbum(album)"
          >
            <!-- Album Cover -->
            <div class="aspect-w-16 aspect-h-9 bg-gray-200 overflow-hidden">
              <img
                v-if="album.album_cover"
                :src="album.album_cover"
                :alt="album.album_title || 'Gallery Album'"
                class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300"
                @error="handleImageError"
              />
              <div v-else class="w-full h-64 bg-gray-300 flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>

            <!-- Album Info -->
            <div class="p-6">
              <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                {{ album.album_title || 'Untitled Album' }}
              </h3>
              <p v-if="album.album_title_bn" class="text-gray-600 mb-2">{{ album.album_title_bn }}</p>
              <p class="text-gray-500 text-sm mb-4 line-clamp-2" v-if="album.album_description">
                {{ album.album_description }}
              </p>
              <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ getPhotoCount(album) }} photos</span>
                <span>{{ formatDate(album.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="processedAlbums.length === 0" class="text-center py-12">
          <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <h3 class="mt-4 text-lg font-medium text-gray-900">No albums available</h3>
          <p class="mt-2 text-gray-500">Gallery albums will be available soon.</p>
        </div>
      </div>
    </div>
  </InvestProLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import InvestProLayout from '@/Layouts/InvestProLayout.vue'

const loading = ref(true)
const error = ref(null)
const albums = ref([])

const processedAlbums = computed(() => {
  return albums.value.map(album => ({
    id: album.id || Math.random().toString(36).substr(2, 9),
    album_title: album.album_title || 'Untitled Album',
    album_title_bn: album.album_title_bn || '',
    album_description: album.album_description || '',
    album_description_bn: album.album_description_bn || '',
    album_cover: album.album_cover || '',
    album_images: Array.isArray(album.album_images) ? album.album_images : [],
    created_at: album.created_at || new Date().toISOString(),
    updated_at: album.updated_at || new Date().toISOString()
  }))
})

const formatDate = (dateString) => {
  if (!dateString) return 'Unknown date'
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (e) {
    console.warn('Invalid date format:', dateString)
    return 'Invalid Date'
  }
}

const getPhotoCount = (album) => {
  if (!album.album_images || !Array.isArray(album.album_images)) return 0
  return album.album_images.length
}

const viewAlbum = (album) => {
  if (album.id) {
    router.visit(`/gallery/album/${album.id}`)
  } else {
    console.error('Cannot view album: Invalid album ID')
  }
}

const handleImageError = (event) => {
  console.warn('Album cover image failed to load:', event.target.src)
  event.target.style.display = 'none'
  // The fallback SVG will be shown since the image is hidden
}

const fetchAlbums = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await fetch('/api/gallery/albums')
    
    if (!response.ok) {
      throw new Error(`Failed to fetch albums: ${response.status} ${response.statusText}`)
    }
    
    const data = await response.json()
    
    if (data.success && Array.isArray(data.albums)) {
      albums.value = data.albums
    } else {
      throw new Error(data.message || 'Invalid data format received from server')
    }
  } catch (err) {
    console.error('Error fetching albums:', err)
    error.value = err.message || 'An unexpected error occurred while loading the gallery.'
    albums.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchAlbums()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.aspect-w-16 {
  position: relative;
}

.aspect-w-16::before {
  content: '';
  display: block;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
}

.aspect-w-16 > * {
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}

/* Enhanced hover effects */
.album-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.album-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Smooth image transitions */
img {
  transition: all 0.5s ease;
}

img:hover {
  transform: scale(1.05);
}

/* Loading animation enhancement */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
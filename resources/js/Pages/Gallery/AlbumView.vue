<template>
  <InvestProLayout>
    <div class="bg-white min-h-screen">
      <!-- Album Header -->
      <div class="bg-gray-50 py-12">
        <div class="container mx-auto px-6">
          <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ album.album_title }}</h1>
            <p class="text-xl text-gray-600 mb-4">{{ album.album_title_bn }}</p>
            <p class="text-gray-500 max-w-2xl mx-auto" v-if="album.album_description">
              {{ album.album_description }}
            </p>
            <p class="text-gray-500 max-w-2xl mx-auto" v-if="album.album_description_bn">
              {{ album.album_description_bn }}
            </p>
            <div class="mt-6 flex items-center justify-center space-x-4 text-sm text-gray-500">
              <span>{{ album.album_images.length }} photos</span>
              <span>•</span>
              <span>Created {{ formatDate(album.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="container mx-auto px-6 py-12 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Loading album...</p>
      </div>

      <!-- Gallery Grid -->
      <div v-else class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div
            v-for="(image, index) in album.album_images"
            :key="index"
            class="group cursor-pointer"
            @click="openLightbox(index)"
          >
            <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
              <img
                :src="image"
                :alt="`Image ${index + 1} from ${album.album_title}`"
                class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300"
              />
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="album.album_images.length === 0" class="text-center py-12">
          <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <h3 class="mt-4 text-lg font-medium text-gray-900">No photos in this album</h3>
          <p class="mt-2 text-gray-500">This album doesn't contain any photos yet.</p>
        </div>
      </div>

      <!-- Lightbox Modal -->
      <div v-if="showLightbox" class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center">
        <div class="relative max-w-7xl max-h-full w-full h-full flex items-center justify-center">
          <!-- Close Button -->
          <button
            @click="closeLightbox"
            class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-2"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- Navigation Buttons -->
          <button
            v-if="currentImageIndex > 0"
            @click="previousImage"
            class="absolute left-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-3"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>

          <button
            v-if="currentImageIndex < album.album_images.length - 1"
            @click="nextImage"
            class="absolute right-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-3"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>

          <!-- Image Counter -->
          <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white bg-black bg-opacity-50 rounded-full px-4 py-2">
            {{ currentImageIndex + 1 }} / {{ album.album_images.length }}
          </div>

          <!-- Current Image -->
          <img
            :src="album.album_images[currentImageIndex]"
            :alt="`Image ${currentImageIndex + 1} from ${album.album_title}`"
            class="max-w-full max-h-full object-contain"
          />
        </div>
      </div>
    </div>
  </InvestProLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import InvestProLayout from '../../Layouts/InvestProLayout.vue'

const props = defineProps({
  album: {
    type: Object,
    required: true
  }
})

const loading = ref(false)
const showLightbox = ref(false)
const currentImageIndex = ref(0)

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const openLightbox = (index) => {
  currentImageIndex.value = index
  showLightbox.value = true
  document.body.style.overflow = 'hidden'
}

const closeLightbox = () => {
  showLightbox.value = false
  document.body.style.overflow = 'auto'
}

const nextImage = () => {
  if (currentImageIndex.value < props.album.album_images.length - 1) {
    currentImageIndex.value++
  }
}

const previousImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--
  }
}

// Keyboard navigation
const handleKeydown = (event) => {
  if (!showLightbox.value) return

  switch (event.key) {
    case 'Escape':
      closeLightbox()
      break
    case 'ArrowLeft':
      previousImage()
      break
    case 'ArrowRight':
      nextImage()
      break
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
  document.body.style.overflow = 'auto'
})
</script>

<style scoped>
.aspect-w-1 {
  position: relative;
}

.aspect-w-1::before {
  content: '';
  display: block;
  padding-bottom: 100%;
}

.aspect-w-1 > * {
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
</style>
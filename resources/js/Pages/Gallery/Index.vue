<template>
  <InvestProLayout>
    <div class="bg-white min-h-screen">
      <!-- Header -->
      <div class="bg-gray-50 py-12">
        <div class="container mx-auto px-6 text-center">
          <h1 class="text-4xl font-bold text-gray-900 mb-4">Photo Gallery</h1>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Explore our collection of photo albums and memorable moments
          </p>
        </div>
      </div>

      <!-- Albums Grid -->
      <div class="container mx-auto px-6 py-12">
        <div v-if="albums.length === 0" class="text-center py-12">
          <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <h3 class="mt-4 text-lg font-medium text-gray-900">No albums available</h3>
          <p class="mt-2 text-gray-500">Check back later for new photo albums.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="album in albums"
            :key="album.id"
            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
            @click="viewAlbum(album)"
          >
            <!-- Album Cover -->
            <div class="aspect-w-16 aspect-h-9 bg-gray-200">
              <img
                :src="album.album_cover"
                :alt="album.album_title"
                class="w-full h-64 object-cover"
              />
            </div>

            <!-- Album Info -->
            <div class="p-6">
              <h3 class="text-xl font-semibold text-gray-900 mb-2">
                {{ album.album_title }}
              </h3>
              <p class="text-gray-600 mb-2">
                {{ album.album_title_bn }}
              </p>
              <p class="text-gray-500 text-sm mb-4 line-clamp-2">
                {{ album.album_description || 'No description available' }}
              </p>
              <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ album.album_images.length }} photos</span>
                <span>{{ formatDate(album.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </InvestProLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import InvestProLayout from '../../Layouts/InvestProLayout.vue'

const props = defineProps({
  albums: {
    type: Array,
    required: true
  }
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const viewAlbum = (album) => {
  router.visit(`/gallery/album/${album.id}`)
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
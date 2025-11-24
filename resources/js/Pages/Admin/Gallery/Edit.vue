<template>
  <div class="min-h-screen bg-gray-50 flex">
    <AdminSidebar />
    
    <!-- Main Content Area -->
    <div class="flex-1 lg:ml-64 flex flex-col min-w-0">
      <AdminNavbar />
      
      <!-- Header -->
      <div class="bg-white shadow flex-shrink-0">
        <div class="px-6 py-4">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Update Albums</h1>
              <p class="text-gray-600 mt-1">Manage and update existing photo albums</p>
            </div>
            <div class="flex space-x-3">
              <a
                href="/admin/gallery/site/1"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create New Album
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex-1 p-6 overflow-hidden">
        <!-- Albums List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">All Albums</h2>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="p-8 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-2 text-gray-600">Loading albums...</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="albums.length === 0" class="p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No albums</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new album.</p>
            <div class="mt-6">
              <a
                href="/admin/gallery/site/1"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Create New Album
              </a>
            </div>
          </div>

          <!-- Albums Grid -->
          <div v-else class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="album in albums"
                :key="album.id"
                class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow"
              >
                <!-- Album Cover -->
                <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                  <img
                    :src="album.album_cover"
                    :alt="album.album_title"
                    class="w-full h-48 object-cover"
                  />
                </div>

                <!-- Album Info -->
                <div class="p-4">
                  <h3 class="text-lg font-medium text-gray-900 mb-1">
                    {{ album.album_title }}
                  </h3>
                  <p class="text-sm text-gray-600 mb-1">
                    {{ album.album_title_bn }}
                  </p>
                  <p class="text-sm text-gray-500 mb-3 line-clamp-2">
                    {{ album.album_description || 'No description' }}
                  </p>

                  <!-- Album Stats -->
                  <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <span>{{ album.album_images.length }} images</span>
                    <span>{{ formatDate(album.created_at) }}</span>
                  </div>

                  <!-- Actions -->
                  <div class="flex space-x-2">
                    <button
                      @click="viewAlbum(album)"
                      class="flex-1 bg-green-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-green-700 transition-colors"
                    >
                      View
                    </button>
                    <button
                      @click="editAlbum(album)"
                      class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-blue-700 transition-colors"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteAlbum(album)"
                      class="flex-1 bg-red-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-red-700 transition-colors"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Album Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <!-- Modal Header -->
          <div class="flex items-center justify-between pb-3 border-b">
            <h3 class="text-xl font-medium text-gray-900">Edit Album</h3>
            <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Edit Form -->
          <div v-if="editingAlbum" class="mt-4 space-y-4">
            <!-- Album Title -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Album Title (English)
                </label>
                <input
                  type="text"
                  v-model="editingAlbum.album_title"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Album Title (Bangla)
                </label>
                <input
                  type="text"
                  v-model="editingAlbum.album_title_bn"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <!-- Album Description -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Description (English)
                </label>
                <textarea
                  v-model="editingAlbum.album_description"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Description (Bangla)
                </label>
                <textarea
                  v-model="editingAlbum.album_description_bn"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
              </div>
            </div>

            <!-- Current Images -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Current Images ({{ editingAlbum.album_images.length }})
              </label>
              <div class="grid grid-cols-4 gap-2 mb-4">
                <div
                  v-for="(image, index) in editingAlbum.album_images"
                  :key="index"
                  class="relative group"
                >
                  <img :src="image" alt="Album image" class="h-20 w-full object-cover rounded">
                  <button
                    @click="removeAlbumImage(editingAlbum.id, index)"
                    class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity"
                  >
                    ×
                  </button>
                </div>
              </div>
            </div>

            <!-- Add More Images -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Add More Images
              </label>
              <input
                type="file"
                @change="handleAdditionalImages"
                multiple
                accept="image/*"
                class="w-full px-3 py-2 border border-gray-300 rounded-md"
              />
            </div>

            <!-- Modal Actions -->
            <div class="flex justify-end space-x-3 pt-4">
              <button
                @click="closeEditModal"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                @click="updateAlbum"
                :disabled="updating"
                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 disabled:opacity-50"
              >
                <span v-if="updating">Updating...</span>
                <span v-else>Update Album</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Message -->
    <div v-if="showSuccess" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
      <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        {{ successMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminSidebar from '../../../Layouts/AdminSidebar.vue'
import AdminNavbar from '../../../Layouts/AdminNavbar.vue'

const loading = ref(true)
const albums = ref([])
const showEditModal = ref(false)
const editingAlbum = ref(null)
const updating = ref(false)
const additionalImages = ref([])
const showSuccess = ref(false)
const successMessage = ref('')

onMounted(() => {
  loadAlbums()
})

const loadAlbums = async () => {
  try {
    const response = await fetch('/admin/gallery/all')
    const result = await response.json()
    
    if (result.success) {
      albums.value = result.albums
    } else {
      alert('Failed to load albums')
    }
  } catch (error) {
    alert('Error loading albums')
    console.error('Error:', error)
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const viewAlbum = (album) => {
  // Open album in new tab for user view
  window.open(`/gallery/album/${album.id}`, '_blank')
}

const editAlbum = (album) => {
  editingAlbum.value = { ...album }
  showEditModal.value = true
  additionalImages.value = []
}

const closeEditModal = () => {
  showEditModal.value = false
  editingAlbum.value = null
  additionalImages.value = []
}

const handleAdditionalImages = (event) => {
  additionalImages.value = Array.from(event.target.files)
}

const removeAlbumImage = async (albumId, imageIndex) => {
  if (!confirm('Are you sure you want to remove this image?')) return

  try {
    const response = await fetch(`/admin/gallery/delete-image/${albumId}/${imageIndex}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    })

    const result = await response.json()

    if (result.success) {
      // Remove image from local state
      editingAlbum.value.album_images.splice(imageIndex, 1)
      showSuccessMessage('Image removed successfully!')
    } else {
      alert('Failed to remove image')
    }
  } catch (error) {
    alert('Error removing image')
    console.error('Error:', error)
  }
}

const updateAlbum = async () => {
  if (!editingAlbum.value) return

  updating.value = true

  try {
    const formData = new FormData()
    formData.append('album_title', editingAlbum.value.album_title)
    formData.append('album_title_bn', editingAlbum.value.album_title_bn)
    formData.append('album_description', editingAlbum.value.album_description)
    formData.append('album_description_bn', editingAlbum.value.album_description_bn)
    
    // Add additional images
    additionalImages.value.forEach(image => {
      formData.append('album_images[]', image)
    })

    const response = await fetch(`/admin/gallery/${editingAlbum.value.id}`, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    })

    const result = await response.json()

    if (result.success) {
      showSuccessMessage('Album updated successfully!')
      closeEditModal()
      loadAlbums() // Reload the albums list
    } else {
      alert(result.message || 'Failed to update album')
    }
  } catch (error) {
    alert('Error updating album')
    console.error('Error:', error)
  } finally {
    updating.value = false
  }
}

const deleteAlbum = async (album) => {
  if (!confirm('Are you sure you want to delete this album? This action cannot be undone.')) return

  try {
    const response = await fetch(`/admin/gallery/${album.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    })

    const result = await response.json()

    if (result.success) {
      showSuccessMessage('Album deleted successfully!')
      loadAlbums() // Reload the albums list
    } else {
      alert('Failed to delete album')
    }
  } catch (error) {
    alert('Error deleting album')
    console.error('Error:', error)
  }
}

const showSuccessMessage = (message) => {
  successMessage.value = message
  showSuccess.value = true
  setTimeout(() => {
    showSuccess.value = false
  }, 3000)
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
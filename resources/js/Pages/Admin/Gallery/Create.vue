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
              <h1 class="text-2xl font-bold text-gray-900">Create New Album</h1>
              <p class="text-gray-600 mt-1">Create a new photo album with cover image and multiple photos</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex-1 p-6 overflow-hidden">
        <!-- Create Album Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Album Title -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="album_title" class="block text-sm font-medium text-gray-700 mb-2">
                  Album Title (English) *
                </label>
                <input
                  type="text"
                  id="album_title"
                  v-model="form.album_title"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter album title in English"
                  required
                />
                <div v-if="errors.album_title" class="text-red-500 text-sm mt-1">
                  {{ errors.album_title[0] }}
                </div>
              </div>

              <div>
                <label for="album_title_bn" class="block text-sm font-medium text-gray-700 mb-2">
                  Album Title (Bangla) *
                </label>
                <input
                  type="text"
                  id="album_title_bn"
                  v-model="form.album_title_bn"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="বাংলায় অ্যালবামের শিরোনাম লিখুন"
                  required
                />
                <div v-if="errors.album_title_bn" class="text-red-500 text-sm mt-1">
                  {{ errors.album_title_bn[0] }}
                </div>
              </div>
            </div>

            <!-- Album Description -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="album_description" class="block text-sm font-medium text-gray-700 mb-2">
                  Album Description (English)
                </label>
                <textarea
                  id="album_description"
                  v-model="form.album_description"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter album description in English"
                ></textarea>
              </div>

              <div>
                <label for="album_description_bn" class="block text-sm font-medium text-gray-700 mb-2">
                  Album Description (Bangla)
                </label>
                <textarea
                  id="album_description_bn"
                  v-model="form.album_description_bn"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="বাংলায় অ্যালবামের বিবরণ লিখুন"
                ></textarea>
              </div>
            </div>

            <!-- Album Cover -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Album Cover Image *
              </label>
              <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                <div class="space-y-1 text-center">
                  <svg v-if="!coverPreview" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <div v-if="coverPreview" class="mx-auto">
                    <img :src="coverPreview" alt="Cover preview" class="h-32 w-32 object-cover rounded-md">
                  </div>
                  <div class="flex text-sm text-gray-600">
                    <label for="album_cover" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                      <span>Upload cover image</span>
                      <input 
                        id="album_cover" 
                        type="file" 
                        @change="handleCoverUpload" 
                        class="sr-only" 
                        accept="image/*"
                        required
                      />
                    </label>
                    <p class="pl-1">or drag and drop</p>
                  </div>
                  <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                </div>
              </div>
              <div v-if="errors.album_cover" class="text-red-500 text-sm mt-1">
                {{ errors.album_cover[0] }}
              </div>
            </div>

            <!-- Album Images -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Album Images *
              </label>
              <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                <div class="space-y-1 text-center">
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <div class="flex text-sm text-gray-600">
                    <label for="album_images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                      <span>Upload images</span>
                      <input 
                        id="album_images" 
                        type="file" 
                        @change="handleImagesUpload" 
                        class="sr-only" 
                        accept="image/*"
                        multiple
                        required
                      />
                    </label>
                    <p class="pl-1">or drag and drop</p>
                  </div>
                  <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                </div>
              </div>

              <!-- Preview Uploaded Images -->
              <div v-if="imagePreviews.length > 0" class="mt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Images ({{ imagePreviews.length }})</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                  <div v-for="(preview, index) in imagePreviews" :key="index" class="relative">
                    <img :src="preview" alt="Preview" class="h-24 w-full object-cover rounded-md">
                    <button
                      type="button"
                      @click="removeImage(index)"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                    >
                      ×
                    </button>
                  </div>
                </div>
              </div>
              <div v-if="errors.album_images" class="text-red-500 text-sm mt-1">
                {{ errors.album_images[0] }}
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4 pt-6">
              <button
                type="button"
                @click="resetForm"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Reset
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              >
                <span v-if="loading">Creating...</span>
                <span v-else>Create Album</span>
              </button>
            </div>
          </form>
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
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminSidebar from '../../../Layouts/AdminSidebar.vue'
import AdminNavbar from '../../../Layouts/AdminNavbar.vue'

const loading = ref(false)
const errors = ref({})
const coverPreview = ref(null)
const imagePreviews = ref([])
const showSuccess = ref(false)
const successMessage = ref('')

const form = reactive({
  album_title: '',
  album_title_bn: '',
  album_description: '',
  album_description_bn: '',
  album_cover: null,
  album_images: []
})

const handleCoverUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.album_cover = file
    const reader = new FileReader()
    reader.onload = (e) => {
      coverPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const handleImagesUpload = (event) => {
  const files = Array.from(event.target.files)
  files.forEach(file => {
    if (file) {
      form.album_images.push(file)
      const reader = new FileReader()
      reader.onload = (e) => {
        imagePreviews.value.push(e.target.result)
      }
      reader.readAsDataURL(file)
    }
  })
}

const removeImage = (index) => {
  form.album_images.splice(index, 1)
  imagePreviews.value.splice(index, 1)
}

const resetForm = () => {
  form.album_title = ''
  form.album_title_bn = ''
  form.album_description = ''
  form.album_description_bn = ''
  form.album_cover = null
  form.album_images = []
  coverPreview.value = null
  imagePreviews.value = []
  errors.value = {}
}

const showSuccessMessage = (message) => {
  successMessage.value = message
  showSuccess.value = true
  setTimeout(() => {
    showSuccess.value = false
  }, 3000)
}

const submitForm = async () => {
  loading.value = true
  errors.value = {}

  try {
    const formData = new FormData()
    formData.append('album_title', form.album_title)
    formData.append('album_title_bn', form.album_title_bn)
    formData.append('album_description', form.album_description)
    formData.append('album_description_bn', form.album_description_bn)
    formData.append('album_cover', form.album_cover)
    
    form.album_images.forEach(image => {
      formData.append('album_images[]', image)
    })

    const response = await fetch('/admin/gallery', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    })

    const result = await response.json()

    if (result.success) {
      showSuccessMessage('Album created successfully!')
      router.visit('/admin/gallery/site/2')
    } else {
      errors.value = result.errors || {}
      alert(result.message || 'Failed to create album. Please check the form.')
    }
  } catch (error) {
    alert('An error occurred while creating the album.')
    console.error('Error:', error)
  } finally {
    loading.value = false
  }
}
</script>
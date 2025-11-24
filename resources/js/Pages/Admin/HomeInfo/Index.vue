<template>
  <div class="min-h-screen bg-gray-50 flex">
    <AdminSidebar />
    
    <!-- Main Content Area -->
    <div class="flex-1 lg:ml-64">
      <AdminNavbar />
      
      <!-- Header -->
      <div class="bg-white shadow">
        <div class="px-6 py-4">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Home Page Management</h1>
              <p class="text-gray-600 mt-1">Manage your home page content and settings</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="p-6">
        <!-- Success/Error Messages -->
        <div v-if="message" :class="['p-4 rounded-lg mb-6', messageType === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800']">
          {{ message }}
        </div>

        <!-- Action Button -->
        <div class="mb-6">
          <button 
            @click="openModal"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Edit Home Page Content
          </button>
        </div>

        <!-- Current Content Preview -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Home Page Content</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="font-medium text-gray-700 mb-2">Hero Section</h3>
              <p class="text-sm text-gray-600"><strong>Title:</strong> {{ homeInfo.hero_title }}</p>
              <p class="text-sm text-gray-600"><strong>Subtitle:</strong> {{ homeInfo.hero_subtitle }}</p>
              <p class="text-sm text-gray-600"><strong>Button 1:</strong> {{ homeInfo.hero_button1 }}</p>
              <p class="text-sm text-gray-600"><strong>Button 2:</strong> {{ homeInfo.hero_button2 }}</p>
              <div v-if="homeInfo.hero_image || heroImageUrl" class="mt-2">
                <p class="text-sm text-gray-600 mb-1"><strong>Hero Image:</strong></p>
                <img :src="getImageUrl(homeInfo.hero_image, heroImageUrl)" alt="Hero Image" class="w-32 h-20 object-cover rounded border">
              </div>
            </div>
            <div>
              <h3 class="font-medium text-gray-700 mb-2">Section Titles</h3>
              <p class="text-sm text-gray-600"><strong>Services:</strong> {{ homeInfo.service_title }}</p>
              <p class="text-sm text-gray-600"><strong>News:</strong> {{ homeInfo.news_title }}</p>
              <p class="text-sm text-gray-600"><strong>FAQ:</strong> {{ homeInfo.faq_title }}</p>
              <p class="text-sm text-gray-600"><strong>CTA Button 1:</strong> {{ homeInfo.cta_button1 }}</p>
              <p class="text-sm text-gray-600"><strong>CTA Button 2:</strong> {{ homeInfo.cta_button2 }}</p>
            </div>
          </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="border-b px-6 py-4">
              <h2 class="text-xl font-semibold text-gray-800">Edit Home Page Content</h2>
              <p class="text-gray-600 text-sm mt-1">Update your home page content in both English and Bengali</p>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
              <form @submit.prevent="saveHomeData">
                <!-- Hero Section -->
                <div class="mb-8">
                  <h3 class="text-lg font-medium text-gray-800 mb-4 border-b pb-2">Hero Section</h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Hero Title (English)</label>
                      <input 
                        type="text" 
                        v-model="formData.hero_title"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Enter hero title in English"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Hero Title (Bengali)</label>
                      <input 
                        type="text" 
                        v-model="formData.hero_title_bn"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="বাংলায় হিরো শিরোনাম লিখুন"
                      >
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Hero Subtitle (English)</label>
                      <textarea 
                        v-model="formData.hero_subtitle"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Enter hero subtitle in English"
                      ></textarea>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Hero Subtitle (Bengali)</label>
                      <textarea 
                        v-model="formData.hero_subtitle_bn"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="বাংলায় হিরো সাবটাইটেল লিখুন"
                      ></textarea>
                    </div>
                  </div>

                  <!-- Hero Image Upload -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hero Image</label>
                    <div class="flex items-center space-x-4">
                      <div class="flex-1">
                        <input 
                          type="file" 
                          ref="fileInput"
                          @change="handleImageUpload"
                          accept="image/*"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                      </div>
                      <div v-if="currentImageUrl" class="w-20 h-20 bg-gray-100 rounded-md overflow-hidden border">
                        <img :src="currentImageUrl" alt="Hero Image" class="w-full h-full object-cover">
                      </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Recommended size: 1200x600px. Max file size: 5MB</p>
                    <div v-if="currentImageUrl" class="mt-2">
                      <button 
                        type="button"
                        @click="removeImage"
                        class="text-red-600 text-sm hover:text-red-800 flex items-center"
                      >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Remove Image
                      </button>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Hero Button 1 (English)</label>
                      <input 
                        type="text" 
                        v-model="formData.hero_button1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Button text in English"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Hero Button 1 (Bengali)</label>
                      <input 
                        type="text" 
                        v-model="formData.hero_button1_bn"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="বাংলায় বাটন টেক্সট"
                      >
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Hero Button 2 (English)</label>
                      <input 
                        type="text" 
                        v-model="formData.hero_button2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Button text in English"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Hero Button 2 (Bengali)</label>
                      <input 
                        type="text" 
                        v-model="formData.hero_button2_bn"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="বাংলায় বাটন টেক্সট"
                      >
                    </div>
                  </div>
                </div>

                <!-- Section Titles -->
                <div class="mb-8">
                  <h3 class="text-lg font-medium text-gray-800 mb-4 border-b pb-2">Section Titles</h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Service Title (English)</label>
                      <input 
                        type="text" 
                        v-model="formData.service_title"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Enter service title in English"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Service Title (Bengali)</label>
                      <input 
                        type="text" 
                        v-model="formData.service_title_bn"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="বাংলায় সার্ভিস শিরোনাম লিখুন"
                      >
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">News Title (English)</label>
                      <input 
                        type="text" 
                        v-model="formData.news_title"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Enter news title in English"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">News Title (Bengali)</label>
                      <input 
                        type="text" 
                        v-model="formData.news_title_bn"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="বাংলায় নিউজ শিরোনাম লিখুন"
                      >
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">FAQ Title (English)</label>
                      <input 
                        type="text" 
                        v-model="formData.faq_title"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Enter FAQ title in English"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">FAQ Title (Bengali)</label>
                      <input 
                        type="text" 
                        v-model="formData.faq_title_bn"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="বাংলায় FAQ শিরোনাম লিখুন"
                      >
                    </div>
                  </div>
                </div>

                <!-- CTA Buttons -->
                <div class="mb-8">
                  <h3 class="text-lg font-medium text-gray-800 mb-4 border-b pb-2">Call to Action Buttons</h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">CTA Button 1 (English)</label>
                      <input 
                        type="text" 
                        v-model="formData.cta_button1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Enter CTA button text in English"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">CTA Button 1 (Bengali)</label>
                      <input 
                        type="text" 
                        v-model="formData.cta_button1_bn"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="বাংলায় CTA বাটন টেক্সট"
                      >
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">CTA Button 2 (English)</label>
                      <input 
                        type="text" 
                        v-model="formData.cta_button2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Enter CTA button text in English"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">CTA Button 2 (Bengali)</label>
                      <input 
                        type="text" 
                        v-model="formData.cta_button2_bn"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="বাংলায় CTA বাটন টেক্সট"
                      >
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <!-- Modal Footer -->
            <div class="border-t px-6 py-4 bg-gray-50 rounded-b-lg">
              <div class="flex justify-end space-x-3">
                <button 
                  @click="closeModal"
                  class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </button>
                <button 
                  @click="saveHomeData"
                  :disabled="loading"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                >
                  <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ loading ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminSidebar from '../../../Layouts/AdminSidebar.vue'
import AdminNavbar from '../../../Layouts/AdminNavbar.vue'

// Reactive data
const showModal = ref(false)
const loading = ref(false)
const message = ref('')
const messageType = ref('')
const fileInput = ref(null)
const uploadedFile = ref(null)

// Props
const props = defineProps({
  homeInfo: {
    type: Object,
    default: () => ({})
  },
  heroImageUrl: {
    type: String,
    default: null
  }
})

// Computed property for current image URL
const currentImageUrl = computed(() => {
  if (uploadedFile.value) {
    return URL.createObjectURL(uploadedFile.value)
  }
  return props.heroImageUrl || (props.homeInfo.hero_image ? getImageUrl(props.homeInfo.hero_image, props.heroImageUrl) : null)
})

// Form data
const formData = reactive({ ...props.homeInfo })

// Methods
const getImageUrl = (imagePath, imageUrl) => {
  if (imageUrl) return imageUrl
  if (!imagePath) return null
  
  // If it's already a full URL, return as is
  if (imagePath.startsWith('http')) {
    return imagePath
  }
  
  // If it's a storage path, construct the URL
  if (imagePath.startsWith('home-images/')) {
    return `/storage/${imagePath}`
  }
  
  return imagePath
}

const openModal = () => {
  // Copy current data to form
  Object.assign(formData, props.homeInfo)
  uploadedFile.value = null
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  message.value = ''
  uploadedFile.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file type and size
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']
    const maxSize = 5 * 1024 * 1024 // 5MB
    
    if (!validTypes.includes(file.type)) {
      message.value = 'Please select a valid image file (JPEG, PNG, GIF, WebP)'
      messageType.value = 'error'
      event.target.value = ''
      return
    }
    
    if (file.size > maxSize) {
      message.value = 'Image size should be less than 5MB'
      messageType.value = 'error'
      event.target.value = ''
      return
    }
    
    uploadedFile.value = file
    message.value = ''
  }
}

const removeImage = () => {
  uploadedFile.value = null
  formData.hero_image = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const saveHomeData = async () => {
  loading.value = true
  message.value = ''
  
  try {
    let dataToSend = { ...formData }

    // Handle file upload if a new file was selected
    if (uploadedFile.value) {
      const formDataWithFile = new FormData()
      
      // Append all form data
      Object.keys(dataToSend).forEach(key => {
        if (dataToSend[key] !== null && dataToSend[key] !== undefined) {
          formDataWithFile.append(key, dataToSend[key])
        }
      })
      
      // Append the file
      formDataWithFile.append('hero_image_file', uploadedFile.value)
      
      // Send as FormData for file upload
      const response = await fetch('/admin/home-info/upload-image', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formDataWithFile
      })

      const result = await response.json()
      
      if (result.success) {
        message.value = 'Home page content updated successfully!'
        messageType.value = 'success'
        
        // Close modal and refresh data
        closeModal()
        
        // Refresh the page to get updated data after a short delay
        setTimeout(() => {
          router.reload({ only: ['homeInfo', 'heroImageUrl'] })
        }, 1000)
      } else {
        message.value = result.message || 'Error updating home page content'
        messageType.value = 'error'
      }
    } else {
      // Regular JSON update without file
      const response = await fetch('/admin/home-info', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(dataToSend)
      })

      const result = await response.json()

      if (result.success) {
        message.value = 'Home page content updated successfully!'
        messageType.value = 'success'
        
        // Close modal and refresh data
        closeModal()
        
        // Refresh the page to get updated data after a short delay
        setTimeout(() => {
          router.reload({ only: ['homeInfo', 'heroImageUrl'] })
        }, 1000)
      } else {
        message.value = result.message || 'Error updating home page content'
        messageType.value = 'error'
      }
    }
    
  } catch (error) {
    message.value = 'Error updating home page content. Please try again.'
    messageType.value = 'error'
    console.error('Error:', error)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.container {
  font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
</style>
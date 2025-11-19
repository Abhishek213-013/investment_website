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
              <h1 class="text-2xl font-bold text-gray-900">Update Existing Pages</h1>
              <p class="text-gray-600 mt-1">Manage and update your website pages</p>
            </div>
            <a 
              href="/admin/pages/site/1"
              class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Create New Page
            </a>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="p-6">
        <!-- Success/Error Messages -->
        <div v-if="message" :class="['p-4 rounded-lg mb-6', messageType === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800']">
          {{ message }}
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
          <div v-if="loading" class="text-center py-8">
            <div class="flex justify-center items-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>
            <p class="text-gray-600 mt-2">Loading pages...</p>
          </div>

          <div v-else-if="pages.length === 0" class="text-center py-8">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 mb-4">No pages found.</p>
            <a href="/admin/pages/site/1" class="text-indigo-600 hover:text-indigo-800 font-medium">
              Create your first page
            </a>
          </div>

          <div v-else class="space-y-6">
            <div v-for="page in pages" :key="page.page_id" class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50 transition-colors">
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-900">{{ page.page_name }}</h3>
                  <p class="text-gray-600 text-sm mt-1">{{ page.page_name_bn }}</p>
                  <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                    <span>Slug: {{ page.page_slug }}</span>
                    <span>{{ page.sections.length }} section(s)</span>
                    <span>Created: {{ formatDate(page.created_at) }}</span>
                  </div>
                  
                  <!-- Sections Preview -->
                  <div class="mt-4 space-y-3">
                    <div v-for="(section, sectionIndex) in page.sections" :key="section.page_section_id" class="border-l-4 border-blue-200 pl-3">
                      <h4 class="text-sm font-medium text-gray-700">Section {{ sectionIndex + 1 }}</h4>
                      <div class="mt-1 text-xs text-gray-600">
                        <span v-if="section.heading" class="mr-3">📝 {{ section.heading }}</span>
                        <span v-if="section.attachments && section.attachments.length" class="mr-3">
                          🖼️ {{ section.attachments.length }} attachment(s)
                        </span>
                      </div>
                      
                      <!-- Attachments Preview -->
                      <div v-if="section.attachments && section.attachments.length" class="mt-2 flex flex-wrap gap-2">
                        <div v-for="(attachment, attIndex) in section.attachments.slice(0, 3)" :key="attIndex" 
                             class="relative group">
                          <div v-if="isImage(attachment)" class="w-16 h-16 border border-gray-300 rounded overflow-hidden">
                            <img :src="getAttachmentUrl(attachment)" 
                                  :alt="getFileName(attachment)"
                                  class="w-full h-full object-cover">
                          </div>
                          <div v-else class="w-16 h-16 border border-gray-300 rounded flex items-center justify-center bg-gray-100">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                          </div>
                        </div>
                        <div v-if="section.attachments.length > 3" class="w-16 h-16 border border-gray-300 rounded flex items-center justify-center bg-gray-100 text-xs text-gray-500">
                          +{{ section.attachments.length - 3 }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="flex space-x-2 ml-4">
                  <button 
                    @click="viewPage(page.page_slug)"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors text-sm flex items-center space-x-1"
                    title="View Page"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>View</span>
                  </button>
                  <button 
                    @click="editPage(page.page_id)"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm flex items-center space-x-1"
                    title="Edit Page"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Edit</span>
                  </button>
                  <button 
                    @click="deletePage(page.page_id, page.page_name)"
                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors text-sm flex items-center space-x-1"
                    title="Delete Page"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Delete</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminSidebar from '../../../Layouts/AdminSidebar.vue'
import AdminNavbar from '../../../Layouts/AdminNavbar.vue'

const pages = ref([])
const loading = ref(true)
const message = ref('')
const messageType = ref('')

const showMessage = (text, type = 'success') => {
  message.value = text
  messageType.value = type
  setTimeout(() => {
    message.value = ''
  }, 5000)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getFileName = (path) => {
  return path.split('/').pop()
}

const isImage = (filename) => {
  const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.svg']
  return imageExtensions.some(ext => filename.toLowerCase().includes(ext))
}

const getAttachmentUrl = (attachmentPath) => {
  return `/storage/${attachmentPath}`
}

const viewPage = (slug) => {
  window.open(`/page/${slug}`, '_blank')
}

const editPage = (pageId) => {
  router.visit(`/admin/pages/${pageId}/edit`)
}

const deletePage = async (pageId, pageName) => {
  if (confirm(`Are you sure you want to delete the page "${pageName}"? This action cannot be undone.`)) {
    try {
      const response = await fetch(`/admin/pages/${pageId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json',
        }
      })

      const data = await response.json()

      if (data.success) {
        pages.value = pages.value.filter(page => page.page_id !== pageId)
        showMessage('Page deleted successfully!', 'success')
      } else {
        showMessage(data.message || 'Failed to delete page', 'error')
      }
    } catch (error) {
      console.error('Error deleting page:', error)
      showMessage('An error occurred while deleting the page', 'error')
    }
  }
}

const loadPages = async () => {
  try {
    const response = await fetch('/admin/pages/all')
    const data = await response.json()
    pages.value = data.pages
  } catch (error) {
    console.error('Error loading pages:', error)
    showMessage('Failed to load pages', 'error')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadPages()
})
</script>
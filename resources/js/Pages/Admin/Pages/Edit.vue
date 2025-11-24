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
              <h1 class="text-2xl font-bold text-gray-900">Edit Page: {{ pageData.page_name }}</h1>
              <p class="text-gray-600 mt-1">Update page content and sections</p>
            </div>
            <div class="flex space-x-2">
              <button 
                @click="viewPage"
                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors text-sm flex items-center space-x-1"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <span>View Page</span>
              </button>
              <button 
                @click="goBack"
                class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors text-sm"
              >
                Back to List
              </button>
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

        <!-- Page Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-lg font-semibold mb-4">Page Information</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Page Name (English) *
              </label>
              <input
                v-model="pageForm.page_name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter page name in English"
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Page Name (Bengali)
              </label>
              <input
                v-model="pageForm.page_name_bn"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter page name in Bengali"
              >
            </div>
          </div>
        </div>

        <!-- Sections -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold">Page Sections</h2>
            <button
              @click="addSection"
              class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
            >
              + Add Section
            </button>
          </div>

          <!-- Sections List -->
          <div v-for="(section, index) in pageForm.sections" :key="section.page_section_id || index" class="border border-gray-200 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-md font-medium">Section {{ index + 1 }}</h3>
              <button
                @click="removeSection(index)"
                class="text-red-600 hover:text-red-800"
                v-if="pageForm.sections.length > 1"
              >
                Remove
              </button>
            </div>

            <!-- Section Content -->
            <div class="space-y-4">
              <!-- Headings -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Heading (English)
                  </label>
                  <input
                    v-model="section.heading"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Section heading in English"
                  >
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Heading (Bengali)
                  </label>
                  <input
                    v-model="section.heading_bn"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Section heading in Bengali"
                  >
                </div>
              </div>

              <!-- Descriptions -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Description (English)
                  </label>
                  <textarea
                    v-model="section.description"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Short description in English"
                  ></textarea>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Description (Bengali)
                  </label>
                  <textarea
                    v-model="section.description_bn"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Short description in Bengali"
                  ></textarea>
                </div>
              </div>

              <!-- Content Type Selection -->
              <div class="border-t pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                  Section Content Type
                </label>
                
                <div class="flex space-x-4 mb-4">
                  <label class="inline-flex items-center">
                    <input
                      type="checkbox"
                      v-model="section.hasContent"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span class="ml-2 text-sm text-gray-700">Content</span>
                  </label>
                  
                  <label class="inline-flex items-center">
                    <input
                      type="checkbox"
                      v-model="section.hasAttachments"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span class="ml-2 text-sm text-gray-700">Attachments</span>
                  </label>
                </div>

                <!-- Content Area -->
                <div v-if="section.hasContent" class="mb-4">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Content Width
                      </label>
                      <select
                        v-model="section.content_width"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      >
                        <option value="25%">25%</option>
                        <option value="50%">50%</option>
                        <option value="75%">75%</option>
                        <option value="100%">100%</option>
                      </select>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Content Alignment
                      </label>
                      <select
                        v-model="section.content_allignment"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <option value="L">Left</option>
                        <option value="R">Right</option>
                        <option value="C">Center</option>
                        </select>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Content (English)
                      </label>
                      <textarea
                        v-model="section.content"
                        rows="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 summernote-editor"
                        :id="'content-en-' + index"
                        placeholder="Main content in English"
                        @input="updateSummernoteContent(index, 'content', $event.target.value)"
                      ></textarea>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Content (Bengali)
                      </label>
                      <textarea
                        v-model="section.content_bn"
                        rows="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 summernote-editor"
                        :id="'content-bn-' + index"
                        placeholder="Main content in Bengali"
                        @input="updateSummernoteContent(index, 'content_bn', $event.target.value)"
                      ></textarea>
                    </div>
                  </div>
                </div>

                <!-- Attachments Area -->
                <div v-if="section.hasAttachments" class="mt-4">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Attachment Width
                      </label>
                      <select
                        v-model="section.attachment_width"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      >
                        <option value="25%">25%</option>
                        <option value="50%">50%</option>
                        <option value="75%">75%</option>
                        <option value="100%">100%</option>
                      </select>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Attachment Alignment
                      </label>
                      <select
                        v-model="section.attachment_allignment"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      >
                        <option value="L">Left</option>
                        <option value="R">Right</option>
                      </select>
                    </div>
                  </div>

                  <!-- Existing Attachments -->
                  <div v-if="section.existing_attachments && section.existing_attachments.length" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Existing Attachments
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                      <div v-for="(attachment, attachmentIndex) in section.existing_attachments" :key="attachmentIndex" 
                           class="border border-gray-200 rounded-lg p-3 group relative">
                        
                        <!-- Image Preview -->
                        <div v-if="isImage(attachment)" class="aspect-square bg-gray-100 rounded-md overflow-hidden">
                          <img :src="getAttachmentUrl(attachment)" 
                               :alt="getFileName(attachment)"
                               class="w-full h-full object-cover">
                        </div>
                        
                        <!-- File Icon for non-images -->
                        <div v-else class="aspect-square bg-gray-100 rounded-md flex items-center justify-center">
                          <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                        </div>
                        
                        <!-- File Name -->
                        <p class="text-xs text-gray-600 mt-2 truncate">{{ getFileName(attachment) }}</p>
                        
                        <!-- Remove Button -->
                        <button
                          @click="deleteExistingAttachment(section.page_section_id, attachmentIndex)"
                          class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                          title="Remove Attachment"
                        >
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- New Attachments -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Add New Attachments (JPG, PNG, WEBP, PDF, DOCS, CSV)
                    </label>
                    <input
                      type="file"
                      multiple
                      @change="handleFileUpload($event, index)"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.csv"
                    >
                    
                    <!-- New File Preview -->
                    <div v-if="section.new_attachments && section.new_attachments.length" class="mt-3 space-y-2">
                      <div v-for="(file, fileIndex) in section.new_attachments" :key="fileIndex" class="flex items-center justify-between bg-blue-50 px-3 py-2 rounded">
                        <span class="text-sm text-blue-600">{{ file.name }}</span>
                        <button
                          @click="removeFile(index, fileIndex)"
                          class="text-red-600 hover:text-red-800 text-sm"
                        >
                          Remove
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
          <button
            @click="goBack"
            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="submitForm"
            :disabled="isSubmitting"
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors disabled:opacity-50"
          >
            <span v-if="isSubmitting">Updating...</span>
            <span v-else>Update Page</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminSidebar from '../../../Layouts/AdminSidebar.vue'
import AdminNavbar from '../../../Layouts/AdminNavbar.vue'

const props = defineProps({
  page: Object
})

// Reactive data
const isSubmitting = ref(false)
const message = ref('')
const messageType = ref('')
const pageData = ref(props.page)
const summernoteInitialized = ref(false)

// Page form data
const pageForm = reactive({
  page_name: '',
  page_name_bn: '',
  sections: []
})

// Methods
const showMessage = (text, type = 'success') => {
  message.value = text
  messageType.value = type
  setTimeout(() => {
    message.value = ''
  }, 5000)
}

const getFileName = (path) => {
  return path.split('/').pop()
}

const isImage = (filename) => {
  const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.svg']
  return imageExtensions.some(ext => filename.toLowerCase().includes(ext))
}

const getAttachmentUrl = (attachmentPath) => {
  return `/storage/page-attachments/${getFileName(attachmentPath)}`
}

const initializeForm = () => {
  pageForm.page_name = pageData.value.page_name
  pageForm.page_name_bn = pageData.value.page_name_bn
  
  // Initialize sections
  pageForm.sections = pageData.value.sections.map(section => ({
    page_section_id: section.page_section_id,
    heading: section.heading || '',
    heading_bn: section.heading_bn || '',
    description: section.description || '',
    description_bn: section.description_bn || '',
    content: section.content || '',
    content_bn: section.content_bn || '',
    existing_attachments: section.attachments || [],
    new_attachments: [],
    hasContent: !!(section.content || section.content_bn),
    hasAttachments: !!(section.attachments && section.attachments.length),
    content_width: section.content_width || '100%',
    attachment_width: section.attachment_width || '100%',
    content_allignment: section.content_allignment || 'L',
    attachment_allignment: section.attachment_allignment || 'R'
  }))
}

const addSection = () => {
  pageForm.sections.push({
    heading: '',
    heading_bn: '',
    description: '',
    description_bn: '',
    content: '',
    content_bn: '',
    existing_attachments: [],
    new_attachments: [],
    hasContent: false,
    hasAttachments: false,
    content_width: '100%',
    attachment_width: '100%',
    content_allignment: 'L',
    attachment_allignment: 'R'
  })
  
  // Reinitialize Summernote for new section
  nextTick(() => {
    initSummernote()
  })
}

const removeSection = (index) => {
  if (pageForm.sections.length > 1) {
    // Destroy Summernote instance before removing section
    const textareaEn = document.getElementById(`content-en-${index}`)
    const textareaBn = document.getElementById(`content-bn-${index}`)
    
    if (textareaEn && window.$ && window.$.fn.summernote) {
      $(textareaEn).summernote('destroy')
    }
    if (textareaBn && window.$ && window.$.fn.summernote) {
      $(textareaBn).summernote('destroy')
    }
    
    pageForm.sections.splice(index, 1)
    
    // Reinitialize Summernote for remaining sections
    nextTick(() => {
      initSummernote()
    })
  }
}

const handleFileUpload = (event, sectionIndex) => {
  const files = Array.from(event.target.files)
  if (!pageForm.sections[sectionIndex].new_attachments) {
    pageForm.sections[sectionIndex].new_attachments = []
  }
  pageForm.sections[sectionIndex].new_attachments.push(...files)
  event.target.value = '' // Reset file input
}

const removeFile = (sectionIndex, fileIndex) => {
  pageForm.sections[sectionIndex].new_attachments.splice(fileIndex, 1)
}

const updateSummernoteContent = (sectionIndex, field, value) => {
  // This ensures the Vue data is updated when Summernote content changes
  pageForm.sections[sectionIndex][field] = value
}

const deleteExistingAttachment = async (sectionId, attachmentIndex) => {
  if (confirm('Are you sure you want to delete this attachment?')) {
    try {
      const response = await fetch(`/admin/pages/sections/${sectionId}/attachments/${attachmentIndex}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
      })

      // Check if response is JSON
      const contentType = response.headers.get('content-type')
      if (!contentType || !contentType.includes('application/json')) {
        const text = await response.text()
        console.error('Non-JSON response received:', text.substring(0, 500))
        throw new Error('Server returned non-JSON response')
      }

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.message || `HTTP error! status: ${response.status}`)
      }

      if (data.success) {
        // Remove attachment from the section
        const section = pageForm.sections.find(s => s.page_section_id === sectionId)
        if (section) {
          section.existing_attachments.splice(attachmentIndex, 1)
          // Update hasAttachments flag
          section.hasAttachments = section.existing_attachments.length > 0 || section.new_attachments.length > 0
        }
        showMessage('Attachment deleted successfully!', 'success')
      } else {
        throw new Error(data.message || 'Failed to delete attachment')
      }
    } catch (error) {
      console.error('Error deleting attachment:', error)
      showMessage(error.message || 'An error occurred while deleting the attachment', 'error')
    }
  }
}

const viewPage = () => {
  window.open(`/page/${pageData.value.page_slug}`, '_blank')
}

const goBack = () => {
  router.visit('/admin/pages/site/2')
}

const syncSummernoteContent = () => {
  if (window.$ && window.$.fn.summernote) {
    $('.summernote-editor').each(function() {
      if ($(this).hasClass('summernote-initialized')) {
        const contents = $(this).summernote('code')
        const textarea = $(this)
        const idParts = textarea.attr('id').split('-')
        const sectionIndex = parseInt(idParts[idParts.length - 1])
        const isBengali = textarea.attr('id').includes('content-bn')
        
        if (!isNaN(sectionIndex) && pageForm.sections[sectionIndex]) {
          if (isBengali) {
            pageForm.sections[sectionIndex].content_bn = contents
          } else {
            pageForm.sections[sectionIndex].content = contents
          }
        }
      }
    })
  }
}

const submitForm = async () => {
  // Sync Summernote content before submission
  syncSummernoteContent()
  
  // Validation
  if (!pageForm.page_name.trim()) {
    showMessage('Page name in English is required', 'error')
    return
  }

  if (pageForm.sections.length === 0) {
    showMessage('At least one section is required', 'error')
    return
  }

  // Check if each section has at least content or attachments
  for (let i = 0; i < pageForm.sections.length; i++) {
    const section = pageForm.sections[i]
    if (!section.hasContent && !section.hasAttachments) {
      showMessage(`Section ${i + 1} must have either content or attachments`, 'error')
      return
    }
  }

  isSubmitting.value = true

  try {
    const formData = new FormData()
    formData.append('page_name', pageForm.page_name)
    formData.append('page_name_bn', pageForm.page_name_bn)
    formData.append('_method', 'PUT')

    // Process sections
    pageForm.sections.forEach((section, index) => {
      // Add section data
      formData.append(`sections[${index}][page_section_id]`, section.page_section_id || '')
      formData.append(`sections[${index}][heading]`, section.heading || '')
      formData.append(`sections[${index}][heading_bn]`, section.heading_bn || '')
      formData.append(`sections[${index}][description]`, section.description || '')
      formData.append(`sections[${index}][description_bn]`, section.description_bn || '')
      
      // Ensure content is properly set (this is the key fix)
      formData.append(`sections[${index}][content]`, section.content || '')
      formData.append(`sections[${index}][content_bn]`, section.content_bn || '')
      
      formData.append(`sections[${index}][content_width]`, section.content_width)
      formData.append(`sections[${index}][attachment_width]`, section.attachment_width)
      formData.append(`sections[${index}][content_allignment]`, section.content_allignment)
      formData.append(`sections[${index}][attachment_allignment]`, section.attachment_allignment)

      // Debug logging
      console.log(`Section ${index} content:`, section.content ? 'Has content' : 'No content')
      console.log(`Section ${index} content_bn:`, section.content_bn ? 'Has content_bn' : 'No content_bn')

      // Add existing attachments
      if (section.existing_attachments && section.existing_attachments.length > 0) {
        section.existing_attachments.forEach((attachment, attachmentIndex) => {
          formData.append(`sections[${index}][existing_attachments][${attachmentIndex}]`, attachment)
        })
      }

      // Add new files
      if (section.new_attachments && section.new_attachments.length > 0) {
        section.new_attachments.forEach((file, fileIndex) => {
          formData.append(`sections[${index}][new_attachments][${fileIndex}]`, file)
        })
      }
    })

    console.log('Submitting form data with sections:', pageForm.sections)

    const response = await fetch(`/admin/pages/${pageData.value.page_id}`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: formData
    })

    console.log('Response status:', response.status)

    // Check if response is JSON
    const contentType = response.headers.get('content-type')
    if (!contentType || !contentType.includes('application/json')) {
      const text = await response.text()
      console.error('Non-JSON response received:', text.substring(0, 500))
      throw new Error('Server returned non-JSON response. Please check the server logs.')
    }

    const data = await response.json()

    if (!response.ok) {
      // Handle HTTP errors (4xx, 5xx)
      if (data.errors) {
        const errorMessages = Object.values(data.errors).flat().join(', ')
        throw new Error(`Validation failed: ${errorMessages}`)
      }
      throw new Error(data.message || `HTTP error! status: ${response.status}`)
    }

    if (data.success) {
      showMessage('Page updated successfully!', 'success')
      // Refresh page data
      setTimeout(() => {
        router.reload()
      }, 1500)
    } else {
      throw new Error(data.message || 'Failed to update page')
    }
  } catch (error) {
    console.error('Error updating page:', error)
    
    if (error.message.includes('non-JSON response')) {
      showMessage('Server error occurred. Please check the browser console and server logs.', 'error')
    } else {
      showMessage(error.message || 'An error occurred while updating the page', 'error')
    }
  } finally {
    isSubmitting.value = false
  }
}

// Initialize Summernote editors
const initSummernote = () => {
  if (!window.$ || !window.$.fn.summernote) {
    console.warn('Summernote not loaded yet, retrying...')
    setTimeout(initSummernote, 100)
    return
  }

  $('.summernote-editor').each(function() {
    const textarea = $(this)
    const id = textarea.attr('id')
    
    if (!id || textarea.hasClass('summernote-initialized')) {
      return
    }

    const idParts = id.split('-')
    const sectionIndex = parseInt(idParts[idParts.length - 1])
    const isBengali = id.includes('content-bn')
    
    if (isNaN(sectionIndex)) {
      console.warn('Could not parse section index from ID:', id)
      return
    }

    textarea.summernote({
      height: 300,
      toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['fontname', ['fontname']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video']],
        ['view', ['fullscreen', 'codeview', 'help']]
      ],
      callbacks: {
        onInit: function() {
          textarea.addClass('summernote-initialized')
          summernoteInitialized.value = true
          console.log('Summernote initialized for:', id)
        },
        onChange: function(contents) {
          // Update the Vue data model directly
          if (isBengali) {
            pageForm.sections[sectionIndex].content_bn = contents
          } else {
            pageForm.sections[sectionIndex].content = contents
          }
        },
        onBlur: function() {
          // Also update on blur for safety
          const contents = textarea.summernote('code')
          if (isBengali) {
            pageForm.sections[sectionIndex].content_bn = contents
          } else {
            pageForm.sections[sectionIndex].content = contents
          }
        }
      }
    })
  })
}

onMounted(() => {
  initializeForm()
  
  // Wait for next tick to ensure DOM is rendered
  nextTick(() => {
    initSummernote()
  })
  
  // Watch for section changes and reinitialize Summernote
  watch(() => pageForm.sections, () => {
    nextTick(() => {
      initSummernote()
    })
  }, { deep: true, flush: 'post' })
})
</script>

<style scoped>
.summernote-editor {
  min-height: 200px;
}

/* Custom styles for better visual appearance */
.aspect-square {
  aspect-ratio: 1 / 1;
}

/* Smooth transitions for hover effects */
.group:hover .group-hover\:opacity-100 {
  opacity: 1;
}
</style>
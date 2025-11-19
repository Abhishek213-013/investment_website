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
              <h1 class="text-2xl font-bold text-gray-900">Add New Page</h1>
              <p class="text-gray-600 mt-1">Create a new page with multiple sections</p>
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
          <div v-for="(section, index) in pageForm.sections" :key="index" class="border border-gray-200 rounded-lg p-4 mb-4">
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
                      @change="onContentTypeChange(index)"
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
                        @input="onSummernoteContentChange($event, index, 'content')"
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
                        @input="onSummernoteContentChange($event, index, 'content_bn')"
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

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Attachments (JPG, PNG, WEBP, PDF, DOCS, CSV)
                    </label>
                    <input
                      type="file"
                      multiple
                      @change="handleFileUpload($event, index)"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.csv"
                    >
                    
                    <!-- File Preview -->
                    <div v-if="section.attachments && section.attachments.length" class="mt-3 space-y-2">
                      <div v-for="(file, fileIndex) in section.attachments" :key="fileIndex" class="flex items-center justify-between bg-gray-50 px-3 py-2 rounded">
                        <span class="text-sm text-gray-600">{{ file.name }}</span>
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
            @click="resetForm"
            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
          >
            Reset
          </button>
          <button
            @click="submitForm"
            :disabled="isSubmitting"
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors disabled:opacity-50"
          >
            <span v-if="isSubmitting">Creating...</span>
            <span v-else>Create Page</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminSidebar from '../../../Layouts/AdminSidebar.vue'
import AdminNavbar from '../../../Layouts/AdminNavbar.vue'

// Reactive data
const isSubmitting = ref(false)
const message = ref('')
const messageType = ref('')

// Page form data
const pageForm = reactive({
  page_name: '',
  page_name_bn: '',
  sections: [
    {
      heading: '',
      heading_bn: '',
      description: '',
      description_bn: '',
      content: '',
      content_bn: '',
      attachments: [],
      hasContent: false,
      hasAttachments: false,
      content_width: '100%',
      attachment_width: '100%',
      content_allignment: 'L',
      attachment_allignment: 'R'
    }
  ]
})

// Methods
const addSection = () => {
  pageForm.sections.push({
    heading: '',
    heading_bn: '',
    description: '',
    description_bn: '',
    content: '',
    content_bn: '',
    attachments: [],
    hasContent: false,
    hasAttachments: false,
    content_width: '100%',
    attachment_width: '100%',
    content_allignment: 'L',
    attachment_allignment: 'R'
  })
  
  // Initialize Summernote for the new section
  nextTick(() => {
    initSummernoteForSection(pageForm.sections.length - 1);
  });
}

const removeSection = (index) => {
  if (pageForm.sections.length > 1) {
    // Destroy Summernote instances before removing section
    destroySummernoteForSection(index);
    pageForm.sections.splice(index, 1);
  }
}

const onContentTypeChange = (index) => {
  nextTick(() => {
    if (pageForm.sections[index].hasContent) {
      initSummernoteForSection(index);
    } else {
      destroySummernoteForSection(index);
    }
  });
}

const handleFileUpload = (event, sectionIndex) => {
  const files = Array.from(event.target.files)
  if (!pageForm.sections[sectionIndex].attachments) {
    pageForm.sections[sectionIndex].attachments = []
  }
  pageForm.sections[sectionIndex].attachments.push(...files)
  event.target.value = '' // Reset file input
}

const removeFile = (sectionIndex, fileIndex) => {
  pageForm.sections[sectionIndex].attachments.splice(fileIndex, 1)
}

const onSummernoteContentChange = (event, sectionIndex, field) => {
  // This will be triggered when Summernote content changes
  pageForm.sections[sectionIndex][field] = event.target.value;
}

// Summernote initialization
const initSummernoteForSection = (sectionIndex) => {
  nextTick(() => {
    if (window.$ && window.$.fn.summernote) {
      // Initialize English content editor
      const enEditor = $(`#content-en-${sectionIndex}`);
      if (enEditor.length && !enEditor.hasClass('summernote-initialized')) {
        enEditor.summernote({
          height: 200,
          toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
          ],
          callbacks: {
            onChange: function(contents) {
              pageForm.sections[sectionIndex].content = contents;
            },
            onBlur: function() {
              pageForm.sections[sectionIndex].content = $(this).summernote('code');
            }
          }
        }).addClass('summernote-initialized');
      }

      // Initialize Bengali content editor
      const bnEditor = $(`#content-bn-${sectionIndex}`);
      if (bnEditor.length && !bnEditor.hasClass('summernote-initialized')) {
        bnEditor.summernote({
          height: 200,
          toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
          ],
          callbacks: {
            onChange: function(contents) {
              pageForm.sections[sectionIndex].content_bn = contents;
            },
            onBlur: function() {
              pageForm.sections[sectionIndex].content_bn = $(this).summernote('code');
            }
          }
        }).addClass('summernote-initialized');
      }
    }
  });
}

const destroySummernoteForSection = (sectionIndex) => {
  if (window.$ && window.$.fn.summernote) {
    $(`#content-en-${sectionIndex}`).summernote('destroy');
    $(`#content-bn-${sectionIndex}`).summernote('destroy');
  }
}

const initSummernote = () => {
  // Initialize Summernote for existing sections
  pageForm.sections.forEach((section, index) => {
    if (section.hasContent) {
      initSummernoteForSection(index);
    }
  });
}

const showMessage = (text, type = 'success') => {
  message.value = text
  messageType.value = type
  setTimeout(() => {
    message.value = ''
  }, 5000)
}

const resetForm = () => {
  // Destroy all Summernote instances first
  pageForm.sections.forEach((section, index) => {
    destroySummernoteForSection(index);
  });
  
  pageForm.page_name = ''
  pageForm.page_name_bn = ''
  pageForm.sections = [
    {
      heading: '',
      heading_bn: '',
      description: '',
      description_bn: '',
      content: '',
      content_bn: '',
      attachments: [],
      hasContent: false,
      hasAttachments: false,
      content_width: '100%',
      attachment_width: '100%',
      content_allignment: 'L',
      attachment_allignment: 'R'
    }
  ]
  showMessage('Form has been reset', 'success')
}

const submitForm = async () => {
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

    // Process sections
    pageForm.sections.forEach((section, index) => {
      // Add section data
      formData.append(`sections[${index}][heading]`, section.heading || '')
      formData.append(`sections[${index}][heading_bn]`, section.heading_bn || '')
      formData.append(`sections[${index}][description]`, section.description || '')
      formData.append(`sections[${index}][description_bn]`, section.description_bn || '')
      formData.append(`sections[${index}][content]`, section.content || '')
      formData.append(`sections[${index}][content_bn]`, section.content_bn || '')
      formData.append(`sections[${index}][content_width]`, section.content_width)
      formData.append(`sections[${index}][attachment_width]`, section.attachment_width)
      formData.append(`sections[${index}][content_allignment]`, section.content_allignment)
      formData.append(`sections[${index}][attachment_allignment]`, section.attachment_allignment)

      // Add files
      if (section.attachments && section.attachments.length > 0) {
        section.attachments.forEach((file, fileIndex) => {
          formData.append(`sections[${index}][attachments][${fileIndex}]`, file)
        })
      }
    })

    const response = await fetch('/admin/pages/store', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: formData
    })

    const data = await response.json()

    if (data.success) {
      showMessage('Page created successfully!', 'success')
      resetForm()
      // Optionally redirect to pages list
      // router.visit('/admin/pages/site/2')
    } else {
      showMessage(data.message || 'Failed to create page', 'error')
    }
  } catch (error) {
    console.error('Error creating page:', error)
    showMessage('An error occurred while creating the page', 'error')
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  // Initialize Summernote after component is mounted
  setTimeout(() => {
    initSummernote();
  }, 300);
})

// Cleanup Summernote instances when component is unmounted
import { onUnmounted } from 'vue'
onUnmounted(() => {
  pageForm.sections.forEach((section, index) => {
    destroySummernoteForSection(index);
  });
})
</script>

<style scoped>
.summernote-editor {
  min-height: 200px;
}

/* Ensure Summernote modal z-index is high enough */
.note-editor.note-frame {
  z-index: 1000;
}
</style>
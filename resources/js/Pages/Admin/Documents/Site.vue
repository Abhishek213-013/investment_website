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
              <h1 class="text-2xl font-bold text-gray-900">{{ getSectionName(siteId) }}</h1>
              <p class="text-gray-600 mt-1">Manage documents and content for {{ getSectionName(siteId).toLowerCase() }}</p>
            </div>
            <button 
              @click="openAddDocumentModal"
              class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Add New {{ getSectionName(siteId) }}
            </button>
          </div>
        </div>
      </div>

      <!-- Submenu Selection for Services and News -->
      <div v-if="hasSubTypes(siteId)" class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center space-x-4">
          <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Select Subtype:</label>
          <select 
            v-model="selectedSubType"
            @change="onSubTypeChange"
            class="block w-64 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">All {{ getSectionName(siteId) }}</option>
            <option 
              v-for="subType in getSubTypes(siteId)" 
              :key="subType.id" 
              :value="subType.id"
            >
              {{ subType.type_name }}
            </option>
          </select>
          <div class="text-sm text-gray-500">
            {{ getSelectedSubTypeName() }}
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="p-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="p-6">
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-8">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
              <p class="text-gray-600 mt-2">Loading documents...</p>
            </div>

            <!-- Documents Table -->
            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th 
                      v-for="field in getFieldsForSection()" 
                      :key="field.id"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      {{ field.name }}
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="filteredDocuments.length === 0">
                    <td :colspan="getFieldsForSection().length + 1" class="px-4 py-8 text-center text-gray-500">
                      <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                      No documents found. 
                      <span v-if="selectedSubType">
                        for {{ getSelectedSubTypeName() }}
                      </span>
                      Click "Add New {{ getSectionName(siteId) }}" to get started.
                    </td>
                  </tr>
                  <tr v-else v-for="document in filteredDocuments" :key="document.id">
                    <td 
                      v-for="field in getFieldsForSection()" 
                      :key="field.id"
                      class="px-4 py-4 text-sm text-gray-900"
                    >
                      <template v-if="field.type === 'title'">
                        <div>
                          <div class="font-medium">{{ document.title }}</div>
                          <div v-if="document.title_bn" class="text-gray-500 text-xs">{{ document.title_bn }}</div>
                        </div>
                      </template>
                      <template v-else-if="field.type === 'description'">
                        <div>
                          <div class="max-w-xs">{{ document.description || '-' }}</div>
                          <div v-if="document.description_bn" class="text-gray-500 text-xs">{{ document.description_bn }}</div>
                        </div>
                      </template>
                      <template v-else-if="field.type === 'attachments'">
                        <div v-if="document.attachments" class="flex flex-wrap gap-1">
                          <span 
                            v-for="(attachment, index) in document.attachments.split(',')" 
                            :key="index"
                            @click="viewAttachment(attachment)"
                            class="inline-flex items-center px-2 py-1 rounded text-xs bg-blue-100 text-blue-800 hover:bg-blue-200 cursor-pointer"
                          >
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            {{ getFileName(attachment) }}
                          </span>
                        </div>
                        <span v-else class="text-gray-400 text-xs">No attachments</span>
                      </template>
                      <template v-else-if="field.type === 'content'">
                        <div class="max-w-xs">
                          <!-- English Content -->
                          <div 
                            v-if="document.content"
                            class="prose prose-sm max-w-none text-gray-900 mb-3"
                            v-html="document.content"
                          ></div>
                          <div v-else class="text-gray-400 text-sm">-</div>
                          
                          <!-- Bangla Content -->
                          <div 
                            v-if="document.content_bn"
                            class="prose prose-sm max-w-none text-gray-500 text-xs border-t border-gray-100 pt-2 mt-2"
                            v-html="document.content_bn"
                          ></div>
                        </div>
                      </template>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                      <button 
                        @click="editDocument(document)" 
                        class="text-indigo-600 hover:text-indigo-900 mr-3 text-xs"
                      >
                        Edit
                      </button>
                      <button 
                        @click="deleteDocument(document)" 
                        class="text-red-600 hover:text-red-900 text-xs"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Document Modal -->
  <div v-if="showDocumentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">
          {{ editingDocument ? 'Edit' : 'Add New' }} {{ getSectionName(siteId) }}
          <span v-if="selectedSubType" class="text-indigo-600"> - {{ getSelectedSubTypeName() }}</span>
        </h3>
        <!-- X Button to close modal -->
        <button 
          @click="closeDocumentModal"
          class="text-gray-400 hover:text-gray-600 transition-colors"
          :disabled="formLoading"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      
      <form @submit.prevent="saveDocument" class="p-6 space-y-6">
        <!-- Loading State for Form -->
        <div v-if="formLoading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
          <div class="text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
            <p class="text-gray-600 mt-2">Saving document...</p>
          </div>
        </div>

        <!-- Subtype Selection (only when adding new document and subtype is not selected) -->
        <div v-if="hasSubTypes(siteId) && !editingDocument && !selectedSubType" class="bg-blue-50 border border-blue-200 rounded-md p-4">
          <label class="block text-sm font-medium text-blue-700 mb-2">
            Select Subtype <span class="text-red-500">*</span>
          </label>
          <select 
            v-model="formData.doc_type_id"
            class="block w-full px-3 py-2 border border-blue-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            required
          >
            <option value="">Select a subtype</option>
            <option 
              v-for="subType in getSubTypes(siteId)" 
              :key="subType.id" 
              :value="subType.id"
            >
              {{ subType.type_name }}
            </option>
          </select>
          <p class="text-xs text-blue-600 mt-1">
            Please select the specific subtype for this document
          </p>
        </div>

        <!-- Title Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
              Title (English) <span class="text-red-500">*</span>
            </label>
            <input
              id="title"
              v-model="formData.title"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Enter title in English"
              required
              :disabled="formLoading"
            >
          </div>
          <div>
            <label for="title_bn" class="block text-sm font-medium text-gray-700 mb-2">
              Title (Bangla)
            </label>
            <input
              id="title_bn"
              v-model="formData.title_bn"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="বাংলায় শিরোনাম লিখুন"
              :disabled="formLoading"
            >
          </div>
        </div>

        <!-- Description Fields (if applicable for this section) -->
        <div v-if="hasField('description')" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
              Description (English)
            </label>
            <textarea
              id="description"
              v-model="formData.description"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Enter description in English"
              :disabled="formLoading"
            ></textarea>
          </div>
          <div>
            <label for="description_bn" class="block text-sm font-medium text-gray-700 mb-2">
              Description (Bangla)
            </label>
            <textarea
              id="description_bn"
              v-model="formData.description_bn"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="বাংলায় বর্ণনা লিখুন"
              :disabled="formLoading"
            ></textarea>
          </div>
        </div>

        <!-- Content Fields (if applicable for this section) -->
        <div v-if="hasField('content')" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="content_text" class="block text-sm font-medium text-gray-700 mb-2">
              Content (English)
            </label>
            <div class="summernote-container">
              <textarea
                id="content_text"
                ref="contentTextEditor"
                v-model="formData.content_text"
                rows="6"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 summernote-editor"
                placeholder="Enter content in English"
                :disabled="formLoading"
              ></textarea>
            </div>
          </div>
          <div>
            <label for="content_bn" class="block text-sm font-medium text-gray-700 mb-2">
              Content (Bangla)
            </label>
            <div class="summernote-container">
              <textarea
                id="content_bn"
                ref="contentBnEditor"
                v-model="formData.content_bn"
                rows="6"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 summernote-editor"
                placeholder="বাংলায় কন্টেন্ট লিখুন"
                :disabled="formLoading"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Attachments Field (if applicable for this section) -->
        <div v-if="hasField('attachments')">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Attachments
          </label>
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
            <input
              type="file"
              ref="fileInput"
              @change="handleFileUpload"
              multiple
              class="hidden"
              accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
              :disabled="formLoading"
            >
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <div class="flex text-sm text-gray-600 justify-center">
              <button
                type="button"
                @click="$refs.fileInput.click()"
                class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                :disabled="formLoading"
              >
                <span>Upload files</span>
              </button>
              <p class="pl-1">or drag and drop</p>
            </div>
            <p class="text-xs text-gray-500 mt-2">
              PNG, JPG, PDF, DOC up to 10MB each
            </p>
          </div>

          <!-- Selected Files Preview -->
          <div v-if="selectedFiles.length > 0" class="mt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Files:</h4>
            <div class="space-y-2">
              <div 
                v-for="(file, index) in selectedFiles" 
                :key="index"
                class="flex items-center justify-between bg-gray-50 px-3 py-2 rounded-md"
              >
                <div class="flex items-center">
                  <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <span class="text-sm text-gray-700">{{ file.name }}</span>
                  <span class="text-xs text-gray-500 ml-2">({{ formatFileSize(file.size) }})</span>
                </div>
                <button
                  type="button"
                  @click="removeFile(index)"
                  class="text-red-500 hover:text-red-700 text-sm"
                  :disabled="formLoading"
                >
                  Remove
                </button>
              </div>
            </div>
          </div>

          <!-- Existing Attachments (when editing) -->
          <div v-if="editingDocument && editingDocument.attachments" class="mt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Current Attachments:</h4>
            <div class="space-y-2">
              <div 
                v-for="(attachment, index) in editingDocument.attachments.split(',')" 
                :key="index"
                class="flex items-center justify-between bg-blue-50 px-3 py-2 rounded-md"
              >
                <div class="flex items-center">
                  <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <span class="text-sm text-blue-700">{{ getFileName(attachment) }}</span>
                </div>
                <div class="flex space-x-2">
                  <button
                    type="button"
                    @click="viewAttachment(attachment)"
                    class="text-blue-500 hover:text-blue-700 text-sm"
                    :disabled="formLoading"
                  >
                    View
                  </button>
                  <button
                    type="button"
                    @click="removeExistingAttachment(index)"
                    class="text-red-500 hover:text-red-700 text-sm"
                    :disabled="formLoading"
                  >
                    Remove
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <button
            type="button"
            @click="closeDocumentModal"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            :disabled="formLoading"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="formLoading || (hasSubTypes(siteId) && !editingDocument && !selectedSubType && !formData.doc_type_id)"
          >
            {{ formLoading ? 'Saving...' : (editingDocument ? 'Update' : 'Save') }} Document
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Success Notification -->
  <div
    v-if="showSuccess"
    class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300"
  >
    {{ successMessage }}
  </div>

  <!-- Error Notification -->
  <div
    v-if="showError"
    class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300"
  >
    {{ errorMessage }}
  </div>

  <!-- Attachment Preview Modal -->
  <div v-if="showAttachmentPreview" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Attachment Preview</h3>
        <button 
          @click="closeAttachmentPreview" 
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <div class="p-6 max-h-[calc(90vh-80px)] overflow-y-auto">
        <!-- Loading State -->
        <div v-if="previewLoading" class="flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
          <span class="ml-2 text-gray-600">Loading preview...</span>
        </div>

        <!-- Image Preview -->
        <div v-else-if="isImageFile(currentAttachment)" class="text-center">
          <div v-if="previewLoading" class="flex flex-col items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
            <span class="text-gray-600">Loading image preview...</span>
            <span class="text-gray-400 text-sm mt-1">URL: {{ getAttachmentUrl(currentAttachment) }}</span>
          </div>
          
          <div v-else class="space-y-4">
            <!-- Image container with fallback -->
            <div class="relative bg-gray-100 rounded-lg p-4 min-h-[200px] flex items-center justify-center">
              <img 
                :src="getAttachmentUrl(currentAttachment)" 
                :alt="getFileName(currentAttachment)"
                class="max-w-full max-h-[60vh] mx-auto rounded-lg shadow-md"
                @error="handleImageError"
              >
            </div>
            
            <!-- Image info and actions -->
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-medium text-gray-700 mb-1">File Information</p>
              <p class="text-xs text-gray-600 mb-2">{{ getFileName(currentAttachment) }}</p>
              <div class="flex justify-center space-x-3">
                <button
                  @click="downloadAttachment(currentAttachment)"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors inline-flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  Download Image
                </button>
                <button
                  @click="openImageInNewTab(currentAttachment)"
                  class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors inline-flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                  </svg>
                  Open in New Tab
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- PDF Preview -->
        <div v-else-if="isPdfFile(currentAttachment)" class="text-center">
          <div class="bg-red-50 border border-red-200 rounded-lg p-8 max-w-md mx-auto">
            <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h4 class="text-lg font-medium text-red-800 mb-2">PDF Document</h4>
            <p class="text-red-700 mb-4">{{ getFileName(currentAttachment) }}</p>
            <p class="text-sm text-red-600 mb-4">PDF files can be viewed directly in the browser or downloaded.</p>
            <div class="flex justify-center space-x-3">
              <button
                @click="openPdfInNewTab(currentAttachment)"
                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors inline-flex items-center"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View PDF
              </button>
              <button
                @click="downloadAttachment(currentAttachment)"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors inline-flex items-center"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
              </button>
            </div>
          </div>
        </div>

        <!-- Document Preview (Word, etc.) -->
        <div v-else-if="isDocumentFile(currentAttachment)" class="text-center">
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 max-w-md mx-auto">
            <svg class="w-16 h-16 mx-auto text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h4 class="text-lg font-medium text-blue-800 mb-2">Document File</h4>
            <p class="text-blue-700 mb-4">{{ getFileName(currentAttachment) }}</p>
            <p class="text-sm text-blue-600 mb-4">This document can be downloaded and opened with appropriate software.</p>
            <button
              @click="downloadAttachment(currentAttachment)"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors inline-flex items-center"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Download Document
            </button>
          </div>
        </div>

        <!-- Unknown File Type -->
        <div v-else class="text-center">
          <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 max-w-md mx-auto">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h4 class="text-lg font-medium text-gray-800 mb-2">File Preview</h4>
            <p class="text-gray-600 mb-2">{{ currentAttachment }}</p>
            <p class="text-sm text-gray-500 mb-4">Preview not available for this file type. You can download the file to view it.</p>
            <button
              @click="downloadAttachment(currentAttachment)"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
            >
              Download File
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, nextTick, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import $ from 'jquery'
import 'summernote/dist/summernote-lite.min.css'
import 'summernote/dist/summernote-lite.min.js'
import AdminSidebar from '../../../Layouts/AdminSidebar.vue'
import AdminNavbar from '../../../Layouts/AdminNavbar.vue'

// Get siteId from route props
const props = defineProps({
  siteId: {
    type: [String, Number],
    required: true
  }
})

// Reactive data
const showDocumentModal = ref(false)
const showSuccess = ref(false)
const showError = ref(false)
const showAttachmentPreview = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const loading = ref(false)
const formLoading = ref(false)
const documentTypes = ref([])
const editingDocument = ref(null)
const selectedFiles = ref([])
const currentAttachment = ref('')
const selectedSubType = ref('') // For submenu selection
const previewLoading = ref(false)
const contentTextEditor = ref(null)
const contentBnEditor = ref(null)
const summernoteInstances = ref([])
const availableFields = ref([])
// Documents data
const documents = ref([])
const fieldConfigurations = ref({})

const initializeSummernote = () => {
  // Destroy existing instances first
  destroySummernote()
  
  // Initialize English content editor
  if (contentTextEditor.value) {
    $(contentTextEditor.value).summernote({
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
        onChange: function(contents) {
          formData.content_text = contents
        },
        onBlur: function() {
          formData.content_text = $(contentTextEditor.value).summernote('code')
        }
      }
    })
    summernoteInstances.value.push($(contentTextEditor.value))
  }

  // Initialize Bangla content editor
  if (contentBnEditor.value) {
    $(contentBnEditor.value).summernote({
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
        onChange: function(contents) {
          formData.content_bn = contents
        },
        onBlur: function() {
          formData.content_bn = $(contentBnEditor.value).summernote('code')
        }
      }
    })
    summernoteInstances.value.push($(contentBnEditor.value))
  }
}

const destroySummernote = () => {
  summernoteInstances.value.forEach(instance => {
    if (instance.summernote) {
      instance.summernote('destroy')
    }
  })
  summernoteInstances.value = []
}

const resetSummernoteContent = () => {
  if (contentTextEditor.value && $(contentTextEditor.value).summernote) {
    $(contentTextEditor.value).summernote('reset')
  }
  if (contentBnEditor.value && $(contentBnEditor.value).summernote) {
    $(contentBnEditor.value).summernote('reset')
  }
}

// Form data
const formData = reactive({
  doc_type_id: '', // For subtype selection when creating new document
  title: '',
  title_bn: '',
  description: '',
  description_bn: '',
  content_text: '',
  content_bn: '',
  attachments: ''
});

// Section configurations based on siteId
// const sectionConfigs = {
//   1: { // Services
//     name: 'Services',
//     fields: [
//       { type: 'title', name: 'Title' },
//       { type: 'description', name: 'Description' },
//       { type: 'attachments', name: 'Attachments' }
//     ]
//   },
//   2: { // Download
//     name: 'Download',
//     fields: [
//       { type: 'title', name: 'Title' },
//       { type: 'attachments', name: 'Attachments' }
//     ]
//   },
//   3: { // News
//     name: 'News',
//     fields: [
//       { type: 'title', name: 'Title' },
//       { type: 'description', name: 'Description' },
//       { type: 'attachments', name: 'Attachments' },
//       { type: 'content', name: 'Content' }
//     ]
//   },
//   4: { // FAQ
//     name: 'FAQ',
//     fields: [
//       { type: 'title', name: 'Title' },
//       { type: 'content', name: 'Content' }
//     ]
//   },
//   5: { // Contact
//     name: 'Contact',
//     fields: [
//       { type: 'title', name: 'Title' },
//       { type: 'attachments', name: 'Attachments' },
//       { type: 'content', name: 'Content' }
//     ]
//   }
// }

// Subtypes data (this would come from your API in real app)
const subTypes = ref({
  1: [ // Services subtypes
    { id: 6, type_name: 'Offline Services', type_name_bn: 'অফলাইন সেবা', parent_id: 1 },
    { id: 7, type_name: 'Online Services', type_name_bn: 'অনলাইন সেবা', parent_id: 1 }
  ],
  3: [ // News subtypes
    { id: 8, type_name: 'SSL News', type_name_bn: 'এসএসএল খবর', parent_id: 3 },
    { id: 9, type_name: 'DSE News', type_name_bn: 'ডিএসই খবর', parent_id: 3 }
  ]
})

// Computed properties
const filteredDocuments = computed(() => {
  if (!selectedSubType.value) {
    return documents.value
  }
  return documents.value.filter(doc => doc.doc_type_id == selectedSubType.value)
})

const hasSubTypes = (siteId) => {
  return [1, 3].includes(parseInt(siteId)) // Services and News have subtypes
}

const getSubTypes = (siteId) => {
  return subTypes.value[siteId] || []
}

const getSelectedSubTypeName = () => {
  if (!selectedSubType.value) return ''
  const subType = getSubTypes(props.siteId).find(st => st.id == selectedSubType.value)
  return subType ? subType.type_name : ''
}

const getFileType = (filename) => {
  if (!filename) return 'unknown'
  
  const ext = filename.toLowerCase().split('.').pop()
  
  const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg']
  const pdfTypes = ['pdf']
  const documentTypes = ['doc', 'docx', 'txt', 'rtf']
  
  if (imageTypes.includes(ext)) return 'image'
  if (pdfTypes.includes(ext)) return 'pdf'
  if (documentTypes.includes(ext)) return 'document'
  
  return 'unknown'
}

const isImageFile = (filename) => {
  return getFileType(filename) === 'image'
}

const openImageInNewTab = (attachment) => {
  const imageUrl = getAttachmentUrl(attachment);
  window.open(imageUrl, '_blank');
}

const isPdfFile = (filename) => {
  return getFileType(filename) === 'pdf'
}

const isDocumentFile = (filename) => {
  return getFileType(filename) === 'document'
}

const getAttachmentUrl = (attachment) => {
  console.log('Original attachment path:', attachment); // Debug log
  
  // If attachment is already a full storage path like 'documents/filename.jpg'
  if (attachment && attachment.startsWith('documents/')) {
    return `/storage/${attachment}`;
  }
  
  // If it's just a filename, construct the full path
  const filename = getFileName(attachment);
  return `/storage/documents/${filename}`;
}

const openPdfInNewTab = (attachment) => {
  const filename = getFileName(attachment)
  const url = `/storage/documents/${filename}`
  window.open(url, '_blank')
}

const handleImageError = (event) => {
  console.error('Image error event:', event);
  previewLoading.value = false;
  
  // Show fallback content
  const fallbackDiv = document.createElement('div');
  fallbackDiv.className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center';
  fallbackDiv.innerHTML = `
    <svg class="w-12 h-12 mx-auto text-yellow-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
    </svg>
    <p class="text-yellow-800 font-medium">Image failed to load</p>
    <p class="text-yellow-600 text-sm mt-1">You can still download the image file.</p>
  `;
  
  // Replace the image with fallback content
  if (event.target.parentNode) {
    event.target.style.display = 'none';
    event.target.parentNode.appendChild(fallbackDiv);
  }
}

// Update the viewAttachment method to handle loading state
const viewAttachment = async (attachment) => {
  currentAttachment.value = attachment;
  previewLoading.value = true;
  showAttachmentPreview.value = true;
  
  console.log('Viewing attachment:', attachment); // Debug log
  console.log('Image URL will be:', getAttachmentUrl(attachment)); // Debug log
  
  // For non-image files, we don't need loading state
  if (!isImageFile(attachment)) {
    previewLoading.value = false;
  } else {
    // For images, preload to check if they're accessible
    await preloadImage(attachment);
  }
}

const preloadImage = (attachment) => {
  return new Promise((resolve) => {
    const img = new Image();
    const imageUrl = getAttachmentUrl(attachment);
    
    img.onload = () => {
      console.log('Image loaded successfully:', imageUrl);
      previewLoading.value = false;
      resolve(true);
    };
    
    img.onerror = () => {
      console.error('Failed to load image:', imageUrl);
      previewLoading.value = false;
      resolve(false);
    };
    
    img.src = imageUrl;
  });
}

// Update the closeAttachmentPreview method to reset loading state
const closeAttachmentPreview = () => {
  showAttachmentPreview.value = false
  currentAttachment.value = ''
  previewLoading.value = false
}

// Methods
const getSectionName = (siteId) => {
  const type = documentTypes.value.find(t => t.id === parseInt(siteId))
  return type ? type.type_name : 'Document'
}

const loadDocumentTypes = async () => {
  try {
    const response = await fetch('/admin/documents/types')
    if (response.ok) {
      const data = await response.json()
      documentTypes.value = data.types || []
    }
  } catch (error) {
    console.error('Error loading document types:', error)
  }
}

const getFieldsForSection = () => {
  const typeId = getCurrentTypeId()
  if (!typeId || !fieldConfigurations.value[typeId]) {
    return []
  }
  
  const enabledFieldIds = fieldConfigurations.value[typeId] || []
  return availableFields.value.filter(field => enabledFieldIds.includes(field.id))
}

const getCurrentTypeId = () => {
  if (selectedSubType.value) {
    return selectedSubType.value
  }
  return props.siteId
}

const hasField = (fieldType) => {
  return getFieldsForSection().some(field => field.type === fieldType)
}
const loadFieldConfigurations = async () => {
  try {
    const typeId = getCurrentTypeId()
    if (!typeId) return

    const response = await fetch(`/admin/documents/field-config/${typeId}`)
    if (response.ok) {
      const data = await response.json()
      fieldConfigurations.value[typeId] = data.enabledFields || []
    }
  } catch (error) {
    console.error('Error loading field configurations:', error)
  }
}

const loadAvailableFields = async () => {
  try {
    const response = await fetch('/admin/documents/types-with-fields')
    if (response.ok) {
      const data = await response.json()
      availableFields.value = data.availableFields || []
    } else {
      // Fallback fields if API fails
      availableFields.value = [
        { id: 1, name: 'Title', description: 'Document title field', type: 'title' },
        { id: 2, name: 'Description', description: 'Document description field', type: 'description' },
        { id: 3, name: 'Attachments', description: 'File attachments field', type: 'attachments' },
        { id: 4, name: 'Content', description: 'Rich text content field', type: 'content' }
      ]
    }
  } catch (error) {
    console.error('Error loading available fields:', error)
    // Fallback fields
    availableFields.value = [
      { id: 1, name: 'Title', description: 'Document title field', type: 'title' },
      { id: 2, name: 'Description', description: 'Document description field', type: 'description' },
      { id: 3, name: 'Attachments', description: 'File attachments field', type: 'attachments' },
      { id: 4, name: 'Content', description: 'Rich text content field', type: 'content' }
    ]
  }
}
const getFileName = (filePath) => {
  return filePath.split('/').pop() || filePath
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const onSubTypeChange = () => {
  loadDocuments()
  loadFieldConfigurations() // Reload field config when subtype changes
}

const openAddDocumentModal = () => {
  editingDocument.value = null
  resetFormData()
  selectedFiles.value = []
  
  // Pre-select subtype if one is selected in the main dropdown
  if (selectedSubType.value) {
    formData.doc_type_id = selectedSubType.value
  }
  
  showDocumentModal.value = true
  
  // Initialize Summernote after modal is shown
  nextTick(() => {
    setTimeout(() => {
      initializeSummernote()
      resetSummernoteContent()
    }, 100)
  })
}

const editDocument = (document) => {
  editingDocument.value = document
  Object.assign(formData, {
    doc_type_id: document.doc_type_id,
    title: document.title || '',
    title_bn: document.title_bn || '',
    description: document.description || '',
    description_bn: document.description_bn || '',
    content_text: document.content || '',
    content_bn: document.content_bn || '',
    attachments: document.attachments || ''
  })
  selectedFiles.value = []
  showDocumentModal.value = true
  
  // Initialize Summernote with existing content after modal is shown
  nextTick(() => {
    setTimeout(() => {
      initializeSummernote()
      // Set the content in Summernote editors
      if (contentTextEditor.value && $(contentTextEditor.value).summernote) {
        $(contentTextEditor.value).summernote('code', formData.content_text)
      }
      if (contentBnEditor.value && $(contentBnEditor.value).summernote) {
        $(contentBnEditor.value).summernote('code', formData.content_bn)
      }
    }, 100)
  })
}

const deleteDocument = async (document) => {
  if (confirm('Are you sure you want to delete this document?')) {
    try {
      await router.delete(`/admin/documents/content/${document.id}`, {
        onSuccess: () => {
          loadDocuments()
          showSuccessMessage('Document deleted successfully')
        },
        onError: (errors) => {
          showErrorMessage('Failed to delete document')
        }
      })
    } catch (error) {
      showErrorMessage('Failed to delete document')
    }
  }
}

const handleFileUpload = (event) => {
  const files = Array.from(event.target.files)
  selectedFiles.value = [...selectedFiles.value, ...files]
  event.target.value = '' // Reset input
}

const removeFile = (index) => {
  selectedFiles.value.splice(index, 1)
}

const removeExistingAttachment = (index) => {
  if (editingDocument.value && editingDocument.value.attachments) {
    const attachments = editingDocument.value.attachments.split(',')
    attachments.splice(index, 1)
    editingDocument.value.attachments = attachments.join(',')
    formData.attachments = editingDocument.value.attachments
  }
}

const saveDocument = async () => {
  formLoading.value = true

  try {
    const formDataToSend = new FormData()
    
    // Determine the doc_type_id to use
    let docTypeId
    if (editingDocument.value) {
      // For editing, use the existing doc_type_id
      docTypeId = formData.doc_type_id
    } else {
      // For new documents, use selected subtype or main siteId
      docTypeId = formData.doc_type_id || props.siteId
    }
    
    // Add basic form data
    formDataToSend.append('doc_type_id', docTypeId)
    formDataToSend.append('title', formData.title)
    formDataToSend.append('title_bn', formData.title_bn || '')
    formDataToSend.append('description', formData.description || '')
    formDataToSend.append('description_bn', formData.description_bn || '')
    formDataToSend.append('content_text', formData.content_text || '')
    formDataToSend.append('content_bn', formData.content_bn || '')
    formDataToSend.append('attachments', formData.attachments || '')

    // Add files
    selectedFiles.value.forEach(file => {
      formDataToSend.append('files[]', file)
    })

    if (editingDocument.value) {
      // Update existing document
      await router.post(`/admin/documents/content/${editingDocument.value.id}`, formDataToSend, {
        onSuccess: () => {
          // Close modal and show success message
          closeDocumentModal()
          loadDocuments()
          showSuccessMessage('Document updated successfully')
        },
        onError: (errors) => {
          showErrorMessage('Failed to update document')
        }
      })
    } else {
      // Create new document
      await router.post('/admin/documents/content', formDataToSend, {
        onSuccess: () => {
          // Close modal and show success message
          closeDocumentModal()
          loadDocuments()
          showSuccessMessage('Document created successfully')
        },
        onError: (errors) => {
          showErrorMessage('Failed to create document')
        }
      })
    }
    
  } catch (error) {
    showErrorMessage('An error occurred while saving the document')
  } finally {
    formLoading.value = false
  }
}

const downloadAttachment = (attachment) => {
  const filename = getFileName(attachment)
  // Use the proper download route
  const link = document.createElement('a')
  link.href = `/admin/documents/attachment/download/${filename}`
  link.download = filename
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const closeDocumentModal = () => {
  // Only close if not loading
  if (!formLoading.value) {
    // Destroy Summernote instances before closing
    destroySummernote()
    
    showDocumentModal.value = false
    editingDocument.value = null
    resetFormData()
    selectedFiles.value = []
  }
}

const resetFormData = () => {
  Object.assign(formData, {
    doc_type_id: selectedSubType.value || '',
    title: '',
    title_bn: '',
    description: '',
    description_bn: '',
    content_text: '',
    content_bn: '',
    attachments: ''
  })
}

const showSuccessMessage = (message) => {
  successMessage.value = message
  showSuccess.value = true
  setTimeout(() => {
    showSuccess.value = false
  }, 3000)
}

const showErrorMessage = (message) => {
  errorMessage.value = message
  showError.value = true
  setTimeout(() => {
    showError.value = false
  }, 3000)
}

// Update the loadDocuments method to handle "All" selection
const loadDocuments = async () => {
  loading.value = true
  try {
    let typeId;
    
    if (selectedSubType.value) {
      // If a specific subtype is selected, use that subtype ID
      typeId = selectedSubType.value;
    } else {
      // If "All" is selected, we need to get all subtypes for this section plus the main type
      const allTypeIds = [props.siteId]; // Include the main type ID
      
      // Add all subtype IDs for this section
      if (hasSubTypes(props.siteId)) {
        const subtypes = getSubTypes(props.siteId);
        subtypes.forEach(subtype => {
          allTypeIds.push(subtype.id);
        });
      }
      
      // We'll need to modify the backend to handle multiple type IDs
      // For now, we'll fetch all documents and filter client-side
      typeId = props.siteId; // This will get documents for the main type
    }
    
    const response = await fetch(`/admin/documents/content/${typeId}`);
    const data = await response.json();
    
    let allDocuments = data.content || [];
    
    // If we're showing "All", we need to fetch documents for all subtypes
    if (!selectedSubType.value && hasSubTypes(props.siteId)) {
      // Fetch documents for each subtype
      const subtypes = getSubTypes(props.siteId);
      const subtypePromises = subtypes.map(async (subtype) => {
        try {
          const subtypeResponse = await fetch(`/admin/documents/content/${subtype.id}`);
          const subtypeData = await subtypeResponse.json();
          return subtypeData.content || [];
        } catch (error) {
          console.error(`Error loading documents for subtype ${subtype.id}:`, error);
          return [];
        }
      });
      
      const subtypeDocumentsArrays = await Promise.all(subtypePromises);
      const subtypeDocuments = subtypeDocumentsArrays.flat();
      
      // Combine main type documents with subtype documents
      allDocuments = [...allDocuments, ...subtypeDocuments];
    }
    
    documents.value = allDocuments;
    await loadFieldConfigurations()
  } catch (error) {
    console.error('Error loading documents:', error)
    showErrorMessage('Failed to load documents')
  } finally {
    loading.value = false
  }
}

// Add keyboard event listener for Escape key
const handleKeydown = (event) => {
  if (event.key === 'Escape') {
    if (showDocumentModal.value) {
      closeDocumentModal()
    }
    if (showAttachmentPreview.value) {
      closeAttachmentPreview()
    }
  }
}

// Lifecycle
onMounted(() => {
  console.log('Document Site page mounted for site:', props.siteId)
  loadAvailableFields().then(() => {
    loadDocumentTypes().then(() => {
      loadDocuments()
      loadFieldConfigurations()
    })
  })
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  destroySummernote()
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
/* Summernote custom styles */
.summernote-container {
  position: relative;
}

:deep(.note-editor) {
  border: 1px solid #d1d5db !important;
  border-radius: 0.375rem !important;
}

:deep(.note-toolbar) {
  background-color: #f9fafb !important;
  border-bottom: 1px solid #d1d5db !important;
  border-radius: 0.375rem 0.375rem 0 0 !important;
}

:deep(.note-editing-area) {
  border-radius: 0 0 0.375rem 0.375rem !important;
}

:deep(.note-btn) {
  background-color: white !important;
  border: 1px solid #d1d5db !important;
  color: #374151 !important;
}

:deep(.note-btn:hover) {
  background-color: #f3f4f6 !important;
}

:deep(.note-modal) {
  z-index: 10000 !important;
}

:deep(.note-dropdown-menu) {
  z-index: 10001 !important;
}
</style>
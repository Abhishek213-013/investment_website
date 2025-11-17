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
              <h1 class="text-2xl font-bold text-gray-900">Document Types</h1>
              <p class="text-gray-600 mt-1">Manage documents for different sections</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="p-6">
        <!-- Tabs Navigation -->
        <div class="bg-white rounded-lg shadow mb-6">
          <div class="border-b border-gray-200">
            <nav class="flex -mb-px overflow-x-auto">
              <button
                v-for="section in sections"
                :key="section.id"
                @click="activeTab = section.id"
                :class="[
                  'px-6 py-4 text-sm font-medium border-b-2 transition-colors duration-200 whitespace-nowrap',
                  activeTab === section.id
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                {{ section.section_name }}
              </button>
            </nav>
          </div>
        </div>

        <!-- Tab Content -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <!-- Services Tab -->
          <div v-if="activeTab === 1" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Services Documents</h3>
              <div class="flex space-x-3">
                <button
                  @click="openAddFieldModal(1)"
                  class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  Add New Field
                </button>
                <button
                  @click="openAddDocumentModal(1)"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  Add New Service
                </button>
              </div>
            </div>

            <!-- Services Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th 
                      v-for="field in servicesFields" 
                      :key="field.id"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      <div class="flex items-center">
                        {{ field.name }}
                        <button 
                          v-if="!field.isDefault"
                          @click="removeField(1, field.id)"
                          class="ml-2 text-red-500 hover:text-red-700 text-xs"
                        >
                          ×
                        </button>
                      </div>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="services.length === 0">
                    <td :colspan="servicesFields.length + 1" class="px-4 py-8 text-center text-gray-500">
                      No services found. Click "Add New Service" to get started.
                    </td>
                  </tr>
                  <tr v-else v-for="service in services" :key="service.id">
                    <td 
                      v-for="field in servicesFields" 
                      :key="field.id"
                      class="px-4 py-4 text-sm text-gray-900"
                    >
                      <template v-if="field.type === 'title'">
                        <div>
                          <div class="font-medium">{{ service.title }}</div>
                          <div class="text-gray-500 text-xs">{{ service.title_bn }}</div>
                        </div>
                      </template>
                      <template v-else-if="field.type === 'description'">
                        <div>
                          <div class="max-w-xs truncate">{{ service.description }}</div>
                          <div class="text-gray-500 text-xs max-w-xs truncate">{{ service.description_bn }}</div>
                        </div>
                      </template>
                      <template v-else-if="field.type === 'attachments'">
                        <span v-if="service.attachments" class="text-indigo-600 hover:text-indigo-900 cursor-pointer text-xs">
                          View Attachment
                        </span>
                        <span v-else class="text-gray-400 text-xs">No attachment</span>
                      </template>
                      <template v-else-if="field.type === 'content'">
                        <div>
                          <div class="max-w-xs truncate">{{ service.content }}</div>
                          <div class="text-gray-500 text-xs max-w-xs truncate">{{ service.content_bn }}</div>
                        </div>
                      </template>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                      <button @click="editDocument(service, 1)" class="text-indigo-600 hover:text-indigo-900 mr-3 text-xs">
                        Edit
                      </button>
                      <button @click="deleteDocument(service, 1)" class="text-red-600 hover:text-red-900 text-xs">
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Download Tab -->
          <div v-if="activeTab === 2" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Download Documents</h3>
              <div class="flex space-x-3">
                <button
                  @click="openAddFieldModal(2)"
                  class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  Add New Field
                </button>
                <button
                  @click="openAddDocumentModal(2)"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  Add New Download
                </button>
              </div>
            </div>

            <!-- Download Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th 
                      v-for="field in downloadsFields" 
                      :key="field.id"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      <div class="flex items-center">
                        {{ field.name }}
                        <button 
                          v-if="!field.isDefault"
                          @click="removeField(2, field.id)"
                          class="ml-2 text-red-500 hover:text-red-700 text-xs"
                        >
                          ×
                        </button>
                      </div>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="downloads.length === 0">
                    <td :colspan="downloadsFields.length + 1" class="px-4 py-8 text-center text-gray-500">
                      No downloads found. Click "Add New Download" to get started.
                    </td>
                  </tr>
                  <tr v-else v-for="download in downloads" :key="download.id">
                    <td 
                      v-for="field in downloadsFields" 
                      :key="field.id"
                      class="px-4 py-4 text-sm text-gray-900"
                    >
                      <template v-if="field.type === 'title'">
                        <div>
                          <div class="font-medium">{{ download.title }}</div>
                          <div class="text-gray-500 text-xs">{{ download.title_bn }}</div>
                        </div>
                      </template>
                      <template v-else-if="field.type === 'description'">
                        <div>
                          <div class="max-w-xs truncate">{{ download.description }}</div>
                          <div class="text-gray-500 text-xs max-w-xs truncate">{{ download.description_bn }}</div>
                        </div>
                      </template>
                      <template v-else-if="field.type === 'attachments'">
                        <span v-if="download.attachments" class="text-indigo-600 hover:text-indigo-900 cursor-pointer text-xs">
                          View Attachment
                        </span>
                        <span v-else class="text-gray-400 text-xs">No attachment</span>
                      </template>
                      <template v-else-if="field.type === 'content'">
                        <div>
                          <div class="max-w-xs truncate">{{ download.content }}</div>
                          <div class="text-gray-500 text-xs max-w-xs truncate">{{ download.content_bn }}</div>
                        </div>
                      </template>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                      <button @click="editDocument(download, 2)" class="text-indigo-600 hover:text-indigo-900 mr-3 text-xs">
                        Edit
                      </button>
                      <button @click="deleteDocument(download, 2)" class="text-red-600 hover:text-red-900 text-xs">
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- News Tab -->
          <div v-if="activeTab === 3" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">News</h3>
              <div class="flex space-x-3">
                <button
                  @click="openAddFieldModal(3)"
                  class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  Add New Field
                </button>
                <button
                  @click="openAddDocumentModal(3)"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  Add New News
                </button>
              </div>
            </div>

            <!-- News Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th 
                      v-for="field in newsFields" 
                      :key="field.id"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      <div class="flex items-center">
                        {{ field.name }}
                        <button 
                          v-if="!field.isDefault"
                          @click="removeField(3, field.id)"
                          class="ml-2 text-red-500 hover:text-red-700 text-xs"
                        >
                          ×
                        </button>
                      </div>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="news.length === 0">
                    <td :colspan="newsFields.length + 1" class="px-4 py-8 text-center text-gray-500">
                      No news found. Click "Add New News" to get started.
                    </td>
                  </tr>
                  <tr v-else v-for="item in news" :key="item.id">
                    <td 
                      v-for="field in newsFields" 
                      :key="field.id"
                      class="px-4 py-4 text-sm text-gray-900"
                    >
                      <template v-if="field.type === 'title'">
                        <div>
                          <div class="font-medium">{{ item.title }}</div>
                          <div class="text-gray-500 text-xs">{{ item.title_bn }}</div>
                        </div>
                      </template>
                      <template v-else-if="field.type === 'description'">
                        <div>
                          <div class="max-w-xs truncate">{{ item.description }}</div>
                          <div class="text-gray-500 text-xs max-w-xs truncate">{{ item.description_bn }}</div>
                        </div>
                      </template>
                      <template v-else-if="field.type === 'attachments'">
                        <span v-if="item.attachments" class="text-indigo-600 hover:text-indigo-900 cursor-pointer text-xs">
                          View Attachment
                        </span>
                        <span v-else class="text-gray-400 text-xs">No attachment</span>
                      </template>
                      <template v-else-if="field.type === 'content'">
                        <div>
                          <div class="max-w-xs truncate">{{ item.content }}</div>
                          <div class="text-gray-500 text-xs max-w-xs truncate">{{ item.content_bn }}</div>
                        </div>
                      </template>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                      <button @click="editDocument(item, 3)" class="text-indigo-600 hover:text-indigo-900 mr-3 text-xs">
                        Edit
                      </button>
                      <button @click="deleteDocument(item, 3)" class="text-red-600 hover:text-red-900 text-xs">
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- FAQ Tab -->
          <div v-if="activeTab === 4" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">FAQ</h3>
              <div class="flex space-x-3">
                <button
                  @click="openAddFieldModal(4)"
                  class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  Add New Field
                </button>
                <button
                  @click="openAddDocumentModal(4)"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  Add New FAQ
                </button>
              </div>
            </div>

            <!-- FAQ Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th 
                      v-for="field in faqsFields" 
                      :key="field.id"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      <div class="flex items-center">
                        {{ field.name }}
                        <button 
                          v-if="!field.isDefault"
                          @click="removeField(4, field.id)"
                          class="ml-2 text-red-500 hover:text-red-700 text-xs"
                        >
                          ×
                        </button>
                      </div>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="faqs.length === 0">
                    <td :colspan="faqsFields.length + 1" class="px-4 py-8 text-center text-gray-500">
                      No FAQs found. Click "Add New FAQ" to get started.
                    </td>
                  </tr>
                  <tr v-else v-for="faq in faqs" :key="faq.id">
                    <td 
                      v-for="field in faqsFields" 
                      :key="field.id"
                      class="px-4 py-4 text-sm text-gray-900"
                    >
                      <template v-if="field.type === 'title'">
                        <div>
                          <div class="font-medium">{{ faq.title }}</div>
                          <div class="text-gray-500 text-xs">{{ faq.title_bn }}</div>
                        </div>
                      </template>
                      <template v-else-if="field.type === 'description'">
                        <div>
                          <div class="max-w-xs truncate">{{ faq.description }}</div>
                          <div class="text-gray-500 text-xs max-w-xs truncate">{{ faq.description_bn }}</div>
                        </div>
                      </template>
                      <template v-else-if="field.type === 'attachments'">
                        <span v-if="faq.attachments" class="text-indigo-600 hover:text-indigo-900 cursor-pointer text-xs">
                          View Attachment
                        </span>
                        <span v-else class="text-gray-400 text-xs">No attachment</span>
                      </template>
                      <template v-else-if="field.type === 'content'">
                        <div>
                          <div class="max-w-xs truncate">{{ faq.content }}</div>
                          <div class="text-gray-500 text-xs max-w-xs truncate">{{ faq.content_bn }}</div>
                        </div>
                      </template>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                      <button @click="editDocument(faq, 4)" class="text-indigo-600 hover:text-indigo-900 mr-3 text-xs">
                        Edit
                      </button>
                      <button @click="deleteDocument(faq, 4)" class="text-red-600 hover:text-red-900 text-xs">
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Contact Tab -->
          <div v-if="activeTab === 5" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
              <div class="flex space-x-3">
                <button
                  @click="openAddFieldModal(5)"
                  class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  Add New Field
                </button>
                <button
                  @click="openAddDocumentModal(5)"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  Add New Contact
                </button>
              </div>
            </div>

            <!-- Contact Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th 
                      v-for="field in contactsFields" 
                      :key="field.id"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      <div class="flex items-center">
                        {{ field.name }}
                        <button 
                          v-if="!field.isDefault"
                          @click="removeField(5, field.id)"
                          class="ml-2 text-red-500 hover:text-red-700 text-xs"
                        >
                          ×
                        </button>
                      </div>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="contacts.length === 0">
                    <td :colspan="contactsFields.length + 1" class="px-4 py-8 text-center text-gray-500">
                      No contact information found. Click "Add New Contact" to get started.
                    </td>
                  </tr>
                  <tr v-else v-for="contact in contacts" :key="contact.id">
                    <td 
                      v-for="field in contactsFields" 
                      :key="field.id"
                      class="px-4 py-4 text-sm text-gray-900"
                    >
                      <template v-if="field.type === 'title'">
                        <div>
                          <div class="font-medium">{{ contact.title }}</div>
                          <div class="text-gray-500 text-xs">{{ contact.title_bn }}</div>
                        </div>
                      </template>
                      <template v-else-if="field.type === 'description'">
                        <div>
                          <div class="max-w-xs truncate">{{ contact.description }}</div>
                          <div class="text-gray-500 text-xs max-w-xs truncate">{{ contact.description_bn }}</div>
                        </div>
                      </template>
                      <template v-else-if="field.type === 'attachments'">
                        <span v-if="contact.attachments" class="text-indigo-600 hover:text-indigo-900 cursor-pointer text-xs">
                          View Attachment
                        </span>
                        <span v-else class="text-gray-400 text-xs">No attachment</span>
                      </template>
                      <template v-else-if="field.type === 'content'">
                        <div>
                          <div class="max-w-xs truncate">{{ contact.content }}</div>
                          <div class="text-gray-500 text-xs max-w-xs truncate">{{ contact.content_bn }}</div>
                        </div>
                      </template>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                      <button @click="editDocument(contact, 5)" class="text-indigo-600 hover:text-indigo-900 mr-3 text-xs">
                        Edit
                      </button>
                      <button @click="deleteDocument(contact, 5)" class="text-red-600 hover:text-red-900 text-xs">
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

  <!-- Add Field Modal -->
  <div v-if="showFieldModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">
          Add New Field to {{ getSectionName(activeTab) }}
        </h3>
      </div>
      
      <form @submit.prevent="addNewField" class="p-6 space-y-4">
        <div>
          <label for="fieldType" class="block text-sm font-medium text-gray-700 mb-2">
            Field Type
          </label>
          <select
            id="fieldType"
            v-model="newField.type"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            required
          >
            <option value="">Select Field Type</option>
            <option value="description">Description</option>
            <option value="content">Content</option>
            <option value="attachments">Attachments</option>
          </select>
        </div>

        <div>
          <label for="fieldName" class="block text-sm font-medium text-gray-700 mb-2">
            Field Name
          </label>
          <input
            id="fieldName"
            v-model="newField.name"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="Enter field name"
            required
          >
        </div>

        <div class="flex justify-end space-x-3 pt-4">
          <button
            type="button"
            @click="closeFieldModal"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            Add Field
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add/Edit Document Modal -->
  <div v-if="showDocumentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">
          {{ editingDocument ? 'Edit' : 'Add New' }} {{ getSectionName(activeTab) }}
        </h3>
      </div>
      
      <form @submit.prevent="saveDocument" class="p-6 space-y-4">
        <!-- Title Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
              Title (English)
            </label>
            <input
              id="title"
              v-model="formData.title"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Enter title in English"
              required
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
              placeholder="Enter title in Bangla"
              required
            >
          </div>
        </div>

        <!-- Dynamic Fields based on available fields -->
        <div v-for="field in getCurrentFields()" :key="field.id">
          <div v-if="field.type === 'description'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description (English)
              </label>
              <textarea
                id="description"
                v-model="formData.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="Enter description in English"
              ></textarea>
            </div>
            <div>
              <label for="description_bn" class="block text-sm font-medium text-gray-700 mb-2">
                Description (Bangla)
              </label>
              <textarea
                id="description_bn"
                v-model="formData.description_bn"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="Enter description in Bangla"
              ></textarea>
            </div>
          </div>

          <div v-if="field.type === 'content'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                Content (English)
              </label>
              <textarea
                id="content"
                v-model="formData.content"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="Enter content in English"
              ></textarea>
            </div>
            <div>
              <label for="content_bn" class="block text-sm font-medium text-gray-700 mb-2">
                Content (Bangla)
              </label>
              <textarea
                id="content_bn"
                v-model="formData.content_bn"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="Enter content in Bangla"
              ></textarea>
            </div>
          </div>

          <div v-if="field.type === 'attachments'">
            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
              Attachments
            </label>
            <input
              id="attachments"
              type="file"
              @change="handleFileUpload"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
            <p class="text-xs text-gray-500 mt-1">Upload relevant files</p>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
          <button
            type="button"
            @click="closeDocumentModal"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            {{ editingDocument ? 'Update' : 'Save' }}
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
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import AdminSidebar from '../../../Layouts/AdminSidebar.vue'
import AdminNavbar from '../../../Layouts/AdminNavbar.vue'

// Reactive data
const activeTab = ref(1)
const showFieldModal = ref(false)
const showDocumentModal = ref(false)
const showSuccess = ref(false)
const successMessage = ref('')
const editingDocument = ref(null)

// Sections data
const sections = ref([
  { id: 1, section_name: 'Services' },
  { id: 2, section_name: 'Download' },
  { id: 3, section_name: 'News' },
  { id: 4, section_name: 'FAQ' },
  { id: 5, section_name: 'Contact' }
])

// Field configurations for each section
const servicesFields = ref([
  { id: 1, type: 'title', name: 'Title', isDefault: true },
  { id: 2, type: 'description', name: 'Attachments', isDefault: true },
  { id: 3, type: 'attachments', name: 'Attachments', isDefault: true }
])

const downloadsFields = ref([
  { id: 1, type: 'title', name: 'Title', isDefault: true },
  { id: 2, type: 'attachments', name: 'Attachments', isDefault: true }
])

const newsFields = ref([
  { id: 1, type: 'title', name: 'Title', isDefault: true },
  { id: 2, type: 'description', name: 'Description', isDefault: true },
  { id: 3, type: 'attachments', name: 'Attachments', isDefault: true },
  { id: 4, type: 'content', name: 'Content', isDefault: true }
])

const faqsFields = ref([
  { id: 1, type: 'title', name: 'Title', isDefault: true },
  { id: 2, type: 'content', name: 'Content', isDefault: true }
])

const contactsFields = ref([
  { id: 1, type: 'title', name: 'Title', isDefault: true },
  { id: 2, type: 'attachments', name: 'Attachments', isDefault: true },
  { id: 3, type: 'content', name: 'Content', isDefault: true }
])

// Sample data for each section
const services = ref([])
const downloads = ref([])
const news = ref([])
const faqs = ref([])
const contacts = ref([])

const formData = reactive({
  title: '',
  title_bn: '',
  description: '',
  description_bn: '',
  content: '',
  content_bn: '',
  attachments: null
})

const newField = reactive({
  type: '',
  name: ''
})

// Methods
const getSectionName = (sectionId) => {
  const section = sections.value.find(s => s.id === sectionId)
  return section ? section.section_name : ''
}

const getCurrentFields = () => {
  switch (activeTab.value) {
    case 1: return servicesFields.value
    case 2: return downloadsFields.value
    case 3: return newsFields.value
    case 4: return faqsFields.value
    case 5: return contactsFields.value
    default: return []
  }
}

const getCurrentData = () => {
  switch (activeTab.value) {
    case 1: return services.value
    case 2: return downloads.value
    case 3: return news.value
    case 4: return faqs.value
    case 5: return contacts.value
    default: return []
  }
}

const setCurrentData = (data) => {
  switch (activeTab.value) {
    case 1: services.value = data; break
    case 2: downloads.value = data; break
    case 3: news.value = data; break
    case 4: faqs.value = data; break
    case 5: contacts.value = data; break
  }
}

const openAddFieldModal = (sectionId) => {
  activeTab.value = sectionId
  showFieldModal.value = true
}

const openAddDocumentModal = (sectionId) => {
  activeTab.value = sectionId
  editingDocument.value = null
  resetFormData()
  showDocumentModal.value = true
}

const addNewField = () => {
  const currentFields = getCurrentFields()
  const newFieldId = Math.max(...currentFields.map(f => f.id), 0) + 1
  
  currentFields.push({
    id: newFieldId,
    type: newField.type,
    name: newField.name,
    isDefault: false
  })
  
  closeFieldModal()
  showSuccessMessage('Field added successfully')
}

const removeField = (sectionId, fieldId) => {
  if (confirm('Are you sure you want to remove this field?')) {
    let currentFields
    switch (sectionId) {
      case 1: 
        currentFields = servicesFields.value
        break
      case 2: 
        currentFields = downloadsFields.value
        break
      case 3: 
        currentFields = newsFields.value
        break
      case 4: 
        currentFields = faqsFields.value
        break
      case 5: 
        currentFields = contactsFields.value
        break
    }
    
    const fieldIndex = currentFields.findIndex(f => f.id === fieldId)
    if (fieldIndex !== -1 && !currentFields[fieldIndex].isDefault) {
      currentFields.splice(fieldIndex, 1)
      showSuccessMessage('Field removed successfully')
    }
  }
}

const editDocument = (document, sectionId) => {
  activeTab.value = sectionId
  editingDocument.value = document
  Object.assign(formData, document)
  showDocumentModal.value = true
}

const deleteDocument = (document, sectionId) => {
  if (confirm('Are you sure you want to delete this document?')) {
    const currentData = getCurrentData()
    const updatedData = currentData.filter(item => item.id !== document.id)
    setCurrentData(updatedData)
    showSuccessMessage('Document deleted successfully')
  }
}

const saveDocument = () => {
  const currentData = getCurrentData()
  
  if (editingDocument.value) {
    // Update existing document
    const index = currentData.findIndex(item => item.id === editingDocument.value.id)
    if (index !== -1) {
      currentData[index] = { ...editingDocument.value, ...formData }
    }
    showSuccessMessage('Document updated successfully')
  } else {
    // Add new document
    const newDocument = {
      id: Math.max(...currentData.map(item => item.id), 0) + 1,
      ...formData
    }
    currentData.push(newDocument)
    showSuccessMessage('Document added successfully')
  }
  
  setCurrentData(currentData)
  closeDocumentModal()
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    formData.attachments = file.name
  }
}

const closeFieldModal = () => {
  showFieldModal.value = false
  newField.type = ''
  newField.name = ''
}

const closeDocumentModal = () => {
  showDocumentModal.value = false
  editingDocument.value = null
  resetFormData()
}

const resetFormData = () => {
  Object.assign(formData, {
    title: '',
    title_bn: '',
    description: '',
    description_bn: '',
    content: '',
    content_bn: '',
    attachments: null
  })
}

const showSuccessMessage = (message) => {
  successMessage.value = message
  showSuccess.value = true
  setTimeout(() => {
    showSuccess.value = false
  }, 3000)
}

// Lifecycle
onMounted(() => {
  console.log('Document Type page mounted')
})
</script>

<style scoped>
/* Add any custom styles here if needed */
</style>

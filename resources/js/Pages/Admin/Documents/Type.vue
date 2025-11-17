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
              <h1 class="text-2xl font-bold text-gray-900">Document Types Management</h1>
              <p class="text-gray-600 mt-1">Manage document types and fields for different sections</p>
            </div>
            <button 
              @click="showAddTypeModal = true"
              class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Add New Type
            </button>
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
                v-for="type in mainTypes"
                :key="type.id"
                @click="setActiveMainTab(type.id)"
                :class="[
                  'px-6 py-4 text-sm font-medium border-b-2 transition-colors duration-200 whitespace-nowrap',
                  activeMainTab === type.id
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                {{ type.type_name }}
              </button>
            </nav>
          </div>
        </div>

        <!-- Tab Content -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <!-- Services Tab -->
          <div v-if="activeMainTab === 1" class="p-6">
            <!-- Services Sub Tabs -->
            <div class="flex space-x-2 mb-6">
              <button
                v-for="subType in getSubTypes(1)"
                :key="subType.id"
                @click="setActiveSubTab(subType.id)"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200',
                  activeSubTab === subType.id
                    ? 'bg-indigo-100 text-indigo-700 border border-indigo-300'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300'
                ]"
              >
                {{ subType.type_name }}
              </button>
            </div>

            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Fields for {{ getActiveSubTypeName() }}</h3>
              <button 
                @click="showAddFieldModal = true"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Field
              </button>
            </div>

            <!-- Services Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attachments</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="getFieldsForType(activeSubTab).length === 0">
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                      <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                      </svg>
                      No fields added yet
                    </td>
                  </tr>
                  <tr v-else v-for="field in getFieldsForType(activeSubTab)" :key="field.id">
                    <td class="px-4 py-4 text-sm text-gray-900">{{ field.title }}</td>
                    <td class="px-4 py-4 text-sm text-gray-900">{{ field.description || '-' }}</td>
                    <td class="px-4 py-4 text-sm">
                      <span :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        field.attachments ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      ]">
                        {{ field.attachments ? 'Yes' : 'No' }}
                      </span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                      <button class="text-indigo-600 hover:text-indigo-900 mr-3 text-xs">
                        Edit
                      </button>
                      <button class="text-red-600 hover:text-red-900 text-xs">
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Download Tab -->
          <div v-if="activeMainTab === 2" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Fields for Download</h3>
              <button 
                @click="showAddFieldModal = true"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Field
              </button>
            </div>

            <!-- Download Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attachments</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="getFieldsForType(2).length === 0">
                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                      <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                      </svg>
                      No fields added yet
                    </td>
                  </tr>
                  <tr v-else v-for="field in getFieldsForType(2)" :key="field.id">
                    <td class="px-4 py-4 text-sm text-gray-900">{{ field.title }}</td>
                    <td class="px-4 py-4 text-sm">
                      <span :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        field.attachments ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      ]">
                        {{ field.attachments ? 'Yes' : 'No' }}
                      </span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                      <button class="text-indigo-600 hover:text-indigo-900 mr-3 text-xs">
                        Edit
                      </button>
                      <button class="text-red-600 hover:text-red-900 text-xs">
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- News Tab -->
          <div v-if="activeMainTab === 3" class="p-6">
            <!-- News Sub Tabs -->
            <div class="flex space-x-2 mb-6">
              <button
                v-for="subType in getSubTypes(3)"
                :key="subType.id"
                @click="setActiveSubTab(subType.id)"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200',
                  activeSubTab === subType.id
                    ? 'bg-indigo-100 text-indigo-700 border border-indigo-300'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300'
                ]"
              >
                {{ subType.type_name }}
              </button>
            </div>

            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Fields for {{ getActiveSubTypeName() }}</h3>
              <button 
                @click="showAddFieldModal = true"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Field
              </button>
            </div>

            <!-- News Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attachments</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="getFieldsForType(activeSubTab).length === 0">
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                      <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                      </svg>
                      No fields added yet
                    </td>
                  </tr>
                  <tr v-else v-for="field in getFieldsForType(activeSubTab)" :key="field.id">
                    <td class="px-4 py-4 text-sm text-gray-900">{{ field.title }}</td>
                    <td class="px-4 py-4 text-sm text-gray-900">{{ field.description || '-' }}</td>
                    <td class="px-4 py-4 text-sm">
                      <span :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        field.attachments ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      ]">
                        {{ field.attachments ? 'Yes' : 'No' }}
                      </span>
                    </td>
                    <td class="px-4 py-4 text-sm">
                      <span :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        field.content ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      ]">
                        {{ field.content ? 'Yes' : 'No' }}
                      </span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                      <button class="text-indigo-600 hover:text-indigo-900 mr-3 text-xs">
                        Edit
                      </button>
                      <button class="text-red-600 hover:text-red-900 text-xs">
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- FAQ Tab -->
          <div v-if="activeMainTab === 4" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Fields for FAQ</h3>
              <button 
                @click="showAddFieldModal = true"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Field
              </button>
            </div>

            <!-- FAQ Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="getFieldsForType(4).length === 0">
                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                      <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                      </svg>
                      No fields added yet
                    </td>
                  </tr>
                  <tr v-else v-for="field in getFieldsForType(4)" :key="field.id">
                    <td class="px-4 py-4 text-sm text-gray-900">{{ field.title }}</td>
                    <td class="px-4 py-4 text-sm">
                      <span :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        field.content ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      ]">
                        {{ field.content ? 'Yes' : 'No' }}
                      </span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                      <button class="text-indigo-600 hover:text-indigo-900 mr-3 text-xs">
                        Edit
                      </button>
                      <button class="text-red-600 hover:text-red-900 text-xs">
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Contact Tab -->
          <div v-if="activeMainTab === 5" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Fields for Contact</h3>
              <button 
                @click="showAddFieldModal = true"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Field
              </button>
            </div>

            <!-- Contact Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attachments</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="getFieldsForType(5).length === 0">
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                      <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                      </svg>
                      No fields added yet
                    </td>
                  </tr>
                  <tr v-else v-for="field in getFieldsForType(5)" :key="field.id">
                    <td class="px-4 py-4 text-sm text-gray-900">{{ field.title }}</td>
                    <td class="px-4 py-4 text-sm">
                      <span :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        field.attachments ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      ]">
                        {{ field.attachments ? 'Yes' : 'No' }}
                      </span>
                    </td>
                    <td class="px-4 py-4 text-sm">
                      <span :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        field.content ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      ]">
                        {{ field.content ? 'Yes' : 'No' }}
                      </span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                      <button class="text-indigo-600 hover:text-indigo-900 mr-3 text-xs">
                        Edit
                      </button>
                      <button class="text-red-600 hover:text-red-900 text-xs">
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

  <!-- Add New Type Modal -->
  <div v-if="showAddTypeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Add New Document Type</h3>
      </div>
      
      <form @submit.prevent="addNewType" class="p-6 space-y-4">
        <div>
          <label for="typeName" class="block text-sm font-medium text-gray-700 mb-2">
            Type Name <span class="text-red-500">*</span>
          </label>
          <input
            id="typeName"
            v-model="newType.type_name"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="Enter type name"
            required
          >
        </div>

        <div>
          <label for="typeNameBn" class="block text-sm font-medium text-gray-700 mb-2">
            Type Name (Bangla)
          </label>
          <input
            id="typeNameBn"
            v-model="newType.type_name_bn"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="বাংলায় টাইপের নাম লিখুন"
          >
        </div>

        <div>
          <label for="parentType" class="block text-sm font-medium text-gray-700 mb-2">
            Parent Type
          </label>
          <select
            id="parentType"
            v-model="newType.parent_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">No Parent (Main Type)</option>
            <option v-for="type in mainTypes" :key="type.id" :value="type.id">
              {{ type.type_name }}
            </option>
          </select>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
          <button
            type="button"
            @click="showAddTypeModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="!newType.type_name"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Add Type
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Field Modal -->
  <div v-if="showAddFieldModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Add New Field</h3>
      </div>
      
      <form @submit.prevent="addNewField" class="p-6 space-y-4">
        <div>
          <label for="fieldType" class="block text-sm font-medium text-gray-700 mb-2">
            Field Type <span class="text-red-500">*</span>
          </label>
          <select
            id="fieldType"
            v-model="newField.field_type"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            required
          >
            <option value="title">Title</option>
            <option value="description">Description</option>
            <option value="attachments">Attachments</option>
            <option value="content">Content</option>
          </select>
        </div>

        <div>
          <label for="fieldLabel" class="block text-sm font-medium text-gray-700 mb-2">
            Field Label <span class="text-red-500">*</span>
          </label>
          <input
            id="fieldLabel"
            v-model="newField.label"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="Enter field label"
            required
          >
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
          <div class="flex items-center">
            <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm text-blue-700">
              This will add a new field to: <strong>{{ getCurrentTypeName() }}</strong>
            </span>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
          <button
            type="button"
            @click="showAddFieldModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="!newField.label"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Add Field
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
const showAddTypeModal = ref(false)
const showAddFieldModal = ref(false)
const showSuccess = ref(false)
const successMessage = ref('')
const activeMainTab = ref(1)
const activeSubTab = ref(6) // Start with Offline Services

// Sample data - in real app, this would come from API
const mainTypes = ref([
  { id: 1, type_name: 'Services', type_name_bn: 'সেবা', parent_id: null },
  { id: 2, type_name: 'Download', type_name_bn: 'ডাউনলোড', parent_id: null },
  { id: 3, type_name: 'News', type_name_bn: 'খবর', parent_id: null },
  { id: 4, type_name: 'FAQ', type_name_bn: 'প্রশ্নোত্তর', parent_id: null },
  { id: 5, type_name: 'Contact', type_name_bn: 'যোগাযোগ', parent_id: null }
])

const subTypes = ref([
  { id: 6, type_name: 'Offline Services', type_name_bn: 'অফলাইন সেবা', parent_id: 1 },
  { id: 7, type_name: 'Online Services', type_name_bn: 'অনলাইন সেবা', parent_id: 1 },
  { id: 8, type_name: 'SSL News', type_name_bn: 'এসএসএল খবর', parent_id: 3 },
  { id: 9, type_name: 'DSE News', type_name_bn: 'ডিএসই খবর', parent_id: 3 }
])

// Sample document fields data
const documentFields = ref({
  6: [ // Offline Services
    { id: 1, title: 'Branch Services', description: 'Services available at branches', attachments: true }
  ],
  7: [ // Online Services
    { id: 2, title: 'Online Portal', description: 'Digital service platform', attachments: false }
  ],
  2: [ // Download
    { id: 3, title: 'Application Form', attachments: true }
  ],
  8: [ // SSL News
    { id: 4, title: 'SSL Quarterly Report', description: 'Financial report', attachments: true, content: 'Detailed content here' }
  ],
  4: [ // FAQ
    { id: 5, title: 'Investment FAQ', content: 'Frequently asked questions about investments' }
  ],
  5: [ // Contact
    { id: 6, title: 'Head Office', attachments: true, content: 'Contact information for head office' }
  ]
})

const newType = reactive({
  type_name: '',
  type_name_bn: '',
  parent_id: ''
})

const newField = reactive({
  field_type: 'title',
  label: ''
})

// Computed properties
const getSubTypes = (parentId) => {
  return subTypes.value.filter(type => type.parent_id === parentId)
}

const getActiveSubTypeName = () => {
  const subType = subTypes.value.find(type => type.id === activeSubTab.value)
  return subType ? subType.type_name : ''
}

const getFieldsForType = (typeId) => {
  return documentFields.value[typeId] || []
}

const getCurrentTypeName = () => {
  if ([1, 2, 4, 5].includes(activeMainTab.value)) {
    const mainType = mainTypes.value.find(type => type.id === activeMainTab.value)
    return mainType ? mainType.type_name : ''
  } else {
    const subType = subTypes.value.find(type => type.id === activeSubTab.value)
    return subType ? subType.type_name : ''
  }
}

// Methods
const setActiveMainTab = (tabId) => {
  activeMainTab.value = tabId
  // Set default sub tab for types that have sub types
  if (tabId === 1) {
    activeSubTab.value = 6 // Offline Services
  } else if (tabId === 3) {
    activeSubTab.value = 8 // SSL News
  }
}

const setActiveSubTab = (tabId) => {
  activeSubTab.value = tabId
}

const addNewType = () => {
  // In real app, make API call here
  const newId = Math.max(...mainTypes.value.map(t => t.id), ...subTypes.value.map(t => t.id)) + 1
  
  if (newType.parent_id) {
    subTypes.value.push({
      id: newId,
      type_name: newType.type_name,
      type_name_bn: newType.type_name_bn,
      parent_id: parseInt(newType.parent_id)
    })
  } else {
    mainTypes.value.push({
      id: newId,
      type_name: newType.type_name,
      type_name_bn: newType.type_name_bn,
      parent_id: null
    })
  }
  
  // Reset form and close modal
  Object.assign(newType, { type_name: '', type_name_bn: '', parent_id: '' })
  showAddTypeModal.value = false
  showSuccessMessage('Document type added successfully')
}

const addNewField = () => {
  // In real app, make API call here
  const currentTypeId = [1, 2, 4, 5].includes(activeMainTab.value) ? activeMainTab.value : activeSubTab.value
  
  if (!documentFields.value[currentTypeId]) {
    documentFields.value[currentTypeId] = []
  }
  
  const newFieldId = Math.max(0, ...documentFields.value[currentTypeId].map(f => f.id)) + 1
  
  documentFields.value[currentTypeId].push({
    id: newFieldId,
    title: newField.label || `New ${newField.field_type}`,
    description: newField.field_type === 'description' ? 'Sample description' : undefined,
    attachments: newField.field_type === 'attachments',
    content: newField.field_type === 'content' ? 'Sample content' : undefined
  })
  
  // Reset form and close modal
  Object.assign(newField, { field_type: 'title', label: '' })
  showAddFieldModal.value = false
  showSuccessMessage('Field added successfully')
}

const showSuccessMessage = (message) => {
  successMessage.value = message
  showSuccess.value = true
  setTimeout(() => {
    showSuccess.value = false
  }, 3000)
}

// Initialize
onMounted(() => {
  console.log('Document Types page mounted')
})
</script>

<style scoped>
/* Custom styles if needed */
</style>
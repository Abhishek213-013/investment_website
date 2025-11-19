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
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>

        <!-- Tabs Navigation -->
        <!-- Tabs Navigation -->
        <div v-else class="bg-white rounded-lg shadow mb-6">
          <div class="border-b border-gray-200">
            <nav class="flex -mb-px overflow-x-auto">
              <button
                v-for="type in getSortedMainTypes()"
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
                <!-- Delete button for dynamic types -->
                <button 
                  v-if="isDynamicType(type.id)"
                  @click.stop="confirmDeleteType(type)"
                  class="ml-2 text-red-500 hover:text-red-700 transition-colors"
                  title="Delete Type"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </button>
            </nav>
          </div>
        </div>

        <!-- Tab Content -->
        <div v-if="!loading" class="bg-white rounded-lg shadow overflow-hidden">
          <!-- Services Tab -->
          <div v-if="activeMainTab === 1" class="p-6">
            <!-- Services Sub Tabs -->
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
                <!-- Delete button for subtypes -->
                <button 
                  v-if="subType.id > 9"
                  @click.stop="confirmDeleteSubType(subType)"
                  class="ml-2 text-red-500 hover:text-red-700 transition-colors"
                  title="Delete Subtype"
                >
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </button>
              <!-- Add Subtype Button -->
              <button
                @click="openAddSubTypeModal(1)"
                class="px-4 py-2 text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 transition-colors duration-200 flex items-center"
                title="Add New Subtype"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add
              </button>
            </div>

            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Fields for {{ getActiveSubTypeName() }}</h3>
              <div class="flex space-x-2">
                <button 
                  @click="showFieldConfigModal = true"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  Configure Fields
                </button>
                <button 
                  @click="copyConfigurationToAllServices()"
                  class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                  </svg>
                  Copy to All Services
                </button>
              </div>
            </div>

            <!-- Services Field Configuration -->
            <div class="bg-gray-50 rounded-lg p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div 
                  v-for="field in availableFields" 
                  :key="field.id"
                  class="bg-white rounded-lg border p-4 flex items-center justify-between transition-all duration-200 hover:shadow-md"
                  :class="isFieldEnabled(field.id) ? 'border-indigo-300 ring-1 ring-indigo-200' : 'border-gray-200'"
                >
                  <div>
                    <h4 class="font-medium text-gray-900">{{ field.name }}</h4>
                    <p class="text-sm text-gray-500 mt-1">{{ field.description }}</p>
                  </div>
                  <div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        :checked="isFieldEnabled(field.id)"
                        @change="toggleField(field.id, $event.target.checked)"
                        :disabled="savingFields.includes(field.id)"
                        class="sr-only peer"
                      >
                      <div 
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                        :class="savingFields.includes(field.id) ? 'opacity-50 cursor-not-allowed' : ''"
                      ></div>
                    </label>
                    <div v-if="savingFields.includes(field.id)" class="ml-2">
                      <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Current Configuration Summary -->
              <div class="mt-6 p-4 bg-white rounded-lg border border-gray-200">
                <h4 class="font-medium text-gray-900 mb-2">Current Field Configuration</h4>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="field in getEnabledFields()" 
                    :key="field.id"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                  >
                    {{ field.name }}
                  </span>
                  <span 
                    v-if="getEnabledFields().length === 0"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                  >
                    No fields enabled
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Download Tab -->
          <div v-if="activeMainTab === 2" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Fields for Download</h3>
              <button 
                @click="showFieldConfigModal = true"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Configure Fields
              </button>
            </div>

            <!-- Download Field Configuration -->
            <div class="bg-gray-50 rounded-lg p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div 
                  v-for="field in availableFields" 
                  :key="field.id"
                  class="bg-white rounded-lg border p-4 flex items-center justify-between transition-all duration-200 hover:shadow-md"
                  :class="isFieldEnabled(field.id) ? 'border-indigo-300 ring-1 ring-indigo-200' : 'border-gray-200'"
                >
                  <div>
                    <h4 class="font-medium text-gray-900">{{ field.name }}</h4>
                    <p class="text-sm text-gray-500 mt-1">{{ field.description }}</p>
                  </div>
                  <div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        :checked="isFieldEnabled(field.id)"
                        @change="toggleField(field.id, $event.target.checked)"
                        :disabled="savingFields.includes(field.id)"
                        class="sr-only peer"
                      >
                      <div 
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                        :class="savingFields.includes(field.id) ? 'opacity-50 cursor-not-allowed' : ''"
                      ></div>
                    </label>
                    <div v-if="savingFields.includes(field.id)" class="ml-2">
                      <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Current Configuration Summary -->
              <div class="mt-6 p-4 bg-white rounded-lg border border-gray-200">
                <h4 class="font-medium text-gray-900 mb-2">Current Field Configuration</h4>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="field in getEnabledFields()" 
                    :key="field.id"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                  >
                    {{ field.name }}
                  </span>
                  <span 
                    v-if="getEnabledFields().length === 0"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                  >
                    No fields enabled
                  </span>
                </div>
              </div>
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
                <!-- Delete button for subtypes -->
                <button 
                  v-if="subType.id > 9"
                  @click.stop="confirmDeleteSubType(subType)"
                  class="ml-2 text-red-500 hover:text-red-700 transition-colors"
                  title="Delete Subtype"
                >
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </button>
              <!-- Add Subtype Button -->
              <button
                @click="openAddSubTypeModal(3)"
                class="px-4 py-2 text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 transition-colors duration-200 flex items-center"
                title="Add New Subtype"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add
              </button>
            </div>

            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Fields for {{ getActiveSubTypeName() }}</h3>
              <div class="flex space-x-2">
                <button 
                  @click="showFieldConfigModal = true"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  Configure Fields
                </button>
                <button 
                  @click="copyConfigurationToAllNews()"
                  class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                  </svg>
                  Copy to All News
                </button>
              </div>
            </div>

            <!-- News Field Configuration -->
            <div class="bg-gray-50 rounded-lg p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div 
                  v-for="field in availableFields" 
                  :key="field.id"
                  class="bg-white rounded-lg border p-4 flex items-center justify-between transition-all duration-200 hover:shadow-md"
                  :class="isFieldEnabled(field.id) ? 'border-indigo-300 ring-1 ring-indigo-200' : 'border-gray-200'"
                >
                  <div>
                    <h4 class="font-medium text-gray-900">{{ field.name }}</h4>
                    <p class="text-sm text-gray-500 mt-1">{{ field.description }}</p>
                  </div>
                  <div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        :checked="isFieldEnabled(field.id)"
                        @change="toggleField(field.id, $event.target.checked)"
                        :disabled="savingFields.includes(field.id)"
                        class="sr-only peer"
                      >
                      <div 
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                        :class="savingFields.includes(field.id) ? 'opacity-50 cursor-not-allowed' : ''"
                      ></div>
                    </label>
                    <div v-if="savingFields.includes(field.id)" class="ml-2">
                      <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Current Configuration Summary -->
              <div class="mt-6 p-4 bg-white rounded-lg border border-gray-200">
                <h4 class="font-medium text-gray-900 mb-2">Current Field Configuration</h4>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="field in getEnabledFields()" 
                    :key="field.id"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                  >
                    {{ field.name }}
                  </span>
                  <span 
                    v-if="getEnabledFields().length === 0"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                  >
                    No fields enabled
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- FAQ Tab -->
          <div v-if="activeMainTab === 4" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Fields for FAQ</h3>
              <button 
                @click="showFieldConfigModal = true"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Configure Fields
              </button>
            </div>

            <!-- FAQ Field Configuration -->
            <div class="bg-gray-50 rounded-lg p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div 
                  v-for="field in availableFields" 
                  :key="field.id"
                  class="bg-white rounded-lg border p-4 flex items-center justify-between transition-all duration-200 hover:shadow-md"
                  :class="isFieldEnabled(field.id) ? 'border-indigo-300 ring-1 ring-indigo-200' : 'border-gray-200'"
                >
                  <div>
                    <h4 class="font-medium text-gray-900">{{ field.name }}</h4>
                    <p class="text-sm text-gray-500 mt-1">{{ field.description }}</p>
                  </div>
                  <div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        :checked="isFieldEnabled(field.id)"
                        @change="toggleField(field.id, $event.target.checked)"
                        :disabled="savingFields.includes(field.id)"
                        class="sr-only peer"
                      >
                      <div 
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                        :class="savingFields.includes(field.id) ? 'opacity-50 cursor-not-allowed' : ''"
                      ></div>
                    </label>
                    <div v-if="savingFields.includes(field.id)" class="ml-2">
                      <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Current Configuration Summary -->
              <div class="mt-6 p-4 bg-white rounded-lg border border-gray-200">
                <h4 class="font-medium text-gray-900 mb-2">Current Field Configuration</h4>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="field in getEnabledFields()" 
                    :key="field.id"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                  >
                    {{ field.name }}
                  </span>
                  <span 
                    v-if="getEnabledFields().length === 0"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                  >
                    No fields enabled
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Contact Tab -->
          <div v-if="activeMainTab === 5" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Fields for Contact</h3>
              <button 
                @click="showFieldConfigModal = true"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Configure Fields
              </button>
            </div>

            <!-- Contact Field Configuration -->
            <div class="bg-gray-50 rounded-lg p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div 
                  v-for="field in availableFields" 
                  :key="field.id"
                  class="bg-white rounded-lg border p-4 flex items-center justify-between transition-all duration-200 hover:shadow-md"
                  :class="isFieldEnabled(field.id) ? 'border-indigo-300 ring-1 ring-indigo-200' : 'border-gray-200'"
                >
                  <div>
                    <h4 class="font-medium text-gray-900">{{ field.name }}</h4>
                    <p class="text-sm text-gray-500 mt-1">{{ field.description }}</p>
                  </div>
                  <div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        :checked="isFieldEnabled(field.id)"
                        @change="toggleField(field.id, $event.target.checked)"
                        :disabled="savingFields.includes(field.id)"
                        class="sr-only peer"
                      >
                      <div 
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                        :class="savingFields.includes(field.id) ? 'opacity-50 cursor-not-allowed' : ''"
                      ></div>
                    </label>
                    <div v-if="savingFields.includes(field.id)" class="ml-2">
                      <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Current Configuration Summary -->
              <div class="mt-6 p-4 bg-white rounded-lg border border-gray-200">
                <h4 class="font-medium text-gray-900 mb-2">Current Field Configuration</h4>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="field in getEnabledFields()" 
                    :key="field.id"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                  >
                    {{ field.name }}
                  </span>
                  <span 
                    v-if="getEnabledFields().length === 0"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                  >
                    No fields enabled
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Dynamic Tabs for Newly Added Types -->
          <template v-for="type in getDynamicMainTabs()" :key="type.id">
            <div 
              v-if="activeMainTab === type.id" 
              class="p-6"
            >
              <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Fields for {{ type.type_name }}</h3>
                <button 
                  @click="showFieldConfigModal = true"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  Configure Fields
                </button>
              </div>

              <!-- Field Configuration for Dynamic Types -->
              <div class="bg-gray-50 rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                  <div 
                    v-for="field in availableFields" 
                    :key="field.id"
                    class="bg-white rounded-lg border p-4 flex items-center justify-between transition-all duration-200 hover:shadow-md"
                    :class="isFieldEnabled(field.id) ? 'border-indigo-300 ring-1 ring-indigo-200' : 'border-gray-200'"
                  >
                    <div>
                      <h4 class="font-medium text-gray-900">{{ field.name }}</h4>
                      <p class="text-sm text-gray-500 mt-1">{{ field.description }}</p>
                    </div>
                    <div class="flex items-center">
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input
                          type="checkbox"
                          :checked="isFieldEnabled(field.id)"
                          @change="toggleField(field.id, $event.target.checked)"
                          :disabled="savingFields.includes(field.id)"
                          class="sr-only peer"
                        >
                        <div 
                          class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                          :class="savingFields.includes(field.id) ? 'opacity-50 cursor-not-allowed' : ''"
                        ></div>
                      </label>
                      <div v-if="savingFields.includes(field.id)" class="ml-2">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Current Configuration Summary -->
                <div class="mt-6 p-4 bg-white rounded-lg border border-gray-200">
                  <h4 class="font-medium text-gray-900 mb-2">Current Field Configuration</h4>
                  <div class="flex flex-wrap gap-2">
                    <span 
                      v-for="field in getEnabledFields()" 
                      :key="field.id"
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                    >
                      {{ field.name }}
                    </span>
                    <span 
                      v-if="getEnabledFields().length === 0"
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                    >
                      No fields enabled
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </template>
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
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
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
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
            placeholder="বাংলায় টাইপের নাম লিখুন"
          >
        </div>

        <div class="flex justify-end space-x-3 pt-4">
          <button
            type="button"
            @click="showAddTypeModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="!newType.type_name || addingType"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
          >
            <span v-if="addingType" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
            {{ addingType ? 'Adding...' : 'Add Type' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add New SubType Modal -->
  <div v-if="showAddSubTypeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Add New Subtype for {{ getMainTypeName(parentTypeIdForSubType) }}</h3>
      </div>
      
      <form @submit.prevent="addNewSubType" class="p-6 space-y-4">
        <div>
          <label for="subTypeName" class="block text-sm font-medium text-gray-700 mb-2">
            Subtype Name <span class="text-red-500">*</span>
          </label>
          <input
            id="subTypeName"
            v-model="newSubType.type_name"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
            placeholder="Enter subtype name"
            required
          >
        </div>

        <div>
          <label for="subTypeNameBn" class="block text-sm font-medium text-gray-700 mb-2">
            Subtype Name (Bangla)
          </label>
          <input
            id="subTypeNameBn"
            v-model="newSubType.type_name_bn"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
            placeholder="বাংলায় সাবটাইপের নাম লিখুন"
          >
        </div>

        <div class="flex justify-end space-x-3 pt-4">
          <button
            type="button"
            @click="showAddSubTypeModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="!newSubType.type_name || addingSubType"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
          >
            <span v-if="addingSubType" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
            {{ addingSubType ? 'Adding...' : 'Add Subtype' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Delete Document Type</h3>
      </div>
      
      <div class="p-6">
        <p class="text-gray-700 mb-4">
          Are you sure you want to delete the type "<strong>{{ typeToDelete?.type_name }}</strong>"?
        </p>
        <p class="text-sm text-red-600 mb-4">
          This action cannot be undone. All documents and field configurations for this type will be permanently deleted.
        </p>
        
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="showDeleteModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="deleteType"
            :disabled="deletingType"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
          >
            <span v-if="deletingType" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
            {{ deletingType ? 'Deleting...' : 'Delete Type' }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete SubType Confirmation Modal -->
  <div v-if="showDeleteSubTypeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Delete Subtype</h3>
      </div>
      
      <div class="p-6">
        <p class="text-gray-700 mb-4">
          Are you sure you want to delete the subtype "<strong>{{ subTypeToDelete?.type_name }}</strong>"?
        </p>
        <p class="text-sm text-red-600 mb-4">
          This action cannot be undone. All documents and field configurations for this subtype will be permanently deleted.
        </p>
        
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="showDeleteSubTypeModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="deleteSubType"
            :disabled="deletingSubType"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
          >
            <span v-if="deletingSubType" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
            {{ deletingSubType ? 'Deleting...' : 'Delete Subtype' }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Field Configuration Modal -->
  <div v-if="showFieldConfigModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Configure Fields for {{ getCurrentTypeName() }}</h3>
      </div>
      
      <div class="p-6 overflow-y-auto max-h-[60vh]">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div 
            v-for="field in availableFields" 
            :key="field.id"
            class="bg-gray-50 rounded-lg border p-4 flex items-center justify-between transition-all duration-200 hover:shadow-md"
            :class="isFieldEnabled(field.id) ? 'border-indigo-300 ring-1 ring-indigo-200' : 'border-gray-200'"
          >
            <div>
              <h4 class="font-medium text-gray-900">{{ field.name }}</h4>
              <p class="text-sm text-gray-500 mt-1">{{ field.description }}</p>
            </div>
            <div class="flex items-center">
              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  :checked="isFieldEnabled(field.id)"
                  @change="toggleField(field.id, $event.target.checked)"
                  :disabled="savingFields.includes(field.id)"
                  class="sr-only peer"
                >
                <div 
                  class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                  :class="savingFields.includes(field.id) ? 'opacity-50 cursor-not-allowed' : ''"
                ></div>
              </label>
              <div v-if="savingFields.includes(field.id)" class="ml-2">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Current Configuration Summary in Modal -->
        <div class="mt-6 p-4 bg-white rounded-lg border border-gray-200">
          <h4 class="font-medium text-gray-900 mb-2">Current Field Configuration</h4>
          <div class="flex flex-wrap gap-2">
            <span 
              v-for="field in getEnabledFields()" 
              :key="field.id"
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
            >
              {{ field.name }}
            </span>
            <span 
              v-if="getEnabledFields().length === 0"
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
            >
              No fields enabled
            </span>
          </div>
        </div>
      </div>
      
      <div class="flex justify-end space-x-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
        <button
          type="button"
          @click="showFieldConfigModal = false"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
        >
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- Success Notification -->
  <div
    v-if="showSuccess"
    class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 flex items-center"
  >
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    {{ successMessage }}
  </div>

  <!-- Error Notification -->
  <div
    v-if="showError"
    class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 flex items-center"
  >
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
    {{ errorMessage }}
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import AdminSidebar from '../../../Layouts/AdminSidebar.vue'
import AdminNavbar from '../../../Layouts/AdminNavbar.vue'

const router = useRouter()

// Reactive data
const showAddTypeModal = ref(false)
const showDeleteModal = ref(false)
const showDeleteSubTypeModal = ref(false)
const showFieldConfigModal = ref(false)
const showSuccess = ref(false)
const showError = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const activeMainTab = ref(1)
const activeSubTab = ref(6)
const loading = ref(true)
const savingFields = ref([])
const showAddSubTypeModal = ref(false)
const addingType = ref(false)
const addingSubType = ref(false)
const deletingType = ref(false)
const deletingSubType = ref(false)
const typeToDelete = ref(null)
const subTypeToDelete = ref(null)
const parentTypeIdForSubType = ref(null)

// Data from API
const mainTypes = ref([])
const subTypes = ref([])
const availableFields = ref([])
const fieldConfigurations = ref({})

const newType = reactive({
  type_name: '',
  type_name_bn: '',
  parent_id: null
})

const newSubType = reactive({
  type_name: '',
  type_name_bn: '',
  parent_id: null
})

// Fixed type IDs for core types (cannot be deleted)
const CORE_TYPE_IDS = [1, 2, 3, 4, 5] // Services, Download, News, FAQ, Contact

// API Service Functions
const apiService = {
  // Fetch all document types
  async fetchDocumentTypes() {
    try {
      const response = await fetch('/admin/documents/types')
      
      // Check if response is OK and is JSON
      if (!response.ok) {
        console.error('API Response not OK:', response.status, response.statusText)
        throw new Error(`Failed to fetch document types: ${response.status} ${response.statusText}`)
      }
      
      // Check if response is JSON
      const contentType = response.headers.get('content-type')
      if (!contentType || !contentType.includes('application/json')) {
        console.error('Response is not JSON:', contentType)
        throw new Error('API returned non-JSON response')
      }
      
      const data = await response.json()
      console.log('API Response:', data) // Debug log
      
      // Handle different response structures
      let allTypes = []
      
      if (data.types && Array.isArray(data.types)) {
        // If response has 'types' array
        allTypes = data.types
      } else if (Array.isArray(data)) {
        // If response is directly an array
        allTypes = data
      } else if (data.mainTypes && Array.isArray(data.mainTypes)) {
        // If response has mainTypes and subTypes separately
        allTypes = [...(data.mainTypes || []), ...(data.subTypes || [])]
      } else {
        console.error('Unexpected API response structure:', data)
        throw new Error('Unexpected API response structure')
      }
      
      return {
        mainTypes: allTypes.filter(type => !type.parent_id),
        subTypes: allTypes.filter(type => type.parent_id),
        availableFields: [
          { id: 1, name: 'Title', description: 'Document title field', type: 'title' },
          { id: 2, name: 'Description', description: 'Document description field', type: 'description' },
          { id: 3, name: 'Attachments', description: 'File attachments field', type: 'attachments' },
          { id: 4, name: 'Content', description: 'Rich text content field', type: 'content' }
        ],
        fieldConfigurations: {}
      }
    } catch (error) {
      console.error('Error fetching document types:', error)
      
      // Return comprehensive fallback data
      return {
        mainTypes: [
          { id: 1, type_name: 'Services', type_name_bn: 'সেবা', parent_id: null },
          { id: 2, type_name: 'Download', type_name_bn: 'ডাউনলোড', parent_id: null },
          { id: 3, type_name: 'News', type_name_bn: 'খবর', parent_id: null },
          { id: 4, type_name: 'FAQ', type_name_bn: 'প্রশ্নোত্তর', parent_id: null },
          { id: 5, type_name: 'Contact', type_name_bn: 'যোগাযোগ', parent_id: null }
        ],
        subTypes: [
          { id: 6, type_name: 'Offline Services', type_name_bn: 'অফলাইন সেবা', parent_id: 1 },
          { id: 7, type_name: 'Online Services', type_name_bn: 'অনলাইন সেবা', parent_id: 1 },
          { id: 8, type_name: 'SSL News', type_name_bn: 'এসএসএল খবর', parent_id: 3 },
          { id: 9, type_name: 'DSE News', type_name_bn: 'ডিএসই খবর', parent_id: 3 }
        ],
        availableFields: [
          { id: 1, name: 'Title', description: 'Document title field', type: 'title' },
          { id: 2, name: 'Description', description: 'Document description field', type: 'description' },
          { id: 3, name: 'Attachments', description: 'File attachments field', type: 'attachments' },
          { id: 4, name: 'Content', description: 'Rich text content field', type: 'content' }
        ],
        fieldConfigurations: {}
      }
    }
  },

  // Create new document type
  async createDocumentType(typeData) {
    try {
      console.log('Creating document type with data:', typeData);
      
      const response = await fetch('/admin/documents/types', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(typeData)
      });
      
      console.log('Response status:', response.status);
      console.log('Response ok:', response.ok);
      
      const responseText = await response.text();
      console.log('Raw response:', responseText);
      
      let responseData;
      try {
        responseData = JSON.parse(responseText);
      } catch (e) {
        console.error('Failed to parse JSON response:', e);
        throw new Error(`Server returned invalid JSON: ${responseText.substring(0, 100)}`);
      }
      
      if (!response.ok) {
        console.error('API Error Response:', responseData);
        throw new Error(responseData.error || responseData.message || `HTTP ${response.status}: ${response.statusText}`);
      }
      
      console.log('Success response:', responseData);
      return responseData;
      
    } catch (error) {
      console.error('Error creating document type:', error);
      throw error;
    }
  },

  // Delete document type
  async deleteDocumentType(typeId) {
    try {
      const response = await fetch(`/admin/documents/types/${typeId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      
      const responseText = await response.text();
      let responseData;
      try {
        responseData = JSON.parse(responseText);
      } catch (e) {
        throw new Error(`Server returned invalid JSON: ${responseText.substring(0, 100)}`);
      }
      
      if (!response.ok) {
        throw new Error(responseData.error || responseData.message || 'Failed to delete document type');
      }
      
      return responseData;
    } catch (error) {
      console.error('Error deleting document type:', error);
      throw error;
    }
  },

  // Fetch field configurations for a specific document type
  async fetchFieldConfigurations(documentTypeId) {
    try {
      const response = await fetch(`/admin/documents/field-config/${documentTypeId}`)
      
      if (!response.ok) {
        // Return empty array if endpoint doesn't work
        return { enabledFields: [] }
      }
      
      return await response.json()
    } catch (error) {
      console.error('Error fetching field configurations:', error)
      return { enabledFields: [] }
    }
  },

  // Update field configuration
  async updateFieldConfiguration(documentTypeId, fieldId, enabled) {
    try {
      const response = await fetch(`/admin/documents/field-config/${documentTypeId}/${fieldId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ enabled })
      })
      
      if (!response.ok) throw new Error('Failed to update field configuration')
      return await response.json()
    } catch (error) {
      console.error('Error updating field configuration:', error)
      throw error
    }
  },

  // Copy field configuration to multiple types
  async copyFieldConfiguration(sourceTypeId, targetTypeIds, fieldConfig) {
    try {
      const response = await fetch('/admin/documents/field-config/copy', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          source_type_id: sourceTypeId,
          target_type_ids: targetTypeIds,
          field_config: fieldConfig
        })
      })
      
      if (!response.ok) throw new Error('Failed to copy field configuration')
      return await response.json()
    } catch (error) {
      console.error('Error copying field configuration:', error)
      throw error
    }
  }
}

// Computed properties
const getSortedMainTypes = () => {
  if (!mainTypes.value || !Array.isArray(mainTypes.value)) {
    return []
  }
  
  // Sort main types: core types first in fixed order, then dynamic types
  const coreTypes = mainTypes.value.filter(type => CORE_TYPE_IDS.includes(type.id))
    .sort((a, b) => CORE_TYPE_IDS.indexOf(a.id) - CORE_TYPE_IDS.indexOf(b.id))
  
  const dynamicTypes = mainTypes.value.filter(type => !CORE_TYPE_IDS.includes(type.id))
    .sort((a, b) => a.type_name.localeCompare(b.type_name))
  
  return [...coreTypes, ...dynamicTypes]
}

// Check if a type is dynamic (can be deleted)
const isDynamicType = (typeId) => {
  return !CORE_TYPE_IDS.includes(typeId)
}

const getSubTypes = (parentId) => {
  if (!subTypes.value || !Array.isArray(subTypes.value)) {
    return []
  }
  return subTypes.value.filter(type => type && type.parent_id === parentId)
}

const getActiveSubTypeName = () => {
  if (!subTypes.value || !Array.isArray(subTypes.value)) {
    return ''
  }
  const subType = subTypes.value.find(type => type && type.id === activeSubTab.value)
  return subType ? subType.type_name : ''
}

const getMainTypeName = (typeId) => {
  if (!mainTypes.value || !Array.isArray(mainTypes.value)) {
    return ''
  }
  const mainType = mainTypes.value.find(type => type && type.id === typeId)
  return mainType ? mainType.type_name : ''
}

const getCurrentTypeName = () => {
  if ([1, 2, 4, 5].includes(activeMainTab.value)) {
    const mainType = mainTypes.value.find(type => type && type.id === activeMainTab.value)
    return mainType ? mainType.type_name : ''
  } else {
    const subType = subTypes.value.find(type => type && type.id === activeSubTab.value)
    return subType ? subType.type_name : ''
  }
}

// Get dynamic main tabs (types added after the initial ones)
const getDynamicMainTabs = () => {
  if (!mainTypes.value || !Array.isArray(mainTypes.value)) {
    return []
  }
  return mainTypes.value.filter(type => type && !CORE_TYPE_IDS.includes(type.id))
}

// Get current type ID (either main tab or sub tab)
const currentTypeId = computed(() => {
  if (!activeMainTab.value) return null
  
  if (activeMainTab.value === 1 || activeMainTab.value === 3) {
    return activeSubTab.value
  }
  return activeMainTab.value
})

// Check if a field is enabled for the current type
const isFieldEnabled = (fieldId) => {
  const typeId = currentTypeId.value
  if (!typeId) return false
  
  const config = fieldConfigurations.value[typeId]
  return config ? config.includes(fieldId) : false
}

// Get list of enabled fields for current type
const getEnabledFields = () => {
  const typeId = currentTypeId.value
  if (!typeId) return []
  
  const config = fieldConfigurations.value[typeId]
  if (!config || config.length === 0) return []
  
  return availableFields.value.filter(field => config.includes(field.id))
}

// Methods
const setActiveMainTab = (tabId) => {
  activeMainTab.value = tabId
  if (tabId === 1) {
    activeSubTab.value = 6
  } else if (tabId === 3) {
    activeSubTab.value = 8
  }
}

const setActiveSubTab = (tabId) => {
  activeSubTab.value = tabId
}

const openAddSubTypeModal = (parentTypeId) => {
  parentTypeIdForSubType.value = parentTypeId
  newSubType.parent_id = parentTypeId
  newSubType.type_name = ''
  newSubType.type_name_bn = ''
  showAddSubTypeModal.value = true
}

const confirmDeleteType = (type) => {
  if (!isDynamicType(type.id)) {
    showErrorMessage('Core document types cannot be deleted.')
    return
  }
  
  typeToDelete.value = type
  showDeleteModal.value = true
}

const confirmDeleteSubType = (subType) => {
  subTypeToDelete.value = subType
  showDeleteSubTypeModal.value = true
}

const deleteType = async () => {
  if (!typeToDelete.value) return
  
  deletingType.value = true
  
  try {
    await apiService.deleteDocumentType(typeToDelete.value.id)
    
    // Remove from local state
    mainTypes.value = mainTypes.value.filter(type => type.id !== typeToDelete.value.id)
    
    // Remove field configurations
    delete fieldConfigurations.value[typeToDelete.value.id]
    
    // Switch to first tab if current tab is being deleted
    if (activeMainTab.value === typeToDelete.value.id) {
      const sortedTypes = getSortedMainTypes()
      if (sortedTypes.length > 0) {
        setActiveMainTab(sortedTypes[0].id)
      }
    }
    
    showDeleteModal.value = false
    typeToDelete.value = null
    
    showSuccessMessage('Document type deleted successfully')
    
  } catch (error) {
    showErrorMessage(error.message || 'Failed to delete document type')
    console.error('Error deleting document type:', error)
  } finally {
    deletingType.value = false
  }
}

const deleteSubType = async () => {
  if (!subTypeToDelete.value) return
  
  deletingSubType.value = true
  
  try {
    await apiService.deleteDocumentType(subTypeToDelete.value.id)
    
    // Remove from local state
    subTypes.value = subTypes.value.filter(type => type.id !== subTypeToDelete.value.id)
    
    // Remove field configurations
    delete fieldConfigurations.value[subTypeToDelete.value.id]
    
    // Switch to first subtype if current subtype is being deleted
    if (activeSubTab.value === subTypeToDelete.value.id) {
      const parentSubTypes = getSubTypes(subTypeToDelete.value.parent_id)
      if (parentSubTypes.length > 0) {
        setActiveSubTab(parentSubTypes[0].id)
      }
    }
    
    showDeleteSubTypeModal.value = false
    subTypeToDelete.value = null
    
    showSuccessMessage('Subtype deleted successfully')
    
  } catch (error) {
    showErrorMessage(error.message || 'Failed to delete subtype')
    console.error('Error deleting subtype:', error)
  } finally {
    deletingSubType.value = false
  }
}

const addNewType = async () => {
  addingType.value = true
  
  try {
    // Prepare data for API
    const typeData = {
      type_name: newType.type_name,
      type_name_bn: newType.type_name_bn || '',
      parent_id: null // Always null for main types
    }

    console.log('Sending type data:', typeData)

    // Send request to create new type
    const response = await apiService.createDocumentType(typeData)
    
    console.log('API Response:', response)
    
    // Add the new type to mainTypes
    const newTypeData = response.type
    mainTypes.value.push(newTypeData)
    
    // Initialize field configuration for the new type
    fieldConfigurations.value[newTypeData.id] = []
    
    // Reset form and close modal
    Object.assign(newType, { type_name: '', type_name_bn: '', parent_id: null })
    showAddTypeModal.value = false
    
    // Switch to the new tab
    setActiveMainTab(newTypeData.id)
    
    showSuccessMessage('Document type added successfully')
    
  } catch (error) {
    console.error('Detailed error adding document type:', error)
    showErrorMessage(error.message || 'Failed to add document type. Please check console for details.')
  } finally {
    addingType.value = false
  }
}

const addNewSubType = async () => {
  addingSubType.value = true
  
  try {
    // Prepare data for API
    const typeData = {
      type_name: newSubType.type_name,
      type_name_bn: newSubType.type_name_bn || '',
      parent_id: newSubType.parent_id
    }

    console.log('Sending subtype data:', typeData)

    // Send request to create new subtype
    const response = await apiService.createDocumentType(typeData)
    
    console.log('API Response:', response)
    
    // Add the new subtype to subTypes
    const newSubTypeData = response.type
    subTypes.value.push(newSubTypeData)
    
    // Initialize field configuration for the new subtype
    fieldConfigurations.value[newSubTypeData.id] = []
    
    // Reset form and close modal
    Object.assign(newSubType, { type_name: '', type_name_bn: '', parent_id: null })
    showAddSubTypeModal.value = false
    
    // Switch to the new subtype
    setActiveSubTab(newSubTypeData.id)
    
    showSuccessMessage('Subtype added successfully')
    
  } catch (error) {
    console.error('Detailed error adding subtype:', error)
    showErrorMessage(error.message || 'Failed to add subtype. Please check console for details.')
  } finally {
    addingSubType.value = false
  }
}

const toggleField = async (fieldId, enabled) => {
  const typeId = currentTypeId.value
  if (!typeId) return
  
  // Add to saving queue
  savingFields.value.push(fieldId)
  
  try {
    // Update local state immediately for better UX
    if (!fieldConfigurations.value[typeId]) {
      fieldConfigurations.value[typeId] = []
    }
    
    if (enabled) {
      if (!fieldConfigurations.value[typeId].includes(fieldId)) {
        fieldConfigurations.value[typeId].push(fieldId)
      }
    } else {
      fieldConfigurations.value[typeId] = fieldConfigurations.value[typeId].filter(id => id !== fieldId)
    }
    
    // Save to database
    await apiService.updateFieldConfiguration(typeId, fieldId, enabled)
    
    showSuccessMessage(`Field ${enabled ? 'enabled' : 'disabled'} successfully`)
    
  } catch (error) {
    // Revert local state on error
    if (enabled) {
      fieldConfigurations.value[typeId] = fieldConfigurations.value[typeId].filter(id => id !== fieldId)
    } else {
      if (!fieldConfigurations.value[typeId].includes(fieldId)) {
        fieldConfigurations.value[typeId].push(fieldId)
      }
    }
    
    showErrorMessage('Failed to update field configuration')
    console.error('Error toggling field:', error)
  } finally {
    // Remove from saving queue
    savingFields.value = savingFields.value.filter(id => id !== fieldId)
  }
}

const copyConfigurationToAllServices = async () => {
  const typeId = currentTypeId.value
  if (!typeId) return
  
  const currentConfig = fieldConfigurations.value[typeId] || []
  const serviceSubTypes = getSubTypes(1)
  const targetTypeIds = serviceSubTypes.map(st => st.id)
  
  try {
    await apiService.copyFieldConfiguration(typeId, targetTypeIds, currentConfig)
    
    // Update local state
    serviceSubTypes.forEach(subType => {
      fieldConfigurations.value[subType.id] = [...currentConfig]
    })
    
    showSuccessMessage('Configuration copied to all Service types')
  } catch (error) {
    showErrorMessage('Failed to copy configuration to all services')
    console.error('Error copying configuration:', error)
  }
}

const copyConfigurationToAllNews = async () => {
  const typeId = currentTypeId.value
  if (!typeId) return
  
  const currentConfig = fieldConfigurations.value[typeId] || []
  const newsSubTypes = getSubTypes(3)
  const targetTypeIds = newsSubTypes.map(st => st.id)
  
  try {
    await apiService.copyFieldConfiguration(typeId, targetTypeIds, currentConfig)
    
    // Update local state
    newsSubTypes.forEach(subType => {
      fieldConfigurations.value[subType.id] = [...currentConfig]
    })
    
    showSuccessMessage('Configuration copied to all News types')
  } catch (error) {
    showErrorMessage('Failed to copy configuration to all news')
    console.error('Error copying configuration:', error)
  }
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

// Load initial data
const loadInitialData = async () => {
  loading.value = true
  try {
    const data = await apiService.fetchDocumentTypes()
    console.log('Loaded data:', data) // Debug log
    
    // Ensure we have arrays even if API fails
    mainTypes.value = Array.isArray(data.mainTypes) ? data.mainTypes : []
    subTypes.value = Array.isArray(data.subTypes) ? data.subTypes : []
    availableFields.value = Array.isArray(data.availableFields) ? data.availableFields : []
    
    console.log('Main Types:', mainTypes.value) // Debug log
    console.log('Sub Types:', subTypes.value) // Debug log
    
    // Load field configurations for all types
    const allTypeIds = [
      ...mainTypes.value.map(t => t.id),
      ...subTypes.value.map(t => t.id)
    ]
    
    console.log('All Type IDs:', allTypeIds) // Debug log
    
    const configPromises = allTypeIds.map(typeId => 
      apiService.fetchFieldConfigurations(typeId)
    )
    
    const configResults = await Promise.allSettled(configPromises)
    
    configResults.forEach((result, index) => {
      if (result.status === 'fulfilled') {
        const typeId = allTypeIds[index]
        fieldConfigurations.value[typeId] = result.value.enabledFields || []
      }
    })
    
    console.log('Field Configurations:', fieldConfigurations.value) // Debug log
    
  } catch (error) {
    console.error('Error loading initial data:', error)
    showErrorMessage('Failed to load document types. Using fallback data.')
    
    // Ensure we have fallback data even if everything fails
    if (mainTypes.value.length === 0) {
      mainTypes.value = [
        { id: 1, type_name: 'Services', type_name_bn: 'সেবা', parent_id: null },
        { id: 2, type_name: 'Download', type_name_bn: 'ডাউনলোড', parent_id: null },
        { id: 3, type_name: 'News', type_name_bn: 'খবর', parent_id: null },
        { id: 4, type_name: 'FAQ', type_name_bn: 'প্রশ্নোত্তর', parent_id: null },
        { id: 5, type_name: 'Contact', type_name_bn: 'যোগাযোগ', parent_id: null }
      ]
    }
    
    if (subTypes.value.length === 0) {
      subTypes.value = [
        { id: 6, type_name: 'Offline Services', type_name_bn: 'অফলাইন সেবা', parent_id: 1 },
        { id: 7, type_name: 'Online Services', type_name_bn: 'অনলাইন সেবা', parent_id: 1 },
        { id: 8, type_name: 'SSL News', type_name_bn: 'এসএসএল খবর', parent_id: 3 },
        { id: 9, type_name: 'DSE News', type_name_bn: 'ডিএসই খবর', parent_id: 3 }
      ]
    }
    
    if (availableFields.value.length === 0) {
      availableFields.value = [
        { id: 1, name: 'Title', description: 'Document title field', type: 'title' },
        { id: 2, name: 'Description', description: 'Document description field', type: 'description' },
        { id: 3, name: 'Attachments', description: 'File attachments field', type: 'attachments' },
        { id: 4, name: 'Content', description: 'Rich text content field', type: 'content' }
      ]
    }
  } finally {
    loading.value = false
  }
}

// Initialize
onMounted(() => {
  loadInitialData()
})
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
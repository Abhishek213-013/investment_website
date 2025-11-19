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
              <h1 class="text-2xl font-bold text-gray-900">Committee Management</h1>
              <p class="text-gray-600 mt-1">Create and manage committees</p>
            </div>
            <button 
              @click="showCreateModal = true"
              class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Create New Committee
            </button>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="p-6">
        <!-- Create Committee Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Create New Committee</h3>
                <button 
                  @click="closeModal"
                  class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
              <!-- Committee Information Form -->
              <div class="mb-8">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Committee Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Committee Name -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Committee Name *
                    </label>
                    <input
                      v-model="committeeForm.name"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Enter committee name"
                    />
                    <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
                  </div>

                  <!-- Committee Type -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Committee Type *
                    </label>
                    <select
                      v-model="committeeForm.type"
                      @change="onCommitteeTypeChange"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                      <option value="">Select Committee Type</option>
                      <option value="Board Of Directors">Board Of Directors</option>
                      <option value="Executive Committee">Executive Committee</option>
                      <option value="Other">Other</option>
                    </select>
                    <p v-if="errors.type" class="text-red-500 text-xs mt-1">{{ errors.type }}</p>
                  </div>

                  <!-- Starting Date -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Starting Date *
                    </label>
                    <input
                      v-model="committeeForm.startingDate"
                      type="date"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    <p v-if="errors.startingDate" class="text-red-500 text-xs mt-1">{{ errors.startingDate }}</p>
                  </div>

                  <!-- Expiry Date -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Expiry Date
                    </label>
                    <input
                      v-model="committeeForm.expiryDate"
                      type="date"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    <p v-if="errors.expiryDate" class="text-red-500 text-xs mt-1">{{ errors.expiryDate }}</p>
                  </div>
                </div>
              </div>

              <!-- Committee Members Section -->
              <div>
                <div class="flex items-center justify-between mb-4">
                  <h4 class="text-md font-semibold text-gray-900">Committee Members</h4>
                  <button
                    @click="addMember"
                    type="button"
                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Member
                  </button>
                </div>

                <!-- Designation Usage Warning -->
                <div v-if="committeeForm.type && committeeMembers.length > 0" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                  <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div class="text-sm text-yellow-700">
                      <strong>Note:</strong> 
                      <span v-if="committeeForm.type === 'Board Of Directors'">
                        Only one member can be assigned as Chairman and Managing Director. Multiple members can be Directors.
                      </span>
                      <span v-else>
                        Only one member each for President, Vice President, General Secretary, Joint Secretary, and Organising Secretary. Multiple Executive and General Members are allowed.
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Members List -->
                <div v-if="committeeMembers.length > 0" class="space-y-4 mb-6">
                  <div
                    v-for="(member, index) in committeeMembers"
                    :key="index"
                    class="border border-gray-200 rounded-lg p-4"
                  >
                    <div class="flex items-center justify-between mb-3">
                      <h5 class="font-medium text-gray-900">Member {{ index + 1 }}</h5>
                      <button
                        @click="removeMember(index)"
                        type="button"
                        class="text-red-600 hover:text-red-800 transition-colors"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <!-- Member Name -->
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Member Name *
                        </label>
                        <input
                          v-model="member.name"
                          type="text"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Enter member name"
                        />
                        <p v-if="memberErrors[index]?.name" class="text-red-500 text-xs mt-1">
                          {{ memberErrors[index]?.name }}
                        </p>
                      </div>

                      <!-- Member Designation -->
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Designation *
                        </label>
                        <select
                          v-model="member.designation"
                          @change="onDesignationChange(index, $event)"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          :class="{
                            'border-red-300': isDesignationAlreadyUsed(member.designation, index)
                          }"
                        >
                          <option value="">Select Designation</option>
                          <option 
                            v-for="designation in availableDesignations" 
                            :key="designation.value"
                            :value="designation.value"
                            :disabled="designation.disabled || isDesignationLimitReached(designation.value, index)"
                          >
                            {{ designation.label }}
                            <template v-if="designation.limit === 1">(Only 1)</template>
                            <template v-else-if="designation.limit === 'multiple'">(Multiple)</template>
                          </option>
                        </select>
                        <p v-if="memberErrors[index]?.designation" class="text-red-500 text-xs mt-1">
                          {{ memberErrors[index]?.designation }}
                        </p>
                        <p v-if="isDesignationAlreadyUsed(member.designation, index)" class="text-red-500 text-xs mt-1">
                          This designation is already assigned to another member
                        </p>
                        <p v-if="isDesignationLimitReached(member.designation, index)" class="text-red-500 text-xs mt-1">
                          Maximum limit reached for this designation
                        </p>
                      </div>

                      <!-- Member Image -->
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Member Image
                        </label>
                        <input
                          type="file"
                          @change="handleImageUpload($event, index)"
                          accept="image/*"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                        <p v-if="member.imagePreview" class="mt-2">
                          <img :src="member.imagePreview" class="h-20 w-20 object-cover rounded" />
                        </p>
                      </div>

                      <!-- Member DOB -->
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Date of Birth
                        </label>
                        <input
                          v-model="member.dob"
                          type="date"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                      </div>

                      <!-- Member Joining Date -->
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Joining Date *
                        </label>
                        <input
                          v-model="member.joiningDate"
                          type="date"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                        <p v-if="memberErrors[index]?.joiningDate" class="text-red-500 text-xs mt-1">
                          {{ memberErrors[index]?.joiningDate }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- No Members Message -->
                <div v-else class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                  <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                  <p class="text-gray-500">No members added yet. Click "Add Member" to get started.</p>
                </div>
              </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
              <button
                @click="closeModal"
                type="button"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
              >
                Cancel
              </button>
              <button
                @click="submitCommittee"
                :disabled="isSubmitting || hasDesignationConflicts"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <span v-if="isSubmitting">Creating...</span>
                <span v-else>Create Committee</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <div v-if="showSuccess" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Committee created successfully!
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminSidebar from '../../../Layouts/AdminSidebar.vue'
import AdminNavbar from '../../../Layouts/AdminNavbar.vue'

// Modal state
const showCreateModal = ref(false)
const isSubmitting = ref(false)
const showSuccess = ref(false)

// Form data
const committeeForm = reactive({
  name: '',
  type: '',
  startingDate: '',
  expiryDate: ''
})

// Committee members array
const committeeMembers = ref([])

// Error handling
const errors = reactive({})
const memberErrors = ref([])

// Designation configurations
const boardOfDirectorsDesignations = [
  { value: 'Chairman', label: 'Chairman', limit: 1 },
  { value: 'Managing Director', label: 'Managing Director', limit: 1 },
  { value: 'Director', label: 'Director', limit: 'multiple' }
]

const otherCommitteeDesignations = [
  { value: 'President', label: 'President', limit: 1 },
  { value: 'Vice President', label: 'Vice President', limit: 1 },
  { value: 'General Secretary', label: 'General Secretary', limit: 1 },
  { value: 'Joint Secretary', label: 'Joint Secretary', limit: 1 },
  { value: 'Organising Secretary', label: 'Organising Secretary', limit: 1 },
  { value: 'Executive Member', label: 'Executive Member', limit: 'multiple' },
  { value: 'General Member', label: 'General Member', limit: 'multiple' }
]

// Computed property for available designations based on committee type
const availableDesignations = computed(() => {
  if (committeeForm.type === 'Board Of Directors') {
    return boardOfDirectorsDesignations
  } else if (committeeForm.type) {
    return otherCommitteeDesignations
  }
  return []
})

// Check if designation is already used by another member
const isDesignationAlreadyUsed = (designation, currentIndex) => {
  if (!designation) return false
  
  const designationConfig = availableDesignations.value.find(d => d.value === designation)
  if (!designationConfig || designationConfig.limit === 'multiple') return false
  
  return committeeMembers.value.some((member, index) => 
    index !== currentIndex && member.designation === designation
  )
}

// Check if designation limit is reached
const isDesignationLimitReached = (designation, currentIndex) => {
  if (!designation) return false
  
  const designationConfig = availableDesignations.value.find(d => d.value === designation)
  if (!designationConfig || designationConfig.limit === 'multiple') return false
  
  const currentCount = committeeMembers.value.filter((member, index) => 
    index !== currentIndex && member.designation === designation
  ).length
  
  return currentCount >= designationConfig.limit
}

// Check if there are any designation conflicts
const hasDesignationConflicts = computed(() => {
  return committeeMembers.value.some((member, index) => 
    isDesignationAlreadyUsed(member.designation, index) || 
    isDesignationLimitReached(member.designation, index)
  )
})

// Handle committee type change
const onCommitteeTypeChange = () => {
  // Reset all designations when committee type changes
  committeeMembers.value.forEach(member => {
    member.designation = ''
  })
}

// Handle designation change
const onDesignationChange = (index, event) => {
  const newDesignation = event.target.value
  committeeMembers.value[index].designation = newDesignation
}

// Add new member
const addMember = () => {
  committeeMembers.value.push({
    name: '',
    designation: '',
    image: null,
    imagePreview: null,
    dob: '',
    joiningDate: ''
  })
  memberErrors.value.push({})
}

// Remove member
const removeMember = (index) => {
  committeeMembers.value.splice(index, 1)
  memberErrors.value.splice(index, 1)
}

// Handle image upload
const handleImageUpload = (event, index) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file type
    if (!file.type.startsWith('image/')) {
      alert('Please select an image file')
      return
    }

    // Validate file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
      alert('Image size should be less than 2MB')
      return
    }

    committeeMembers.value[index].image = file
    
    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      committeeMembers.value[index].imagePreview = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

// Close modal and reset form
const closeModal = () => {
  showCreateModal.value = false
  resetForm()
}

// Reset form
const resetForm = () => {
  Object.assign(committeeForm, {
    name: '',
    type: '',
    startingDate: '',
    expiryDate: ''
  })
  committeeMembers.value = []
  memberErrors.value = []
  Object.keys(errors).forEach(key => delete errors[key])
}

// Validate committee form
const validateCommitteeForm = () => {
  let isValid = true
  Object.keys(errors).forEach(key => delete errors[key])

  if (!committeeForm.name.trim()) {
    errors.name = 'Committee name is required'
    isValid = false
  }

  if (!committeeForm.type) {
    errors.type = 'Committee type is required'
    isValid = false
  }

  if (!committeeForm.startingDate) {
    errors.startingDate = 'Starting date is required'
    isValid = false
  }

  if (committeeForm.expiryDate && committeeForm.expiryDate < committeeForm.startingDate) {
    errors.expiryDate = 'Expiry date cannot be before starting date'
    isValid = false
  }

  return isValid
}

// Validate member form
const validateMemberForm = () => {
  let isValid = true
  memberErrors.value = committeeMembers.value.map(() => ({}))

  if (committeeMembers.value.length === 0) {
    alert('Please add at least one committee member')
    return false
  }

  // Check for duplicate designations
  const designationCounts = {}
  committeeMembers.value.forEach((member, index) => {
    if (!member.name.trim()) {
      memberErrors.value[index].name = 'Member name is required'
      isValid = false
    }

    if (!member.designation) {
      memberErrors.value[index].designation = 'Designation is required'
      isValid = false
    }

    if (!member.joiningDate) {
      memberErrors.value[index].joiningDate = 'Joining date is required'
      isValid = false
    }

    // Count designations
    if (member.designation) {
      designationCounts[member.designation] = (designationCounts[member.designation] || 0) + 1
    }
  })

  // Validate designation limits
  Object.keys(designationCounts).forEach(designation => {
    const config = availableDesignations.value.find(d => d.value === designation)
    if (config && config.limit !== 'multiple' && designationCounts[designation] > config.limit) {
      isValid = false
      // Find all members with this designation and show error
      committeeMembers.value.forEach((member, index) => {
        if (member.designation === designation) {
          if (!memberErrors.value[index]) memberErrors.value[index] = {}
          memberErrors.value[index].designation = `Only ${config.limit} ${designation} allowed`
        }
      })
    }
  })

  return isValid
}

// Submit committee
const submitCommittee = async () => {
  if (!validateCommitteeForm() || !validateMemberForm()) {
    return
  }

  isSubmitting.value = true

  try {
    // Prepare form data for file upload
    const formData = new FormData()
    
    // Add committee data
    formData.append('name', committeeForm.name)
    formData.append('type', committeeForm.type)
    formData.append('starting_date', committeeForm.startingDate)
    formData.append('expiry_date', committeeForm.expiryDate || '')

    // Add members data
    committeeMembers.value.forEach((member, index) => {
      formData.append(`members[${index}][name]`, member.name)
      formData.append(`members[${index}][designation]`, member.designation)
      formData.append(`members[${index}][dob]`, member.dob || '')
      formData.append(`members[${index}][joining_date]`, member.joiningDate)
      
      if (member.image) {
        formData.append(`members[${index}][image]`, member.image)
      }
    })

    // Send POST request
    const response = await fetch('/admin/committee', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    })

    const result = await response.json()

    if (response.ok) {
      // Show success message
      showSuccess.value = true
      setTimeout(() => {
        showSuccess.value = false
      }, 3000)

      // Close modal and reset form
      closeModal()

      // Redirect or show success message
      console.log('Committee created successfully:', result)
    } else {
      // Handle server-side validation errors
      if (result.errors) {
        Object.keys(result.errors).forEach(key => {
          if (key.startsWith('members.')) {
            const match = key.match(/members\.(\d+)\.(.+)/)
            if (match) {
              const memberIndex = match[1]
              const field = match[2]
              if (!memberErrors.value[memberIndex]) {
                memberErrors.value[memberIndex] = {}
              }
              memberErrors.value[memberIndex][field] = result.errors[key][0]
            }
          } else {
            errors[key] = result.errors[key][0]
          }
        })
      }
      alert(result.message || 'Failed to create committee')
    }
  } catch (error) {
    console.error('Error creating committee:', error)
    alert('An error occurred while creating the committee')
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
/* Custom styles if needed */
</style>
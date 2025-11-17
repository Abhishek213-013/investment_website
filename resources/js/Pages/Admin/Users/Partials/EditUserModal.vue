<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Edit {{ userTypeLabel }}</h3>
      </div>

      <form @submit.prevent="$emit('submit')" class="p-6 space-y-4">
        <!-- Personal Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
            <input 
              v-model="form.name"
              type="text" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Enter full name"
              :class="{ 'border-red-300': form.errors.name }"
            >
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
            <input 
              v-model="form.username"
              type="text" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Enter username"
              :class="{ 'border-red-300': form.errors.username }"
            >
            <div v-if="form.errors.username" class="text-red-500 text-sm mt-1">{{ form.errors.username }}</div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
            <input 
              v-model="form.email"
              type="email" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Enter email address"
              :class="{ 'border-red-300': form.errors.email }"
            >
            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
            <input 
              v-model="form.dob"
              type="date" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-300': form.errors.dob }"
            >
            <div v-if="form.errors.dob" class="text-red-500 text-sm mt-1">{{ form.errors.dob }}</div>
          </div>
        </div>

        <!-- Education Information (for admins only) -->
        <div v-if="userType !== 'investor'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Education Qualification *</label>
            <select 
              v-model="form.education_qualification"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-300': form.errors.education_qualification }"
            >
              <option value="">Select qualification</option>
              <option value="HSC">HSC</option>
              <option value="BSC">BSC</option>
              <option value="BA">BA</option>
              <option value="MA">MA</option>
              <option value="MSC">MSC</option>
              <option value="PhD">PhD</option>
              <option value="Other">Other</option>
            </select>
            <div v-if="form.errors.education_qualification" class="text-red-500 text-sm mt-1">{{ form.errors.education_qualification }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Institute *</label>
            <input 
              v-model="form.institute"
              type="text" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Enter institute name"
              :class="{ 'border-red-300': form.errors.institute }"
            >
            <div v-if="form.errors.institute" class="text-red-500 text-sm mt-1">{{ form.errors.institute }}</div>
          </div>
        </div>

        <!-- Password (Optional for edit) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">New Password (Leave blank to keep current)</label>
            <input 
              v-model="form.password"
              type="password" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Enter new password"
              :class="{ 'border-red-300': form.errors.password }"
            >
            <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
            <input 
              v-model="form.password_confirmation"
              type="password" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Confirm new password"
            >
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button 
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
          <button 
            type="submit"
            :disabled="form.processing"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            {{ form.processing ? 'Updating...' : `Update ${userTypeLabel}` }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  userType: {
    type: String,
    required: true
  },
  form: {
    type: Object,
    required: true
  }
})

defineEmits(['submit', 'close'])

const userTypeLabel = computed(() => {
  switch (props.userType) {
    case 'super_admin': return 'Super Admin'
    case 'admin': return 'Admin'
    case 'investor': return 'Investor'
    default: return 'User'
  }
})
</script>
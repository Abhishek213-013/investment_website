<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <InvestorSidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <InvestorNavbar page-title="My Profile" />

      <!-- Page Content -->
      <div class="p-6">
        <div class="max-w-4xl mx-auto">
          <!-- Header -->
          <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
            <p class="text-gray-600">Manage your account settings and personal information</p>
          </div>

          <!-- Flash Messages -->
          <FlashMessages :flash="$page.props.flash" />

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Information -->
            <div class="lg:col-span-2">
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                
                <form @submit.prevent="updateProfile">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                      <input 
                        v-model="form.name"
                        type="text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-300': form.errors.name }"
                      >
                      <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                      <input 
                        v-model="form.email"
                        type="email" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-300': form.errors.email }"
                      >
                      <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                      <input 
                        v-model="form.phone"
                        type="tel" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      >
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                      <input 
                        v-model="form.dob"
                        type="date" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      >
                    </div>
                  </div>

                  <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea 
                      v-model="form.address"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    ></textarea>
                  </div>

                  <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                    <button 
                      type="button"
                      @click="resetForm"
                      class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                      Cancel
                    </button>
                    <button 
                      type="submit"
                      :disabled="form.processing"
                      class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                    >
                      {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </button>
                  </div>
                </form>
              </div>

              <!-- Change Password -->
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h3>
                
                <form @submit.prevent="updatePassword">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                      <input 
                        v-model="passwordForm.current_password"
                        type="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-300': passwordForm.errors.current_password }"
                      >
                      <div v-if="passwordForm.errors.current_password" class="text-red-500 text-sm mt-1">{{ passwordForm.errors.current_password }}</div>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                      <input 
                        v-model="passwordForm.password"
                        type="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-300': passwordForm.errors.password }"
                      >
                      <div v-if="passwordForm.errors.password" class="text-red-500 text-sm mt-1">{{ passwordForm.errors.password }}</div>
                    </div>
                  </div>

                  <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input 
                      v-model="passwordForm.password_confirmation"
                      type="password" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                  </div>

                  <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                    <button 
                      type="button"
                      @click="resetPasswordForm"
                      class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                      Cancel
                    </button>
                    <button 
                      type="submit"
                      :disabled="passwordForm.processing"
                      class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                    >
                      {{ passwordForm.processing ? 'Updating...' : 'Update Password' }}
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Profile Summary -->
            <div class="space-y-6">
              <!-- Account Status -->
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Status</h3>
                <div class="space-y-3">
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Member Since</span>
                    <span class="text-sm font-medium text-gray-800">{{ formatDate(user.created_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Account Type</span>
                    <span class="text-sm font-medium text-green-600">Investor</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Status</span>
                    <span class="text-sm font-medium text-green-600">Active</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Last Login</span>
                    <span class="text-sm font-medium text-gray-800">{{ formatDate(user.last_login_at) || 'Never' }}</span>
                  </div>
                </div>
              </div>

              <!-- Profile Completion -->
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Profile Completion</h3>
                <div class="space-y-2">
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Basic Information</span>
                    <span class="font-medium text-green-600">Complete</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Contact Details</span>
                    <span :class="user.phone ? 'text-green-600' : 'text-yellow-600'">
                      {{ user.phone ? 'Complete' : 'Incomplete' }}
                    </span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-600">KYC Verification</span>
                    <span class="font-medium text-green-600">Verified</span>
                  </div>
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
import { useForm } from '@inertiajs/vue3'
import InvestorSidebar from '../../Layouts/InvestorSidebar.vue'
import InvestorNavbar from '../../Layouts/InvestorNavbar.vue'
import FlashMessages from '../../Components/FlashMessages.vue'

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

// Profile form
const form = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone || '',
  dob: props.user.dob || '',
  address: props.user.address || ''
})

// Password form
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const updateProfile = () => {
  form.put('/investor/profile', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
    }
  })
}

const updatePassword = () => {
  passwordForm.put('/investor/password', {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
    }
  })
}

const resetForm = () => {
  form.reset()
  form.clearErrors()
}

const resetPasswordForm = () => {
  passwordForm.reset()
  passwordForm.clearErrors()
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>
<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <AdminSidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <AdminNavbar :page-title="pageTitle" @search="handleSearch" />

      <!-- Page Content -->
      <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ pageTitle }}</h1>
            <p class="text-gray-600">{{ pageDescription }}</p>
          </div>
          <button 
            @click="showCreateModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Add {{ userTypeLabel }}</span>
          </button>
        </div>

        <!-- Flash Messages -->
        <FlashMessages :flash="flash" />

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <StatsCard 
            :title="`Total ${userTypeLabel}s`"
            :value="users.length"
            :color="'blue'"
            :icon="'users'"
          />
          
          <StatsCard 
            title="Active Today"
            :value="stats?.activeToday || 0"
            color="green"
            icon="active"
          />
          
          <StatsCard 
            title="Last 7 Days"
            :value="stats?.last7Days || 0"
            color="purple"
            icon="chart"
          />
          
          <StatsCard 
            :title="userType === 'investor' ? 'Total Investment' : 'System Access'"
            :value="userType === 'investor' ? formatCurrency(stats?.totalInvestment || 0) : 'Full'"
            :color="userType === 'investor' ? 'green' : 'orange'"
            :icon="userType === 'investor' ? 'money' : 'security'"
          />
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h3 class="text-lg font-semibold text-gray-800">{{ userTypeLabel }}s</h3>
              <div class="relative">
                <input 
                  type="text" 
                  v-model="searchQuery"
                  :placeholder="`Search ${userTypeLabel.toLowerCase()}s...`" 
                  class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" v-if="userType !== 'investor'">Education</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" v-if="userType === 'investor'">Investment</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-semibold">
                          {{ getUserInitials(user.name) }}
                        </span>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ user.email }}</div>
                    <div class="text-sm text-gray-500" v-if="user.dob">DOB: {{ formatDate(user.dob) }}</div>
                    <div class="text-sm text-gray-500" v-else>Joined: {{ formatDate(user.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap" v-if="userType !== 'investor'">
                    <div class="text-sm text-gray-900">{{ user.education_qualification || 'N/A' }}</div>
                    <div class="text-sm text-gray-500">{{ user.institute || 'N/A' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap" v-if="userType === 'investor'">
                    <div class="text-sm font-medium text-gray-900">{{ formatCurrency(user.total_investment || 0) }}</div>
                    <div class="text-sm text-gray-500">{{ user.active_investments || 0 }} active</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                      user.is_active 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-red-100 text-red-800'
                    ]">
                      {{ user.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button 
                      @click="editUser(user)"
                      class="text-blue-600 hover:text-blue-900 mr-3"
                    >
                      Edit
                    </button>
                    <button 
                      v-if="userType !== 'investor'"
                      @click="deleteUser(user)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Delete
                    </button>
                    <button 
                      v-else
                      @click="viewInvestor(user)"
                      class="text-green-600 hover:text-green-900"
                    >
                      View
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <EmptyState 
            v-if="filteredUsers.length === 0"
            :title="`No ${userTypeLabel.toLowerCase()}s found`"
            :description="emptyStateDescription"
            :button-text="`Add ${userTypeLabel}`"
            @button-click="showCreateModal = true"
          />
        </div>

        <!-- Create User Modal -->
        <CreateUserModal 
          v-if="showCreateModal"
          :user-type="userType"
          :form="form"
          @submit="createUser"
          @close="closeModal"
        />

        <!-- Edit User Modal -->
        <EditUserModal 
          v-if="showEditModal"
          :user-type="userType"
          :form="editForm"
          @submit="updateUser"
          @close="closeEditModal"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminSidebar from '../../../Layouts/AdminSidebar.vue'
import AdminNavbar from '../../../Layouts/AdminNavbar.vue'
import FlashMessages from '../../../Components/FlashMessages.vue'
import StatsCard from '../../../Components/StatsCard.vue'
import EmptyState from '../../../Components/EmptyState.vue'
import CreateUserModal from './Partials/CreateUserModal.vue'
import EditUserModal from './Partials/EditUserModal.vue'

// Props from Laravel backend
const props = defineProps({
  users: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({})
  },
  flash: {
    type: Object,
    default: () => ({})
  },
  userType: {
    type: String,
    default: 'super_admin'
  }
})

// Reactive data
const showCreateModal = ref(false)
const showEditModal = ref(false)
const searchQuery = ref('')
const editingUser = ref(null)

// Forms
const form = useForm({
  name: '',
  email: '',
  dob: '',
  education_qualification: '',
  institute: '',
  password: '',
  password_confirmation: '',
  role: props.userType,
  // Investor specific fields
  phone: '',
  address: '',
  initial_investment: ''
})

const editForm = useForm({
  id: null,
  name: '',
  email: '',
  dob: '',
  education_qualification: '',
  institute: '',
  password: '',
  password_confirmation: '',
  // Investor specific fields
  phone: '',
  address: ''
})

// Computed properties
const userTypeLabel = computed(() => {
  switch (props.userType) {
    case 'super_admin': return 'Super Admin'
    case 'admin': return 'Admin'
    case 'investor': return 'Investor'
    default: return 'User'
  }
})

const pageTitle = computed(() => {
  switch (props.userType) {
    case 'super_admin': return 'Super Admins Management'
    case 'admin': return 'Administrators Management'
    case 'investor': return 'Investors Management'
    default: return 'Users Management'
  }
})

const pageDescription = computed(() => {
  switch (props.userType) {
    case 'super_admin': return 'Manage all super administrator accounts'
    case 'admin': return 'Manage all administrator accounts'
    case 'investor': return 'Manage all investor accounts and investments'
    default: return 'Manage all user accounts'
  }
})

const emptyStateDescription = computed(() => {
  switch (props.userType) {
    case 'super_admin': return 'Get started by creating your first super administrator.'
    case 'admin': return 'Get started by creating your first administrator.'
    case 'investor': return 'No investors found in the system.'
    default: return 'No users found.'
  }
})

const filteredUsers = computed(() => {
  if (!searchQuery.value) return props.users
  
  const query = searchQuery.value.toLowerCase()
  return props.users.filter(user => 
    user.name?.toLowerCase().includes(query) ||
    user.email?.toLowerCase().includes(query)
  )
})

// Methods
const handleSearch = (query) => {
  searchQuery.value = query
}

const createUser = () => {
  const route = getRouteForUserType('store')
  form.post(route, {
    preserveScroll: true,
    onSuccess: () => {
      closeModal()
      form.reset()
    },
  })
}

const editUser = (user) => {
  editingUser.value = user
  editForm.id = user.id
  editForm.name = user.name
  editForm.email = user.email
  editForm.dob = user.dob ? user.dob.split('T')[0] : ''
  editForm.education_qualification = user.education_qualification
  editForm.institute = user.institute
  editForm.phone = user.phone || ''
  editForm.address = user.address || ''
  editForm.password = ''
  editForm.password_confirmation = ''
  showEditModal.value = true
}

const updateUser = () => {
  if (!editingUser.value) return
  
  const route = getRouteForUserType('update', editingUser.value.id)
  editForm.put(route, {
    preserveScroll: true,
    onSuccess: () => {
      closeEditModal()
      editForm.reset()
    },
  })
}

const deleteUser = (user) => {
  if (confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) {
    const route = getRouteForUserType('destroy', user.id)
    router.delete(route, {
      preserveScroll: true,
    })
  }
}

const viewInvestor = (investor) => {
  router.visit(`/admin/investors/${investor.id}`)
}

const closeModal = () => {
  showCreateModal.value = false
  form.reset()
  form.clearErrors()
}

const closeEditModal = () => {
  showEditModal.value = false
  editingUser.value = null
  editForm.reset()
  editForm.clearErrors()
}

const getRouteForUserType = (action, id = null) => {
  const baseRoutes = {
    super_admin: '/admin/users/super-admins',
    admin: '/admin/users/admins',
    investor: '/admin/users/investors'
  }
  
  const baseRoute = baseRoutes[props.userType]
  
  switch (action) {
    case 'store': return baseRoute
    case 'update': return `${baseRoute}/${id}`
    case 'destroy': return `${baseRoute}/${id}`
    default: return baseRoute
  }
}

// Helper functions
const getUserInitials = (name) => {
  if (!name) return 'U'
  return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2)
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount)
}

// Initialize on mount
onMounted(() => {
  console.log('User Management mounted for:', props.userType)
})
</script>
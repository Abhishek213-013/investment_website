<template>
  <div class="min-h-screen bg-gray-50 flex">
    <AdminSidebar />
    
    <!-- Main Content Area -->
    <div class="flex-1 lg:ml-64 flex flex-col min-w-0">
      <AdminNavbar />
      
      <!-- Header -->
      <div class="bg-white shadow flex-shrink-0">
        <div class="px-6 py-4">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Committee List</h1>
              <p class="text-gray-600 mt-1">View and manage all committees (Present and Expired)</p>
            </div>
            <div class="flex space-x-3">
              <button 
                @click="exportToExcel"
                :disabled="isExporting"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span v-if="isExporting">Exporting...</span>
                <span v-else>Export Excel</span>
              </button>
              <button 
                @click="createNewCommittee"
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
      </div>

      <!-- Main Content -->
      <div class="flex-1 p-6 overflow-hidden">
        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search by committee name..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <!-- Committee Type Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Committee Type</label>
              <select
                v-model="filters.type"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              >
                <option value="">All Types</option>
                <option value="Board Of Directors">Board Of Directors</option>
                <option value="Executive Committee">Executive Committee</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <!-- Status Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select
                v-model="filters.status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              >
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="no-expiry">No Expiry</option>
              </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end space-x-2">
              <button
                @click="applyFilters"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md flex items-center transition-colors"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                </svg>
                Apply Filters
              </button>
              <button
                @click="resetFilters"
                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md transition-colors"
              >
                Reset
              </button>
            </div>
          </div>
        </div>

        <!-- Committees Table Container -->
        <div class="bg-white rounded-lg shadow overflow-hidden flex-1 flex flex-col">
          <!-- Table Header -->
          <div class="px-6 py-4 border-b border-gray-200 flex-shrink-0">
            <h2 class="text-lg font-semibold text-gray-900">
              Committees ({{ filteredCommittees.length }})
            </h2>
          </div>

          <!-- Table Container with Proper Scrolling -->
          <div class="flex-1 overflow-auto">
            <div class="min-w-full">
              <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                      Committee Name
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                      Type
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                      Duration
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                      Members
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                      Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                      Created Date
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr 
                    v-for="committee in paginatedCommittees" 
                    :key="committee.Committee_ID"
                    class="hover:bg-gray-50 transition-colors"
                  >
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ committee.Committee_Name }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ committee.Committee_Type }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ formatDate(committee.Committee_Starting_Date) }} - 
                      {{ committee.Committee_Expiry_Date ? formatDate(committee.Committee_Expiry_Date) : 'No expiry' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                      <div>{{ committee.members?.length || 0 }} members</div>
                      <div class="text-xs text-gray-500">
                        {{ getTopDesignations(committee.members) }}
                      </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                      <span 
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap"
                        :class="getStatusClass(committee)"
                      >
                        {{ getStatusText(committee) }}
                      </span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ formatDateTime(committee.Created_At) }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end space-x-2">
                        <button
                          @click="viewCommittee(committee)"
                          class="text-indigo-600 hover:text-indigo-900 transition-colors"
                          title="View Details"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                          </svg>
                        </button>
                        <button
                          @click="editCommittee(committee)"
                          class="text-blue-600 hover:text-blue-900 transition-colors"
                          title="Edit Committee"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                          </svg>
                        </button>
                        <button
                          @click="confirmDelete(committee)"
                          class="text-red-600 hover:text-red-900 transition-colors"
                          title="Delete Committee"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Empty State -->
            <div v-if="filteredCommittees.length === 0" class="text-center py-12">
              <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No committees found</h3>
              <p class="text-gray-500 mb-4">
                {{ committees.length === 0 ? 'Get started by creating your first committee.' : 'No committees match your current filters.' }}
              </p>
              <button
                v-if="committees.length === 0"
                @click="createNewCommittee"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors mx-auto"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create New Committee
              </button>
              <button
                v-else
                @click="resetFilters"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
              >
                Clear Filters
              </button>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="filteredCommittees.length > 0" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between flex-shrink-0">
            <div class="text-sm text-gray-700">
              Showing {{ startIndex + 1 }} to {{ endIndex }} of {{ filteredCommittees.length }} results
            </div>
            <div class="flex space-x-2">
              <button
                @click="previousPage"
                :disabled="currentPage === 1"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                Previous
              </button>
              <button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Delete Committee</h3>
        </div>
        <div class="p-6">
          <p class="text-gray-700">
            Are you sure you want to delete the committee "<strong>{{ committeeToDelete?.Committee_Name }}</strong>"?
            This action cannot be undone and all member information will be permanently deleted.
          </p>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="deleteCommittee"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 transition-colors"
          >
            Delete Committee
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
        {{ successMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminSidebar from '../../../Layouts/AdminSidebar.vue'
import AdminNavbar from '../../../Layouts/AdminNavbar.vue'

// Define props
const props = defineProps({
  committees: {
    type: Array,
    default: () => []
  }
})

// State
const showDeleteModal = ref(false)
const showSuccess = ref(false)
const successMessage = ref('')
const committeeToDelete = ref(null)
const isDeleting = ref(false)
const isExporting = ref(false)

// Filters
const filters = reactive({
  search: '',
  type: '',
  status: ''
})

// Pagination
const currentPage = ref(1)
const itemsPerPage = 10

// Computed properties
const filteredCommittees = computed(() => {
  let filtered = props.committees

  // Apply search filter
  if (filters.search) {
    const searchTerm = filters.search.toLowerCase()
    filtered = filtered.filter(committee => 
      committee.Committee_Name.toLowerCase().includes(searchTerm)
    )
  }

  // Apply type filter
  if (filters.type) {
    filtered = filtered.filter(committee => 
      committee.Committee_Type === filters.type
    )
  }

  // Apply status filter
  if (filters.status) {
    filtered = filtered.filter(committee => {
      if (filters.status === 'active') {
        return isCommitteeActive(committee)
      } else if (filters.status === 'expired') {
        return isCommitteeExpired(committee)
      } else if (filters.status === 'no-expiry') {
        return !committee.Committee_Expiry_Date
      }
      return true
    })
  }

  return filtered
})

const totalPages = computed(() => {
  return Math.ceil(filteredCommittees.value.length / itemsPerPage)
})

const startIndex = computed(() => {
  return (currentPage.value - 1) * itemsPerPage
})

const endIndex = computed(() => {
  return Math.min(startIndex.value + itemsPerPage, filteredCommittees.value.length)
})

const paginatedCommittees = computed(() => {
  return filteredCommittees.value.slice(startIndex.value, endIndex.value)
})

// Methods
const applyFilters = () => {
  currentPage.value = 1
}

const resetFilters = () => {
  filters.search = ''
  filters.type = ''
  filters.status = ''
  currentPage.value = 1
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const isCommitteeActive = (committee) => {
  if (!committee.Committee_Expiry_Date) return true
  const today = new Date()
  const expiryDate = new Date(committee.Committee_Expiry_Date)
  return expiryDate >= today
}

const isCommitteeExpired = (committee) => {
  if (!committee.Committee_Expiry_Date) return false
  const today = new Date()
  const expiryDate = new Date(committee.Committee_Expiry_Date)
  return expiryDate < today
}

const getStatusText = (committee) => {
  if (!committee.Committee_Expiry_Date) return 'No Expiry'
  return isCommitteeActive(committee) ? 'Active' : 'Expired'
}

const getStatusClass = (committee) => {
  if (!committee.Committee_Expiry_Date) {
    return 'bg-gray-100 text-gray-800'
  }
  return isCommitteeActive(committee) 
    ? 'bg-green-100 text-green-800' 
    : 'bg-red-100 text-red-800'
}

const getTopDesignations = (members) => {
  if (!members || members.length === 0) return 'No members'
  
  const designations = members.map(m => m.Member_Designation)
  const uniqueDesignations = [...new Set(designations)]
  return uniqueDesignations.slice(0, 2).join(', ') + (uniqueDesignations.length > 2 ? '...' : '')
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString()
}

const viewCommittee = (committee) => {
  // Navigate to committee details page or show modal
  router.visit(`/admin/committee/${committee.Committee_ID}`)
}

const editCommittee = (committee) => {
  // Navigate to edit page
  router.visit('/admin/committee/site/2')
}

const createNewCommittee = () => {
  // Navigate to create committee page
  router.visit('/admin/committee/site/1')
}

const confirmDelete = (committee) => {
  committeeToDelete.value = committee
  showDeleteModal.value = true
}

const deleteCommittee = async () => {
  if (!committeeToDelete.value) return

  isDeleting.value = true

  try {
    const response = await fetch(`/admin/committee/${committeeToDelete.value.Committee_ID}`, {
      method: 'DELETE',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    })

    const result = await response.json()

    if (response.ok) {
      showSuccessMessage('Committee deleted successfully!')
      // Reload the page to get updated data
      window.location.reload()
    } else {
      alert(result.message || 'Failed to delete committee')
    }
  } catch (error) {
    console.error('Error deleting committee:', error)
    alert('An error occurred while deleting the committee')
  } finally {
    isDeleting.value = false
    showDeleteModal.value = false
    committeeToDelete.value = null
  }
}

const exportToExcel = async () => {
  if (filteredCommittees.value.length === 0) {
    alert('No data to export')
    return
  }

  isExporting.value = true

  try {
    // Prepare data for export
    const exportData = filteredCommittees.value.map(committee => ({
      'Committee Name': committee.Committee_Name,
      'Committee Type': committee.Committee_Type,
      'Starting Date': formatDate(committee.Committee_Starting_Date),
      'Expiry Date': committee.Committee_Expiry_Date ? formatDate(committee.Committee_Expiry_Date) : 'No Expiry',
      'Status': getStatusText(committee),
      'Total Members': committee.members?.length || 0,
      'Member Designations': committee.members?.map(m => m.Member_Designation).join(', ') || 'None',
      'Created Date': formatDateTime(committee.Created_At)
    }))

    // Create CSV content
    const headers = Object.keys(exportData[0])
    const csvContent = [
      headers.join(','),
      ...exportData.map(row => 
        headers.map(header => {
          const value = row[header] || ''
          // Escape quotes and wrap in quotes if contains comma
          return `"${String(value).replace(/"/g, '""')}"`
        }).join(',')
      )
    ].join('\n')

    // Create and download file
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const link = document.createElement('a')
    const url = URL.createObjectURL(blob)
    
    link.setAttribute('href', url)
    link.setAttribute('download', `committees_export_${new Date().toISOString().split('T')[0]}.csv`)
    link.style.visibility = 'hidden'
    
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    
    showSuccessMessage('Excel export completed successfully!')
    
  } catch (error) {
    console.error('Error exporting to Excel:', error)
    alert('An error occurred while exporting to Excel')
  } finally {
    isExporting.value = false
  }
}

const showSuccessMessage = (message) => {
  successMessage.value = message
  showSuccess.value = true
  setTimeout(() => {
    showSuccess.value = false
  }, 3000)
}

// Debug: Log the received props
onMounted(() => {
  console.log('Received committees prop:', props.committees)
  console.log('Number of committees:', props.committees.length)
})
</script>
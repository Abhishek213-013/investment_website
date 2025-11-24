<template>
  <InvestProLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <header class="bg-white shadow-sm">
        <div class="container mx-auto px-6 py-4">
          <div class="flex justify-between items-center">
            <!-- <div>
              <h1 class="text-2xl font-bold text-gray-900">Board of Directors</h1>
              <p class="text-gray-600 mt-1">Meet our leadership team</p>
            </div> -->
            <div class="flex items-center space-x-4">
              <button 
                @click="goBack"
                class="text-blue-600 hover:text-blue-800 text-sm flex items-center space-x-1"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back</span>
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="container mx-auto px-6 py-8">
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-4 text-gray-600">Loading board members...</p>
        </div>

        <div v-else-if="error" class="text-center py-12">
          <svg class="mx-auto h-24 w-24 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
          <h3 class="mt-4 text-lg font-medium text-gray-900">Unable to load board information</h3>
          <p class="mt-2 text-gray-500">{{ error }}</p>
          <button 
            @click="fetchBoardCommittees"
            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
          >
            Try Again
          </button>
        </div>

        <div v-else-if="committees.length === 0" class="text-center py-12">
          <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
          </svg>
          <h3 class="mt-4 text-lg font-medium text-gray-900">No board information available</h3>
          <p class="mt-2 text-gray-500">Board of directors information will be available soon.</p>
        </div>

        <div v-else class="space-y-8">
          <div v-for="committee in committees" :key="committee.Committee_ID" class="bg-white rounded-lg shadow-md p-6">
            <!-- Committee Header -->
            <div class="text-center mb-8">
              <h2 class="text-2xl font-bold text-gray-900">{{ committee.Committee_Name || 'Unnamed Committee' }}</h2>
              <p class="text-gray-600 mt-2">{{ committee.Committee_Type || 'Committee' }}</p>
              <div class="flex justify-center items-center space-x-4 mt-4 text-sm text-gray-500">
                <span v-if="committee.Committee_Starting_Date">Starting: {{ formatDate(committee.Committee_Starting_Date) }}</span>
                <span v-if="committee.Committee_Expiry_Date">• Expiry: {{ formatDate(committee.Committee_Expiry_Date) }}</span>
              </div>
            </div>

            <!-- Committee Members Grid -->
            <div v-if="committee.members && committee.members.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="member in committee.members" :key="member.Member_ID" 
                   class="bg-gray-50 rounded-lg p-6 text-center hover:shadow-lg transition-all duration-300 member-card">
                
                <!-- Member Photo -->
                <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden bg-gray-200 border-4 border-white shadow-md">
                  <img 
                    v-if="member.Member_Image" 
                    :src="getMemberImageUrl(member.Member_Image)" 
                    :alt="member.Member_Name || 'Board Member'"
                    class="w-full h-full object-cover"
                    @error="handleImageError"
                  >
                  <div v-else class="w-full h-full flex items-center justify-center bg-gray-300">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                </div>

                <!-- Member Details -->
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ member.Member_Name || 'Unnamed Member' }}</h3>
                <p class="text-blue-600 font-medium mb-3">{{ member.Member_Designation || 'Board Member' }}</p>
                
                <div class="text-sm text-gray-600 space-y-1">
                  <div v-if="member.Member_DOB" class="flex items-center justify-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Born: {{ formatDate(member.Member_DOB) }}</span>
                  </div>
                  <div v-if="member.Member_Joining_Date" class="flex items-center justify-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Joined: {{ formatDate(member.Member_Joining_Date) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- No Members Message -->
            <div v-else class="text-center py-8">
              <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
              </svg>
              <p class="mt-4 text-gray-500">No members found for this committee.</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  </InvestProLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import InvestProLayout from '@/Layouts/InvestProLayout.vue'

const loading = ref(true)
const error = ref(null)
const committees = ref([])

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (e) {
    console.warn('Invalid date format:', dateString)
    return 'Invalid Date'
  }
}

const getMemberImageUrl = (imagePath) => {
  if (!imagePath) return ''
  return `/storage/${imagePath}`
}

const handleImageError = (event) => {
  console.warn('Image failed to load:', event.target.src)
  // Replace with fallback icon
  event.target.style.display = 'none'
  const parent = event.target.parentElement
  if (parent) {
    const fallback = document.createElement('div')
    fallback.className = 'w-full h-full flex items-center justify-center bg-gray-300'
    fallback.innerHTML = `
      <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
      </svg>
    `
    parent.appendChild(fallback)
  }
}

const goBack = () => {
  window.history.back()
}

const fetchBoardCommittees = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await fetch('/api/committees/board-of-directors')
    
    if (!response.ok) {
      throw new Error(`Failed to fetch data: ${response.status} ${response.statusText}`)
    }
    
    const data = await response.json()
    
    if (data.success && data.committees) {
      // Process and validate the data
      committees.value = data.committees.map(committee => ({
        Committee_ID: committee.Committee_ID || Math.random().toString(36).substr(2, 9),
        Committee_Name: committee.Committee_Name || 'Unnamed Committee',
        Committee_Type: committee.Committee_Type || 'Committee',
        Committee_Starting_Date: committee.Committee_Starting_Date,
        Committee_Expiry_Date: committee.Committee_Expiry_Date,
        members: Array.isArray(committee.members) ? committee.members.map(member => ({
          Member_ID: member.Member_ID || Math.random().toString(36).substr(2, 9),
          Member_Name: member.Member_Name || 'Unnamed Member',
          Member_Designation: member.Member_Designation || 'Board Member',
          Member_Image: member.Member_Image,
          Member_DOB: member.Member_DOB,
          Member_Joining_Date: member.Member_Joining_Date
        })) : []
      }))
    } else {
      throw new Error(data.message || 'Failed to load committee data')
    }
  } catch (err) {
    console.error('Error fetching board committees:', err)
    error.value = err.message || 'An unexpected error occurred while loading board information.'
    committees.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchBoardCommittees()
})
</script>

<style scoped>
/* Custom styles for board members */
.member-card {
  transition: all 0.3s ease;
}

.member-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Smooth image transitions */
img {
  transition: opacity 0.3s ease;
}

img:hover {
  opacity: 0.9;
}
</style>
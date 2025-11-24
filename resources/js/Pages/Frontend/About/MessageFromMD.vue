<template>
  <InvestProLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <header class="bg-white shadow-sm">
        <div class="container mx-auto px-6 py-4">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">
                {{ currentLanguage === 'en' ? pageData.page_name : (pageData.page_name_bn || pageData.page_name) }}
              </h1>
            </div>
            <div class="flex items-center space-x-4">
              <!-- Language Toggle -->
              <div class="flex items-center space-x-2 bg-gray-100 rounded-lg p-1">
                <button
                  @click="setLanguage('en')"
                  :class="[
                    'px-3 py-1 rounded-md text-sm font-medium transition-colors',
                    currentLanguage === 'en' 
                      ? 'bg-white text-blue-600 shadow-sm' 
                      : 'text-gray-600 hover:text-gray-900'
                  ]"
                >
                  EN
                </button>
                <button
                  @click="setLanguage('bn')"
                  :class="[
                    'px-3 py-1 rounded-md text-sm font-medium transition-colors',
                    currentLanguage === 'bn' 
                      ? 'bg-white text-blue-600 shadow-sm' 
                      : 'text-gray-600 hover:text-gray-900'
                  ]"
                >
                  BN
                </button>
              </div>
              <button 
                @click="goBack"
                class="text-blue-600 hover:text-blue-800 text-sm flex items-center space-x-1"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ currentLanguage === 'en' ? 'Back' : 'পিছনে' }}</span>
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="container mx-auto px-6 py-8">
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-4 text-gray-600">Loading page...</p>
        </div>

        <div v-else-if="!pageData || !pageData.sections || pageData.sections.length === 0" class="text-center py-12">
          <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
          </svg>
          <h3 class="mt-4 text-lg font-medium text-gray-900">No content available</h3>
          <p class="mt-2 text-gray-500">This page doesn't have any content yet.</p>
        </div>

        <div v-else class="space-y-8">
          <div v-for="(section, index) in processedSections" :key="section.page_section_id || index" 
               class="bg-white rounded-lg shadow-md p-6">
            
            <!-- Section Header and Description with Content Alignment -->
            <div v-if="getSectionHeading(section) || getSectionDescription(section)" 
                 :class="getHeaderAlignmentClass(section)" 
                 class="mb-6">
              <h2 v-if="getSectionHeading(section)" class="text-xl font-semibold text-gray-900">
                {{ getSectionHeading(section) }}
              </h2>
              <p v-if="getSectionDescription(section)" class="text-gray-700 leading-relaxed mt-2">
                {{ getSectionDescription(section) }}
              </p>
            </div>

            <!-- Content Layout -->
            <div :class="getSectionLayoutClasses(section)">
              
              <!-- Content Area -->
              <div v-if="sectionHasContent(section)" :class="getContentClasses(section)">
                <div class="prose max-w-none" :class="getContentAlignmentClass(section)">
                  <div v-if="getSectionContent(section)" v-html="getSectionContent(section)"></div>
                  <div v-else class="text-gray-500 italic">
                    {{ currentLanguage === 'en' ? 'No content available' : 'কোনো কন্টেন্ট উপলব্ধ নেই' }}
                  </div>
                </div>
              </div>

              <!-- Attachments Area -->
              <div v-if="sectionHasAttachments(section)" :class="getAttachmentClasses(section)">
                <!-- Image Slider for Multiple Images -->
                <div v-if="hasMultipleImages(section.attachments)" class="relative">
                  <div class="relative overflow-hidden rounded-lg bg-gray-100"
                       @mouseenter="pauseAutoSlide(section.page_section_id)"
                       @mouseleave="resumeAutoSlide(section.page_section_id)">
                    <!-- Slider Container -->
                    <div class="flex transition-transform duration-500 ease-in-out" 
                         :style="{ transform: `translateX(-${getCurrentSlide(section.page_section_id) * 100}%)` }">
                      <div v-for="(attachment, attIndex) in getImageAttachments(section.attachments)" 
                           :key="attIndex" 
                           class="w-full flex-shrink-0">
                        <img :src="getAttachmentUrl(attachment)" 
                             :alt="getFileName(attachment)"
                             class="w-full h-64 md:h-96 object-cover"
                             @error="handleImageError">
                      </div>
                    </div>
                    
                    <!-- Navigation Arrows -->
                    <button v-if="getImageAttachments(section.attachments).length > 1"
                            @click="prevSlide(section.page_section_id)"
                            class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white rounded-full p-2 hover:bg-opacity-70 transition-all">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                      </svg>
                    </button>
                    <button v-if="getImageAttachments(section.attachments).length > 1"
                            @click="nextSlide(section.page_section_id)"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white rounded-full p-2 hover:bg-opacity-70 transition-all">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                      </svg>
                    </button>
                    
                    <!-- Slide Indicators -->
                    <div v-if="getImageAttachments(section.attachments).length > 1" 
                         class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                      <button v-for="(img, imgIndex) in getImageAttachments(section.attachments)" 
                              :key="imgIndex"
                              @click="goToSlide(section.page_section_id, imgIndex)"
                              :class="[
                                'w-3 h-3 rounded-full transition-all',
                                getCurrentSlide(section.page_section_id) === imgIndex ? 'bg-white' : 'bg-white bg-opacity-50'
                              ]">
                      </button>
                    </div>

                    <!-- Auto-play indicator -->
                    <div v-if="getImageAttachments(section.attachments).length > 1" 
                         class="absolute top-4 right-4">
                      <button
                        @click="toggleAutoSlide(section.page_section_id)"
                        :class="[
                          'w-8 h-8 rounded-full bg-black bg-opacity-50 flex items-center justify-center transition-all',
                          autoSlideStates[section.page_section_id]?.paused ? 'text-gray-300' : 'text-white'
                        ]"
                      >
                        <svg v-if="autoSlideStates[section.page_section_id]?.paused" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M8 5v14l11-7z"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M6 4h4v16H6zm8 0h4v16h-4z"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Single Image Display -->
                <div v-else-if="hasSingleImage(section.attachments)" class="space-y-4">
                  <div v-for="(attachment, attIndex) in getImageAttachments(section.attachments)" 
                       :key="attIndex"
                       class="rounded-lg overflow-hidden">
                    <img :src="getAttachmentUrl(attachment)" 
                         :alt="getFileName(attachment)"
                         class="w-full h-auto max-h-96 object-contain"
                         @error="handleImageError">
                  </div>
                </div>

                <!-- File Attachments List -->
                <div v-if="hasFileAttachments(section.attachments)" class="space-y-2">
                  <h4 class="text-sm font-medium text-gray-700">
                    {{ currentLanguage === 'en' ? 'File Attachments' : 'ফাইল সংযুক্তি' }}
                  </h4>
                  <div class="grid grid-cols-1 gap-2">
                    <div v-for="(attachment, attIndex) in getFileAttachments(section.attachments)" 
                         :key="attIndex" 
                         class="flex items-center justify-between bg-gray-50 px-4 py-3 rounded-lg border border-gray-200">
                      <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div>
                          <p class="text-sm font-medium text-gray-900">{{ getFileName(attachment) }}</p>
                          <p class="text-xs text-gray-500">{{ getFileType(attachment) }}</p>
                        </div>
                      </div>
                      <button 
                        @click="downloadAttachment(attachment)"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                      >
                        {{ currentLanguage === 'en' ? 'Download' : 'ডাউনলোড' }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </InvestProLayout>
</template>

<script setup>
import { ref, onMounted, reactive, onUnmounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import InvestProLayout from '@/Layouts/InvestProLayout.vue'

const props = defineProps({
  page: {
    type: Object,
    default: () => ({})
  }
})

const loading = ref(true)
const currentLanguage = ref('en')
const slideStates = reactive({})
const autoSlideStates = reactive({})
const slideIntervals = reactive({})

// Process page data with proper defaults
const pageData = computed(() => {
  return props.page || {}
})

const processedSections = computed(() => {
  if (!pageData.value.sections || !Array.isArray(pageData.value.sections)) {
    return []
  }

  return pageData.value.sections.map(section => ({
    page_section_id: section.page_section_id || Math.random().toString(36).substr(2, 9),
    heading: section.heading || '',
    heading_bn: section.heading_bn || '',
    description: section.description || '',
    description_bn: section.description_bn || '',
    content: section.content || '',
    content_bn: section.content_bn || '',
    attachments: Array.isArray(section.attachments) ? section.attachments : [],
    content_width: section.content_width || '50%',
    attachment_width: section.attachment_width || '50%',
    content_allignment: section.content_allignment || 'L',
    attachment_allignment: section.attachment_allignment || 'R',
    section_order: section.section_order || 0,
    hasContent: !!(section.content || section.content_bn),
    hasAttachments: !!(section.attachments && section.attachments.length > 0)
  }))
})

// Content checking methods
const sectionHasContent = (section) => {
  return !!(section.content || section.content_bn)
}

const sectionHasAttachments = (section) => {
  return !!(section.attachments && section.attachments.length > 0)
}

// Language methods
const setLanguage = (lang) => {
  currentLanguage.value = lang
}

const getSectionHeading = (section) => {
  const heading = currentLanguage.value === 'en' ? section.heading : (section.heading_bn || section.heading)
  return heading && heading.trim() ? heading : null
}

const getSectionDescription = (section) => {
  const description = currentLanguage.value === 'en' ? section.description : (section.description_bn || section.description)
  return description && description.trim() ? description : null
}

const getSectionContent = (section) => {
  const content = currentLanguage.value === 'en' ? section.content : (section.content_bn || section.content)
  return content && content.trim() ? content : null
}

// Auto-slide methods
const startAutoSlide = (sectionId) => {
  if (!autoSlideStates[sectionId]) {
    autoSlideStates[sectionId] = { paused: false }
  }
  
  if (slideIntervals[sectionId]) {
    clearInterval(slideIntervals[sectionId])
  }
  
  slideIntervals[sectionId] = setInterval(() => {
    if (!autoSlideStates[sectionId]?.paused) {
      nextSlide(sectionId)
    }
  }, 5000)
}

const pauseAutoSlide = (sectionId) => {
  if (autoSlideStates[sectionId]) {
    autoSlideStates[sectionId].paused = true
  }
}

const resumeAutoSlide = (sectionId) => {
  if (autoSlideStates[sectionId]) {
    autoSlideStates[sectionId].paused = false
  }
}

const toggleAutoSlide = (sectionId) => {
  if (autoSlideStates[sectionId]) {
    autoSlideStates[sectionId].paused = !autoSlideStates[sectionId].paused
  }
}

const stopAutoSlide = (sectionId) => {
  if (slideIntervals[sectionId]) {
    clearInterval(slideIntervals[sectionId])
    slideIntervals[sectionId] = null
  }
}

// Alignment helper methods
const getHeaderAlignmentClass = (section) => {
  if (section.content_allignment === 'C') {
    return 'text-center mx-auto max-w-4xl'
  } else if (section.content_allignment === 'R') {
    return 'text-right ml-auto max-w-4xl'
  }
  return 'text-left max-w-4xl'
}

const getContentAlignmentClass = (section) => {
  if (section.content_allignment === 'C') {
    return 'text-center mx-auto'
  } else if (section.content_allignment === 'R') {
    return 'text-right ml-auto'
  }
  return 'text-left'
}

// Layout helper methods
const getSectionLayoutClasses = (section) => {
  const classes = ['section-layout']
  
  if (section.hasContent && section.hasAttachments) {
    classes.push('grid-layout')
    
    if (section.content_allignment === 'C') {
      classes.push('content-center')
    } else if (section.content_allignment === 'R') {
      classes.push('content-right')
    } else {
      classes.push('content-left')
    }
  } else {
    classes.push('single-column')
    
    if (section.content_allignment === 'C') {
      classes.push('section-center')
    } else if (section.content_allignment === 'R') {
      classes.push('section-right')
    }
  }
  
  return classes.join(' ')
}

const getContentClasses = (section) => {
  const classes = ['content-area']
  
  if (section.hasAttachments) {
    const widthClass = getWidthClass(section.content_width)
    if (widthClass) {
      classes.push(widthClass)
    }
    
    if (section.content_allignment === 'R') {
      classes.push('order-second')
    } else if (section.content_allignment === 'C') {
      classes.push('order-first', 'justify-center')
    } else {
      classes.push('order-first')
    }
  } else {
    classes.push('full-width')
    
    if (section.content_allignment === 'C') {
      classes.push('text-center', 'mx-auto')
    } else if (section.content_allignment === 'R') {
      classes.push('text-right', 'ml-auto')
    }
  }
  
  return classes.join(' ')
}

const getAttachmentClasses = (section) => {
  const classes = ['attachments-area']
  
  if (section.hasContent) {
    const widthClass = getWidthClass(section.attachment_width)
    if (widthClass) {
      classes.push(widthClass)
    }
    
    if (section.content_allignment === 'R') {
      classes.push('order-first')
    } else if (section.content_allignment === 'C') {
      classes.push('order-second', 'justify-center')
    } else {
      classes.push('order-second')
    }
  } else {
    classes.push('full-width')
    
    if (section.content_allignment === 'C') {
      classes.push('text-center', 'mx-auto')
    } else if (section.content_allignment === 'R') {
      classes.push('text-right', 'ml-auto')
    }
  }
  
  return classes.join(' ')
}

const getWidthClass = (width) => {
  const widthMap = {
    '25%': 'width-25',
    '50%': 'width-50',
    '75%': 'width-75',
    '100%': 'width-100'
  }
  
  return widthMap[width] || 'width-50'
}

const getCurrentSlide = (sectionId) => {
  return slideStates[sectionId] || 0
}

const getFileName = (path) => {
  if (!path) return 'Unknown File'
  return path.split('/').pop() || 'Unknown File'
}

const getFileType = (filename) => {
  if (!filename) return 'FILE'
  const ext = filename.split('.').pop()?.toUpperCase()
  return ext || 'FILE'
}

const isImage = (filename) => {
  if (!filename) return false
  const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.svg']
  return imageExtensions.some(ext => filename.toLowerCase().includes(ext))
}

const getAttachmentUrl = (attachmentPath) => {
  if (!attachmentPath) return ''
  return `/storage/${attachmentPath}`
}

const getImageAttachments = (attachments) => {
  if (!attachments || !Array.isArray(attachments)) return []
  return attachments.filter(att => att && isImage(att))
}

const getFileAttachments = (attachments) => {
  if (!attachments || !Array.isArray(attachments)) return []
  return attachments.filter(att => att && !isImage(att))
}

const hasMultipleImages = (attachments) => {
  return getImageAttachments(attachments).length > 1
}

const hasSingleImage = (attachments) => {
  return getImageAttachments(attachments).length === 1
}

const hasFileAttachments = (attachments) => {
  return getFileAttachments(attachments).length > 0
}

const nextSlide = (sectionId) => {
  if (!slideStates[sectionId]) {
    slideStates[sectionId] = 0
  }
  const section = processedSections.value.find(s => s.page_section_id === sectionId)
  const images = section ? getImageAttachments(section.attachments) : []
  slideStates[sectionId] = (slideStates[sectionId] + 1) % images.length
}

const prevSlide = (sectionId) => {
  if (!slideStates[sectionId]) {
    slideStates[sectionId] = 0
  }
  const section = processedSections.value.find(s => s.page_section_id === sectionId)
  const images = section ? getImageAttachments(section.attachments) : []
  slideStates[sectionId] = (slideStates[sectionId] - 1 + images.length) % images.length
}

const goToSlide = (sectionId, index) => {
  slideStates[sectionId] = index
}

const downloadAttachment = (attachmentPath) => {
  if (!attachmentPath) return
  const link = document.createElement('a')
  link.href = `/storage/${attachmentPath}`
  link.download = getFileName(attachmentPath)
  link.click()
}

const handleImageError = (event) => {
  console.warn('Image failed to load:', event.target.src)
  event.target.style.display = 'none'
}

const goBack = () => {
  window.history.back()
}

onMounted(() => {
  // Simulate loading to ensure data is processed
  setTimeout(() => {
    loading.value = false
  }, 100)
  
  // Initialize auto-slide for sections with multiple images
  processedSections.value.forEach(section => {
    if (section.attachments && hasMultipleImages(section.attachments)) {
      slideStates[section.page_section_id] = 0
      startAutoSlide(section.page_section_id)
    }
  })
})

onUnmounted(() => {
  Object.keys(slideIntervals).forEach(sectionId => {
    stopAutoSlide(sectionId)
  })
})
</script>

<style scoped>
/* Custom slider styles */
.slider-container {
  scroll-behavior: smooth;
}

/* Prose styles for content */
.prose {
  line-height: 1.75;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
  margin-top: 1.5em;
  margin-bottom: 0.5em;
  font-weight: 600;
}

.prose p {
  margin-bottom: 1em;
}

.prose ul, .prose ol {
  margin-bottom: 1em;
  padding-left: 1.5em;
}

.prose li {
  margin-bottom: 0.5em;
}

/* Layout styles for sections */
.section-layout {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.single-column {
  /* No special styles needed for single column */
}

.section-center {
  text-align: center;
}

.section-right {
  text-align: right;
}

.grid-layout {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

/* Desktop layout */
@media (min-width: 768px) {
  .grid-layout {
    grid-template-columns: 1fr 1fr;
  }
  
  .content-right .content-area {
    order: 2;
  }
  
  .content-right .attachments-area {
    order: 1;
  }
  
  .content-left .content-area {
    order: 1;
  }
  
  .content-left .attachments-area {
    order: 2;
  }
  
  .content-center .content-area {
    order: 1;
    justify-self: center;
  }
  
  .content-center .attachments-area {
    order: 2;
    justify-self: center;
  }
  
  /* Width classes */
  .width-25 {
    grid-column: span 1;
  }
  
  .width-50 {
    grid-column: span 1;
  }
  
  .width-75 {
    grid-column: span 2;
  }
  
  .width-100 {
    grid-column: span 2;
  }
}

/* Mobile layout */
@media (max-width: 767px) {
  .section-layout {
    flex-direction: column;
  }
  
  .content-area,
  .attachments-area {
    width: 100%;
  }
  
  .order-first,
  .order-second {
    order: unset !important;
  }
  
  .justify-center {
    justify-content: flex-start !important;
  }
}

/* Content and attachment area base styles */
.content-area,
.attachments-area {
  min-width: 0;
}

.full-width {
  width: 100%;
}

/* Alignment utilities */
.text-center {
  text-align: center;
}

.text-right {
  text-align: right;
}

.text-left {
  text-align: left;
}

.mx-auto {
  margin-left: auto;
  margin-right: auto;
}

.ml-auto {
  margin-left: auto;
}

.mr-auto {
  margin-right: auto;
}

.justify-center {
  display: flex;
  justify-content: center;
}

/* Ensure images don't overflow */
.attachments-area img {
  max-width: 100%;
  height: auto;
}

/* Smooth transitions for slider */
.flex {
  transition: transform 0.5s ease-in-out;
}

/* Header alignment */
.max-w-4xl {
  max-width: 56rem;
}
</style>
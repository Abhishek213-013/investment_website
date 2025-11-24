<template>
  <header>
    <div id="header-fixed-height"></div>
    <div id="sticky-header" class="header-area">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="menu-wrap">
              <nav class="main-nav">
                <!-- SECTION 1: Logo -->
                <div class="logo-section">
                  <div class="logo">
                    <Link href="/">
                      <div class="logo-container">
                        <img src="/assets/logo.png" alt="InvestPro" class="logo-image">
                      </div>
                    </Link>
                  </div>
                </div>

                <!-- SECTION 2: Navigation Components -->
                <div class="nav-components-section">
                  <ul class="navigation">
                    <li><Link href="/">{{ t('Home') }}</Link></li>
                    
                    <!-- About Us with Dropdown -->
                    <li class="dropdown-parent" 
                        @mouseenter="handleMouseEnter('about')" 
                        @mouseleave="handleMouseLeave('about')">
                      <a href="#" class="dropdown-trigger" @click.prevent="toggleDropdown('about')">
                        {{ t('About Us') }}
                        <i class="fas fa-chevron-down dropdown-arrow" :class="{ 'rotate': dropdowns.about.open }"></i>
                      </a>
                      
                      <!-- About Us Dropdown -->
                      <div class="dropdown-menu" 
                           v-show="dropdowns.about.open" 
                           v-click-outside="() => closeDropdown('about')"
                           @mouseenter="keepDropdownOpen('about')"
                           @mouseleave="handleDropdownMouseLeave('about')">
                        <div class="dropdown-content">
                          <Link href="/about" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-building"></i>
                            <span>{{ t('About InvestPro') }}</span>
                          </Link>
                          
                          <Link href="/about/vision-mission" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-bullseye"></i>
                            <span>{{ t('Vision, Mission, Values') }}</span>
                          </Link>
                          
                          <Link href="/about/board" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-users"></i>
                            <span>{{ t('Board Of Directors') }}</span>
                          </Link>
                          
                          <Link href="/about/message-md" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-envelope"></i>
                            <span>{{ t('Message From MD') }}</span>
                          </Link>
                          
                          <Link href="/about/gallery" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-images"></i>
                            <span>{{ t('Gallery') }}</span>
                          </Link>
                        </div>
                      </div>
                    </li>
                    
                    <!-- Services with Dropdown -->
                    <li class="dropdown-parent" 
                        @mouseenter="handleMouseEnter('services')" 
                        @mouseleave="handleMouseLeave('services')">
                      <a href="#" class="dropdown-trigger" @click.prevent="toggleDropdown('services')">
                        {{ t('Services') }}
                        <i class="fas fa-chevron-down dropdown-arrow" :class="{ 'rotate': dropdowns.services.open }"></i>
                      </a>
                      
                      <!-- Services Dropdown -->
                      <div class="dropdown-menu" 
                           v-show="dropdowns.services.open" 
                           v-click-outside="() => closeDropdown('services')"
                           @mouseenter="keepDropdownOpen('services')"
                           @mouseleave="handleDropdownMouseLeave('services')">
                        <div class="dropdown-content">
                          <Link href="/services" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-cogs"></i>
                            <span>{{ t('All Services') }}</span>
                          </Link>
                          
                          <Link href="/services/offline" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-store"></i>
                            <span>{{ t('Offline Services') }}</span>
                          </Link>
                          
                          <Link href="/services/online" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-globe"></i>
                            <span>{{ t('Online Services') }}</span>
                          </Link>
                        </div>
                      </div>
                    </li>
                    
                    <!-- News with Dropdown -->
                    <li class="dropdown-parent" 
                        @mouseenter="handleMouseEnter('news')" 
                        @mouseleave="handleMouseLeave('news')">
                      <a href="#" class="dropdown-trigger" @click.prevent="toggleDropdown('news')">
                        {{ t('News') }}
                        <i class="fas fa-chevron-down dropdown-arrow" :class="{ 'rotate': dropdowns.news.open }"></i>
                      </a>
                      
                      <!-- News Dropdown -->
                      <div class="dropdown-menu" 
                           v-show="dropdowns.news.open" 
                           v-click-outside="() => closeDropdown('news')"
                           @mouseenter="keepDropdownOpen('news')"
                           @mouseleave="handleDropdownMouseLeave('news')">
                        <div class="dropdown-content">
                          <Link href="/news" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-newspaper"></i>
                            <span>{{ t('All News') }}</span>
                          </Link>
                          
                          <Link href="/news/ssl" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-chart-line"></i>
                            <span>{{ t('SSL News') }}</span>
                          </Link>
                          
                          <Link href="/news/dse" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-building-columns"></i>
                            <span>{{ t('DSE News') }}</span>
                          </Link>
                        </div>
                      </div>
                    </li>
                    
                    <li><Link href="/download">{{ t('Download') }}</Link></li>
                    <li><Link href="/faq">{{ t('FAQ') }}</Link></li>
                    <li><Link href="/contact">{{ t('Contact') }}</Link></li>
                  </ul>
                </div>

                <!-- SECTION 3: Utilities & Profile -->
                <div class="utilities-section">
                  <!-- Search Bar -->
                  <div class="search-utility">
                    <div class="nav-search">
                      <form @submit.prevent="searchContent">
                        <div class="search-input-group" :class="{ 'active': showSuggestions }">
                          <input 
                            v-model="searchQuery" 
                            type="text" 
                            :placeholder="t('Search...')"
                            @input="handleSearchInput"
                            @focus="handleSearchFocus"
                            @blur="onSearchBlur"
                          >
                          <button type="submit"><i class="fas fa-search"></i></button>
                          
                          <!-- Search Suggestions Dropdown -->
                          <div class="search-suggestions" v-show="showSuggestions && (filteredSuggestions.length > 0 || isLoadingSuggestions)">
                            <div class="suggestions-header">
                              <span>{{ t('Search Results') }}</span>
                              <span class="suggestion-count" v-if="!isLoadingSuggestions && filteredSuggestions.length > 0">
                                {{ filteredSuggestions.length }} {{ t('results') }}
                              </span>
                            </div>
                            
                            <div class="loading-suggestions" v-if="isLoadingSuggestions">
                              <i class="fas fa-spinner fa-spin"></i>
                              <span>{{ t('Searching...') }}</span>
                            </div>
                            
                            <div class="suggestions-list" v-else-if="filteredSuggestions.length > 0">
                              <button 
                                v-for="item in filteredSuggestions" 
                                :key="item.id"
                                @click="selectSuggestion(item)"
                                class="suggestion-item"
                                type="button"
                              >
                                <i :class="item.icon"></i>
                                <div class="item-info">
                                  <div class="item-title">{{ item.name }}</div>
                                  <div class="item-details">
                                    <span class="item-type">{{ item.type }}</span>
                                  </div>
                                </div>
                              </button>
                            </div>
                            
                            <div class="no-suggestions" v-else-if="searchQuery.trim()">
                              <i class="fas fa-search"></i>
                              <span>{{ t('No results found for') }} "{{ searchQuery }}"</span>
                            </div>
                            
                            <div class="no-suggestions" v-else>
                              <i class="fas fa-search"></i>
                              <span>{{ t('Start typing to search') }}</span>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>

                  <!-- Theme Toggle -->
                  <div class="theme-utility">
                    <button 
                      @click="toggleTheme"
                      class="theme-btn-nav"
                      :title="currentTheme === 'light' ? t('Switch to dark mode') : t('Switch to light mode')"
                      type="button"
                    >
                      <i v-if="currentTheme === 'light'" class="fas fa-moon"></i>
                      <i v-else class="fas fa-sun"></i>
                    </button>
                  </div>

                  <!-- Login/Register -->
                  <div class="auth-utility" v-if="!$page.props.auth?.user">
                    <div class="auth-buttons-simple">
                      <Link href="/login" class="text-link">{{ t('Login') }}</Link>
                      <span class="separator">|</span>
                      <Link href="/register" class="text-link primary">{{ t('Get Started') }}</Link>
                    </div>
                  </div>

                  <!-- User Profile -->
                  <div class="profile-utility" v-else>
                    <div class="profile-wrapper">
                      <button class="profile-trigger" @click="toggleProfileDropdown" type="button">
                        <img 
                          v-if="userAvatar && !avatarError" 
                          :src="userAvatar" 
                          alt="Profile" 
                          class="avatar-image"
                          @error="handleAvatarError"
                          @load="handleAvatarLoad"
                        >
                        <i v-else class="fas fa-user-circle fallback-avatar"></i>
                        <i class="fas fa-chevron-down" :class="{ 'rotate': profileOpen }"></i>
                      </button>
                      
                      <div class="profile-dropdown" v-show="profileOpen" v-click-outside="closeProfileDropdown">
                        <div class="dropdown-header">
                          <div class="user-info">
                            <img 
                              v-if="userAvatar && !avatarError" 
                              :src="userAvatar" 
                              alt="Profile" 
                              class="user-avatar-image"
                              @error="handleAvatarError"
                              @load="handleAvatarLoad"
                            >
                            <i v-else class="fas fa-user-circle user-avatar fallback-avatar"></i>
                            <div class="user-details">
                              <div class="user-name">{{ $page.props.auth?.user?.name }}</div>
                              <div class="user-email">{{ $page.props.auth?.user?.email }}</div>
                              <div class="user-account" v-if="$page.props.auth?.user?.account_type">
                                {{ t('Account') }}: {{ $page.props.auth?.user?.account_type }}
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="dropdown-divider"></div>
                        
                        <div class="dropdown-menu-items">
                          <button @click="navigateToDashboard" class="profile-dropdown-item" type="button">
                            <i class="fas fa-chart-line"></i>
                            <span class="dropdown-text">{{ t('Dashboard') }}</span>
                          </button>
                          <button @click="navigateToPortfolio" class="profile-dropdown-item" type="button">
                            <i class="fas fa-briefcase"></i>
                            <span class="dropdown-text">{{ t('My Portfolio') }}</span>
                          </button>
                          <button @click="navigateToProfile" class="profile-dropdown-item" type="button">
                            <i class="fas fa-user"></i>
                            <span class="dropdown-text">{{ t('My Profile') }}</span>
                          </button>
                          <button @click="navigateToSettings" class="profile-dropdown-item" type="button">
                            <i class="fas fa-cog"></i>
                            <span class="dropdown-text">{{ t('Settings') }}</span>
                          </button>
                          
                          <div class="dropdown-divider"></div>
                          
                          <button class="profile-dropdown-item logout" @click="logout" :disabled="isLoggingOut" type="button">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="dropdown-text">
                              {{ isLoggingOut ? t('Logging out...') : t('Logout') }}
                            </span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Mobile Menu Toggler -->
                <div class="mobile-menu-toggler" @click="toggleMobileMenu">
                  <i class="fas fa-bars"></i>
                </div>
              </nav>
            </div>

            <!-- Mobile Menu -->
            <div class="mobile-menu" :class="{ 'open': mobileOpen }">
              <div class="mobile-menu-content">
                <div class="mobile-menu-header">
                  <div class="mobile-logo">
                    <Link href="/" @click="closeAll">
                      <div class="logo-container mobile-logo-container">
                        <img src="/assets/logo.png" alt="InvestPro" class="logo-image">
                      </div>
                    </Link>
                  </div>
                  
                  <!-- Theme Toggle in Mobile -->
                  <div class="mobile-controls">
                    <div class="mobile-theme-switcher">
                      <button 
                        @click="toggleTheme"
                        class="theme-btn-mobile"
                        :title="currentTheme === 'light' ? t('Switch to dark mode') : t('Switch to light mode')"
                        type="button"
                      >
                        <i v-if="currentTheme === 'light'" class="fas fa-moon"></i>
                        <i v-else class="fas fa-sun"></i>
                      </button>
                    </div>
                    
                    <div class="mobile-menu-close" @click="closeAll">
                      <i class="fas fa-times"></i>
                    </div>
                  </div>
                </div>
                
                <!-- Mobile Search -->
                <div class="mobile-search">
                  <form @submit.prevent="searchContent">
                    <div class="mobile-search-wrapper" :class="{ 'active': showMobileSuggestions }">
                      <input 
                        v-model="searchQuery" 
                        type="text" 
                        :placeholder="t('Search...')"
                        @input="handleSearchInput"
                        @focus="handleSearchFocus"
                        @blur="onMobileSearchBlur"
                      >
                      <button type="submit"><i class="fas fa-search"></i></button>
                      
                      <!-- Mobile Search Suggestions -->
                      <div class="mobile-search-suggestions" v-show="showMobileSuggestions && (filteredSuggestions.length > 0 || isLoadingSuggestions)">
                        <div class="mobile-suggestions-header">
                          <span>{{ t('Search Results') }}</span>
                        </div>
                        
                        <div class="mobile-loading-suggestions" v-if="isLoadingSuggestions">
                          <i class="fas fa-spinner fa-spin"></i>
                          <span>{{ t('Searching...') }}</span>
                        </div>
                        
                        <div class="mobile-suggestions-list" v-else-if="filteredSuggestions.length > 0">
                          <button 
                            v-for="item in filteredSuggestions" 
                            :key="item.id"
                            @click="selectSuggestion(item)"
                            class="mobile-suggestion-item"
                            type="button"
                          >
                            <i :class="item.icon"></i>
                            <div class="mobile-item-info">
                              <div class="mobile-item-title">{{ item.name }}</div>
                              <div class="mobile-item-details">
                                <span>{{ item.type }}</span>
                              </div>
                            </div>
                          </button>
                        </div>
                        
                        <div class="mobile-no-suggestions" v-else-if="searchQuery.trim()">
                          <i class="fas fa-search"></i>
                          <span>{{ t('No results found for') }} "{{ searchQuery }}"</span>
                        </div>
                        
                        <div class="mobile-no-suggestions" v-else>
                          <i class="fas fa-search"></i>
                          <span>{{ t('Start typing to search') }}</span>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
                
                <div class="mobile-navigation">
                  <ul class="mobile-nav-list">
                    <li><Link href="/" @click="closeAll">{{ t('Home') }}</Link></li>
                    
                    <!-- Mobile About Us Dropdown -->
                    <li class="mobile-dropdown-parent">
                      <button class="mobile-dropdown-trigger" @click="toggleMobileDropdown('about')">
                        <span>{{ t('About Us') }}</span>
                        <i class="fas fa-chevron-down mobile-dropdown-arrow" :class="{ 'rotate': mobileDropdowns.about.open }"></i>
                      </button>
                      
                      <div class="mobile-dropdown-menu" v-show="mobileDropdowns.about.open">
                        <Link href="/about" class="mobile-dropdown-item" @click="closeAll">
                          <i class="fas fa-building"></i>
                          <span>{{ t('About InvestPro') }}</span>
                        </Link>
                        
                        <Link href="/about/vision-mission" class="mobile-dropdown-item" @click="closeAll">
                          <i class="fas fa-bullseye"></i>
                          <span>{{ t('Vision, Mission, Values') }}</span>
                        </Link>
                        
                        <Link href="/about/board" class="mobile-dropdown-item" @click="closeAll">
                          <i class="fas fa-users"></i>
                          <span>{{ t('Board Of Directors') }}</span>
                        </Link>
                        
                        <Link href="/about/message-md" class="mobile-dropdown-item" @click="closeAll">
                          <i class="fas fa-envelope"></i>
                          <span>{{ t('Message From MD') }}</span>
                        </Link>
                        
                        <Link href="/about/gallery" class="mobile-dropdown-item" @click="closeAll">
                          <i class="fas fa-images"></i>
                          <span>{{ t('Gallery') }}</span>
                        </Link>
                      </div>
                    </li>
                    
                    <!-- Mobile Services Dropdown -->
                    <li class="mobile-dropdown-parent">
                      <button class="mobile-dropdown-trigger" @click="toggleMobileDropdown('services')">
                        <span>{{ t('Services') }}</span>
                        <i class="fas fa-chevron-down mobile-dropdown-arrow" :class="{ 'rotate': mobileDropdowns.services.open }"></i>
                      </button>
                      
                      <div class="mobile-dropdown-menu" v-show="mobileDropdowns.services.open">
                        <Link href="/services" class="mobile-dropdown-item" @click="closeAll">
                          <i class="fas fa-cogs"></i>
                          <span>{{ t('All Services') }}</span>
                        </Link>
                        
                        <Link href="/services/offline" class="mobile-dropdown-item" @click="closeAll">
                          <i class="fas fa-store"></i>
                          <span>{{ t('Offline Services') }}</span>
                        </Link>
                        
                        <Link href="/services/online" class="mobile-dropdown-item" @click="closeAll">
                          <i class="fas fa-globe"></i>
                          <span>{{ t('Online Services') }}</span>
                        </Link>
                      </div>
                    </li>
                    
                    <!-- Mobile News Dropdown -->
                    <li class="mobile-dropdown-parent">
                      <button class="mobile-dropdown-trigger" @click="toggleMobileDropdown('news')">
                        <span>{{ t('News') }}</span>
                        <i class="fas fa-chevron-down mobile-dropdown-arrow" :class="{ 'rotate': mobileDropdowns.news.open }"></i>
                      </button>
                      
                      <div class="mobile-dropdown-menu" v-show="mobileDropdowns.news.open">
                        <Link href="/news" class="mobile-dropdown-item" @click="closeAll">
                          <i class="fas fa-newspaper"></i>
                          <span>{{ t('All News') }}</span>
                        </Link>
                        
                        <Link href="/news/ssl" class="mobile-dropdown-item" @click="closeAll">
                          <i class="fas fa-chart-line"></i>
                          <span>{{ t('SSL News') }}</span>
                        </Link>
                        
                        <Link href="/news/dse" class="mobile-dropdown-item" @click="closeAll">
                          <i class="fas fa-building-columns"></i>
                          <span>{{ t('DSE News') }}</span>
                        </Link>
                      </div>
                    </li>
                    
                    <li><Link href="/download" @click="closeAll">{{ t('Download') }}</Link></li>
                    <li><Link href="/faq" @click="closeAll">{{ t('FAQ') }}</Link></li>
                    <li><Link href="/contact" @click="closeAll">{{ t('Contact') }}</Link></li>
                    
                    <template v-if="$page.props.auth?.user">
                      <li class="mobile-profile-section">
                        <div class="mobile-profile-header">
                          <div class="mobile-user-info">
                            <img 
                              v-if="userAvatar && !avatarError" 
                              :src="userAvatar" 
                              alt="Profile" 
                              class="mobile-avatar-image"
                              @error="handleAvatarError"
                              @load="handleAvatarLoad"
                            >
                            <i v-else class="fas fa-user-circle fallback-avatar"></i>
                            <div class="mobile-user-details">
                              <div class="mobile-user-name">{{ $page.props.auth.user.name }}</div>
                              <div class="mobile-user-email">{{ $page.props.auth.user.email }}</div>
                            </div>
                          </div>
                        </div>
                      </li>
                      <li><button @click="navigateToDashboardMobile" class="mobile-nav-btn" type="button"><i class="fas fa-chart-line"></i>{{ t('Dashboard') }}</button></li>
                      <li><button @click="navigateToPortfolioMobile" class="mobile-nav-btn" type="button"><i class="fas fa-briefcase"></i>{{ t('My Portfolio') }}</button></li>
                      <li><button @click="navigateToProfileMobile" class="mobile-nav-btn" type="button"><i class="fas fa-user"></i>{{ t('My Profile') }}</button></li>
                      <li><button @click="navigateToSettingsMobile" class="mobile-nav-btn" type="button"><i class="fas fa-cog"></i>{{ t('Settings') }}</button></li>
                      <li><button @click="logoutMobile" class="mobile-logout-btn" :disabled="isLoggingOut" type="button">
                        <i class="fas fa-sign-out-alt"></i>
                        {{ isLoggingOut ? t('Logging out...') : t('Logout') }}
                      </button></li>
                    </template>
                    <template v-else>
                      <li class="mobile-auth-section">
                        <div class="mobile-auth-simple">
                          <Link href="/login" @click="closeAll" class="mobile-text-link">{{ t('Login') }}</Link>
                          <Link href="/register" @click="closeAll" class="mobile-text-link primary">{{ t('Get Started') }}</Link>
                        </div>
                      </li>
                    </template>
                  </ul>
                </div>
              </div>
            </div>
            
            <div class="mobile-overlay" v-if="mobileOpen" @click="closeAll"></div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, watch, computed, onUnmounted } from 'vue'
import { useTranslation } from '@/composables/useTranslation'

// Use Inertia.js page props
const $page = usePage()

// Use the global translation composable
const { t } = useTranslation()

// Reactive data
const mobileOpen = ref(false)
const profileOpen = ref(false)
const searchQuery = ref('')
const currentTheme = ref('light')
const showSuggestions = ref(false)
const showMobileSuggestions = ref(false)
const contentSuggestions = ref([])
const isLoadingSuggestions = ref(false)
const avatarError = ref(false)
const isLoggingOut = ref(false)
const avatarLoaded = ref(false)

// Dropdown management - FIXED: Proper reactive dropdown management
const dropdowns = ref({
  about: { open: false, clicked: false },
  services: { open: false, clicked: false },
  news: { open: false, clicked: false }
})

const mobileDropdowns = ref({
  about: { open: false },
  services: { open: false },
  news: { open: false }
})

// Computed property for user avatar
const userData = computed(() => {
  return $page.props.auth?.user || null
})

const userAvatar = computed(() => {
  return userData.value?.profile_picture_url || userData.value?.avatar_url || '/assets/img/default-avatar.png'
})

// Dropdown Methods - UPDATED WITH PROPER CLOSING LOGIC
const closeAllDropdowns = (exceptType = null) => {
  Object.keys(dropdowns.value).forEach(key => {
    if (key !== exceptType) {
      dropdowns.value[key].open = false
      dropdowns.value[key].clicked = false
    }
  })
}

const closeAllMobileDropdowns = (exceptType = null) => {
  Object.keys(mobileDropdowns.value).forEach(key => {
    if (key !== exceptType) {
      mobileDropdowns.value[key].open = false
    }
  })
}

const openDropdown = (type) => {
  if (!dropdowns.value[type].clicked) {
    // Close all other dropdowns before opening this one
    closeAllDropdowns(type)
    dropdowns.value[type].open = true
  }
}

const closeDropdown = (type) => {
  // Only close if it wasn't explicitly clicked open
  if (!dropdowns.value[type].clicked) {
    dropdowns.value[type].open = false
  }
}

const keepDropdownOpen = (type) => {
  dropdowns.value[type].open = true
}

const handleMouseEnter = (type) => {
  // Only open on hover if dropdown wasn't clicked
  if (!dropdowns.value[type].clicked) {
    // Close all other dropdowns before opening this one
    closeAllDropdowns(type)
    dropdowns.value[type].open = true
  }
}

const handleMouseLeave = (type, event) => {
  // Only close on hover if dropdown wasn't clicked
  if (!dropdowns.value[type].clicked) {
    const relatedTarget = event.relatedTarget
    if (!relatedTarget || !event.currentTarget.contains(relatedTarget)) {
      setTimeout(() => {
        if (!dropdowns.value[type].open) return
        dropdowns.value[type].open = false
      }, 150)
    }
  }
}

const handleDropdownMouseLeave = (type, event) => {
  // Only close dropdown on mouse leave if it wasn't clicked
  if (!dropdowns.value[type].clicked) {
    const relatedTarget = event.relatedTarget
    if (!relatedTarget || !event.currentTarget.contains(relatedTarget)) {
      setTimeout(() => {
        if (!dropdowns.value[type].open) return
        dropdowns.value[type].open = false
      }, 150)
    }
  }
}

const toggleDropdown = (type) => {
  // Close all other dropdowns first
  closeAllDropdowns(type)
  
  // Toggle the current dropdown
  dropdowns.value[type].clicked = !dropdowns.value[type].open
  dropdowns.value[type].open = !dropdowns.value[type].open
}

const toggleMobileDropdown = (type) => {
  // Close all other mobile dropdowns first
  closeAllMobileDropdowns(type)
  
  // Toggle the current mobile dropdown
  mobileDropdowns.value[type].open = !mobileDropdowns.value[type].open
}

// Theme management
const getCurrentTheme = () => {
  const savedTheme = localStorage.getItem('preferredTheme')
  if (savedTheme === 'dark') {
    return 'dark'
  }
  return 'light'
}

const applyTheme = (theme) => {
  currentTheme.value = theme
  document.documentElement.classList.remove('light-theme', 'dark-theme')
  document.body.classList.remove('light-theme', 'dark-theme')
  
  if (theme === 'dark') {
    document.documentElement.classList.add('dark-theme')
    document.body.classList.add('dark-theme')
  } else {
    document.documentElement.classList.add('light-theme')
    document.body.classList.add('light-theme')
  }
  
  localStorage.setItem('preferredTheme', theme)
  document.documentElement.setAttribute('data-theme', theme)
}

const toggleTheme = () => {
  const newTheme = currentTheme.value === 'light' ? 'dark' : 'light'
  currentTheme.value = newTheme
  applyTheme(newTheme)
  window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }))
  closeAll()
}

const initializeTheme = () => {
  const theme = getCurrentTheme()
  currentTheme.value = theme
  applyTheme(theme)
}

// Avatar handling
const handleAvatarError = (event) => {
  avatarError.value = true
  avatarLoaded.value = false
  if (event.target) {
    event.target.style.display = 'none'
  }
}

const handleAvatarLoad = (event) => {
  avatarError.value = false
  avatarLoaded.value = true
  if (event.target) {
    event.target.style.display = 'block'
  }
}

// Search functionality
const fetchContentSuggestions = async (query = '') => {
  try {
    isLoadingSuggestions.value = true
    // Mock data for InvestPro - replace with actual API call
    const mockData = [
      { id: 1, name: 'Stock Market Analysis', type: 'Service', icon: 'fas fa-chart-line' },
      { id: 2, name: 'Investment Portfolio', type: 'Service', icon: 'fas fa-briefcase' },
      { id: 3, name: 'Market News', type: 'News', icon: 'fas fa-newspaper' },
      { id: 4, name: 'Mobile App', type: 'Download', icon: 'fas fa-mobile-alt' },
      { id: 5, name: 'Investment FAQ', type: 'FAQ', icon: 'fas fa-question-circle' }
    ]
    
    if (query.trim()) {
      const filtered = mockData.filter(item => 
        item.name.toLowerCase().includes(query.toLowerCase()) ||
        item.type.toLowerCase().includes(query.toLowerCase())
      )
      contentSuggestions.value = filtered
    } else {
      contentSuggestions.value = mockData.slice(0, 3)
    }
  } catch (error) {
    console.error('Error fetching suggestions:', error)
    contentSuggestions.value = []
  } finally {
    isLoadingSuggestions.value = false
  }
}

const filteredSuggestions = computed(() => {
  if (!searchQuery.value.trim()) {
    return contentSuggestions.value.slice(0, 3)
  }
  
  const query = searchQuery.value.toLowerCase()
  return contentSuggestions.value.filter(item => 
    item.name.toLowerCase().includes(query) ||
    item.type.toLowerCase().includes(query)
  ).slice(0, 8)
})

let searchTimeout = null
const handleSearchInput = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  
  searchTimeout = setTimeout(() => {
    fetchContentSuggestions(searchQuery.value)
    showSuggestions.value = true
    showMobileSuggestions.value = true
  }, 300)
}

const handleSearchFocus = () => {
  if (contentSuggestions.value.length === 0) {
    fetchContentSuggestions()
  }
  showSuggestions.value = true
  showMobileSuggestions.value = true
}

const onSearchBlur = () => {
  setTimeout(() => {
    showSuggestions.value = false
  }, 200)
}

const onMobileSearchBlur = () => {
  setTimeout(() => {
    showMobileSuggestions.value = false
  }, 200)
}

const selectSuggestion = (item) => {
  searchQuery.value = item.name
  showSuggestions.value = false
  showMobileSuggestions.value = false
  searchContent()
}

const searchContent = () => {
  if (searchQuery.value.trim()) {
    router.visit('/search', { data: { q: searchQuery.value } })
    searchQuery.value = ''
    showSuggestions.value = false
    showMobileSuggestions.value = false
  }
  closeAll()
}

// Navigation methods
const navigateToDashboard = () => {
  closeAll()
  router.visit('/dashboard')
}

const navigateToPortfolio = () => {
  closeAll()
  router.visit('/portfolio')
}

const navigateToProfile = () => {
  closeAll()
  router.visit('/profile')
}

const navigateToSettings = () => {
  closeAll()
  router.visit('/settings')
}

// Mobile Navigation Methods
const navigateToDashboardMobile = () => {
  closeAll()
  router.visit('/dashboard')
}

const navigateToPortfolioMobile = () => {
  closeAll()
  router.visit('/portfolio')
}

const navigateToProfileMobile = () => {
  closeAll()
  router.visit('/profile')
}

const navigateToSettingsMobile = () => {
  closeAll()
  router.visit('/settings')
}

// UI control methods
const toggleProfileDropdown = (event) => {
  event?.stopPropagation()
  // Close all navigation dropdowns when opening profile dropdown
  closeAllDropdowns()
  profileOpen.value = !profileOpen.value
}

const closeProfileDropdown = () => {
  profileOpen.value = false
}

const toggleMobileMenu = () => {
  mobileOpen.value = !mobileOpen.value
  profileOpen.value = false
}

const closeAll = () => {
  mobileOpen.value = false
  profileOpen.value = false
  
  // Close all dropdowns and reset clicked states
  closeAllDropdowns()
  closeAllMobileDropdowns()
  
  showSuggestions.value = false
  showMobileSuggestions.value = false
}

// Logout Methods
const logout = async () => {
  if (isLoggingOut.value) return;
  
  isLoggingOut.value = true;
  try {
    closeAll();
    await router.post('/logout');
  } catch (error) {
    console.error('Logout error:', error);
    window.location.href = '/login';
  } finally {
    isLoggingOut.value = false;
  }
}

const logoutMobile = () => {
  logout();
}

// Lifecycle hooks
onMounted(() => {
  initializeTheme()
  fetchContentSuggestions()
  
  // Listen for theme changes
  window.addEventListener('themeChanged', (event) => {
    currentTheme.value = event.detail.theme
    applyTheme(event.detail.theme)
  })
})

// Watch for auth changes
watch(() => $page.props.auth?.user, (newUser, oldUser) => {
  if (newUser && newUser.id !== (oldUser?.id)) {
    avatarError.value = false
    avatarLoaded.value = false
  } else if (!newUser) {
    avatarError.value = false
  }
})

// Click outside directive
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = function(event) {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event)
      }
    }
    document.body.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.body.removeEventListener('click', el.clickOutsideEvent)
  }
}
</script>

<style scoped>
/* All the CSS styles remain exactly the same as before */
/* The CSS doesn't need changes since we're using the same dropdown structure */

/* The existing CSS handles all the dropdown styling */
/* Only ensure the navigation items have proper spacing */
.navigation {
  gap: 8px;
}

.navigation li a {
  padding: 8px 8px;
}
/* All the CSS styles remain exactly the same as before */
/* ... (previous CSS code) ... */

/* Reset and base styles */
* {
  box-sizing: border-box;
}

/* Header Styles - FIXED SIZE */
.header-area {
  background: var(--bg-primary);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  width: 100%;
  transition: all 0.3s ease;
  border-bottom: 1px solid var(--border-color);
  height: 70px;
  display: flex;
  align-items: center;
}

.container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 10px;
}

.row {
  display: flex;
  flex-wrap: wrap;
  margin: 0 -10px;
}

.col-12 {
  width: 100%;
  padding: 0 10px;
}

.menu-wrap {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0;
  width: 100%;
}

.main-nav {
  display: flex;
  align-items: center;
  width: 100%;
  justify-content: space-between;
  padding: 0;
  gap: 15px;
  max-width: 100%;
  overflow: visible;
  height: 100%;
}

/* Logo Section - More Compact */
.logo-section {
  flex-shrink: 0;
  min-width: 90px;
  height: 100%;
  display: flex;
  align-items: center;
}

.logo-container {
  display: flex;
  align-items: center;
  text-decoration: none;
  transition: opacity 0.3s ease;
  height: 100%;
}

.logo-container:hover {
  opacity: 0.9;
}

.logo-image {
  max-height: 100px;
  width: auto;
  object-fit: contain;
  transition: all 0.3s ease;
}

/* Navigation Section - Optimized for single line */
.nav-components-section {
  flex: 2;
  display: flex;
  justify-content: center;
  min-width: 0;
  max-width: calc(100% - 320px);
  height: 100%;
  display: flex;
  align-items: center;
  overflow: visible;
}

.navigation {
  display: flex;
  align-items: center;
  list-style: none;
  gap: 7px;
  margin: 0;
  padding: 0;
  flex-wrap: nowrap;
  justify-content: center;
  max-width: 100%;
  overflow: visible;
}

.navigation li {
  flex-shrink: 0;
  position: relative;
  overflow: visible;
}

.navigation li a {
  text-decoration: none;
  color: var(--text-primary);
  font-weight: 500;
  font-size: 12px;
  padding: 8px 6px;
  transition: all 0.3s ease;
  white-space: nowrap;
  display: block;
  border-radius: 4px;
}

.navigation li a:hover {
  color: var(--primary-color);
  background: var(--bg-secondary);
}

/* About Us Dropdown - FIXED MOUSE HANDLING */
.dropdown-parent {
  position: relative;
}

.dropdown-trigger {
  display: flex;
  align-items: center;
  gap: 3px;
  text-decoration: none;
  color: var(--text-primary);
  font-weight: 500;
  font-size: 12px;
  padding: 8px 6px;
  transition: all 0.3s ease;
  white-space: nowrap;
  cursor: pointer;
  border-radius: 4px;
  margin: 0;
  background: none;
  border: none;
  font-family: inherit;
}

.dropdown-trigger:hover {
  color: var(--primary-color);
  background: var(--bg-secondary);
}

.dropdown-arrow {
  font-size: 9px;
  transition: transform 0.3s ease;
  margin-left: 1px;
  color: var(--text-muted);
}

.dropdown-arrow.rotate {
  transform: rotate(180deg);
  color: var(--primary-color);
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background: var(--bg-primary);
  min-width: 220px;
  border-radius: 8px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  padding: 8px 0;
  margin-top: 4px;
  z-index: 1003;
  border: 1px solid var(--border-color);
  overflow: visible;
  backdrop-filter: blur(10px);
  animation: dropdownFadeIn 0.2s ease;
  transform-origin: top left;
}

@keyframes dropdownFadeIn {
  from {
    opacity: 0;
    transform: translateY(-6px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.dropdown-content {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  color: var(--text-primary);
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 13px;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  margin: 0;
  position: relative;
  font-weight: 500;
}

.dropdown-item:hover {
  background: var(--bg-secondary);
  color: var(--primary-color);
  padding-left: 20px;
}

.dropdown-item:hover::before {
  content: '';
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 4px;
  height: 4px;
  background: var(--primary-color);
  border-radius: 50%;
  animation: dotFadeIn 0.2s ease;
}

@keyframes dotFadeIn {
  from {
    opacity: 0;
    transform: translateY(-50%) scale(0);
  }
  to {
    opacity: 1;
    transform: translateY(-50%) scale(1);
  }
}

.dropdown-item i {
  width: 14px;
  color: var(--text-muted);
  font-size: 13px;
  text-align: center;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.dropdown-item:hover i {
  color: var(--primary-color);
}

.dropdown-item span {
  flex: 1;
  line-height: 1.4;
}

/* Active state for current page */
.dropdown-item.router-link-active {
  color: var(--primary-color);
  background: var(--bg-secondary);
  padding-left: 20px;
}

.dropdown-item.router-link-active::before {
  content: '';
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 4px;
  height: 4px;
  background: var(--primary-color);
  border-radius: 50%;
}

.dropdown-item.router-link-active i {
  color: var(--primary-color);
}

/* The rest of the CSS remains exactly the same as before */
/* ... (all other CSS styles from previous code) ... */

/* Utilities Section - More Compact */
.utilities-section {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 6px;
  min-width: 150px;
  justify-content: flex-end;
  height: 100%;
}

.search-utility {
  position: relative;
  flex-shrink: 1;
  min-width: 120px;
  max-width: 180px;
}

.nav-search {
  position: relative;
  width: 100%;
}

.search-input-group {
  display: flex;
  align-items: center;
  background: var(--bg-secondary);
  border-radius: 14px;
  padding: 3px 8px;
  width: 100%;
  min-width: 120px;
  transition: all 0.3s ease;
  position: relative;
  border: 1px solid var(--border-color);
}

.search-input-group:hover {
  background: var(--bg-tertiary);
}

.search-input-group.active {
  border-radius: 14px 14px 0 0;
  background: var(--bg-secondary);
}

.search-input-group input {
  border: none;
  background: transparent;
  padding: 3px 0;
  outline: none;
  width: 100%;
  font-size: 11px;
  flex: 1;
  color: var(--text-primary);
  min-width: 0;
}

.search-input-group input::placeholder {
  color: var(--text-muted);
}

.search-input-group button {
  border: none;
  background: transparent;
  color: var(--primary-color);
  cursor: pointer;
  padding: 2px;
  margin-left: 2px;
  transition: color 0.3s ease;
  flex-shrink: 0;
  font-size: 11px;
}

.search-input-group button:hover {
  color: var(--primary-hover);
}

/* Search Suggestions */
.search-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: var(--bg-primary);
  border-radius: 0 0 14px 14px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  z-index: 1001;
  max-height: 300px;
  overflow-y: auto;
  border: 1px solid var(--border-color);
  border-top: none;
  width: 100%;
}

.suggestions-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border-color);
  font-size: 10px;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.suggestion-count {
  font-size: 9px;
  color: var(--text-muted);
  font-weight: 500;
}

.suggestions-list {
  padding: 4px 0;
}

.loading-suggestions,
.no-suggestions {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px;
  color: var(--text-secondary);
  font-size: 11px;
  justify-content: center;
}

.no-suggestions {
  flex-direction: column;
  text-align: center;
}

.no-suggestions i {
  color: var(--text-muted);
  font-size: 16px;
  margin-bottom: 4px;
}

.suggestion-item {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  padding: 8px 12px;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  transition: all 0.3s ease;
  color: var(--text-primary);
}

.suggestion-item:hover {
  background: var(--bg-secondary);
}

.suggestion-item i {
  color: var(--primary-color);
  font-size: 11px;
  width: 12px;
  text-align: center;
  margin-top: 1px;
  flex-shrink: 0;
}

.item-info {
  flex: 1;
  min-width: 0;
}

.item-title {
  font-weight: 500;
  font-size: 11px;
  margin-bottom: 2px;
  line-height: 1.3;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.item-details {
  font-size: 9px;
  color: var(--text-secondary);
}

/* Theme Toggle - Compact */
.theme-utility {
  display: flex;
  flex-shrink: 0;
}

.theme-btn-nav {
  padding: 4px 6px;
  border: none;
  border-radius: 10px;
  background: var(--bg-secondary);
  color: var(--primary-color);
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 11px;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 32px;
  flex-shrink: 0;
}

.theme-btn-nav:hover {
  background: var(--bg-tertiary);
  transform: scale(1.05);
}

/* Auth Buttons - Compact */
.auth-utility {
  display: flex;
  flex-shrink: 0;
}

.auth-buttons-simple {
  display: flex;
  gap: 4px;
  align-items: center;
  flex-wrap: nowrap;
}

.text-link {
  text-decoration: none;
  font-weight: 500;
  font-size: 12px;
  color: var(--text-primary);
  transition: color 0.3s ease;
  white-space: nowrap;
  padding: 4px 0;
  flex-shrink: 0;
}

.text-link.primary {
  font-weight: 600;
  color: var(--primary-color);
}

.text-link:hover {
  color: var(--primary-color);
}

.text-link.primary:hover {
  color: var(--primary-hover);
}

.separator {
  color: var(--border-color);
  font-size: 12px;
  flex-shrink: 0;
}

/* Profile Dropdown - Compact */
.profile-utility {
  position: relative;
  flex-shrink: 0;
}

.profile-wrapper {
  position: relative;
}

.profile-trigger {
  display: flex;
  align-items: center;
  gap: 4px;
  background: var(--bg-secondary);
  padding: 4px 8px;
  border-radius: 12px;
  border: none;
  color: var(--text-primary);
  font-weight: 500;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
  max-width: 140px;
}

.profile-trigger:hover {
  background: var(--bg-tertiary);
}

.profile-trigger .fa-chevron-down {
  font-size: 9px;
  transition: transform 0.3s ease;
  margin-left: 1px;
  flex-shrink: 0;
}

.rotate {
  transform: rotate(180deg);
}

.avatar-image {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  object-fit: cover;
  border: 1px solid var(--border-color);
  flex-shrink: 0;
}

.fallback-avatar {
  color: var(--primary-color);
  font-size: 20px;
  flex-shrink: 0;
}

.profile-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  background: var(--bg-primary);
  min-width: 240px;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  padding: 0;
  margin-top: 4px;
  z-index: 1001;
  border: 1px solid var(--border-color);
  overflow: hidden;
  width: max-content;
}

.dropdown-header {
  padding: 12px 16px;
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border-color);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.user-avatar-image {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--primary-color);
  flex-shrink: 0;
}

.user-details {
  flex: 1;
  min-width: 0;
}

.user-name {
  font-weight: 600;
  font-size: 12px;
  color: var(--text-primary);
  margin-bottom: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-email {
  font-size: 10px;
  color: var(--text-secondary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-account {
  font-size: 9px;
  color: var(--text-muted);
  font-weight: 500;
}

.dropdown-menu-items {
  padding: 6px 6px;
}

.profile-dropdown-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  color: var(--text-primary);
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 12px;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  margin: 0;
}

.profile-dropdown-item:hover {
  background: var(--bg-secondary);
  color: var(--primary-color);
}

.profile-dropdown-item i {
  width: 12px;
  color: var(--text-muted);
  font-size: 11px;
  text-align: center;
  flex-shrink: 0;
}

.profile-dropdown-item:hover i {
  color: var(--primary-color);
}

.dropdown-text {
  flex: 1;
  font-weight: 500;
}

.dropdown-divider {
  height: 1px;
  background: var(--border-color);
  margin: 4px 0;
  border: none;
}

.logout {
  color: #ef4444;
}

.logout:hover {
  background: #fef2f2;
  color: #dc2626;
}

.dark-theme .logout:hover {
  background: #7f1d1d;
  color: #fca5a5;
}

.logout:hover i {
  color: #dc2626;
}

.logout:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.logout:disabled:hover {
  background: transparent;
  color: #ef4444;
}

/* Mobile Menu Toggler */
.mobile-menu-toggler {
  display: none;
  cursor: pointer;
  font-size: 20px;
  color: var(--text-primary);
  padding: 4px;
  transition: color 0.3s ease;
  flex-shrink: 0;
}

.mobile-menu-toggler:hover {
  color: var(--primary-color);
}

/* Mobile Menu - Fixed Height Header */
.mobile-menu {
  position: fixed;
  top: 0;
  left: -100%;
  width: 280px;
  height: 100vh;
  background: var(--bg-primary);
  z-index: 1002;
  transition: left 0.3s ease;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.mobile-menu.open {
  left: 0;
}

.mobile-menu-content {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.mobile-menu-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px;
  border-bottom: 1px solid var(--border-color);
  height: 70px;
}

.mobile-controls {
  display: flex;
  align-items: center;
  gap: 4px;
}

.theme-btn-mobile {
  padding: 4px 6px;
  border: none;
  border-radius: 10px;
  background: var(--bg-secondary);
  color: var(--primary-color);
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 11px;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 32px;
}

.theme-btn-mobile:hover {
  background: var(--bg-tertiary);
  transform: scale(1.05);
}

.mobile-menu-close {
  cursor: pointer;
  font-size: 16px;
  color: var(--text-primary);
  padding: 4px;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.mobile-menu-close:hover {
  background: var(--bg-secondary);
}

/* Mobile Search */
.mobile-search {
  padding: 14px;
  border-bottom: 1px solid var(--border-color);
  position: relative;
}

.mobile-search-wrapper {
  position: relative;
}

.mobile-search-wrapper.active {
  z-index: 1003;
}

.mobile-search form {
  display: flex;
  background: var(--bg-secondary);
  border-radius: 14px;
  padding: 4px 8px;
  border: 1px solid var(--border-color);
  position: relative;
}

.mobile-search-wrapper.active form {
  border-radius: 14px 14px 0 0;
  background: var(--bg-secondary);
}

.mobile-search form:hover {
  background: var(--bg-tertiary);
}

.mobile-search input {
  border: none;
  background: transparent;
  flex: 1;
  outline: none;
  font-size: 12px;
  color: var(--text-primary);
  min-width: 0;
}

.mobile-search input::placeholder {
  color: var(--text-muted);
}

.mobile-search button {
  border: none;
  background: transparent;
  color: var(--primary-color);
  cursor: pointer;
  margin-left: 4px;
  transition: color 0.3s ease;
  flex-shrink: 0;
  font-size: 11px;
}

.mobile-search button:hover {
  color: var(--primary-hover);
}

.mobile-search-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: var(--bg-primary);
  border-radius: 0 0 14px 14px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  z-index: 1002;
  max-height: 250px;
  overflow-y: auto;
  border: 1px solid var(--border-color);
  border-top: none;
}

.mobile-suggestions-header {
  padding: 8px 12px;
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border-color);
  font-size: 10px;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.mobile-suggestions-list {
  padding: 4px 0;
}

.mobile-loading-suggestions,
.mobile-no-suggestions {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px;
  color: var(--text-secondary);
  font-size: 11px;
  justify-content: center;
}

.mobile-no-suggestions {
  flex-direction: column;
  text-align: center;
}

.mobile-no-suggestions i {
  color: var(--text-muted);
  font-size: 16px;
  margin-bottom: 4px;
}

.mobile-suggestion-item {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  padding: 8px 12px;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  transition: all 0.3s ease;
  color: var(--text-primary);
}

.mobile-suggestion-item:hover {
  background: var(--bg-secondary);
}

.mobile-suggestion-item i {
  color: var(--primary-color);
  font-size: 11px;
  width: 12px;
  text-align: center;
  margin-top: 1px;
  flex-shrink: 0;
}

.mobile-item-info {
  flex: 1;
  min-width: 0;
}

.mobile-item-title {
  font-weight: 500;
  font-size: 11px;
  margin-bottom: 2px;
  line-height: 1.3;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.mobile-item-details {
  font-size: 9px;
  color: var(--text-secondary);
}

/* Mobile Navigation */
.mobile-navigation {
  flex: 1;
  overflow-y: auto;
  padding: 14px 0;
}

.mobile-nav-list {
  list-style: none;
  margin: 0;
  padding: 0;
}

.mobile-nav-list li {
  border-bottom: 1px solid var(--border-light);
}

.mobile-nav-list li:last-child {
  border-bottom: none;
}

.mobile-nav-list a,
.mobile-nav-btn,
.mobile-logout-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  color: var(--text-primary);
  text-decoration: none;
  transition: all 0.3s ease;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  font-size: 13px;
  cursor: pointer;
}

.mobile-nav-list a:hover,
.mobile-nav-btn:hover,
.mobile-logout-btn:hover {
  background: var(--bg-secondary);
  color: var(--primary-color);
}

.mobile-logout-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.mobile-logout-btn:disabled:hover {
  background: transparent;
  color: var(--text-primary);
}

/* Mobile About Us Dropdown */
.mobile-dropdown-parent {
  border-bottom: 1px solid var(--border-light);
  position: relative;
}

.mobile-dropdown-trigger {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 14px 16px;
  color: var(--text-primary);
  text-decoration: none;
  transition: all 0.3s ease;
  border: none;
  background: none;
  text-align: left;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
}

.mobile-dropdown-trigger:hover {
  background: var(--bg-secondary);
  color: var(--primary-color);
}

.mobile-dropdown-arrow {
  font-size: 11px;
  transition: transform 0.3s ease;
  margin-left: 6px;
  color: var(--text-muted);
}

.mobile-dropdown-arrow.rotate {
  transform: rotate(180deg);
  color: var(--primary-color);
}

.mobile-dropdown-menu {
  background: var(--bg-secondary);
  border-top: 1px solid var(--border-light);
  padding: 8px 0;
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.mobile-dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px 12px 32px;
  color: var(--text-primary);
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 14px;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  border-bottom: 1px solid var(--border-light);
  position: relative;
  font-weight: 500;
}

.mobile-dropdown-item:last-child {
  border-bottom: none;
}

.mobile-dropdown-item:hover {
  background: var(--bg-tertiary);
  color: var(--primary-color);
  padding-left: 36px;
}

.mobile-dropdown-item:hover::before {
  content: '';
  position: absolute;
  left: 24px;
  top: 50%;
  transform: translateY(-50%);
  width: 4px;
  height: 4px;
  background: var(--primary-color);
  border-radius: 50%;
  animation: dotFadeIn 0.2s ease;
}

.mobile-dropdown-item i {
  width: 14px;
  color: var(--text-muted);
  font-size: 14px;
  text-align: center;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.mobile-dropdown-item:hover i {
  color: var(--primary-color);
}

.mobile-dropdown-item span {
  flex: 1;
  line-height: 1.4;
}

/* Active state for mobile dropdown */
.mobile-dropdown-item.router-link-active {
  color: var(--primary-color);
  background: var(--bg-tertiary);
  padding-left: 36px;
}

.mobile-dropdown-item.router-link-active::before {
  content: '';
  position: absolute;
  left: 24px;
  top: 50%;
  transform: translateY(-50%);
  width: 4px;
  height: 4px;
  background: var(--primary-color);
  border-radius: 50%;
}

.mobile-dropdown-item.router-link-active i {
  color: var(--primary-color);
}

.mobile-profile-section {
  background: var(--bg-secondary);
}

.mobile-profile-header {
  padding: 14px;
}

.mobile-user-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.mobile-avatar-image {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  object-fit: cover;
  border: 1px solid var(--border-color);
  flex-shrink: 0;
}

.mobile-user-details {
  flex: 1;
  min-width: 0;
}

.mobile-user-name {
  font-weight: 600;
  font-size: 13px;
  color: var(--text-primary);
  margin-bottom: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mobile-user-email {
  font-size: 10px;
  color: var(--text-secondary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Mobile Auth */
.mobile-auth-section {
  border-top: 1px solid var(--border-light);
  padding: 16px 0;
}

.mobile-auth-simple {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 0 14px;
}

.mobile-text-link {
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
  color: var(--text-primary);
  padding: 10px 0;
  text-align: center;
  border-bottom: 1px solid var(--border-light);
  transition: color 0.3s ease;
}

.mobile-text-link.primary {
  font-weight: 600;
  color: var(--primary-color);
  border-bottom: none;
}

.mobile-text-link:hover {
  color: var(--primary-color);
}

.mobile-text-link.primary:hover {
  color: var(--primary-hover);
}

.mobile-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1001;
}

#header-fixed-height {
  height: 70px;
}

/* Responsive */
@media (max-width: 1199px) {
  .nav-components-section,
  .utilities-section {
    display: none;
  }
  
  .mobile-menu-toggler {
    display: block;
  }
  
  .main-nav {
    gap: 8px;
  }
}

@media (max-width: 991px) {
  .navigation {
    gap: 1px;
  }
  
  .navigation li a {
    font-size: 11px;
    padding: 8px 4px;
  }
  
  .search-utility {
    min-width: 110px;
  }
}

@media (max-width: 767px) {
  .mobile-menu {
    width: 260px;
  }
  
  .logo-image {
    max-height: 48px;
  }
  
  .container {
    padding: 0 8px;
  }
  
  .mobile-dropdown-item {
    padding: 10px 14px 10px 28px;
    font-size: 13px;
  }
  
  .mobile-dropdown-item:hover {
    padding-left: 32px;
  }
  
  .mobile-dropdown-item:hover::before {
    left: 20px;
  }
  
  .mobile-dropdown-item.router-link-active {
    padding-left: 32px;
  }
  
  .mobile-dropdown-item.router-link-active::before {
    left: 20px;
  }
}

@media (max-width: 480px) {
  .mobile-menu {
    width: 240px;
  }
  
  .logo-image {
    max-height: 46px;
  }
  
  .container {
    padding: 0 6px;
  }
}

/* Ensure proper icon display */
:deep(i[class*="fa-"]) {
  display: inline-block !important;
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
  font-style: normal !important;
  text-rendering: auto !important;
  -webkit-font-smoothing: antialiased !important;
}

:deep(.fas) {
  font-family: 'Font Awesome 6 Free' !important;
}

:deep(.far) {
  font-family: 'Font Awesome 6 Free' !important;
}

:deep(.fab) {
  font-family: 'Font Awesome 6 Brands' !important;
}

/* Critical fixes for overflow */
.main-nav {
  overflow: visible;
}

.utilities-section {
  overflow: visible;
}

.search-utility {
  overflow: visible;
}

.profile-utility {
  overflow: visible;
}

/* Ensure containers don't cause horizontal scroll */
.container, .row, .col-12 {
  overflow: visible;
}

.header-area {
  overflow: visible;
}

/* Fix dropdown z-index issues */
.dropdown-parent {
  z-index: 1002;
}

.dropdown-menu {
  z-index: 1003;
}

/* Ensure dropdown is above other elements */
.header-area {
  z-index: 1000;
}

.mobile-menu {
  z-index: 1004;
}

.mobile-overlay {
  z-index: 1003;
}
</style>
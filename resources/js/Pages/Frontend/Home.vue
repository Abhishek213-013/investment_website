<template>
  <InvestProLayout>
      <!-- Content with Cross-fade Transition -->
      <transition name="fade" mode="out-in">
        <div :key="contentRefreshKey" class="home-page">
          <!-- Hero Section -->
          <section 
            class="hero-section"
            :style="heroSectionStyle"
          >
            <div class="container">
              <div class="row align-items-center min-h-screen">
                <div class="col-lg-6">
                  <div class="hero-content">
                    <h1 class="hero-title gradient-text-accent">{{ displayContent.home_hero_title || t('Smart Investing Made Simple') }}</h1>
                    <p class="hero-subtitle text-investpro-light">{{ displayContent.home_hero_subtitle || t('Join thousands of investors growing their wealth with our expert guidance and advanced trading tools.') }}</p>
                    
                    <div class="hero-actions">
                      <Link href="/register" class="btn-invest">
                        {{ displayContent.home_cta_button || t('Start Investing') }}
                      </Link>
                      <Link href="/download" class="btn btn-accent">
                        <i class="fas fa-download icon-fixed"></i>
                        {{ t('Download App') }}
                      </Link>
                    </div>
                    
                    <div class="hero-stats">
                      <div class="stat-item">
                        <span class="stat-number text-investpro-light">{{ stats.total_investors.toLocaleString() }}+</span>
                        <span class="stat-label text-investpro-secondary">{{ t('Active Investors') }}</span>
                      </div>
                      <div class="stat-item">
                        <span class="stat-number text-investpro-light">{{ stats.total_assets }}+</span>
                        <span class="stat-label text-investpro-accent">{{ t('Trading Assets') }}</span>
                      </div>
                      <div class="stat-item">
                        <span class="stat-number text-investpro-light">{{ stats.total_returns }}%</span>
                        <span class="stat-label text-investpro-teal">{{ t('Average Returns') }}</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="hero-image">
                    <img 
                      :src="heroImageUrl" 
                      :alt="t('Investment Platform')" 
                      class="img-fluid hero-main-image"
                      @error="handleHeroImageError"
                    >
                    <img 
                      ref="fallbackImage"
                      src="/assets/img/investpro-hero.png" 
                      :alt="t('Investment Platform')" 
                      class="img-fluid hero-fallback-image"
                    >
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Services Section -->
          <section class="services-section section-py-80 bg-investpro-light">
            <div class="container">
              <div class="row mb-5">
                <div class="col-12">
                  <h2 class="section-title text-investpro-primary">{{ displayContent.home_services_title || t('Our Investment Services') }}</h2>
                  <p class="section-subtitle text-investpro-teal">{{ displayContent.home_services_subtitle || t('Comprehensive investment solutions tailored to your financial goals') }}</p>
                </div>
              </div>
              <div class="row">
                <div class="col-xl-4 col-md-6 mb-4" v-for="service in featuredServices" :key="service.id">
                  <div class="service-card">
                    <div class="service-icon">
                      <i :class="service.icon"></i>
                    </div>
                    <div class="card-body text-center">
                      <h5 class="card-title text-investpro-primary">{{ service.title }}</h5>
                      <p class="card-text text-investpro-secondary">{{ service.description }}</p>
                      <div class="service-features">
                        <span class="badge" v-for="feature in service.features" :key="feature">
                          {{ feature }}
                        </span>
                      </div>
                    </div>
                    <div class="card-footer">
                      <Link :href="`/services#${service.id}`" class="btn btn-primary w-100">
                        {{ t('Learn More') }}
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Stats Section -->
          <section class="stats-section section-py-80">
            <div class="container">
              <div class="row text-center">
                <div class="col-md-3 col-6 mb-4">
                  <div class="stat-card">
                    <h3 class="stats-number">{{ stats.total_investors.toLocaleString() }}+</h3>
                    <p class="stats-label">{{ t('Active Investors') }}</p>
                  </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                  <div class="stat-card">
                    <h3 class="stats-number">${{ stats.total_volume.toLocaleString() }}+</h3>
                    <p class="stats-label">{{ t('Trading Volume') }}</p>
                  </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                  <div class="stat-card">
                    <h3 class="stats-number">{{ stats.total_assets }}+</h3>
                    <p class="stats-label">{{ t('Trading Assets') }}</p>
                  </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                  <div class="stat-card">
                    <h3 class="stats-number">{{ stats.total_countries }}+</h3>
                    <p class="stats-label">{{ t('Countries Served') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- News Section -->
          <section class="news-section section-py-80" v-if="marketNews && marketNews.length > 0">
            <div class="container">
              <div class="row mb-5">
                <div class="col-12">
                  <h2 class="section-title text-investpro-primary">{{ t('Market News & Insights') }}</h2>
                  <p class="section-subtitle text-investpro-teal">{{ t('Stay updated with the latest market trends and investment opportunities') }}</p>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" v-for="news in marketNews.slice(0, 3)" :key="news.id">
                  <div class="news-card">
                    <div class="news-image-container">
                      <img 
                        :src="getNewsImage(news)" 
                        :alt="news.title"
                        class="news-image"
                        @error="handleNewsImageError"
                      >
                      <span class="news-category">{{ news.category }}</span>
                    </div>
                    <div class="news-content">
                      <h5 class="news-title">{{ news.title }}</h5>
                      <p class="news-excerpt">{{ news.excerpt }}</p>
                      <div class="news-meta">
                        <span class="news-date">{{ formatDate(news.published_at) }}</span>
                        <span class="news-read-time">{{ news.read_time }} min read</span>
                      </div>
                    </div>
                    <div class="news-actions">
                      <Link :href="`/news/${news.slug}`" class="btn-read-more">
                        {{ t('Read More') }}
                        <i class="fas fa-arrow-right"></i>
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mt-4">
                <div class="col-12 text-center">
                  <Link href="/news" class="btn btn-outline-primary">
                    {{ t('View All News') }}
                  </Link>
                </div>
              </div>
            </div>
          </section>

          <!-- FAQ Preview Section -->
          <section class="faq-preview-section section-py-80" v-if="featuredFaqs && featuredFaqs.length > 0">
            <div class="container">
              <div class="row align-items-center">
                <div class="col-lg-6">
                  <div class="faq-list">
                    <h2 class="section-title text-investpro-primary">{{ t('Frequently Asked Questions') }}</h2>
                    <p class="section-subtitle text-investpro-teal mb-4">{{ t('Get answers to common questions about investing with us') }}</p>
                    
                    <div class="faq-item" v-for="faq in featuredFaqs.slice(0, 3)" :key="faq.id">
                      <div class="faq-question">
                        <i class="fas fa-question-circle"></i>
                        {{ faq.question }}
                      </div>
                      <div class="faq-answer">
                        {{ faq.answer }}
                      </div>
                    </div>
                    
                    <div class="text-center mt-4">
                      <Link href="/faq" class="btn btn-primary">
                        {{ t('View All FAQs') }}
                      </Link>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="faq-image text-center">
                    <img src="/assets/img/faq-illustration.png" :alt="t('FAQ Illustration')" class="img-fluid">
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- CTA Section -->
          <section class="cta-section section-py-80">
            <div class="container">
              <div class="row text-center">
                <div class="col-12">
                  <h2 class="cta-title text-white">{{ displayContent.home_cta_title || t('Ready to Start Your Investment Journey?') }}</h2>
                  <p class="cta-subtitle text-investpro-light">{{ displayContent.home_cta_subtitle || t('Join thousands of successful investors and take control of your financial future') }}</p>
                  <div class="cta-actions">
                    <Link href="/register" class="btn btn-hero-primary me-3">
                      {{ displayContent.home_cta_button || t('Create Account') }}
                    </Link>
                    <Link href="/download" class="btn btn-hero-secondary">
                      <i class="fas fa-download icon-fixed"></i>
                      {{ t('Download App') }}
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </transition>
  </InvestProLayout>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import InvestProLayout from '../../Layouts/InvestProLayout.vue'
import { ref, onMounted, computed, onUnmounted, watch } from 'vue'
import { useTranslation } from '@/composables/useTranslation'

const { t, translationVersion } = useTranslation()

// Define props
const props = defineProps({
  content: {
    type: Object,
    default: () => ({})
  },
  featuredServices: Array,
  marketNews: Array,
  stats: Object,
  featuredFaqs: Array,
  currentLanguage: {
    type: String,
    default: 'en'
  }
})

// Reactive data
const currentTheme = ref('light')
const heroImageLoaded = ref(false)
const heroImageError = ref(false)
const fallbackImage = ref(null)
const contentRefreshKey = ref(0)
const isDevelopment = ref(false)

// Define currentLanguage as a ref to fix the error
const currentLanguage = ref(props.currentLanguage || 'en')

// Local content state for smooth updates
const localContent = ref({})

// Initialize local content with props
onMounted(() => {
  localContent.value = { ...props.content }
})

// Enhanced computed properties
const displayContent = computed(() => {
  return Object.keys(localContent.value).length > 0 ? localContent.value : props.content
})

const heroImageUrl = computed(() => {
  const customImage = displayContent.value.home_hero_image;
  return customImage && customImage !== '/assets/img/investpro-hero.png' ? customImage : '/assets/img/investpro-hero.png';
})

const heroSectionStyle = computed(() => {
  const isDark = currentTheme.value === 'dark'
  const lightBg = 'linear-gradient(135deg, rgba(245, 247, 250, 0.9) 0%, rgba(228, 232, 240, 0.9) 100%), url(\'/assets/img/investment-bg.png\') center/cover no-repeat'
  const darkBg = 'linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.9) 100%), url(\'/assets/img/investment-bg.png\') center/cover no-repeat'
  
  return {
    background: isDark ? darkBg : lightBg
  }
})

// Default services data
const featuredServices = computed(() => {
  return props.featuredServices || [
    {
      id: 1,
      title: t('Stock Trading'),
      description: t('Trade stocks from global markets with advanced tools and real-time data'),
      icon: 'fas fa-chart-line',
      features: [t('Real-time Data'), t('Advanced Charts'), t('Global Markets')]
    },
    {
      id: 2,
      title: t('Cryptocurrency'),
      description: t('Buy, sell and trade cryptocurrencies with secure wallet integration'),
      icon: 'fab fa-bitcoin',
      features: [t('100+ Coins'), t('Secure Wallet'), t('24/7 Trading')]
    },
    {
      id: 3,
      title: t('Portfolio Management'),
      description: t('Professional portfolio management with automated rebalancing and insights'),
      icon: 'fas fa-briefcase',
      features: [t('Auto Rebalance'), t('Risk Analysis'), t('Performance Tracking')]
    }
  ]
})

// Default market news data
const marketNews = computed(() => {
  return props.marketNews || [
    {
      id: 1,
      title: t('Stock Markets Reach New Highs Amid Economic Recovery'),
      excerpt: t('Global stock markets continue their upward trend as economic indicators show strong recovery signals...'),
      category: t('Stocks'),
      published_at: new Date().toISOString(),
      read_time: 3,
      slug: 'stock-markets-highs'
    },
    {
      id: 2,
      title: t('Cryptocurrency Regulation Updates: What Investors Need to Know'),
      excerpt: t('New regulatory frameworks are shaping the future of cryptocurrency investments worldwide...'),
      category: t('Crypto'),
      published_at: new Date(Date.now() - 86400000).toISOString(),
      read_time: 5,
      slug: 'crypto-regulation-updates'
    },
    {
      id: 3,
      title: t('Federal Reserve Interest Rate Decision Impacts Markets'),
      excerpt: t('The latest Fed meeting results are creating waves across various investment sectors...'),
      category: t('Economy'),
      published_at: new Date(Date.now() - 172800000).toISOString(),
      read_time: 4,
      slug: 'fed-rate-decision'
    }
  ]
})

// Default stats data
const stats = computed(() => {
  return props.stats || {
    total_investors: 50000,
    total_volume: 2500000000,
    total_assets: 150,
    total_countries: 80,
    total_returns: 15.2
  }
})

// Default FAQ data
const featuredFaqs = computed(() => {
  return props.featuredFaqs || [
    {
      id: 1,
      question: t('How do I start investing with InvestPro?'),
      answer: t('Simply download our app, complete the registration process, verify your identity, and fund your account to start investing.')
    },
    {
      id: 2,
      question: t('What is the minimum investment amount?'),
      answer: t('You can start investing with as little as $50. We believe in making investing accessible to everyone.')
    },
    {
      id: 3,
      question: t('Is my investment secure?'),
      answer: t('Yes, we use bank-level security encryption and follow strict regulatory compliance to protect your investments and personal data.')
    }
  ]
})

// Event handlers
const handleThemeChange = (event) => {
  currentTheme.value = event.detail.theme
}

const refreshIcons = () => {
  if (window.FontAwesome && window.FontAwesome.dom && window.FontAwesome.dom.i2svg) {
    setTimeout(() => {
      window.FontAwesome.dom.i2svg();
    }, 100);
  }
}

// Image handling methods
const preloadHeroImage = () => {
  const img = new Image();
  img.src = heroImageUrl.value;
  
  img.onload = () => {
    heroImageLoaded.value = true;
    heroImageError.value = false;
  };
  
  img.onerror = () => {
    heroImageError.value = true;
    heroImageLoaded.value = false;
  };
}

const handleHeroImageError = (event) => {
  heroImageError.value = true;
  
  if (fallbackImage.value) {
    const mainImage = event.target;
    const fallback = fallbackImage.value;
    
    mainImage.style.display = 'none';
    fallback.style.display = 'block';
  }
}

const getNewsImage = (news) => {
  if (news.image_url && news.image_url !== 'null' && news.image_url !== 'NULL') {
    return news.image_url;
  }

  // Fallback to demo images
  const newsImages = [
    '/assets/img/news/stock-news.jpg',
    '/assets/img/news/crypto-news.jpg',
    '/assets/img/news/economy-news.jpg',
    '/assets/img/news/market-news.jpg'
  ];
  
  return newsImages[(news.id - 1) % newsImages.length];
}

const handleNewsImageError = (event) => {
  const img = event.target;
  
  // Use placeholder image
  img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIyMCIgZmlsbD0iI2YzZjRmNiIvPjx0ZXh0IHg9IjIwMCIgeT0iMTEwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM5OTk5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiPk5ld3MgSW1hZ2U8L3RleHQ+PC9zdmc+';
  img.onerror = null;
}

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString(currentLanguage.value, {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
}

// Watchers
watch(currentLanguage, (newLang, oldLang) => {
  if (newLang !== oldLang) {
    contentRefreshKey.value++;
  }
});

watch(() => props.content, (newContent) => {
  if (newContent && Object.keys(newContent).length > 0) {
    localContent.value = { ...newContent };
    contentRefreshKey.value++;
  }
}, { deep: true, immediate: true });

watch(() => props.currentLanguage, (newLang) => {
  if (newLang && newLang !== currentLanguage.value) {
    currentLanguage.value = newLang;
  }
});

watch(translationVersion, () => {
  contentRefreshKey.value++;
});

// Lifecycle
onMounted(() => {
  isDevelopment.value = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
  
  if (props.currentLanguage && props.currentLanguage !== currentLanguage.value) {
    currentLanguage.value = props.currentLanguage;
    localStorage.setItem('preferredLanguage', props.currentLanguage);
  }
  
  const savedTheme = localStorage.getItem('preferredTheme')
  const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  
  currentTheme.value = savedTheme || (systemPrefersDark ? 'dark' : 'light')
  
  preloadHeroImage();
  
  window.addEventListener('themeChanged', handleThemeChange)
  
  refreshIcons()
})

onUnmounted(() => {
  window.removeEventListener('themeChanged', handleThemeChange)
})
</script>

<style scoped>
/* ==================== */
/* STICKY HEADER FIX */
/* ==================== */
.home-page {
  overflow-x: hidden; /* Prevent horizontal scroll */
  width: 100%;
}

/* ==================== */
/* BENGALI FONT SIZE ADJUSTMENTS */
/* ==================== */
.bn-lang .hero-title {
  font-size: 1.7rem !important;
  line-height: 1.3 !important;
}

.bn-lang .hero-subtitle {
  font-size: 1rem !important;
  line-height: 1.5 !important;
}

.bn-lang .section-title {
  font-size: 1.rem !important;
  line-height: 1.3 !important;
}

.bn-lang .section-subtitle {
  font-size: 1rem !important;
  line-height: 1.5 !important;
}

.bn-lang .card-title {
  font-size: 1rem !important;
  line-height: 1.3 !important;
}

.bn-lang .card-text {
  font-size: 0.85rem !important;
  line-height: 1.4 !important;
}

.bn-lang .teacher-name {
  font-size: 1.1rem !important;
  line-height: 1.3 !important;
}

.bn-lang .education-text {
  font-size: 0.8rem !important;
  line-height: 1.4 !important;
}

.bn-lang .stat-label-new {
  font-size: 0.6rem !important;
  line-height: 1.2 !important;
}

.bn-lang .cta-title {
  font-size: 1.8rem !important;
  line-height: 1.3 !important;
}

.bn-lang .cta-subtitle {
  font-size: 1rem !important;
  line-height: 1.5 !important;
}

.bn-lang .btn {
  font-size: 0.9rem !important;
}

.bn-lang .btn-lg {
  font-size: 1rem !important;
}

/* ==================== */
/* SMOOTH TRANSITION STYLES */
/* ==================== */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Smooth content transitions */
.hero-content,
.section-title,
.section-subtitle {
  transition: opacity 0.3s ease;
}

/* ==================== */
/* HERO IMAGE STYLES */
/* ==================== */
.hero-image {
  position: relative;
  text-align: center;
  animation: float 3s ease-in-out infinite;
  max-width: 100%;
  overflow: hidden;
}

.hero-main-image {
  max-width: 100%;
  border-radius: 10px;
  box-shadow: var(--shadow-lg);
  transition: opacity 0.3s ease;
  width: 100%;
  height: auto;
}

.hero-fallback-image {
  max-width: 100%;
  border-radius: 10px;
  box-shadow: var(--shadow-lg);
  display: none; /* Hidden by default */
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

/* ==================== */
/* ICON FIXES FOR LANGUAGE SWITCH */
/* ==================== */
.icon-fixed {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
  font-style: normal !important;
  font-variant: normal !important;
  text-rendering: auto !important;
  -webkit-font-smoothing: antialiased !important;
  speak: none;
}

/* Ensure all Font Awesome icons maintain their font family */
.fas, .fa, .far, .fab {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
}

/* Specific fixes for Bengali language */
.bn-lang .fas,
.bn-lang .fa,
.bn-lang .far,
.bn-lang .fab,
.bn-lang .icon-fixed {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
}

/* ==================== */
/* IMPROVED HERO SECTION */
/* ==================== */
.hero-section {
  padding: 80px 0;
  position: relative;
  overflow: hidden;
  min-height: 100vh;
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, rgba(245, 247, 250, 0.9) 0%, rgba(228, 232, 240, 0.9) 100%), url('/assets/img/investment-bg.png') center/cover no-repeat;
  width: 100%;
}

.hero-content {
  max-width: 600px;
  width: 100%;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 700;
  line-height: 1.1;
  margin-bottom: 1.5rem;
  color: var(--text-primary);
  background: linear-gradient(135deg, var(--primary-color), #667eea);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  max-width: 100%;
  overflow-wrap: break-word;
}

.hero-subtitle {
  font-size: 1.25rem;
  color: var(--text-secondary);
  margin-bottom: 2.5rem;
  line-height: 1.6;
  max-width: 100%;
  overflow-wrap: break-word;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 3rem;
  flex-wrap: wrap;
  max-width: 100%;
}

.btn-invest {
  background: linear-gradient(135deg, var(--primary-color), #667eea);
  border: none;
  color: white;
  padding: 1rem 2.5rem;
  font-weight: 600;
  border-radius: 50px;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 10px 20px rgba(74, 108, 247, 0.3);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: fit-content;
}

.btn-invest:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 30px rgba(74, 108, 247, 0.4);
  color: white;
}

.btn-accent {
  background-color: transparent;
  color: var(--primary-color);
  border: 2px solid var(--primary-color);
  padding: 1rem 2.5rem;
  font-weight: 600;
  border-radius: 50px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  text-decoration: none;
  min-width: fit-content;
}

.btn-accent:hover {
  background-color: var(--primary-color);
  color: white;
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(74, 108, 247, 0.3);
}

.hero-stats {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
  max-width: 100%;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  min-width: 120px;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 0.9rem;
  color: var(--text-secondary);
  font-weight: 500;
}

/* ==================== */
/* SECTION SPACING */
/* ==================== */
.section-py-80 {
  padding: 80px 0;
  width: 100%;
  overflow: hidden;
}

.section-py-120 {
  padding: 120px 0;
  width: 100%;
  overflow: hidden;
}

/* ==================== */
/* SERVICE CARDS IMPROVEMENT */
/* ==================== */
.services-section {
  background: var(--bg-primary);
}

.service-card {
  transition: all 0.3s ease;
  border: none;
  box-shadow: var(--shadow);
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  border-radius: 15px;
  overflow: hidden;
  height: 100%;
  display: flex;
  flex-direction: column;
  max-width: 100%;
}

.service-card:hover {
  transform: translateY(-10px);
  box-shadow: var(--shadow-lg);
}

.service-icon {
  width: 80px;
  height: 80px;
  margin: 2rem auto 1.5rem;
  background: linear-gradient(135deg, var(--primary-color), #667eea);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2rem;
  flex-shrink: 0;
}

.card-body {
  padding: 0 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.card-title {
  color: var(--text-primary);
  font-weight: 600;
  margin-bottom: 1rem;
  font-size: 1.25rem;
  line-height: 1.3;
  overflow-wrap: break-word;
}

.card-text {
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 1.5rem;
  flex: 1;
  overflow-wrap: break-word;
}

.service-features {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.service-features .badge {
  background: var(--primary-color);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: 500;
  font-size: 0.8rem;
  white-space: nowrap;
}

.card-footer {
  background: transparent;
  border-top: 1px solid var(--border-color);
  padding: 1.5rem;
  width: 100%;
}

/* ==================== */
/* NEWS CARDS IMPROVEMENT */
/* ==================== */
.news-section {
  background: var(--bg-secondary);
}

.news-card {
  background: var(--card-bg);
  border-radius: 15px;
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
  height: 100%;
  display: flex;
  flex-direction: column;
  max-width: 100%;
}

.news-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.news-image-container {
  position: relative;
  overflow: hidden;
  height: 200px;
  width: 100%;
}

.news-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.news-card:hover .news-image {
  transform: scale(1.05);
}

.news-category {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: var(--primary-color);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  z-index: 2;
}

.news-content {
  padding: 1.5rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  width: 100%;
}

.news-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
  line-height: 1.4;
  overflow-wrap: break-word;
}

.news-excerpt {
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 1rem;
  flex-grow: 1;
  overflow-wrap: break-word;
}

.news-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.8rem;
  color: var(--text-muted);
  margin-bottom: 1rem;
  width: 100%;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.news-actions {
  padding: 0 1.5rem 1.5rem;
  width: 100%;
}

.btn-read-more {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.btn-read-more:hover {
  color: var(--primary-hover);
  transform: translateX(5px);
}

/* ==================== */
/* STATS SECTION IMPROVEMENT */
/* ==================== */
.stats-section {
  background: linear-gradient(135deg, var(--primary-color), #667eea);
  color: white;
  position: relative;
  overflow: hidden;
}

.stat-card {
  padding: 2rem 1rem;
  text-align: center;
}

.stats-number {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: white;
  line-height: 1;
}

.stats-label {
  color: rgba(255, 255, 255, 0.9);
  margin: 0;
  font-size: 1.1rem;
  font-weight: 500;
}

/* ==================== */
/* FAQ SECTION IMPROVEMENT */
/* ==================== */
.faq-preview-section {
  background: var(--bg-primary);
}

.faq-list {
  margin-bottom: 2rem;
  width: 100%;
  text-align: center;
}

.faq-item {
  background: var(--card-bg);
  border-radius: 10px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  border: 1px solid var(--border-color);
  box-shadow: var(--shadow);
  width: 100%;
  text-align: left;
  margin-left: auto;
  margin-right: auto;
}

.faq-question {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  overflow-wrap: break-word;
}

.faq-question i {
  color: var(--primary-color);
  flex-shrink: 0;
}

.faq-answer {
  color: var(--text-secondary);
  line-height: 1.6;
  overflow-wrap: break-word;
}

.faq-image img {
  border-radius: 15px;
  box-shadow: var(--shadow-lg);
  max-width: 100%;
  height: auto;
}

/* ==================== */
/* CTA SECTION IMPROVEMENT */
/* ==================== */
.cta-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  position: relative;
  overflow: hidden;
  width: 100%;
}

.cta-title {
  font-weight: 700;
  color: white;
  margin-bottom: 1rem;
  font-size: 2.5rem;
  overflow-wrap: break-word;
  text-align: center;
  width: 100%;
  display: block;
}

.cta-subtitle {
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 2rem;
  font-size: 1.2rem;
  overflow-wrap: break-word;
  text-align: center;
  width: 100%;
  display: block;
}

.cta-actions {
  display: flex;
  gap: 1rem;
  justify-content: center !important;
  flex-wrap: wrap;
  max-width: 100%;
  width: 100% !important;
}

/* ==================== */
/* BUTTON IMPROVEMENTS */
/* ==================== */
.btn {
  border-radius: 50px;
  font-weight: 600;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  cursor: pointer;
  white-space: nowrap;
  min-width: fit-content;
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary-color), #667eea);
  border: none;
  color: white;
}

.btn-primary:hover {
  background: linear-gradient(135deg, var(--primary-hover), #764ba2);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(74, 108, 247, 0.3);
}

.btn-outline-primary {
  border: 2px solid white;
  color: white;
  background: transparent;
}

.btn-outline-primary:hover {
  background: white;
  color: var(--primary-color);
  transform: translateY(-2px);
}

.btn-hero-primary {
  background: linear-gradient(135deg, var(--primary-color), #667eea);
  border: none;
  color: white;
  padding: 1rem 2.5rem;
  font-weight: 600;
  border-radius: 50px;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 10px 20px rgba(74, 108, 247, 0.3);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: fit-content;
}

.btn-hero-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 30px rgba(74, 108, 247, 0.4);
  color: white;
}

.btn-hero-secondary {
  background-color: transparent;
  color: white;
  border: 2px solid white;
  padding: 1rem 2.5rem;
  font-weight: 600;
  border-radius: 50px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  text-decoration: none;
  min-width: fit-content;
}

.btn-hero-secondary:hover {
  background-color: white;
  color: var(--primary-color);
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(255, 255, 255, 0.3);
}

/* ==================== */
/* SECTION TITLES - FORCE CENTERED */
/* ==================== */
.section-title {
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
  font-size: 2.5rem;
  text-align: center !important;
  overflow-wrap: break-word;
  max-width: 100%;
  display: block;
  width: 100%;
  margin-left: auto !important;
  margin-right: auto !important;
}

.section-subtitle {
  color: var(--text-secondary);
  font-size: 1.2rem;
  text-align: center !important;
  max-width: 600px;
  margin: 0 auto 3rem !important;
  line-height: 1.6;
  overflow-wrap: break-word;
  display: block;
  width: 100%;
}

/* Force center all section titles with important overrides */
.services-section .section-title,
.news-section .section-title,
.faq-preview-section .section-title,
.stats-section .section-title {
  text-align: center !important;
  margin-left: auto !important;
  margin-right: auto !important;
  display: block !important;
  width: 100% !important;
}

.services-section .section-subtitle,
.news-section .section-subtitle,
.faq-preview-section .section-subtitle {
  text-align: center !important;
  margin-left: auto !important;
  margin-right: auto !important;
  display: block !important;
}

/* Center CTA titles */
.cta-title {
  font-weight: 700;
  color: white;
  margin-bottom: 1rem;
  font-size: 2.5rem;
  overflow-wrap: break-word;
  text-align: center !important;
  width: 100% !important;
  display: block !important;
  margin-left: auto !important;
  margin-right: auto !important;
}

.cta-subtitle {
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 2rem;
  font-size: 1.2rem;
  overflow-wrap: break-word;
  text-align: center !important;
  width: 100% !important;
  display: block !important;
  margin-left: auto !important;
  margin-right: auto !important;
}

/* Force center all container content */
.services-section .container,
.stats-section .container,
.news-section .container,
.faq-preview-section .container,
.cta-section .container {
  text-align: center !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
}

.cta-section .row.text-center {
  text-align: center !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  width: 100% !important;
}


.cta-section .col-12 {
  text-align: center !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  width: 100% !important;
}

/* Force center row content */
.services-section .row,
.news-section .row,
.faq-preview-section .row,
.stats-section .row {
  text-align: center !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  width: 100% !important;
  margin: 0 auto !important;
}

.faq-preview-section .col-lg-6 {
  text-align: center !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  width: 100% !important;
}

/* Force center column content */
.services-section .col-12,
.news-section .col-12,
.faq-preview-section .col-12,
.stats-section .col-12 {
  text-align: center !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
}

/* Remove any potential left-alignment inheritance */
.text-lg-start,
.text-start,
.text-left {
  text-align: center !important;
}

/* Ensure FAQ section is properly centered */
.faq-preview-section .faq-list {
  text-align: center !important;
  width: 100% !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
}

.faq-preview-section .faq-item {
  background: var(--card-bg);
  border-radius: 10px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  border: 1px solid var(--border-color);
  box-shadow: var(--shadow);
  width: 100% !important;
  max-width: 800px !important;
  text-align: left; /* Keep FAQ content left-aligned for readability */
  margin-left: auto !important;
  margin-right: auto !important;
}

/* Center FAQ button */
.faq-preview-section .text-center {
  text-align: center !important;
  justify-content: center !important;
  display: flex !important;
  width: 100% !important;
}

/* ==================== */
/* DARK THEME OVERRIDES */
/* ==================== */
.dark-theme .hero-section {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.9) 100%), url('/assets/img/investment-bg.png') center/cover no-repeat !important;
}

.dark-theme .services-section {
  background: var(--bg-secondary);
}

.dark-theme .news-section {
  background: var(--bg-primary);
}

.dark-theme .faq-preview-section {
  background: var(--bg-secondary);
}

/* ==================== */
/* RESPONSIVE DESIGN */
/* ==================== */
@media (max-width: 1200px) {
  .hero-title {
    font-size: 3rem;
  }
  
  .section-title {
    font-size: 2.2rem;
  }
  
  .cta-title {
    font-size: 2.2rem;
  }
}

@media (max-width: 992px) {
  .hero-section {
    padding: 60px 0;
    min-height: auto;
    text-align: center;
  }
  
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-content {
    margin: 0 auto;
    text-align: center;
  }
  
  .hero-actions {
    justify-content: center;
  }
  
  .hero-stats {
    justify-content: center;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .cta-title {
    font-size: 2rem;
  }
  
  .faq-preview-section .row.align-items-center {
    text-align: center;
  }
}

@media (max-width: 768px) {
  .home-page {
    padding-top: 70px;
  }
  
  .hero-title {
    font-size: 2.2rem;
  }
  
  .hero-subtitle {
    font-size: 1.1rem;
  }
  
  .hero-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .btn-invest,
  .btn-accent,
  .btn-hero-primary,
  .btn-hero-secondary {
    width: 100%;
    max-width: 300px;
    justify-content: center;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .section-title {
    font-size: 1.8rem;
  }
  
  .cta-title {
    font-size: 1.8rem;
  }
  
  .section-py-80 {
    padding: 60px 0;
  }
  
  .section-py-120 {
    padding: 80px 0;
  }
  
  .cta-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .stats-number {
    font-size: 2.5rem;
  }
  
  .service-icon {
    width: 70px;
    height: 70px;
    font-size: 1.75rem;
  }
}

@media (max-width: 576px) {
  .hero-title {
    font-size: 1.8rem;
  }
  
  .hero-subtitle {
    font-size: 1rem;
  }
  
  .btn {
    padding: 0.8rem 1.5rem;
    font-size: 0.9rem;
  }
  
  .section-title {
    font-size: 1.6rem;
  }
  
  .cta-title {
    font-size: 1.6rem;
  }
  
  .section-subtitle {
    font-size: 1rem;
  }
  
  .cta-subtitle {
    font-size: 1rem;
  }
  
  .stats-number {
    font-size: 2rem;
  }
  
  .stats-label {
    font-size: 0.9rem;
  }
  
  .service-features .badge {
    font-size: 0.7rem;
    padding: 0.4rem 0.8rem;
  }
}

/* ==================== */
/* ACCESSIBILITY */
/* ==================== */
.btn:focus {
  outline: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
  outline-offset: 2px;
}

/* ==================== */
/* REDUCED MOTION */
/* ==================== */
@media (prefers-reduced-motion: reduce) {
  .hero-image,
  .service-card,
  .news-card,
  .btn {
    transition: none;
    animation: none;
  }
  
  .hero-image:hover,
  .service-card:hover,
  .news-card:hover,
  .btn:hover {
    transform: none;
  }
}

/* ==================== */
/* CONTAINER FIXES */
/* ==================== */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
  width: 100%;
}

.row {
  margin: 0 -15px;
  display: flex;
  flex-wrap: wrap;
  width: 100%;
}

.col-12, .col-lg-6, .col-xl-4, .col-md-6, .col-md-3, .col-6 {
  padding: 0 15px;
  box-sizing: border-box;
}

/* Ensure all images are responsive */
.img-fluid {
  max-width: 100%;
  height: auto;
}

/* Prevent horizontal scroll */
body {
  overflow-x: hidden;
}
</style>
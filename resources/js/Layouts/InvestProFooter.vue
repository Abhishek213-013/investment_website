<template>
  <footer class="frontend-footer" :class="themeClass">
    <div class="footer-padding-layer">
      <div class="container">
        <div class="row compact-row">
          <div class="col-lg-4 col-md-12 compact-col">
            <div class="footer-logo compact-logo">
              <Link href="/" class="text-decoration-none">
                <div class="footer-logo-container">
                  <img src="/assets/img/investpro-logo.png" alt="InvestPro" class="footer-logo-image">
                </div>
              </Link>
            </div>
            <p class="footer-description compact-description">{{ t('Your trusted partner in investment and financial growth. Make smart investment decisions with our expert guidance and advanced tools.') }}</p>
            <div class="social-links compact-social">
              <a href="#" class="social-link" :title="t('Follow us on Facebook')">
                <i class="fab fa-facebook-f icon-fixed"></i>
              </a>
              <a href="#" class="social-link" :title="t('Follow us on Twitter')">
                <i class="fab fa-twitter icon-fixed"></i>
              </a>
              <a href="#" class="social-link" :title="t('Follow us on LinkedIn')">
                <i class="fab fa-linkedin-in icon-fixed"></i>
              </a>
              <a href="#" class="social-link" :title="t('Follow us on Instagram')">
                <i class="fab fa-instagram icon-fixed"></i>
              </a>
            </div>
          </div>
          
          <!-- Horizontal section for Quick Links, Resources, and Contact Info -->
          <div class="col-lg-8 col-md-12 compact-col horizontal-section">
            <div class="row horizontal-row">
              <div class="col-md-4 col-sm-4 horizontal-col">
                <h6 class="footer-heading compact-heading">{{ t('Quick Links') }}</h6>
                <ul class="footer-links compact-links">
                  <li class="footer-link-item">
                    <Link href="/" class="footer-link">
                      {{ t('Home') }}
                    </Link>
                  </li>
                  <li class="footer-link-item">
                    <Link href="/about" class="footer-link">
                      {{ t('About Us') }}
                    </Link>
                  </li>
                  <li class="footer-link-item">
                    <Link href="/services" class="footer-link">
                      {{ t('Services') }}
                    </Link>
                  </li>
                  <li class="footer-link-item">
                    <Link href="/download" class="footer-link">
                      {{ t('Download') }}
                    </Link>
                  </li>
                </ul>
              </div>
              
              <div class="col-md-4 col-sm-4 horizontal-col">
                <h6 class="footer-heading compact-heading">{{ t('Resources') }}</h6>
                <ul class="footer-links compact-links">
                  <li class="footer-link-item">
                    <Link href="/news" class="footer-link">
                      {{ t('Market News') }}
                    </Link>
                  </li>
                  <li class="footer-link-item">
                    <Link href="/faq" class="footer-link">
                      {{ t('FAQ') }}
                    </Link>
                  </li>
                  <li class="footer-link-item">
                    <Link href="/blog" class="footer-link">
                      {{ t('Investment Blog') }}
                    </Link>
                  </li>
                  <li class="footer-link-item">
                    <Link href="/guides" class="footer-link">
                      {{ t('Investment Guides') }}
                    </Link>
                  </li>
                </ul>
              </div>
              
              <div class="col-md-4 col-sm-4 horizontal-col">
                <h6 class="footer-heading compact-heading">{{ t('Contact Info') }}</h6>
                <ul class="contact-info compact-contact">
                  <li class="contact-item">
                    <i class="fas fa-map-marker-alt contact-icon icon-fixed"></i>
                    <span class="contact-text">Financial District, Downtown, City 10001</span>
                  </li>
                  <li class="contact-item">
                    <i class="fas fa-phone contact-icon icon-fixed"></i>
                    <span class="contact-text">+1 (555) 123-4567</span>
                  </li>
                  <li class="contact-item">
                    <i class="fas fa-envelope contact-icon icon-fixed"></i>
                    <span class="contact-text">support@investpro.com</span>
                  </li>
                  <li class="contact-item">
                    <i class="fas fa-clock contact-icon icon-fixed"></i>
                    <span class="contact-text">{{ t('Mon-Fri: 9AM-6PM') }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        
        <hr class="footer-divider compact-divider">
        
        <div class="footer-bottom compact-bottom">
          <div class="row align-items-center">
            <div class="col-md-6 col-12">
              <p class="copyright">
                &copy; {{ new Date().getFullYear() }} InvestPro. {{ t('All rights reserved.') }}
              </p>
            </div>
            <div class="col-md-6 col-12 text-md-end text-center">
              <div class="footer-bottom-links">
                <Link href="/privacy" class="footer-bottom-link">
                  {{ t('Privacy Policy') }}
                </Link>
                <Link href="/terms" class="footer-bottom-link">
                  {{ t('Terms of Service') }}
                </Link>
                <Link href="/contact" class="footer-bottom-link">
                  {{ t('Contact') }}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useTranslation } from '@/composables/useTranslation'

// Use the global translation composable
const { currentLanguage, t } = useTranslation()

// Theme state
const currentTheme = ref('light')
const themeClass = ref('light-theme')

// Update theme class based on current theme
const updateThemeClass = () => {
  themeClass.value = currentTheme.value === 'dark' ? 'dark-theme' : 'light-theme'
}

// Handle theme change events from header
const handleThemeChange = (event) => {
  currentTheme.value = event.detail.theme
  updateThemeClass()
}

// Load initial theme
const loadInitialTheme = () => {
  const savedTheme = localStorage.getItem('preferredTheme')
  if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
    currentTheme.value = savedTheme
  } else {
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    currentTheme.value = systemPrefersDark ? 'dark' : 'light'
  }
  updateThemeClass()
}

onMounted(() => {
  loadInitialTheme()
  window.addEventListener('themeChanged', handleThemeChange)
})

onUnmounted(() => {
  window.removeEventListener('themeChanged', handleThemeChange)
})

watch(currentTheme, () => {
  updateThemeClass()
})
</script>

<style scoped>
/* Base footer styles - Ultra Compact Height */
.frontend-footer {
  margin-top: auto;
  transition: all 0.3s ease;
  padding: 0;
  min-height: auto;
  width: 100%;
  overflow: hidden;
  position: relative;
}

/* NEW: Additional padding layer for separation - REDUCED */
.footer-padding-layer {
  padding: 1rem 0 0.5rem 0;
  position: relative;
  width: 100%;
  max-width: 100%;
}

/* Add subtle top border for better separation */
.frontend-footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent 0%, var(--border-color) 50%, transparent 100%);
  opacity: 0.6;
}

/* Container fixes */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
  width: 100%;
  overflow: hidden;
}

.row {
  margin: 0 -15px;
  display: flex;
  flex-wrap: wrap;
  width: 100%;
}

.compact-row {
  margin-bottom: 0.25rem;
  width: 100%;
}

/* Horizontal section styling */
.horizontal-section {
  display: flex;
  align-items: flex-start;
}

.horizontal-row {
  display: flex;
  flex-wrap: nowrap;
  width: 100%;
  margin: 0 -8px;
}

.horizontal-col {
  padding: 0 8px;
  flex: 1;
  min-width: 0; /* Prevent overflow */
}

/* Column classes with proper responsive behavior */
.compact-col {
  margin-bottom: 0.75rem !important;
  padding: 0 15px;
  box-sizing: border-box;
  width: 100%;
}

.col-lg-4 { width: 33.333333%; }
.col-lg-8 { width: 66.666667%; }
.col-lg-3 { width: 25%; }
.col-lg-2 { width: 16.666667%; }
.col-md-12 { width: 100%; }
.col-md-6 { width: 50%; }
.col-md-4 { width: 33.333333%; }
.col-sm-4 { width: 33.333333%; }
.col-sm-6 { width: 50%; }
.col-sm-12 { width: 100%; }
.col-12 { width: 100%; }

/* Compact layout classes - FURTHER REDUCED */
.compact-logo {
  margin-bottom: 0.5rem !important;
  width: 100%;
}

.compact-description {
  margin-bottom: 0.5rem !important;
  line-height: 1.2;
  font-size: 0.8rem;
  max-width: 100%;
  overflow-wrap: break-word;
}

.compact-social {
  margin-top: 0.5rem !important;
  width: 100%;
}

.compact-heading {
  margin-bottom: 0.25rem !important;
  padding-bottom: 0.25rem !important;
  font-size: 13px;
  width: 100%;
}

.compact-links {
  margin-top: 0.125rem !important;
  width: 100%;
}

.compact-contact {
  margin-top: 0.125rem !important;
  width: 100%;
}

.compact-divider {
  margin: 0.5rem 0 0.25rem 0 !important;
  width: 100%;
}

.compact-bottom {
  padding-top: 0.125rem !important;
  width: 100%;
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
  display: inline-block !important;
}

/* Ensure all Font Awesome icons maintain their font family */
.fas, .fa, .far, .fab {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
  display: inline-block !important;
}

/* Specific fixes for Bengali language */
:global(.bn-lang) .fas,
:global(.bn-lang) .fa,
:global(.bn-lang) .far,
:global(.bn-lang) .fab,
:global(.bn-lang) .icon-fixed {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
}

/* Light theme */
.frontend-footer.light-theme {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-top: 1px solid #dee2e6;
  color: #333;
}

.frontend-footer.light-theme .footer-description {
  color: #666;
  font-size: 0.8rem;
}

.frontend-footer.light-theme .footer-heading {
  color: #2c3e50;
}

.frontend-footer.light-theme .footer-link {
  color: #555;
}

.frontend-footer.light-theme .footer-link:hover {
  color: var(--primary-color);
}

.frontend-footer.light-theme .contact-item {
  color: #555;
}

.frontend-footer.light-theme .contact-icon {
  color: var(--primary-color);
}

.frontend-footer.light-theme .footer-divider {
  border-color: #dee2e6;
  opacity: 0.7;
}

.frontend-footer.light-theme .copyright {
  color: #666;
}

.frontend-footer.light-theme .footer-bottom-link {
  color: #666;
}

.frontend-footer.light-theme .footer-bottom-link:hover {
  color: var(--primary-color);
}

/* Dark theme */
.frontend-footer.dark-theme {
  background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
  border-top: 1px solid #333;
  color: #fff;
}

.dark-theme .footer-description {
  color: #ccc;
  font-size: 0.8rem;
}

.dark-theme .footer-heading {
  color: #fff;
}

.dark-theme .footer-heading::after {
  background: linear-gradient(90deg, var(--primary-color), #3498db);
}

.dark-theme .footer-link {
  color: #ccc;
}

.dark-theme .footer-link:hover {
  color: #fff;
}

.dark-theme .contact-item {
  color: #ccc;
}

.dark-theme .contact-icon {
  color: var(--primary-color);
}

.dark-theme .footer-divider {
  border-color: #444;
  opacity: 0.5;
}

.dark-theme .copyright {
  color: #999;
}

.dark-theme .footer-bottom-link {
  color: #999;
}

.dark-theme .footer-bottom-link:hover {
  color: #fff;
}

/* Footer Logo Styles - SMALLER */
.footer-logo-container {
  display: flex;
  align-items: center;
  text-decoration: none;
  transition: opacity 0.3s ease;
  max-width: 100%;
}

.footer-logo-container:hover {
  opacity: 0.9;
}

.footer-logo-image {
  height: 28px;
  width: auto;
  object-fit: contain;
  max-width: 100%;
}

.light-theme .footer-logo-image {
  filter: brightness(0.8);
}

.dark-theme .footer-logo-image {
  filter: brightness(0) invert(1);
}

/* Footer Headings - SMALLER */
.footer-heading {
  font-weight: 600;
  font-size: 13px;
  position: relative;
  line-height: 1.2;
  max-width: 100%;
  overflow-wrap: break-word;
}

.footer-heading::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 20px;
  height: 1px;
  border-radius: 1px;
}

.light-theme .footer-heading::after {
  background: linear-gradient(90deg, var(--primary-color), #3498db);
}

.dark-theme .footer-heading::after {
  background: linear-gradient(90deg, var(--primary-color), #3498db);
}

/* Footer Links - SMALLER */
.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
  width: 100%;
}

.footer-link-item {
  margin-bottom: 0.125rem;
  width: 100%;
}

.footer-link {
  opacity: 0.8;
  transition: all 0.3s ease;
  padding: 0.0625rem 0;
  display: inline-block;
  position: relative;
  text-decoration: none;
  font-size: 12px;
  line-height: 1.2;
  max-width: 100%;
  overflow-wrap: break-word;
}

.footer-link::before {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 1px;
  transition: width 0.3s ease;
}

.light-theme .footer-link::before {
  background: linear-gradient(90deg, var(--primary-color), #3498db);
}

.dark-theme .footer-link::before {
  background: linear-gradient(90deg, var(--primary-color), #3498db);
}

.footer-link:hover {
  opacity: 1;
  transform: translateX(3px);
}

.footer-link:hover::before {
  width: 100%;
}

/* Social Links - SMALLER */
.social-links {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  max-width: 100%;
}

.social-link {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  transition: all 0.3s ease;
  text-decoration: none;
  font-size: 11px;
  flex-shrink: 0;
}

.light-theme .social-link {
  background: rgba(0, 0, 0, 0.1);
  color: #555;
}

.dark-theme .social-link {
  background: rgba(255, 255, 255, 0.1);
  color: #ccc;
}

.social-link:hover {
  background: linear-gradient(135deg, var(--primary-color), #3498db);
  color: #ffffff !important;
  transform: translateY(-1px);
  box-shadow: 0 2px 6px rgba(231, 76, 60, 0.4);
}

/* Contact Items - SMALLER */
.contact-info {
  list-style: none;
  padding: 0;
  margin: 0;
  width: 100%;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  padding: 0.125rem 0;
  transition: all 0.3s ease;
  margin-bottom: 0.125rem;
  width: 100%;
}

.contact-item:hover {
  transform: translateX(2px);
}

.contact-icon {
  width: 10px;
  margin-right: 6px;
  margin-top: 1px;
  flex-shrink: 0;
  font-size: 10px;
}

.contact-text {
  line-height: 1.2;
  font-size: 11px;
  flex: 1;
  max-width: calc(100% - 16px);
  overflow-wrap: break-word;
}

/* Footer Bottom - SMALLER */
.footer-divider {
  opacity: 0.5;
  border: none;
  border-top: 1px solid;
  width: 100%;
  max-width: 100%;
}

.copyright {
  margin: 0;
  font-size: 11px;
  line-height: 1.2;
  max-width: 100%;
  overflow-wrap: break-word;
}

.footer-bottom-links {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
  flex-wrap: wrap;
  max-width: 100%;
}

.footer-bottom-link {
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 11px;
  line-height: 1.2;
  white-space: nowrap;
}

.footer-bottom-link:hover {
  text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .col-lg-4,
  .col-lg-8 {
    width: 100%;
  }
  
  .horizontal-row {
    flex-wrap: wrap;
  }
  
  .horizontal-col {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
  }
}

@media (max-width: 992px) {
  .col-md-12 { width: 100%; }
  .col-md-6 { width: 50%; }
  .col-md-4 { width: 33.333333%; }
  
  .footer-padding-layer {
    padding: 0.75rem 0 0.375rem 0;
  }
  
  .horizontal-row {
    flex-wrap: wrap;
  }
  
  .horizontal-col {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
  }
}

@media (max-width: 768px) {
  .frontend-footer {
    text-align: center;
  }
  
  .col-md-6,
  .col-md-4 {
    width: 100%;
  }
  
  .col-sm-4 {
    width: 33.333333%;
  }
  
  .compact-col {
    margin-bottom: 1rem !important;
  }
  
  .footer-logo-container {
    justify-content: center;
  }
  
  .footer-logo-image {
    height: 24px;
  }
  
  .social-links {
    justify-content: center;
    gap: 4px;
  }
  
  .footer-heading::after {
    left: 50%;
    transform: translateX(-50%);
  }
  
  .footer-bottom {
    text-align: center;
  }
  
  .footer-bottom-links {
    justify-content: center;
    gap: 0.5rem;
  }
  
  .footer-link-item {
    margin-bottom: 0.0625rem;
  }
  
  .contact-item {
    justify-content: center;
    text-align: center;
    padding: 0.0625rem 0;
  }
  
  .contact-text {
    text-align: center;
  }
  
  /* Horizontal layout adjustments for tablets */
  .horizontal-row {
    flex-wrap: nowrap;
  }
  
  .horizontal-col {
    flex: 1;
    max-width: 100%;
  }
}

@media (max-width: 576px) {
  .col-sm-4 {
    width: 100%;
  }
  
  .footer-padding-layer {
    padding: 0.5rem 0 0.25rem 0;
  }
  
  .footer-logo-image {
    height: 22px;
  }
  
  .footer-heading {
    font-size: 12px;
  }
  
  .footer-link {
    font-size: 11px;
  }
  
  .contact-text {
    font-size: 10px;
  }
  
  .social-link {
    width: 24px;
    height: 24px;
    font-size: 10px;
  }
  
  .footer-bottom-links {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .footer-bottom-link {
    display: block;
  }
  
  /* Stack horizontal sections on mobile */
  .horizontal-row {
    flex-wrap: wrap;
  }
  
  .horizontal-col {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 0.75rem;
  }
  
  .horizontal-col:last-child {
    margin-bottom: 0;
  }
}

/* Extra padding for very small screens */
@media (max-width: 375px) {
  .footer-padding-layer {
    padding: 0.375rem 0 0.125rem 0;
  }
  
  .container {
    padding-left: 10px;
    padding-right: 10px;
  }
  
  .social-links {
    gap: 3px;
  }
  
  .social-link {
    width: 22px;
    height: 22px;
    font-size: 9px;
  }
}

/* Ensure proper spacing and no overflow */
:deep(*) {
  box-sizing: border-box;
}

/* Prevent horizontal scroll on body */
:deep(body) {
  overflow-x: hidden;
}

/* Ensure all content stays within bounds */
:deep(.container) {
  max-width: 100%;
  overflow: hidden;
}

/* Fix for very long words */
:deep(.footer-description),
:deep(.contact-text),
:deep(.footer-link) {
  word-break: break-word;
  overflow-wrap: break-word;
}
</style>
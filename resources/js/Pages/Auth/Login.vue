<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-sm border border-gray-200 p-8">
      <!-- Logo -->
      <div class="flex justify-center mb-6">
        <div class="logo-container flex items-center">
          <img 
            src="../../../../public/assets/logo.png" 
            alt="InvestPro" 
            class="logo-image h-12 w-auto"
            @error="handleImageError"
          >
        </div>
      </div>

      <!-- Title -->
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-1">Sign In</h2>
      <p class="text-center text-gray-500 mb-6">Access your investment portal</p>

      <!-- Role Tabs -->
      <div class="mb-6">
        <div class="flex border-b border-gray-200">
          <button
            v-for="tab in tabs"
            :key="tab.value"
            type="button"
            :class="[
              'flex-1 py-3 px-4 text-sm font-medium border-b-2 transition-colors',
              activeTab === tab.value
                ? 'border-indigo-600 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
            @click="activeTab = tab.value"
          >
            {{ tab.label }}
          </button>
        </div>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit">
        <!-- Username/Email -->
        <div class="mb-4">
          <label for="email" class="block text-gray-700 text-sm font-medium mb-2">
            {{ activeTab === 'investor' ? 'Email Address' : 'Username' }}
          </label>
          <input
            id="email"
            v-model="form.email"
            :type="activeTab === 'investor' ? 'email' : 'text'"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
            :placeholder="activeTab === 'investor' ? 'Enter your email' : 'Enter your username'"
            :class="{ 'border-red-500': form.errors.email }"
          />
          <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
            {{ form.errors.email }}
          </div>
        </div>

        <!-- Password -->
        <div class="mb-5">
          <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
            placeholder="Enter your password"
            :class="{ 'border-red-500': form.errors.password }"
          />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
            {{ form.errors.password }}
          </div>
        </div>

        <!-- Remember me / Forgot -->
        <div class="flex items-center justify-between mb-6">
          <label class="flex items-center text-sm text-gray-700 cursor-pointer">
            <input 
              type="checkbox" 
              v-model="form.remember" 
              class="mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-colors" 
            /> 
            Remember me
          </label>
          <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition-colors">
            Forgot Password?
          </a>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="form.processing"
          :class="[
            'w-full py-3 rounded-md text-white font-semibold transition-all focus:outline-none focus:ring-2 focus:ring-offset-2',
            form.processing 
              ? 'bg-gray-400 cursor-not-allowed focus:ring-gray-400' 
              : activeTab === 'investor' 
                ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500 shadow-sm hover:shadow-md'
                : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 shadow-sm hover:shadow-md'
          ]"
        >
          {{ form.processing ? 'Signing in...' : `Sign In as ${activeTab === 'investor' ? 'Investor' : 'Administrator'}` }}
        </button>
      </form>

      <!-- Registration Links -->
      <div class="text-center mt-6">
        <p class="text-sm text-gray-600">
          Don't have an account?
          <a 
            :href="activeTab === 'investor' ? '/investor/register' : '/admin/register'" 
            class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors"
          >
            Create {{ activeTab === 'investor' ? 'Investor' : 'Admin' }} account
          </a>
        </p>
      </div>

      <!-- Switch Portal -->
      <div class="text-center mt-4 pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-600">
          Looking for 
          <button 
            type="button" 
            @click="switchTab"
            class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors ml-1"
          >
            {{ activeTab === 'investor' ? 'Administrator' : 'Investor' }} portal?
          </button>
        </p>
      </div>

      <!-- General Error -->
      <div v-if="form.errors.message" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
        <p class="text-red-600 text-sm text-center">{{ form.errors.message }}</p>
      </div>

      <!-- Copyright -->
      <div class="text-center mt-6 pt-4 border-t border-gray-100">
        <p class="text-xs text-gray-500">
          &copy; {{ new Date().getFullYear() }} InvestPro. All rights reserved.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const tabs = [
  { label: "Investor", value: "investor" },
  { label: "Administrator", value: "admin" }
];

const activeTab = ref('investor');

const form = useForm({
  email: '',
  password: '',
  remember: false,
  user_type: 'investor'
});

// Update user_type when tab changes
watch(activeTab, (newTab) => {
  form.user_type = newTab;
  form.clearErrors();
});

// Handle logo image loading error
const handleImageError = (event) => {
  console.log('Logo image failed to load, using fallback');
  event.target.style.display = 'none';
};

// Switch between tabs
const switchTab = () => {
  activeTab.value = activeTab.value === 'investor' ? 'admin' : 'investor';
};

const submit = () => {
  const loginRoute = activeTab.value === 'investor' ? '/investor/login' : '/admin/login';
  
  console.log('🔄 Login attempt:', {
    route: loginRoute,
    email: form.email,
    tab: activeTab.value
  });

  // Use Inertia's form submission
  form.post(loginRoute, {
    preserveScroll: true,
    onSuccess: () => {
      console.log('✅ Login successful - Inertia will handle redirect');
    },
    onError: (errors) => {
      console.log('❌ Login failed with errors:', errors);
    },
    onFinish: () => {
      console.log('🏁 Login attempt finished');
    }
  });
};
</script>

<style scoped>
/* Use deep selector to override */
:deep(*) {
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 400;
}

/* Logo Styles */
.logo-container {
  transition: transform 0.2s ease;
}

.logo-container:hover {
  transform: scale(1.02);
}

.logo-image {
  object-fit: contain;
}

/* Smooth transitions for interactive elements */
button, a, input {
  transition: all 0.2s ease-in-out;
}

/* Focus styles for accessibility */
input:focus, 
button:focus {
  outline: 2px solid #6366f1;
  outline-offset: 2px;
}

input:focus {
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

button:focus:not(:disabled) {
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

/* Form input focus styles */
input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Button hover and focus states */
button:not(:disabled):hover {
  transform: translateY(-1px);
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .logo-container {
    flex-direction: column;
    text-align: center;
  }
  
  .w-full.max-w-md {
    padding: 1.5rem;
  }
}

/* Loading state for button */
button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

/* Link hover effects */
a:hover {
  text-decoration: underline;
}

/* Checkbox focus style */
input[type="checkbox"]:focus {
  outline: 2px solid #6366f1;
  outline-offset: 2px;
}

/* Error state enhancements */
.border-red-500:focus {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

/* Tab active state enhancements */
button[class*="border-indigo-600"] {
  font-weight: 600;
}
</style>
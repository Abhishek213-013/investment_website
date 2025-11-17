<template>
  <div class="min-h-screen bg-gray-50 flex" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol' !important;">
    <!-- Sidebar -->
    <InvestorSidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <InvestorNavbar page-title="Investor Dashboard" @search="handleSearch" />
      
      <!-- Page Content -->
      <div class="p-6">
        <!-- Welcome Message -->
        <div class="mb-6">
          <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome back, {{ getUserName() }}!</h2>
          <p class="text-gray-600">Here's an overview of your investment portfolio and recent activities.</p>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <Link href="/investor/investments" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-blue-500 transition-colors text-left block">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-sm">Browse Investments</h3>
                <p class="text-xs text-gray-600">Explore new opportunities</p>
              </div>
            </div>
          </Link>

          <Link href="/investor/portfolio" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-green-500 transition-colors text-left block">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-sm">My Portfolio</h3>
                <p class="text-xs text-gray-600">View your investments</p>
              </div>
            </div>
          </Link>

          <Link href="/investor/transactions" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-purple-500 transition-colors text-left block">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-sm">Transactions</h3>
                <p class="text-xs text-gray-600">View transaction history</p>
              </div>
            </div>
          </Link>

          <Link href="/investor/profile" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-indigo-500 transition-colors text-left block">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-indigo-100 rounded-lg">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-sm">Profile Settings</h3>
                <p class="text-xs text-gray-600">Manage your account</p>
              </div>
            </div>
          </Link>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Portfolio Value</p>
                <h3 class="text-2xl font-bold text-blue-600">${{ (stats.portfolioValue || 0).toLocaleString() }}</h3>
                <p class="text-xs text-green-600 mt-1">
                  <span class="inline-flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    {{ stats.portfolioGrowth || '5.2' }}%
                  </span>
                </p>
              </div>
              <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Active Investments</p>
                <h3 class="text-2xl font-bold text-green-600">{{ stats.activeInvestments || 0 }}</h3>
                <p class="text-xs text-green-600 mt-1">
                  <span class="inline-flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    {{ stats.newInvestments || '2' }} new
                  </span>
                </p>
              </div>
              <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
            </div>
          </div>

          <Link href="/investor/returns" class="block">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:border-yellow-400 transition-colors cursor-pointer">
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-xs font-medium text-gray-600 mb-1">Total Returns</p>
                  <h3 class="text-2xl font-bold text-yellow-600">${{ (stats.totalReturns || 0).toLocaleString() }}</h3>
                  <p class="text-xs text-yellow-600 mt-1">
                    View detailed returns
                  </p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg">
                  <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
              </div>
            </div>
          </Link>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Pending Returns</p>
                <h3 class="text-2xl font-bold text-purple-600">${{ (stats.pendingReturns || 0).toLocaleString() }}</h3>
                <p class="text-xs text-green-600 mt-1">
                  <span class="inline-flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Next payout in {{ stats.nextPayoutDays || '7' }} days
                  </span>
                </p>
              </div>
              <div class="p-3 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts and Additional Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Recent Activity -->
          <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Investment Activity</h3>
            <div class="space-y-4">
              <div v-for="activity in recentActivities" :key="activity.id" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-4">
                  <div :class="`w-10 h-10 rounded-full flex items-center justify-center ${activity.iconBg}`">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path :d="activity.icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-800 text-sm">{{ activity.title }}</p>
                    <p class="text-xs text-gray-600">{{ activity.description }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-800">{{ activity.amount }}</p>
                  <p class="text-xs text-gray-500">{{ activity.time }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Portfolio Overview -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Portfolio Overview</h3>
            <div class="space-y-4">
              <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                <span class="text-xs font-medium text-blue-800">Available Balance</span>
                <span class="font-bold text-blue-600 text-sm">${{ (stats.availableBalance || 15000).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                <span class="text-xs font-medium text-green-800">This Month's Returns</span>
                <span class="font-bold text-green-600 text-sm">${{ (stats.monthlyReturns || 1200).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                <span class="text-xs font-medium text-yellow-800">Investment Performance</span>
                <span class="font-bold text-yellow-600 text-sm">{{ stats.performance || '+8.5' }}%</span>
              </div>
              <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                <span class="text-xs font-medium text-purple-800">Total Invested</span>
                <span class="font-bold text-purple-600 text-sm">${{ (stats.totalInvested || 85000).toLocaleString() }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Investment Opportunities -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Recommended Investment Opportunities</h3>
            <Link href="/investor/investments" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
              View All Opportunities
            </Link>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div v-for="opportunity in investmentOpportunities" :key="opportunity.id" class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition-colors">
              <div class="flex justify-between items-start mb-3">
                <h4 class="font-semibold text-gray-800 text-sm">{{ opportunity.name }}</h4>
                <span :class="`px-2 py-1 rounded-full text-xs font-medium ${opportunity.riskClass}`">
                  {{ opportunity.risk }}
                </span>
              </div>
              <p class="text-xs text-gray-600 mb-3">{{ opportunity.description }}</p>
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-green-600">{{ opportunity.roi }}</span>
                <span class="text-xs text-gray-500">{{ opportunity.duration }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import InvestorNavbar from '@/Layouts/InvestorNavbar.vue';
import InvestorSidebar from '@/Layouts/InvestorSidebar.vue';
import { Link, usePage } from '@inertiajs/vue3';

// Props from Laravel backend
const props = defineProps({
  stats: {
    type: Object,
    default: () => ({})
  },
  recentActivities: {
    type: Array,
    default: () => []
  }
});

// Safe user name getter
const getUserName = () => {
  try {
    const page = usePage?.();
    return page?.props?.auth?.user?.name || 'Investor';
  } catch (error) {
    console.log('Auth data not available:', error);
    return 'Investor';
  }
};

// Handle search
const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

// Mock data for investment opportunities
const investmentOpportunities = [
  {
    id: 1,
    name: 'Tech Startup Fund',
    description: 'Early-stage technology companies',
    roi: '12.5% ROI',
    duration: '24 months',
    risk: 'Medium',
    riskClass: 'bg-yellow-100 text-yellow-800'
  },
  {
    id: 2,
    name: 'Real Estate Trust',
    description: 'Commercial property investments',
    roi: '8.2% ROI',
    duration: '36 months',
    risk: 'Low',
    riskClass: 'bg-green-100 text-green-800'
  },
  {
    id: 3,
    name: 'Green Energy Fund',
    description: 'Renewable energy projects',
    roi: '15.3% ROI',
    duration: '48 months',
    risk: 'High',
    riskClass: 'bg-red-100 text-red-800'
  }
];

// Use provided data or fallback to mock data
const recentActivities = props.recentActivities.length > 0 ? props.recentActivities : [
  {
    id: 1,
    title: 'Investment Made',
    description: 'Tech Startup Fund - $25,000',
    amount: '-$25,000',
    time: '2 days ago',
    icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    iconBg: 'bg-blue-500'
  },
  {
    id: 2,
    title: 'Return Received',
    description: 'Quarterly dividend payment',
    amount: '+$1,250',
    time: '1 week ago',
    icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    iconBg: 'bg-green-500'
  },
  {
    id: 3,
    title: 'New Opportunity',
    description: 'Green Energy Fund available',
    amount: 'Available',
    time: '3 days ago',
    icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
    iconBg: 'bg-purple-500'
  }
];
</script>

<style scoped>
/* Use deep selector to override */
:deep(*) {
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 400;
}
</style>
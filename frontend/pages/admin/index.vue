<template>
  <div class="container mx-auto p-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-2xl p-8 mb-8 text-white">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-bold mb-2">
            <i class="fas fa-chart-line mr-3"></i>Admin Dashboard
          </h1>
          <p class="text-blue-100 text-lg">FEEDBACK 360 - ระบบประเมินพนักงาน 360 องศา</p>
        </div>
        <div class="text-right">
          <div class="text-3xl font-bold">{{ currentTime }}</div>
          <div class="text-blue-100">{{ currentDate }}</div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Total Users Card -->
      <div
        class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105 hover:shadow-2xl">
        <div class="flex items-center justify-between mb-4">
          <div class="bg-white bg-opacity-30 rounded-full p-4">
            <i class="fas fa-users text-3xl"></i>
          </div>
          <div class="text-right">
            <div class="text-4xl font-bold">{{ stats.totalUsers }}</div>
          </div>
        </div>
        <div class="text-blue-100 text-sm font-semibold uppercase">ผู้ใช้ทั้งหมด</div>
        <div class="mt-2 text-xs text-blue-200">
          <i class="fas fa-arrow-up mr-1"></i>Active Users
        </div>
      </div>

      <!-- Total Surveys Card -->
      <div
        class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105 hover:shadow-2xl">
        <div class="flex items-center justify-between mb-4">
          <div class="bg-white bg-opacity-30 rounded-full p-4">
            <i class="fas fa-clipboard-list text-3xl"></i>
          </div>
          <div class="text-right">
            <div class="text-4xl font-bold">{{ stats.totalSurveys }}</div>
          </div>
        </div>
        <div class="text-green-100 text-sm font-semibold uppercase">แบบประเมินทั้งหมด</div>
        <div class="mt-2 text-xs text-green-200">
          <i class="fas fa-check-circle mr-1"></i>Active: {{ stats.activeSurveys }}
        </div>
      </div>

      <!-- Total Responses Card -->
      <div
        class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105 hover:shadow-2xl">
        <div class="flex items-center justify-between mb-4">
          <div class="bg-white bg-opacity-30 rounded-full p-4">
            <i class="fas fa-edit text-3xl"></i>
          </div>
          <div class="text-right">
            <div class="text-4xl font-bold">{{ stats.totalResponses }}</div>
          </div>
        </div>
        <div class="text-purple-100 text-sm font-semibold uppercase">การประเมินทั้งหมด</div>
        <div class="mt-2 text-xs text-purple-200">
          <i class="fas fa-chart-line mr-1"></i>This Month: {{ stats.monthlyResponses }}
        </div>
      </div>

      <!-- Completion Rate Card -->
      <div
        class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105 hover:shadow-2xl">
        <div class="flex items-center justify-between mb-4">
          <div class="bg-white bg-opacity-30 rounded-full p-4">
            <i class="fas fa-percentage text-3xl"></i>
          </div>
          <div class="text-right">
            <div class="text-4xl font-bold">{{ stats.completionRate }}%</div>
          </div>
        </div>
        <div class="text-orange-100 text-sm font-semibold uppercase">อัตราการทำแบบประเมิน</div>
        <div class="mt-2 text-xs text-orange-200">
          <i class="fas fa-trophy mr-1"></i>Completion Status
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-bolt text-yellow-500 mr-2"></i>Quick Actions
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <NuxtLink to="/admin/survey_list"
          class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-lg p-6 hover:shadow-lg transition-all transform hover:scale-105 text-center">
          <i class="fas fa-clipboard-list text-4xl text-blue-600 mb-3"></i>
          <div class="font-semibold text-gray-800">จัดการแบบประเมิน</div>
          <div class="text-sm text-gray-600 mt-1">สร้าง/แก้ไขแบบประเมิน</div>
        </NuxtLink>

        <NuxtLink to="/admin/user_list"
          class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-lg p-6 hover:shadow-lg transition-all transform hover:scale-105 text-center">
          <i class="fas fa-users text-4xl text-green-600 mb-3"></i>
          <div class="font-semibold text-gray-800">จัดการผู้ใช้</div>
          <div class="text-sm text-gray-600 mt-1">เพิ่ม/แก้ไขผู้ใช้</div>
        </NuxtLink>

        <NuxtLink to="/admin/emp_list"
          class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-lg p-6 hover:shadow-lg transition-all transform hover:scale-105 text-center">
          <i class="fas fa-list-check text-4xl text-purple-600 mb-3"></i>
          <div class="font-semibold text-gray-800">รายการพนักงาน</div>
          <div class="text-sm text-gray-600 mt-1">ดูรายการประเมิน</div>
        </NuxtLink>

        <NuxtLink to="/admin/user_data"
          class="bg-gradient-to-br from-orange-50 to-orange-100 border-2 border-orange-200 rounded-lg p-6 hover:shadow-lg transition-all transform hover:scale-105 text-center">
          <i class="fas fa-chart-bar text-4xl text-orange-600 mb-3"></i>
          <div class="font-semibold text-gray-800">รายงานผล</div>
          <div class="text-sm text-gray-600 mt-1">ดูผลการประเมิน</div>
        </NuxtLink>

        <!-- Test API Action -->
        <button @click="testApi"
          class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-200 rounded-lg p-6 hover:shadow-lg transition-all transform hover:scale-105 text-center">
          <i class="fas fa-plug text-4xl text-gray-700 mb-3"></i>
          <div class="font-semibold text-gray-800">ทดสอบ API</div>
          <div class="text-sm text-gray-600 mt-1">เช็คการเชื่อมต่อ Backend</div>
        </button>
      </div>

      <!-- Test API Result -->
      <div v-if="testLoading" class="mt-6 text-sm text-gray-600">
        กำลังทดสอบการเชื่อมต่อ...
      </div>
      <div v-if="testError" class="mt-4 p-4 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
        {{ testError }}
      </div>
      <div v-if="testResult" class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded text-sm">
        <div class="font-semibold mb-2">ผลลัพธ์จาก API</div>
        <pre class="whitespace-pre-wrap break-all">{{ JSON.stringify(testResult, null, 2) }}</pre>
      </div>
    </div>

    <!-- Recent Activities and Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <!-- Recent Surveys -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
          <i class="fas fa-clock text-blue-500 mr-2"></i>แบบประเมินล่าสุด
        </h2>
        <div class="space-y-4">
          <div v-for="survey in recentSurveys" :key="survey.sub_id"
            class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg hover:shadow-md transition-all">
            <div class="flex-1">
              <div class="font-semibold text-gray-800">{{ survey.sub_title }}</div>
              <div class="text-sm text-gray-600 mt-1">{{ survey.sub_discrip }}</div>
              <div class="flex items-center space-x-4 mt-2">
                <span :class="survey.sub_status == '1' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  class="px-2 py-1 rounded-full text-xs font-semibold">
                  {{ survey.sub_status == '1' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                </span>
                <span class="text-xs text-gray-500">
                  <i class="fas fa-calendar mr-1"></i>{{ formatDate(survey.created_at) }}
                </span>
              </div>
            </div>
            <NuxtLink :to="`/admin/question_list?id=${survey.sub_id}&group=${survey.type_group}`"
              class="ml-4 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-all">
              <i class="fas fa-edit mr-1"></i>จัดการ
            </NuxtLink>
          </div>
          <div v-if="recentSurveys.length === 0" class="text-center py-8 text-gray-500">
            <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
            ยังไม่มีแบบประเมิน
          </div>
        </div>
      </div>

      <!-- Survey Status Chart -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
          <i class="fas fa-chart-pie text-green-500 mr-2"></i>สถานะแบบประเมิน
        </h2>
        <div class="flex items-center justify-center" style="height: 300px;">
          <canvas ref="statusChartCanvas"></canvas>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-6">
          <div class="bg-green-50 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-green-600">{{ stats.activeSurveys }}</div>
            <div class="text-sm text-gray-600 mt-1">แบบประเมินที่เปิดใช้งาน</div>
          </div>
          <div class="bg-red-50 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-red-600">{{ stats.inactiveSurveys }}</div>
            <div class="text-sm text-gray-600 mt-1">แบบประเมินที่ปิดใช้งาน</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Monthly Activity Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-chart-line text-purple-500 mr-2"></i>กิจกรรมรายเดือน
      </h2>
      <div style="height: 300px;">
        <canvas ref="activityChartCanvas"></canvas>
      </div>
    </div>

    <!-- System Information -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg p-6 border-l-4 border-blue-600">
        <div class="flex items-center mb-4">
          <i class="fas fa-server text-3xl text-blue-600 mr-4"></i>
          <div>
            <div class="font-bold text-gray-800">System Status</div>
            <div class="text-sm text-gray-600">ระบบทำงานปกติ</div>
          </div>
        </div>
        <div class="flex items-center text-green-600">
          <i class="fas fa-check-circle mr-2"></i>
          <span class="text-sm font-semibold">All Systems Operational</span>
        </div>
      </div>

      <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-lg p-6 border-l-4 border-green-600">
        <div class="flex items-center mb-4">
          <i class="fas fa-database text-3xl text-green-600 mr-4"></i>
          <div>
            <div class="font-bold text-gray-800">Database</div>
            <div class="text-sm text-gray-600">เชื่อมต่อสำเร็จ</div>
          </div>
        </div>
        <div class="flex items-center text-green-600">
          <i class="fas fa-check-circle mr-2"></i>
          <span class="text-sm font-semibold">Connected</span>
        </div>
      </div>

      <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-lg p-6 border-l-4 border-purple-600">
        <div class="flex items-center mb-4">
          <i class="fas fa-code-branch text-3xl text-purple-600 mr-4"></i>
          <div>
            <div class="font-bold text-gray-800">Version</div>
            <div class="text-sm text-gray-600">FEEDBACK 360 v2.0</div>
          </div>
        </div>
        <div class="flex items-center text-purple-600">
          <i class="fas fa-info-circle mr-2"></i>
          <span class="text-sm font-semibold">Latest Version</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Chart, registerables } from 'chart.js'

definePageMeta({
  layout: 'admin'
})

Chart.register(...registerables)

const { $axios } = useNuxtApp()

const currentTime = ref('')
const currentDate = ref('')
const stats = ref({
  totalUsers: 0,
  totalSurveys: 0,
  totalResponses: 0,
  completionRate: 0,
  activeSurveys: 0,
  inactiveSurveys: 0,
  monthlyResponses: 0
})
const recentSurveys = ref([])

// Test API state
const testLoading = ref(false)
const testError = ref('')
const testResult = ref(null)

const statusChartCanvas = ref(null)
const activityChartCanvas = ref(null)

let statusChart = null
let activityChart = null

const updateDateTime = () => {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('th-TH', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
  currentDate.value = now.toLocaleDateString('th-TH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    weekday: 'long'
  })
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('th-TH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const loadStats = async () => {
  try {
    // Load surveys
    const surveysResponse = await $axios.get('/Backend/api/surveys.php')
    if (surveysResponse.success) {
      stats.value.totalSurveys = surveysResponse.data.length
      stats.value.activeSurveys = surveysResponse.data.filter(s => s.sub_status == '1').length
      stats.value.inactiveSurveys = surveysResponse.data.filter(s => s.sub_status == '0').length
      recentSurveys.value = surveysResponse.data.slice(0, 5)
    }

    // Simulate other stats (replace with real API calls)
    stats.value.totalUsers = 150
    stats.value.totalResponses = 450
    stats.value.completionRate = 75
    stats.value.monthlyResponses = 85

  } catch (error) {
    console.error('Error loading stats:', error)
  }
}

const createStatusChart = () => {
  if (statusChartCanvas.value) {
    const ctx = statusChartCanvas.value.getContext('2d')
    statusChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['เปิดใช้งาน', 'ปิดใช้งาน'],
        datasets: [{
          data: [stats.value.activeSurveys, stats.value.inactiveSurveys],
          backgroundColor: [
            'rgba(34, 197, 94, 0.8)',
            'rgba(239, 68, 68, 0.8)'
          ],
          borderColor: [
            'rgba(34, 197, 94, 1)',
            'rgba(239, 68, 68, 1)'
          ],
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              font: {
                size: 14
              },
              padding: 15
            }
          }
        }
      }
    })
  }
}

const createActivityChart = () => {
  if (activityChartCanvas.value) {
    const ctx = activityChartCanvas.value.getContext('2d')
    activityChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.'],
        datasets: [{
          label: 'การประเมินที่เสร็จสิ้น',
          data: [65, 78, 90, 81, 95, 85],
          borderColor: 'rgba(59, 130, 246, 1)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4,
          fill: true,
          borderWidth: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'top'
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    })
  }
}

onMounted(async () => {
  updateDateTime()
  setInterval(updateDateTime, 1000)

  await loadStats()

  nextTick(() => {
    createStatusChart()
    createActivityChart()
  })
})

onUnmounted(() => {
  if (statusChart) statusChart.destroy()
  if (activityChart) activityChart.destroy()
})

useHead({
  title: 'Admin Dashboard - FEEDBACK 360'
})

// Call backend test endpoint
const testApi = async () => {
  try {
    testLoading.value = true
    testError.value = ''
    testResult.value = null

    // ใช้ URL เต็มเพื่อตัดปัญหา proxy ระหว่าง dev
    const res = await $axios.get('http://localhost/360/api/test')
    testResult.value = res
  } catch (e) {
    console.error('Test API error:', e)
    testError.value = e?.message || 'เชื่อมต่อ API ไม่สำเร็จ'
  } finally {
    testLoading.value = false
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.container>div {
  animation: fadeIn 0.5s ease-out;
}
</style>

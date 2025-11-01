<template>
  <div class="container mx-auto p-6">
    <h3 class="text-center text-3xl font-bold text-blue-600 mb-8">
      <i class="fas fa-chart-radar mr-2"></i>Spider Chart - กราฟแสดงผลการประเมิน
    </h3>

    <div class="mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-md">
          <h4 class="text-lg font-semibold mb-2">เลือกผู้ถูกประเมิน</h4>
          <select
            v-model="selectedUser"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">โปรดเลือกผู้ถูกประเมิน</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }} ({{ user.emp_id }})
            </option>
          </select>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-md">
          <h4 class="text-lg font-semibold mb-2">เลือกหัวข้อ</h4>
          <select
            v-model="selectedSubject"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">โปรดเลือกหัวข้อ</option>
            <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
              {{ subject.title }}
            </option>
          </select>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-md flex items-end">
          <button
            @click="generateChart"
            :disabled="!selectedUser || !selectedSubject"
            class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            สร้างกราฟ
          </button>
        </div>
      </div>
    </div>

    <div v-if="chartData" class="space-y-6">
      <!-- Chart Container -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-center mb-6">
          <h4 class="text-xl font-semibold text-gray-900">Spider Chart</h4>
          <p class="text-gray-600">{{ selectedUserName }} - {{ selectedSubjectTitle }}</p>
        </div>
        
        <div class="flex justify-center">
          <div class="w-96 h-96">
            <canvas ref="spiderChart" width="400" height="400"></canvas>
          </div>
        </div>
      </div>

      <!-- Chart Legend -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h4 class="text-lg font-semibold mb-4">คำอธิบายกราฟ</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div v-for="dimension in chartData.dimensions" :key="dimension.name" class="flex items-center">
            <div class="w-4 h-4 rounded-full mr-3" :style="{ backgroundColor: dimension.color }"></div>
            <span class="text-sm">{{ dimension.name }}: {{ dimension.score }}/10</span>
          </div>
        </div>
      </div>

      <!-- Summary Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-50 p-4 rounded-lg">
          <h4 class="text-sm font-medium text-blue-800">คะแนนเฉลี่ยรวม</h4>
          <p class="text-2xl font-bold text-blue-900">{{ chartData.overall_average }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
          <h4 class="text-sm font-medium text-green-800">จุดแข็งสูงสุด</h4>
          <p class="text-lg font-bold text-green-900">{{ chartData.highest_score }}</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg">
          <h4 class="text-sm font-medium text-red-800">จุดที่ต้องพัฒนา</h4>
          <p class="text-lg font-bold text-red-900">{{ chartData.lowest_score }}</p>
        </div>
      </div>

      <!-- Export Options -->
      <div class="flex justify-center space-x-4">
        <button
          @click="exportChart"
          class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <i class="fas fa-download mr-2"></i>Export Chart
        </button>
        <button
          @click="printChart"
          class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
        >
          <i class="fas fa-print mr-2"></i>Print Chart
        </button>
      </div>
    </div>

    <div v-else-if="selectedUser && selectedSubject" class="text-center py-12">
      <div class="text-gray-500 text-lg">กดปุ่ม "สร้างกราฟ" เพื่อดูผลลัพธ์</div>
    </div>
  </div>
</template>

<script setup>
import { Chart, registerables } from 'chart.js'

definePageMeta({
  layout: 'admin'
})

Chart.register(...registerables)

const selectedUser = ref('')
const selectedSubject = ref('')
const chartData = ref(null)
const spiderChart = ref(null)

const users = ref([
  { id: 1, name: 'สมชาย ใจดี', emp_id: 'E001' },
  { id: 2, name: 'สมหญิง สวยงาม', emp_id: 'E002' },
  { id: 3, name: 'สมศักดิ์ เก่งมาก', emp_id: 'E003' }
])

const subjects = ref([
  { id: 1, title: 'การประเมินผลงานประจำปี 2024' },
  { id: 2, title: 'การประเมินทักษะการทำงานเป็นทีม' },
  { id: 3, title: 'การประเมินภาวะผู้นำ' }
])

const selectedUserName = computed(() => {
  const user = users.value.find(u => u.id == selectedUser.value)
  return user ? user.name : ''
})

const selectedSubjectTitle = computed(() => {
  const subject = subjects.value.find(s => s.id == selectedSubject.value)
  return subject ? subject.title : ''
})

const generateChart = () => {
  console.log('Generate spider chart for user:', selectedUser.value, 'subject:', selectedSubject.value)
  
  // Mock data for spider chart
  chartData.value = {
    dimensions: [
      { name: 'ความสามารถในการทำงาน', score: 8.2, color: '#3B82F6' },
      { name: 'การทำงานเป็นทีม', score: 7.5, color: '#10B981' },
      { name: 'ทักษะการสื่อสาร', score: 7.9, color: '#F59E0B' },
      { name: 'ภาวะผู้นำ', score: 7.3, color: '#EF4444' },
      { name: 'ความคิดสร้างสรรค์', score: 8.0, color: '#8B5CF6' },
      { name: 'การแก้ปัญหา', score: 7.8, color: '#06B6D4' }
    ],
    overall_average: 7.8,
    highest_score: 'ความสามารถในการทำงาน (8.2)',
    lowest_score: 'ภาวะผู้นำ (7.3)'
  }
  
  // Create simple spider chart visualization
  nextTick(() => {
    createSpiderChart()
  })
  
  alert('สร้างกราฟเรียบร้อย - Template only')
}

const createSpiderChart = () => {
  if (!spiderChart.value || !chartData.value) return
  
  const ctx = spiderChart.value.getContext('2d')
  const centerX = 200
  const centerY = 200
  const radius = 150
  const numDimensions = chartData.value.dimensions.length
  
  // Clear canvas
  ctx.clearRect(0, 0, 400, 400)
  
  // Draw grid circles
  for (let i = 1; i <= 5; i++) {
    ctx.beginPath()
    ctx.arc(centerX, centerY, (radius * i) / 5, 0, 2 * Math.PI)
    ctx.strokeStyle = '#E5E7EB'
    ctx.lineWidth = 1
    ctx.stroke()
  }
  
  // Draw grid lines
  for (let i = 0; i < numDimensions; i++) {
    const angle = (2 * Math.PI * i) / numDimensions - Math.PI / 2
    const x = centerX + Math.cos(angle) * radius
    const y = centerY + Math.sin(angle) * radius
    
    ctx.beginPath()
    ctx.moveTo(centerX, centerY)
    ctx.lineTo(x, y)
    ctx.strokeStyle = '#E5E7EB'
    ctx.lineWidth = 1
    ctx.stroke()
  }
  
  // Draw data points and lines
  ctx.beginPath()
  chartData.value.dimensions.forEach((dimension, index) => {
    const angle = (2 * Math.PI * index) / numDimensions - Math.PI / 2
    const distance = (radius * dimension.score) / 10
    const x = centerX + Math.cos(angle) * distance
    const y = centerY + Math.sin(angle) * distance
    
    if (index === 0) {
      ctx.moveTo(x, y)
    } else {
      ctx.lineTo(x, y)
    }
  })
  ctx.closePath()
  ctx.fillStyle = 'rgba(59, 130, 246, 0.2)'
  ctx.fill()
  ctx.strokeStyle = '#3B82F6'
  ctx.lineWidth = 2
  ctx.stroke()
  
  // Draw data points
  chartData.value.dimensions.forEach((dimension, index) => {
    const angle = (2 * Math.PI * index) / numDimensions - Math.PI / 2
    const distance = (radius * dimension.score) / 10
    const x = centerX + Math.cos(angle) * distance
    const y = centerY + Math.sin(angle) * distance
    
    ctx.beginPath()
    ctx.arc(x, y, 4, 0, 2 * Math.PI)
    ctx.fillStyle = dimension.color
    ctx.fill()
  })
}

const exportChart = () => {
  console.log('Export chart')
  alert('Export chart - Template only')
}

const printChart = () => {
  console.log('Print chart')
  window.print()
}

useHead({
  title: 'Spider Chart - FEEDBACK 360'
})
</script>

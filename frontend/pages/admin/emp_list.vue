<template>
  <div class="min-h-screen bg-gray-50">
    <AdminNavbar />
  <div class="container mx-auto p-6">
    <h3 class="text-center text-3xl font-bold text-blue-600 mb-8">
      <i class="fas fa-list-check mr-2"></i>ผู้ส่งผลประเมิน
    </h3>

    <div class="mb-6">
      <div class="flex justify-between items-center">
        <div class="flex space-x-4">
          <select
            v-model="selectedRound"
            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">เลือกรอบการประเมิน</option>
            <option v-for="round in rounds" :key="round.id" :value="round.id">
              รอบที่ {{ round.id }} - {{ round.name }}
            </option>
          </select>
          
          <select
            v-model="selectedStatus"
            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">เลือกสถานะ</option>
            <option value="completed">ประเมินเสร็จ</option>
            <option value="in_progress">กำลังประเมิน</option>
            <option value="not_started">ยังไม่เริ่ม</option>
          </select>
        </div>
        
        <div class="flex space-x-2">
          <button
            @click="exportData"
            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
          >
            <i class="fas fa-file-excel mr-2"></i>Export Excel
          </button>
          <button
            @click="refreshData"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <i class="fas fa-sync-alt mr-2"></i>Refresh
          </button>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-blue-50 p-4 rounded-lg">
        <h4 class="text-sm font-medium text-blue-800">ผู้ประเมินทั้งหมด</h4>
        <p class="text-2xl font-bold text-blue-900">{{ evaluators.length }}</p>
      </div>
      <div class="bg-green-50 p-4 rounded-lg">
        <h4 class="text-sm font-medium text-green-800">ประเมินเสร็จ</h4>
        <p class="text-2xl font-bold text-green-900">{{ completedCount }}</p>
      </div>
      <div class="bg-yellow-50 p-4 rounded-lg">
        <h4 class="text-sm font-medium text-yellow-800">กำลังประเมิน</h4>
        <p class="text-2xl font-bold text-yellow-900">{{ inProgressCount }}</p>
      </div>
      <div class="bg-red-50 p-4 rounded-lg">
        <h4 class="text-sm font-medium text-red-800">ยังไม่เริ่ม</h4>
        <p class="text-2xl font-bold text-red-900">{{ notStartedCount }}</p>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รหัสพนักงาน</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ-นามสกุล</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สำนัก</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">หน่วยงาน</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวนผู้ถูกประเมิน</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ประเมินเสร็จ</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการ</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="evaluator in filteredEvaluators" :key="evaluator.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ evaluator.emp_id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ evaluator.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ evaluator.division }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ evaluator.department }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="text-lg font-semibold text-blue-600">{{ evaluator.total_evaluatees }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="text-lg font-semibold text-green-600">{{ evaluator.completed_evaluations }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="getStatusClass(evaluator.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ getStatusText(evaluator.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <button
                @click="viewProgress(evaluator)"
                class="text-blue-600 hover:text-blue-900 mr-3"
                title="ดูความคืบหน้า"
              >
                <i class="fas fa-chart-line"></i>
              </button>
              <button
                @click="sendReminder(evaluator)"
                class="text-orange-600 hover:text-orange-900"
                title="ส่งการแจ้งเตือน"
              >
                <i class="fas fa-bell"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex items-center justify-between">
      <div class="text-sm text-gray-700">
        แสดง {{ (currentPage - 1) * itemsPerPage + 1 }} ถึง {{ Math.min(currentPage * itemsPerPage, totalItems) }} จาก {{ totalItems }} รายการ
      </div>
      <div class="flex space-x-2">
        <button
          @click="prevPage"
          :disabled="currentPage === 1"
          class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
        >
          ก่อนหน้า
        </button>
        <span class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm">{{ currentPage }}</span>
        <button
          @click="nextPage"
          :disabled="currentPage === totalPages"
          class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
        >
          ถัดไป
        </button>
      </div>
    </div>
  </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'admin'
})

const selectedRound = ref('')
const selectedStatus = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)

const rounds = ref([
  { id: 1, name: 'รอบที่ 1/2024' },
  { id: 2, name: 'รอบที่ 2/2024' },
  { id: 3, name: 'รอบที่ 3/2024' }
])

const evaluators = ref([
  {
    id: 1,
    emp_id: 'E001',
    name: 'สมชาย ใจดี',
    division: 'สำนักเทคโนโลยี',
    department: 'แผนก IT',
    total_evaluatees: 15,
    completed_evaluations: 12,
    status: 'in_progress'
  },
  {
    id: 2,
    emp_id: 'E002',
    name: 'สมหญิง สวยงาม',
    division: 'สำนักการเงิน',
    department: 'แผนกบัญชี',
    total_evaluatees: 10,
    completed_evaluations: 10,
    status: 'completed'
  },
  {
    id: 3,
    emp_id: 'E003',
    name: 'สมศักดิ์ เก่งมาก',
    division: 'สำนักทรัพยากรบุคคล',
    department: 'แผนก HR',
    total_evaluatees: 8,
    completed_evaluations: 0,
    status: 'not_started'
  }
])

const filteredEvaluators = computed(() => {
  let filtered = evaluators.value
  
  if (selectedStatus.value) {
    filtered = filtered.filter(evaluator => evaluator.status === selectedStatus.value)
  }
  
  return filtered
})

const completedCount = computed(() => evaluators.value.filter(e => e.status === 'completed').length)
const inProgressCount = computed(() => evaluators.value.filter(e => e.status === 'in_progress').length)
const notStartedCount = computed(() => evaluators.value.filter(e => e.status === 'not_started').length)

const totalItems = computed(() => filteredEvaluators.value.length)
const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage.value))

const getStatusClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'in_progress':
      return 'bg-yellow-100 text-yellow-800'
    case 'not_started':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getStatusText = (status) => {
  switch (status) {
    case 'completed':
      return 'ประเมินเสร็จ'
    case 'in_progress':
      return 'กำลังประเมิน'
    case 'not_started':
      return 'ยังไม่เริ่ม'
    default:
      return 'ไม่ทราบ'
  }
}

const viewProgress = (evaluator) => {
  console.log('View progress for evaluator:', evaluator)
  alert(`ดูความคืบหน้า: ${evaluator.name} - Template only`)
}

const sendReminder = (evaluator) => {
  console.log('Send reminder to evaluator:', evaluator)
  alert(`ส่งการแจ้งเตือนให้: ${evaluator.name} - Template only`)
}

const exportData = () => {
  console.log('Export data to Excel')
  alert('Export to Excel - Template only')
}

const refreshData = () => {
  console.log('Refresh data')
  alert('Refresh data - Template only')
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

useHead({
  title: 'ผู้ส่งผลประเมิน - FEEDBACK 360'
})
</script>

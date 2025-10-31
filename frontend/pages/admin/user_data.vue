<template>
  <div class="min-h-screen bg-gray-50">
    <AdminNavbar />
  <div class="container mx-auto p-6">
    <h3 class="text-center text-3xl font-bold text-blue-600 mb-8">
      <i class="fas fa-users-viewfinder mr-2"></i>รายชื่อผู้ที่ถูกประเมิน
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
            v-model="selectedDivision"
            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">เลือกสำนัก</option>
            <option v-for="division in divisions" :key="division.id" :value="division.id">
              {{ division.name }}
            </option>
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

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รหัสพนักงาน</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ-นามสกุล</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สำนัก</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">หน่วยงาน</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ตำแหน่ง</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะการประเมิน</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">คะแนนเฉลี่ย</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการ</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.emp_id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.division }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.department }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.position }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="getStatusClass(user.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ getStatusText(user.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span v-if="user.average_score" class="text-lg font-semibold text-blue-600">
                {{ user.average_score }}
              </span>
              <span v-else class="text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <button
                @click="viewDetails(user)"
                class="text-blue-600 hover:text-blue-900 mr-3"
                title="ดูรายละเอียด"
              >
                <i class="fas fa-eye"></i>
              </button>
              <button
                @click="viewReport(user)"
                class="text-green-600 hover:text-green-900"
                title="ดูรายงาน"
              >
                <i class="fas fa-chart-bar"></i>
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
const selectedDivision = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)

const rounds = ref([
  { id: 1, name: 'รอบที่ 1/2024' },
  { id: 2, name: 'รอบที่ 2/2024' },
  { id: 3, name: 'รอบที่ 3/2024' }
])

const divisions = ref([
  { id: 1, name: 'สำนักเทคโนโลยี' },
  { id: 2, name: 'สำนักการเงิน' },
  { id: 3, name: 'สำนักทรัพยากรบุคคล' }
])

const users = ref([
  {
    id: 1,
    emp_id: 'E001',
    name: 'สมชาย ใจดี',
    division: 'สำนักเทคโนโลยี',
    department: 'แผนก IT',
    position: 'โปรแกรมเมอร์',
    status: 'completed',
    average_score: 8.2
  },
  {
    id: 2,
    emp_id: 'E002',
    name: 'สมหญิง สวยงาม',
    division: 'สำนักการเงิน',
    department: 'แผนกบัญชี',
    position: 'นักบัญชี',
    status: 'in_progress',
    average_score: 7.5
  },
  {
    id: 3,
    emp_id: 'E003',
    name: 'สมศักดิ์ เก่งมาก',
    division: 'สำนักทรัพยากรบุคคล',
    department: 'แผนก HR',
    position: 'เจ้าหน้าที่ HR',
    status: 'not_started',
    average_score: null
  }
])

const filteredUsers = computed(() => {
  let filtered = users.value
  
  if (selectedRound.value) {
    // Filter by round logic here
  }
  
  if (selectedDivision.value) {
    filtered = filtered.filter(user => user.division === selectedDivision.value)
  }
  
  return filtered
})

const totalItems = computed(() => filteredUsers.value.length)
const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage.value))

const getStatusClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'in_progress':
      return 'bg-yellow-100 text-yellow-800'
    case 'not_started':
      return 'bg-gray-100 text-gray-800'
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

const viewDetails = (user) => {
  console.log('View details for user:', user)
  alert(`ดูรายละเอียด: ${user.name} - Template only`)
}

const viewReport = (user) => {
  console.log('View report for user:', user)
  alert(`ดูรายงาน: ${user.name} - Template only`)
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
  title: 'รายชื่อผู้ที่ถูกประเมิน - FEEDBACK 360'
})
</script>

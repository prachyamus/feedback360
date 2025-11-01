<template>
  <div class="container mx-auto p-6">
    <div class="text-center mb-8">
      <img v-if="emp_image" :src="emp_image" alt="Employee Photo"
        class="w-20 h-20 rounded-full object-cover mx-auto border-4 border-blue-500 shadow-lg">
      <div v-else class="w-20 h-20 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 mx-auto flex items-center justify-center shadow-inner">
        <i class="fas fa-user text-3xl text-gray-400"></i>
      </div>
      <h3 class="text-2xl font-bold text-gray-900 mt-3 tracking-tight">{{ emp_name }}</h3>
      <p class="text-gray-600 mt-1 text-sm">เลือกรายการแบบสำรวจสำหรับ <span class="font-semibold">{{ emp_name }}</span></p>
    </div>

    <h3 class="text-center text-2xl font-bold mb-6">รายการแบบสำรวจ</h3>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      <div v-for="survey in surveys" :key="survey.id"
        class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
          <h4 class="text-lg font-semibold text-gray-900">{{ survey.title }}</h4>
          <span :class="getStatusClass(survey.status)" class="px-2 py-1 rounded-full text-xs font-medium">
            {{ getStatusText(survey.status) }}
          </span>
        </div>

        <p class="text-gray-600 mb-4">{{ survey.description }}</p>

        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
          <span>วันที่เริ่ม: {{ survey.start_date }}</span>
          <span>วันที่สิ้นสุด: {{ survey.end_date }}</span>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
          <div class="bg-blue-600 h-2 rounded-full" :style="{ width: survey.progress + '%' }"></div>
        </div>

        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-600">ความคืบหน้า: {{ survey.progress }}%</span>
          <button @click="viewSurvey(survey.id)"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            {{ survey.status === 'completed' ? 'ดูผลลัพธ์' : 'ทำแบบสำรวจ' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="surveys.length === 0" class="text-center py-12">
      <div class="text-gray-500 text-lg">ไม่พบแบบสำรวจ</div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
definePageMeta({
  layout: 'default'
})

//ค่าจาก ERP
const emp_image = ref('')
const emp_name = ref('')
const emp_position = ref('')

const route = useRoute()
const config = useRuntimeConfig()
const employeeDataUrl = config.public.apiBaseUrl + '/api/employee_data.php'


const surveys = ref([])

const loadEmployee = async () => {
  try {
    const empId = route.query.person_id
    if (!empId) return
    const res = await axios.get(employeeDataUrl + '?emp_id=' + empId)
    console.log(res)
    if (res && res.data && res.data.success) {
      const data = res.data.data || {}
      emp_image.value = data.ImagePath ? 'http://' + data.ImagePath : ''
      emp_name.value = (data.LocalFirstName || '') + ' ' + (data.LocalLastName || '')
    }
  } catch (e) {
    // silent
  }
}



const getStatusClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'in_progress':
      return 'bg-blue-100 text-blue-800'
    case 'not_started':
      return 'bg-gray-100 text-gray-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getStatusText = (status) => {
  switch (status) {
    case 'completed':
      return 'เสร็จสิ้น'
    case 'in_progress':
      return 'กำลังดำเนินการ'
    case 'not_started':
      return 'ยังไม่เริ่ม'
    default:
      return 'ไม่ทราบสถานะ'
  }
}

const viewSurvey = (surveyId) => {
  console.log('View survey:', surveyId)
  alert(`ดูแบบสำรวจ ID: ${surveyId} - Template only`)
}

onMounted(() => {
  loadEmployee()
})

useHead({
  title: 'รายการแบบสำรวจ - FEEDBACK 360'
})
</script>

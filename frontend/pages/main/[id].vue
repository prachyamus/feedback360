<template>
    <div class="container mx-auto p-6">
      <h3 class="text-center text-2xl font-bold mb-6">รายชื่อผู้เข้าร่วมโครงการ</h3>

    <div class="mb-4 bg-white p-4 rounded-lg shadow-md">
      <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
      <input
        v-model="searchValue"
        type="text"
        class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="ค้นหาจากรหัสพนักงาน, ชื่อ, นามสกุล..."
      />
      <input v-model="rounded" type="hidden" />
    </div>

    <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      {{ error }}
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden p-4">
      <div v-if="loading" class="flex justify-center items-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span class="ml-2 text-gray-600">กำลังโหลดข้อมูล...</span>
      </div>
      
      <EasyDataTable
        v-else
        :headers="headers"
        :items="employees"
        :rows-per-page="10"
        :search-value="searchValue"
        buttons-pagination
        theme-color="#2563eb"
        table-class-name="customize-table"
        hide-rows-per-page
        hide-footer-pagination-info
      >
        <template #item-pass="{ pass }">
          <span v-if="pass === 1" class="bg-green-100 text-green-800 px-2 py-1 text-xs font-medium rounded-full">
            เสร็จสิ้น
          </span>
          <span v-else class="bg-yellow-100 text-yellow-800 px-2 py-1 text-xs font-medium rounded-full">
            รอดำเนินการ
          </span>
        </template>

        <template #item-actions="item">
          <div class="flex justify-center">
            <button
              v-if="item.pass === 1"
              class="bg-green-100 text-green-800 px-4 py-2 rounded-md text-sm font-medium"
              disabled
            >
              <i class="fas fa-check mr-2"></i>แสดงความคิดเห็นแล้ว
            </button>
            <button
              v-else-if="item.status === 'fixed'"
              @click="goToSurvey(item.emp_id, item.emp_level)"
              class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700"
            >
              <i class="fas fa-edit mr-2"></i>ต้องแสดงความคิดเห็น
            </button>
            <button
              v-else
              @click="goToSurvey(item.emp_id, item.emp_level)"
              class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700"
            >
              <i class="fas fa-edit mr-2"></i>แสดงความคิดเห็น
            </button>
          </div>
        </template>

        <template #empty-message>
          <div class="text-center py-8 text-gray-500">
            ไม่พบข้อมูลพนักงาน
          </div>
        </template>
      </EasyDataTable>
    </div>
    </div>
</template>

<script setup>
import axios from 'axios'
import { useToast } from 'vue-toastification'

definePageMeta({
  layout: 'default'
})

const toast = useToast()
const { showConfirm } = useConfirm()
const route = useRoute()

const searchValue = ref('')
const rounded = ref(route.params.id || '5')
const employees = ref([])
const loading = ref(false)
const error = ref('')

const headers = [
  { text: 'รอบ', value: 'rounded', sortable: true },
  { text: 'รหัสพนักงาน', value: 'emp_id', sortable: true },
  { text: 'ชื่อ', value: 'emp_fname', sortable: true },
  { text: 'นามสกุล', value: 'emp_lname', sortable: true },
  { text: 'ชื่อเล่น', value: 'emp_nickname', sortable: true },
  { text: 'สำนัก', value: 'emp_div', sortable: true },
  { text: 'หน่วยงาน', value: 'emp_dep', sortable: true },
  { text: 'ตำแหน่ง', value: 'emp_position', sortable: true },
  { text: 'แบนด์', value: 'emp_level', sortable: true },
  { text: 'สถานะ', value: 'pass', sortable: true, width: 100 },
  { text: 'จัดการ', value: 'actions', width: 250 }
]

const getUserFromCookie = () => {
  if (!process.client) return null
  const name = 'E_Code='
  const parts = document.cookie.split(';')
  for (const raw of parts) {
    const c = raw.trim()
    if (c.startsWith(name)) return c.substring(name.length)
  }
  return null
}

const loadData = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const emId = await getUserFromCookie()
    
    if (!emId) {
      alert('ไม่พบข้อมูลผู้ใช้ กรุณาเข้าสู่ระบบใหม่')
      setTimeout(() => navigateTo('/login'), 1000)
      return
    }
    
    const config = useRuntimeConfig()
    const response = await axios.post(config.public.apiBaseUrl + '/api/fetch_user_survey.php', {
      em_id: emId,
      rounded: rounded.value
    })
    
    if (response && response.data.success) {
      console.log(response.data)
      const data = response.data.data
      
      const importantList = data.important || []
      const otherList = data.otherlist || []
      
      importantList.forEach(item => {
        item.status = 'fixed'
      })
      
      otherList.forEach(item => {
        item.status = ''
      })
      
      employees.value = [...importantList, ...otherList]
    }
  } catch (err) {
    console.error('Error loading data:', err)
    error.value = 'เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + (err.message || 'Unknown error')
  } finally {
    loading.value = false
  }
}

const goToSurvey = (person_id, empLevel) => {
  navigateTo(`/main/u_survey_who?person_id=${person_id}&band=${empLevel}&rounded=${rounded.value}`)
}

onMounted(() => {
  loadData()
})

useHead({
  title: 'หน้าหลัก - FEEDBACK 360'
})
</script>

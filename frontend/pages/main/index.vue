<template>
  <div class="min-h-screen bg-gray-50">
    <MainNavbar />
    
    <div class="container mx-auto p-6">
      <h3 class="text-center text-2xl font-bold mb-6">รายชื่อผู้เข้าร่วมโครงการ</h3>

    <div class="mb-6">
      <div class="flex justify-between items-center">
        <div class="w-full max-w-md">
          <input
            v-model="searchKeyword"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="ค้นหาจากรหัสพนักงานหรือชื่อพนักงาน"
            @keyup="searchList"
          />
        </div>
        <input v-model="rounded" type="hidden" />
      </div>
    </div>

    <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      {{ error }}
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div v-if="loading" class="flex justify-center items-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span class="ml-2 text-gray-600">กำลังโหลดข้อมูล...</span>
      </div>
      
      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รหัสพนักงาน</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">นามสกุล</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อเล่น</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สำนัก</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">หน่วยงาน</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ตำแหน่ง</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แบนด์</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-64">แสดงความคิดเห็น</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="employee in filteredEmployees" :key="employee.emp_id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ employee.emp_id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ employee.emp_fname }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ employee.emp_lname }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ employee.emp_nickname }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ employee.emp_div }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ employee.emp_dep }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ employee.emp_position }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ employee.emp_level }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <button
                v-if="employee.pass === 1"
                class="bg-green-100 text-green-800 px-4 py-2 rounded-md text-sm font-medium w-full"
                disabled
              >
                <i class="fas fa-check mr-2"></i>แสดงความคิดเห็นแล้ว
              </button>
              <button
                v-else-if="employee.status === 'fixed'"
                @click="goToSurvey(employee.emp_id, employee.emp_level)"
                class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium w-full hover:bg-red-700"
              >
                <i class="fas fa-edit mr-2"></i>ต้องแสดงความคิดเห็น
              </button>
              <button
                v-else
                @click="goToSurvey(employee.emp_id, employee.emp_level)"
                class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium w-full hover:bg-blue-700"
              >
                <i class="fas fa-edit mr-2"></i>แสดงความคิดเห็น
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-if="!loading && filteredEmployees.length === 0" class="text-center py-8 text-gray-500">
        ไม่พบข้อมูลพนักงาน
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'default'
})

const searchKeyword = ref('')
const rounded = ref('5')
const employees = ref([])
const filteredEmployees = ref([])
const importantEmployees = ref([])
const otherEmployees = ref([])
const loading = ref(false)
const error = ref('')

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
    const response = await $fetch(config.public.apiBaseUrl + '/api/fetch_user_survey.php', {
      method: 'POST',
      credentials: 'include',
      body: {
        em_id: emId,
        rounded: rounded.value
      }
    })
    
    if (response && response.success) {
      const data = response.data
      
      if (data.important) {
        data.important.forEach(item => {
          item.status = 'fixed'
        })
        importantEmployees.value = data.important
      }
      
      if (data.otherlist) {
        data.otherlist.forEach(item => {
          item.status = ''
        })
        otherEmployees.value = data.otherlist
      }
      
      employees.value = [...importantEmployees.value, ...otherEmployees.value]
      filteredEmployees.value = employees.value
    }
  } catch (err) {
    console.error('Error loading data:', err)
    error.value = 'เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + (err.message || 'Unknown error')
  } finally {
    loading.value = false
  }
}

const searchList = () => {
  if (searchKeyword.value) {
    const keyword = searchKeyword.value.toLowerCase()
    filteredEmployees.value = employees.value.filter(employee => 
      employee.emp_id.toLowerCase().includes(keyword) ||
      employee.emp_fname.toLowerCase().includes(keyword) ||
      employee.emp_nickname.toLowerCase().includes(keyword)
    )
  } else {
    filteredEmployees.value = employees.value
  }
}

const goToSurvey = (empId, empLevel) => {
  const query = {
    id: empId,
    band: empLevel,
    rounded: rounded.value
  }
  navigateTo(`/main/u_survey_who?${new URLSearchParams(query).toString()}`)
}

onMounted(() => {
  loadData()
})

useHead({
  title: 'หน้าหลัก - FEEDBACK 360'
})
</script>

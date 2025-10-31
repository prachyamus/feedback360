<template>
  <div class="min-h-screen bg-gray-50">
    <AdminNavbar />
    
    <div class="container mx-auto p-6">
      <div class="mb-6">
        <h3 class="text-2xl font-bold text-blue-600 text-center">
          <i class="fas fa-chart-bar mr-2"></i>รายชื่อผู้ที่ถูกประเมิน
        </h3>
      </div>

      <div class="mb-4 bg-white p-4 rounded-lg shadow-md">
        <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
        <input v-model="searchValue" type="text"
          class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="ค้นหาจากรหัสพนักงาน, ชื่อ, สำนัก...">
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
          :rows-per-page="50"
          :search-value="searchValue"
          buttons-pagination
          theme-color="#2563eb"
          table-class-name="customize-table"
        >
          <template #item-fullname="{ emp_fname, emp_lname }">
            <span class="font-medium text-gray-900">{{ emp_fname }} {{ emp_lname }}</span>
          </template>

          <template #item-actions="item">
            <div class="flex justify-center">
              <NuxtLink 
                :to="`/admin/user_subject_report?id=${item.emp_id}&band=${item.emp_level}&name=${item.emp_fname} ${item.emp_lname}`"
                class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                <i class="fas fa-chart-line mr-1"></i>สรุปผล
              </NuxtLink>
            </div>
          </template>
          
          <template #empty-message>
            <div class="text-center py-8 text-gray-500">
              ไม่พบข้อมูลผู้ถูกประเมิน
            </div>
          </template>
        </EasyDataTable>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { useToast } from 'vue-toastification'

definePageMeta({
  layout: 'admin'
})

const toast = useToast()
const config = useRuntimeConfig()
const apiBaseUrl = config.public.apiBaseUrl + '/api/reports.php'

const employees = ref([])
const loading = ref(false)
const searchValue = ref('')

const headers = [
  { text: 'รหัสพนักงาน', value: 'emp_id', sortable: true, width: 120 },
  { text: 'รายชื่อผู้ที่ถูกประเมิน', value: 'fullname', sortable: true },
  { text: 'สำนัก', value: 'emp_div', sortable: true },
  { text: 'หน่วยงาน', value: 'emp_dep', sortable: true },
  { text: 'ตำแหน่ง', value: 'emp_position', sortable: true },
  { text: 'แบนด์', value: 'emp_level', sortable: true, width: 100 },
  { text: 'จัดการ', value: 'actions', width: 120 }
]

const loadEmployees = async () => {
  loading.value = true
  
  try {
    const formData = new URLSearchParams()
    formData.append('action', 'list')
    
    const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
    
    if (response.data && response.data.success) {
      employees.value = response.data.data || []
    }
  } catch (err) {
    console.error('Error loading employees:', err)
    toast.error('เกิดข้อผิดพลาดในการโหลดข้อมูล')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadEmployees()
})

useHead({
  title: 'รายชื่อผู้ที่ถูกประเมิน - FEEDBACK 360'
})
</script>


<template>
    <div class="container mx-auto p-6">
      <div class="mb-6">
        <NuxtLink to="/admin/report_list" 
          class="text-blue-600 hover:text-blue-800 mb-3 inline-block">
          <i class="fas fa-arrow-left mr-2"></i>กลับ
        </NuxtLink>
        <h3 class="text-2xl font-bold text-blue-600 text-center">
          <i class="fas fa-chart-bar mr-2"></i>รายงานผลการประเมินคุณ <span class="text-gray-700">{{ personName }}</span>
        </h3>
      </div>

      <div class="mb-4 bg-white p-4 rounded-lg shadow-md">
        <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
        <input v-model="searchValue" type="text"
          class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="ค้นหาจากชื่อแบบสอบถาม...">
      </div>

      <div class="bg-white rounded-lg shadow-md overflow-hidden p-4">
        <div v-if="loading" class="flex justify-center items-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-2 text-gray-600">กำลังโหลดข้อมูล...</span>
        </div>
        
        <EasyDataTable
          v-else
          :headers="headers"
          :items="subjects"
          :rows-per-page="50"
          :search-value="searchValue"
          buttons-pagination
          theme-color="#2563eb"
          table-class-name="customize-table"
        >
          <template #item-sub_title="{ sub_title }">
            <span class="font-semibold text-gray-900">{{ sub_title }}</span>
          </template>

          <template #item-actions="item">
            <div class="flex justify-center">
              <NuxtLink 
                :to="`/admin/user_report?person_id=${personId}&sub_id=${item.sub_id}`"
                class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                <i class="fas fa-search mr-1"></i>ดูสรุปผล
              </NuxtLink>
            </div>
          </template>
          
          <template #empty-message>
            <div class="text-center py-8 text-gray-500">
              ไม่พบข้อมูลแบบสอบถาม
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
  layout: 'admin'
})

const toast = useToast()
const route = useRoute()
const config = useRuntimeConfig()
const apiBaseUrl = config.public.apiBaseUrl + '/api/reports.php'

const personId = route.query.id
const personName = route.query.name || ''

const subjects = ref([])
const loading = ref(false)
const searchValue = ref('')

const headers = [
  { text: 'ชื่อแบบสอบถาม', value: 'sub_title', sortable: true },
  { text: 'รายละเอียดแบบสอบถาม', value: 'sub_discrip', sortable: true },
  { text: 'จัดการ', value: 'actions', width: 150 }
]

const loadSubjects = async () => {
  if (!personId) {
    toast.error('ไม่พบรหัสพนักงาน')
    navigateTo('/admin/report_list')
    return
  }

  loading.value = true
  
  try {
    const formData = new URLSearchParams()
    formData.append('action', 'subjects')
    formData.append('person_id', personId)
    
    const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
    
    if (response.data && response.data.success) {
      subjects.value = response.data.data || []
    }
  } catch (err) {
    console.error('Error loading subjects:', err)
    toast.error('เกิดข้อผิดพลาดในการโหลดข้อมูล')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadSubjects()
})

useHead({
  title: `รายงานผลการประเมิน ${personName} - FEEDBACK 360`
})
</script>

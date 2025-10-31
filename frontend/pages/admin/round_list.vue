<template>
  <div class="min-h-screen bg-gray-50">
    <AdminNavbar />
    
    <div class="container mx-auto p-6">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-blue-600">
          <i class="fas fa-calendar-alt mr-2"></i>จัดการรอบการประเมิน
        </h3>
        <button @click="openModal"
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <i class="fas fa-plus mr-2"></i>เพิ่มรอบการประเมิน
        </button>
      </div>

      <div class="mb-4 bg-white p-4 rounded-lg shadow-md">
        <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
        <input v-model="searchValue" type="text"
          class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="ค้นหาจากชื่อรอบ, วันที่...">
      </div>

      <div class="bg-white rounded-lg shadow-md overflow-hidden p-4">
        <div v-if="loading" class="flex justify-center items-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-2 text-gray-600">กำลังโหลดข้อมูล...</span>
        </div>
        
        <EasyDataTable
          v-else
          :headers="headers"
          :items="rounds"
          :rows-per-page="10"
          :search-value="searchValue"
          buttons-pagination
          theme-color="#2563eb"
          table-class-name="customize-table"
        >
          <template #item-r_title="{ r_title }">
            <span class="text-base font-semibold text-blue-600">{{ r_title }}</span>
          </template>

          <template #item-r_start="{ r_start }">
            {{ formatDate(r_start) }}
          </template>

          <template #item-r_end="{ r_end }">
            {{ formatDate(r_end) }}
          </template>

          <template #item-r_status="{ r_status }">
            <span :class="r_status == '1' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                  class="px-2 py-1 text-xs font-medium rounded-full">
              {{ r_status == '1' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
            </span>
          </template>

          <template #item-actions="item">
            <div class="flex justify-center space-x-2">
              <button @click="editRound(item)" class="text-blue-600 hover:text-blue-900">
                <i class="fas fa-edit"></i>
              </button>
              <button @click="deleteRound(item.r_id)" class="text-red-600 hover:text-red-900">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </template>
          
          <template #empty-message>
            <div class="text-center py-8 text-gray-500">
              ไม่พบข้อมูลรอบการประเมิน
            </div>
          </template>
        </EasyDataTable>
      </div>

    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ isEdit ? 'แก้ไขรอบการประเมิน' : 'เพิ่มรอบการประเมิน' }}
          </h3>

          <form @submit.prevent="saveRound" class="space-y-4">
            <input type="hidden" v-model="form.r_id" />

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อรอบการประเมิน</label>
              <input v-model="form.r_title" type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="เช่น รอบการประเมินครั้งที่ 1/2567" required />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">วันที่เริ่ม</label>
              <input v-model="form.r_start" type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">วันที่สิ้นสุด</label>
              <input v-model="form.r_end" type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
              <select v-model="form.r_status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
                <option value="1">เปิดใช้งาน</option>
                <option value="0">ปิดใช้งาน</option>
              </select>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <button type="button" @click="closeModal"
                class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                ยกเลิก
              </button>
              <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-save mr-2"></i>บันทึก
              </button>
            </div>
          </form>
        </div>
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
const { showConfirm } = useConfirm()
const rounds = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const searchValue = ref('')

const headers = [
  { text: 'ID', value: 'r_id', sortable: true, width: 80 },
  { text: 'ชื่อรอบการประเมิน', value: 'r_title', sortable: true },
  { text: 'วันที่เริ่ม', value: 'r_start', sortable: true, width: 150 },
  { text: 'วันที่สิ้นสุด', value: 'r_end', sortable: true, width: 150 },
  { text: 'สถานะ', value: 'r_status', sortable: true, width: 120 },
  { text: 'จัดการ', value: 'actions', width: 100 }
]

const form = ref({
  r_id: '',
  r_title: '',
  r_start: '',
  r_end: '',
  r_status: '1'
})

const config = useRuntimeConfig()
const apiBaseUrl = config.public.apiBaseUrl + '/api/rounds.php'

const loadRounds = async () => {
  loading.value = true
  
  try {
    const formData = new URLSearchParams()
    formData.append('action', 'list')
    const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
    
    if (response.data.success) {
      rounds.value = response.data.data || []
    } else if (Array.isArray(response.data)) {
      rounds.value = response.data
    }
  } catch (err) {
    console.error('Error loading rounds:', err)
    toast.error('เกิดข้อผิดพลาดในการโหลดข้อมูล')
  } finally {
    loading.value = false
  }
}

const openModal = () => {
  isEdit.value = false
  resetForm()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const resetForm = () => {
  form.value = {
    r_id: '',
    r_title: '',
    r_start: '',
    r_end: '',
    r_status: '1'
  }
}

const editRound = async (round) => {
  isEdit.value = true
  form.value = {
    r_id: round.r_id,
    r_title: round.r_title,
    r_start: round.r_start,
    r_end: round.r_end,
    r_status: round.r_status
  }
  showModal.value = true
}

const deleteRound = async (id) => {
  const confirmed = await showConfirm('คุณต้องการลบรอบการประเมินนี้หรือไม่?')
  if (confirmed) {
    try {
      const formData = new URLSearchParams()
      formData.append('action', 'delete')
      formData.append('r_id', id)
      const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
      const res = response && response.data !== undefined ? response.data : response

      if (res?.success) {
        toast.success('ลบรอบการประเมินสำเร็จ')
        loadRounds()
      }
    } catch (error) {
      console.error('Error deleting round:', error)
      toast.error('เกิดข้อผิดพลาดในการลบข้อมูล')
    }
  }
}

const saveRound = async () => {
  try {
    loading.value = true
    const formData = new URLSearchParams()
    formData.append('action', 'save')
    formData.append('r_id', form.value.r_id)
    formData.append('r_title', form.value.r_title)
    formData.append('r_start', form.value.r_start)
    formData.append('r_end', form.value.r_end)
    formData.append('r_status', form.value.r_status)
    const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
    const res = response && response.data !== undefined ? response.data : response

    if (res?.success) {
      toast.success((res.data && res.data.message) || 'บันทึกข้อมูลสำเร็จ')
      closeModal()
      loadRounds()
    }
  } catch (error) {
    console.error('Error saving round:', error)
    if (error.message) {
      toast.error(error.message)
    } else {
      toast.error('เกิดข้อผิดพลาดในการบันทึกข้อมูล')
    }
  } finally {
    loading.value = false
  }
}

const formatDate = (date) => {
  if (!date) return '-'
  const d = new Date(date)
  return d.toLocaleDateString('th-TH', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
}

onMounted(() => {
  loadRounds()
})

useHead({
  title: 'จัดการรอบการประเมิน - FEEDBACK 360'
})
</script>


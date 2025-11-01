<template>
    <div class="container mx-auto p-6">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-blue-600">
          <i class="fas fa-clipboard-list mr-2"></i>จัดการหัวข้อการประเมิน
        </h3>
        <button @click="openModal"
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <i class="fas fa-plus mr-2"></i>เพิ่มหัวข้อการประเมิน
        </button>
      </div>

      <div class="mb-4 bg-white p-4 rounded-lg shadow-md">
        <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
        <input v-model="searchValue" type="text"
          class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="ค้นหาจากชื่อหัวข้อ, คำอธิบาย...">
      </div>

      <div class="bg-white rounded-lg shadow-md overflow-hidden p-4">
        <div v-if="loading" class="flex justify-center items-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-2 text-gray-600">กำลังโหลดข้อมูล...</span>
        </div>
        
        <EasyDataTable
          v-else
          :headers="headers"
          :items="surveys"
          :rows-per-page="10"
          :search-value="searchValue"
          buttons-pagination
          theme-color="#2563eb"
          table-class-name="customize-table"
        >
          <template #item-sub_title="{ sub_title }">
            <span class="font-semibold text-gray-900">{{ sub_title }}</span>
          </template>

          <template #item-sub_status="{ sub_status }">
            <span :class="sub_status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                  class="px-2 py-1 text-xs font-medium rounded-full">
              {{ sub_status == 1 ? 'เปิด' : 'ปิด' }}
            </span>
          </template>

          <template #item-type_group="{ type_group }">
            <span class="text-sm">
              {{ getTypeGroupText(type_group) }}
            </span>
          </template>

          <template #item-actions="item">
            <div class="flex justify-center space-x-2">
              <button @click="editSurvey(item)" 
                class="text-blue-600 hover:text-blue-900" title="แก้ไข">
                <i class="fas fa-edit"></i>
              </button>
              <NuxtLink :to="`/admin/question_list?id=${item.sub_id}&group=${item.type_group}`" 
                class="text-green-600 hover:text-green-900" title="จัดการคำถาม">
                <i class="fas fa-list"></i>
              </NuxtLink>
              <button @click="deleteSurvey(item.sub_id)" 
                class="text-red-600 hover:text-red-900" title="ลบ">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </template>
          
          <template #empty-message>
            <div class="text-center py-8 text-gray-500">
              ไม่พบข้อมูลแบบสำรวจ
            </div>
          </template>
        </EasyDataTable>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ isEdit ? 'แก้ไขหัวข้อการประเมิน' : 'เพิ่มหัวข้อการประเมิน' }}
          </h3>

          <form @submit.prevent="saveSurvey" class="space-y-4">
            <input type="hidden" v-model="form.sub_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">หัวข้อการประเมิน (ไทย) *</label>
                <textarea v-model="form.sub_title" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          rows="2" 
                          placeholder="ระบุหัวข้อภาษาไทย" 
                          required></textarea>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">หัวข้อการประเมิน (พม่า)</label>
                <textarea v-model="form.sub_title_m" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          rows="2" 
                          placeholder="ระบุหัวข้อภาษาพม่า"></textarea>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">คำอธิบาย (ไทย) *</label>
                <textarea v-model="form.sub_discrip" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          rows="3" 
                          placeholder="ระบุคำอธิบาย" 
                          required></textarea>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">คำอธิบาย (พม่า)</label>
                <textarea v-model="form.sub_discrip_m" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          rows="3" 
                          placeholder="ระบุคำอธิบายภาษาพม่า"></textarea>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ระดับแบนด์ที่ต้องถูกประเมิน *</label>
                <select v-model="form.sub_band" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        required>
                  <option value="">เลือกระดับแบนด์</option>
                  <option v-for="band in bandTypes" :key="band.ban_id" :value="band.ban_id">
                    {{ band.ban_title }}
                  </option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">กลุ่มรายงานผล *</label>
                <select v-model="form.type_group" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        required>
                  <option value="1">band 10 ขึ้นไป</option>
                  <option value="2">น้อยกว่า band 10</option>
                  <option value="3">band 10 ขึ้นไปแบบใหม่</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">สถานะการใช้งาน *</label>
                <select v-model="form.sub_status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        required>
                  <option value="1">เปิด</option>
                  <option value="0">ปิด</option>
                </select>
              </div>
            </div>
            
            <div class="border-t pt-4">
              <h4 class="text-sm font-medium text-gray-700 mb-3">การสรุปผล</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Summary Chart</label>
                  <input v-model="form.sumary_chart" type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Summary Chart" />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Summary Group</label>
                  <input v-model="form.sumary_group" type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Summary Group" />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Summary Question</label>
                  <input v-model="form.sumary_question" type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Summary Question" />
                </div>
              </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
              <button type="button" @click="closeModal" 
                      class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                ยกเลิก
              </button>
              <button type="submit" :disabled="saving" 
                      class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                <i class="fas fa-save mr-2"></i>
                <span v-if="saving">กำลังบันทึก...</span>
                <span v-else>บันทึก</span>
              </button>
            </div>
          </form>
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
const config = useRuntimeConfig()
const apiBaseUrl = config.public.apiBaseUrl + '/api/surveys.php'
const bandTypesUrl = config.public.apiBaseUrl + '/api/band-types.php'

const surveys = ref([])
const bandTypes = ref([])
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const searchValue = ref('')

const headers = [
  { text: 'ID', value: 'sub_id', sortable: true, width: 80 },
  { text: 'ชื่อหัวข้อ', value: 'sub_title', sortable: true },
  { text: 'รายละเอียด', value: 'sub_discrip', sortable: true },
  { text: 'ระดับผู้ประเมิน', value: 'ban_title', sortable: true, width: 150 },
  { text: 'กลุ่มรายงาน', value: 'type_group', sortable: true, width: 150 },
  { text: 'สถานะ', value: 'sub_status', sortable: true, width: 100 },
  { text: 'จัดการ', value: 'actions', width: 120 }
]

const form = ref({
  sub_id: '',
  sub_title: '',
  sub_title_m: '',
  sub_discrip: '',
  sub_discrip_m: '',
  sub_band: '',
  type_group: '1',
  sub_status: '1',
  sumary_chart: '',
  sumary_group: '',
  sumary_question: ''
})

const loadSurveys = async () => {
  loading.value = true
  
  try {
    const response = await axios.get(apiBaseUrl, { withCredentials: true })
    
    if (response.data && response.data.success) {
      surveys.value = response.data.data || []
    }
  } catch (err) {
    console.error('Error loading surveys:', err)
    toast.error('เกิดข้อผิดพลาดในการโหลดข้อมูล')
  } finally {
    loading.value = false
  }
}

const loadBandTypes = async () => {
  try {
    const response = await axios.get(bandTypesUrl, { withCredentials: true })
    
    if (response.data && response.data.success) {
      bandTypes.value = response.data.data
    }
  } catch (err) {
    console.error('Error loading band types:', err)
  }
}

const openModal = () => {
  isEdit.value = false
  form.value = {
    sub_id: '',
    sub_title: '',
    sub_title_m: '',
    sub_discrip: '',
    sub_discrip_m: '',
    sub_band: '',
    type_group: '1',
    sub_status: '1',
    sumary_chart: '',
    sumary_group: '',
    sumary_question: ''
  }
  showModal.value = true
}

const editSurvey = (survey) => {
  isEdit.value = true
  form.value = { ...survey }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const deleteSurvey = async (id) => {
  const confirmed = await showConfirm('คุณต้องการลบหัวข้อการประเมินนี้หรือไม่?')
  if (confirmed) {
    try {
      const formData = new URLSearchParams()
      formData.append('action', 'delete')
      formData.append('sub_id', id)
      
      const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
      
      if (response.data && response.data.success) {
        toast.success('ลบหัวข้อการประเมินสำเร็จ')
        loadSurveys()
      }
    } catch (err) {
      console.error('Error deleting survey:', err)
      toast.error('เกิดข้อผิดพลาดในการลบข้อมูล')
    }
  }
}

const saveSurvey = async () => {
  saving.value = true
  
  try {
    const formData = new URLSearchParams()
    formData.append('action', 'save')
    
    Object.keys(form.value).forEach(key => {
      formData.append(key, form.value[key] || '')
    })
    
    const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
    
    if (response.data && response.data.success) {
      toast.success(response.data.message || 'บันทึกข้อมูลสำเร็จ')
      closeModal()
      loadSurveys()
    }
  } catch (err) {
    console.error('Error saving survey:', err)
    toast.error('เกิดข้อผิดพลาดในการบันทึก')
  } finally {
    saving.value = false
  }
}

const getTypeGroupText = (type_group) => {
  switch (type_group) {
    case '1': return 'band 10 ขึ้นไป'
    case '2': return 'น้อยกว่า band 10'
    case '3': return 'band 10 ขึ้นไปแบบใหม่'
    default: return '-'
  }
}

onMounted(() => {
  loadSurveys()
  loadBandTypes()
})

useHead({
  title: 'จัดการหัวข้อการประเมิน - FEEDBACK 360'
})
</script>

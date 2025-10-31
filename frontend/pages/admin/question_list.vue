<template>
  <div class="min-h-screen bg-gray-50">
    <AdminNavbar />
    
    <div class="container mx-auto p-6">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h3 class="text-2xl font-bold text-blue-600">
            <i class="fas fa-list-alt mr-2"></i>จัดการคำถามประเมิน
          </h3>
          <p class="text-gray-600 mt-1" v-if="surveyTitle">{{ surveyTitle }}</p>
        </div>
        <div class="flex space-x-2">
          <NuxtLink to="/admin/survey_list" 
            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i>กลับ
          </NuxtLink>
          <button @click="openModal"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fas fa-plus mr-2"></i>เพิ่มคำถาม
          </button>
        </div>
      </div>

      <div class="mb-4 bg-white p-4 rounded-lg shadow-md">
        <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
        <input v-model="searchValue" type="text"
          class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="ค้นหาจากข้อที่, คำถาม...">
      </div>

      <div class="bg-white rounded-lg shadow-md overflow-hidden p-4">
        <div v-if="loading" class="flex justify-center items-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-2 text-gray-600">กำลังโหลดข้อมูล...</span>
        </div>
        
        <EasyDataTable
          v-else
          :headers="headers"
          :items="questions"
          :rows-per-page="15"
          :search-value="searchValue"
          buttons-pagination
          theme-color="#2563eb"
          table-class-name="customize-table"
        >
          <template #item-que_title="{ que_title }">
            <span class="font-medium text-gray-900">{{ que_title }}</span>
          </template>

          <template #item-que_type="{ que_type }">
            <span :class="que_type == 1 ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'" 
                  class="px-2 py-1 text-xs font-medium rounded-full">
              {{ que_type == 1 ? 'ให้คะแนน' : 'ความคิดเห็น' }}
            </span>
          </template>

          <template #item-actions="item">
            <div class="flex justify-center space-x-2">
              <button @click="editQuestion(item)" 
                class="text-blue-600 hover:text-blue-900" title="แก้ไข">
                <i class="fas fa-edit"></i>
              </button>
              <button @click="deleteQuestion(item.que_id)" 
                class="text-red-600 hover:text-red-900" title="ลบ">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </template>
          
          <template #empty-message>
            <div class="text-center py-8 text-gray-500">
              ไม่พบข้อมูลคำถาม
            </div>
          </template>
        </EasyDataTable>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ isEdit ? 'แก้ไขคำถาม' : 'เพิ่มคำถาม' }}
          </h3>

          <form @submit.prevent="saveQuestion" class="space-y-4">
            <input type="hidden" v-model="form.que_id">
            <input type="hidden" v-model="form.sub_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ข้อที่ *</label>
                <input v-model="form.que_num" 
                       type="number" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">รูปแบบการประเมิน *</label>
                <select v-model="form.que_type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        required>
                  <option value="">ระบุรูปแบบ</option>
                  <option value="1">ให้คะแนน</option>
                  <option value="2">ความคิดเห็น</option>
                </select>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">คำถาม (ไทย) *</label>
                <textarea v-model="form.que_title" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          rows="3" 
                          placeholder="ระบุคำถามภาษาไทย" 
                          required></textarea>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">คำถาม (พม่า)</label>
                <textarea v-model="form.que_title_m" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          rows="3" 
                          placeholder="คำถามภาษาพม่า"></textarea>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">หมวดหมู่ *</label>
                <select v-model="form.que_group" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        required>
                  <option value="">เลือกหมวดหมู่</option>
                  <option v-for="group in questionGroups" :key="group.que_group" :value="group.que_group">
                    {{ group.group_name }}
                  </option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">DNA</label>
                <select v-model="form.que_dna" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">เลือก DNA</option>
                  <option v-for="dna in dnaList" :key="dna" :value="dna">{{ dna }}</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">กลุ่มคำถาม/สำนัก</label>
                <select v-model="form.div_type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">ไม่ระบุ</option>
                  <option value="Opportunity & Assurance">Opportunity & Assurance</option>
                  <option value="Delicious Innovation">Delicious Innovation</option>
                  <option value="Consistent Quality & Service">Consistent Quality & Service</option>
                  <option value="Happy People">Happy People</option>
                </select>
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
                <span v-else">บันทึก</span>
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
const route = useRoute()
const config = useRuntimeConfig()
const apiBaseUrl = config.public.apiBaseUrl + '/api/questions.php'
const questionGroupsUrl = config.public.apiBaseUrl + '/api/question-groups.php'
const surveysUrl = config.public.apiBaseUrl + '/api/surveys.php'

const surveyId = route.query.id
const groupId = route.query.group

const questions = ref([])
const questionGroups = ref([])
const surveyTitle = ref('')
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const searchValue = ref('')

const headers = [
  { text: 'ข้อที่', value: 'que_num', sortable: true, width: 100 },
  { text: 'ชื่อคำถาม', value: 'que_title', sortable: true },
  { text: 'คำถามภาษาพม่า', value: 'que_title_m', sortable: true },
  { text: 'หมวดหมู่', value: 'group_name', sortable: true, width: 150 },
  { text: 'วิธีประเมิน', value: 'que_type', sortable: true, width: 120 },
  { text: 'จัดการ', value: 'actions', width: 100 }
]

const form = ref({
  que_id: '',
  sub_id: surveyId,
  que_num: '',
  que_title: '',
  que_title_m: '',
  que_dna: '',
  que_group: '',
  div_type: '',
  que_type: ''
})

const dnaList = ref([
  'Courageously Positive',
  'Seek Opportunity',
  'System Thinker',
  'Trustworthy',
  'Always Plan',
  'Calculated Risk Taker',
  'Love Learning',
  'Adaptive',
  'Communicative leader',
  'Show High EQ',
  'Strategy Oriented',
  'Emotionally Intelligent',
  'Manage Time',
  'Service Mind',
  'over all',
  'other'
])

const loadQuestions = async () => {
  loading.value = true
  const formData = new URLSearchParams()
  formData.append('action', 'fetch')
  formData.append('id', surveyId)
  formData.append('group', groupId)
  console.log(formData)
  try {
    const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
    console.log(response.data)
    
    if (response.data && response.data.success) {
      questions.value = response.data.data || []
    } else if (Array.isArray(response.data)) {
      questions.value = response.data
    }
  } catch (err) {
    console.error('Error loading questions:', err)
    toast.error('เกิดข้อผิดพลาดในการโหลดข้อมูล')
  } finally {
    loading.value = false
  }
}

const loadQuestionGroups = async () => {
  try {
    const url = groupId ? `${questionGroupsUrl}?group=${groupId}` : questionGroupsUrl
    const response = await axios.get(url, { withCredentials: true })
    
    if (response.data && response.data.success) {
      questionGroups.value = response.data.data || []
    } else if (Array.isArray(response.data)) {
      questionGroups.value = response.data
    }
  } catch (err) {
    console.error('Error loading question groups:', err)
  }
}

const loadSurveyTitle = async () => {
  try {
    const formData = new URLSearchParams()
    formData.append('action', 'fetch')
    formData.append('id', surveyId)
    
    const response = await axios.post(surveysUrl, formData, { withCredentials: true })
    
    if (response.data && response.data.success) {
      surveyTitle.value = response.data.data?.sub_title || ''
    }
  } catch (err) {
    console.error('Error loading survey title:', err)
  }
}

const openModal = () => {
  isEdit.value = false
  form.value = {
    que_id: '',
    sub_id: surveyId,
    que_num: '',
    que_title: '',
    que_title_m: '',
    que_dna: '',
    que_group: '',
    div_type: '',
    que_type: ''
  }
  showModal.value = true
}

const editQuestion = (question) => {
  isEdit.value = true
  form.value = { ...question }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveQuestion = async () => {
  saving.value = true
  
  try {
    const formData = new URLSearchParams()
    formData.append('action', isEdit.value ? 'update' : 'create')
    
    Object.keys(form.value).forEach(key => {
      formData.append(key, form.value[key] || '')
    })
    
    const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
    
    if (response.data && response.data.success) {
      toast.success(response.data.data?.message || response.data.message || 'บันทึกข้อมูลสำเร็จ')
      closeModal()
      loadQuestions()
    }
  } catch (err) {
    console.error('Error saving question:', err)
    toast.error('เกิดข้อผิดพลาดในการบันทึก')
  } finally {
    saving.value = false
  }
}

const deleteQuestion = async (questionId) => {
  const confirmed = await showConfirm('คุณต้องการลบคำถามนี้หรือไม่?')
  if (confirmed) {
    try {
      const formData = new URLSearchParams()
      formData.append('action', 'delete')
      formData.append('question_id', questionId)
      
      const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
      
      if (response.data && response.data.success) {
        toast.success('ลบคำถามสำเร็จ')
        loadQuestions()
      }
    } catch (err) {
      console.error('Error deleting question:', err)
      toast.error('เกิดข้อผิดพลาดในการลบ')
    }
  }
}

onMounted(() => {
  if (!surveyId) {
    toast.error('ไม่พบรหัสแบบสำรวจ')
    navigateTo('/admin/survey_list')
    return
  }
  
  loadQuestions()
  loadQuestionGroups()
  loadSurveyTitle()
})

useHead({
  title: 'จัดการคำถาม - FEEDBACK 360'
})
</script>

<template>
  <div class="containerw-full mx-auto p-6 max-w-7xl">
    <div class="bg-white rounded-xl shadow-sm border mb-6">
      <!-- ส่วนหัวของแบบสำรวจ -->
      <div class="flex w-full bg-gray-100 flex-col md:flex-row md:items-center md:justify-between border-b  rounded-t-xl">
        <div class="w-full p-3 flex flex-col justify-center align-middle gap-2">
          <div class="text-center w-full">
            <img v-if="emp_image" :src="emp_image" alt="Employee Photo"
              class="w-24 h-24 rounded-full object-cover mx-auto border-4 border-blue-500 shadow-lg"
              @error="handleImageError" />
            <div v-else
              class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 mx-auto flex items-center justify-center shadow-inner">
              <i class="fas fa-user text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-center text-xl mt-3">
              ประเมินคุณ <span class="font-semibold">{{ emp_name }}</span>
            </h3>
          </div>
          <div class="w-full text-center text-gray-700 text-xl">
            ในหัวข้อ: <span id="subject_name" class="font-semibold text-2xl">{{ subjectTitle }}</span>
          </div>
        </div>
      </div>

      <!-- ส่วนคำถามของแบบสำรวจ -->
      <div class="px-6 pt-4 pb-2">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-600">ให้คะแนน 1–10 หรือเลือก N/A</div>
          <div class="hidden md:flex items-center gap-2 text-xs text-gray-500">
            <span class="px-2 py-1 rounded bg-gray-100">1</span>
            <span class="px-2 py-1 rounded bg-gray-100">2</span>
            <span class="px-2 py-1 rounded bg-gray-100">3</span>
            <span class="px-2 py-1 rounded bg-gray-100">4</span>
            <span class="px-2 py-1 rounded bg-gray-100">5</span>
            <span class="px-2 py-1 rounded bg-gray-100">6</span>
            <span class="px-2 py-1 rounded bg-gray-100">7</span>
            <span class="px-2 py-1 rounded bg-gray-100">8</span>
            <span class="px-2 py-1 rounded bg-gray-100">9</span>
            <span class="px-2 py-1 rounded bg-gray-100">10</span>
            <span class="px-2 py-1 rounded bg-gray-100">N/A</span>
          </div>
        </div>
      </div>
    </div>

    <form @submit.prevent="submitSurvey" class="bg-white rounded-xl shadow-sm border p-0">
      <div class="overflow-x-auto rounded-b-xl">
        <table class="w-full border-collapse">
          <tbody id="show_question">
            <template v-for="(question, idx) in questions" :key="question.que_id">
              <tr class="border-b">
                <td
                  :class="['border p-4 text-center font-bold w-16 align-top', idx % 2 === 0 ? 'bg-gray-50' : 'bg-gray-100']">
                  <h3 class="text-lg">{{ question.que_num }}</h3>
                </td>
                <td :class="['border p-4 text-left', idx % 2 === 0 ? 'bg-gray-50' : 'bg-gray-100']" colspan="11">
                  <p class="font-medium text-gray-900">{{ question.que_title }}</p>
                  <p v-if="question.que_title_m" class="text-gray-600 text-sm mt-1">{{ question.que_title_m }}</p>
                </td>
              </tr>
              <tr class="border-b hover:bg-gray-50 transition-colors">
                <td class="border p-4"></td>
                <td v-if="question.que_type === 1" colspan="10" class="border p-3">
                  <div class="flex flex-wrap gap-4 justify-around">
                    <div v-for="i in 10" :key="i" class="flex flex-col items-center">
                      <input :id="`q${question.que_id}-${i}`" type="radio" :name="`question_${question.que_id}`"
                        :value="i" class="h-5 w-5 text-blue-600 focus:ring-blue-500" required />
                      <label :for="`q${question.que_id}-${i}`" class="text-xs mt-1 text-gray-600">{{ i }}</label>
                    </div>
                    <div class="flex flex-col items-center">
                      <input :id="`q${question.que_id}-0`" type="radio" :name="`question_${question.que_id}`" value="0"
                        class="h-5 w-5 text-blue-600 focus:ring-blue-500" />
                      <label :for="`q${question.que_id}-0`" class="text-xs mt-1 text-gray-600">N/A</label>
                    </div>
                  </div>
                </td>
                <td v-else-if="question.que_type === 2" colspan="10" class="border p-4">
                  <textarea :name="`question_${question.que_id}`" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="กรอกความคิดเห็นของคุณ"></textarea>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <div class="p-6 text-center">
        <button type="submit" :disabled="loading"
          class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
          {{ loading ? 'กำลังบันทึก...' : 'บันทึก' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import axios from 'axios'
import { useToast } from 'vue-toastification'

definePageMeta({
  layout: 'default'
})

const route = useRoute()
const config = useRuntimeConfig()
const toast = useToast()

const personName = ref('')
const subjectTitle = ref('')
const questions = ref([])
const loading = ref(false)

//ข้อมูลพนักงาน
const emp_image = ref('')
const emp_name = ref('')
const emp_position = ref('')

//ข้อมูลจาก URL
const sub_id = route.query.sub_id
const person_id = route.query.person_id
const whotype_id = route.query.whotype_id
const rounded = route.query.rounded

//ข้อมูลผู้ประเมิน
const E_Code = useCookie('E_Code')
const emp_id = E_Code.value

const employeeDataUrl = config.public.apiBaseUrl + '/api/employee_data.php'
const questionsUrl = config.public.apiBaseUrl + '/api/questions.php'
const surveysUrl = config.public.apiBaseUrl + '/api/surveys.php'

//ดึงข้อมูลพนักงานจาก ERP
const loadEmployeeData = async () => {
  try {
    const response = await axios.get(employeeDataUrl + '?emp_id=' + person_id)
    emp_image.value = 'http://' + response.data.ImagePath
    emp_name.value = response.data.LocalFirstName + ' ' + response.data.LocalLastName
    emp_position.value = response.data.PositionName
  } catch (error) {
    console.error('Error loading employee data:', error)
  }
}


const loadSurvey = async () => {
  try {
    if (!sub_id) return
    const form = new FormData()
    form.append('action', 'fetch')
    form.append('id', sub_id)
    const res = await axios.post(surveysUrl, form)
    if (res && res.data && res.data.success) {
      subjectTitle.value = res.data.data.sub_title || ''
    }
  } catch (e) {
    console.error('Error loading survey:', e)
  }
}

const loadQuestions = async () => {
  try {
    if (!sub_id) return
    const form = new FormData()
    form.append('action', 'fetch')
    form.append('sub_id', sub_id)
    const res = await axios.post(questionsUrl, form)
    if (res && res.data && res.data.success) {
      questions.value = res.data.data || []
    }
  } catch (e) {
    console.error('Error loading questions:', e)
    toast.error('เกิดข้อผิดพลาดในการโหลดคำถาม')
  }
}

const submitSurvey = async () => {
  const formData = new FormData()
  
  formData.append('sub_id', sub_id || '')
  formData.append('person_id', person_id || '')
  formData.append('whotype_id', whotype_id || '')
  formData.append('rounded', rounded || '')
  formData.append('who_id', emp_id || '')
  
  const answers = []
  
  questions.value.forEach(q => {
    const name = `question_${q.que_id}`
    let value = ''
    if (q.que_type === 1) {
      const checked = document.querySelector(`input[name="${name}"]:checked`)
      value = checked ? checked.value : ''
    } else if (q.que_type === 2) {
      const textarea = document.querySelector(`textarea[name="${name}"]`)
      value = textarea ? textarea.value : ''
    }
    
    if (value !== '') {
      formData.append(`answers[${q.que_id}]`, value)
      answers.push({
        que_id: q.que_id,
        answer_save: value
      })
    }
  })

  // console.log('FormData entries:')
  // for (let pair of formData.entries()) {
  //   console.log(pair[0] + ': ' + pair[1])
  // }

  // console.log('Answers array:', answers)

  loading.value = true
  
  try {
    const response = await axios.post(
      config.public.apiBaseUrl + '/api/survey_ans_save.php',
      formData,
      { withCredentials: true }
    )
    
    if (response.data && response.data.success) {
      toast.success('บันทึกคำตอบสำเร็จ')
      setTimeout(() => {
        navigateTo('/main/'+rounded)
      }, 1000)
    } else {
      toast.error(response.data.message || 'เกิดข้อผิดพลาดในการบันทึก')
    }
  } catch (error) {
    console.error('Error submitting survey:', error)
    toast.error('เกิดข้อผิดพลาดในการบันทึกคำตอบ')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadEmployeeData()
  loadSurvey()
  loadQuestions()
})

useHead({
  title: 'แบบสำรวจ - FEEDBACK 360'
})
</script>

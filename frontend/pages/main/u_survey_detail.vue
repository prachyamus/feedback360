<template>
  <div class="container mx-auto p-6">
    <div class="text-center mb-8">
      <h3 id="person_name" class="text-2xl font-bold text-gray-900 mb-4">
        ประเมินคุณ <span class="text-blue-600">{{ personName }}</span>
      </h3>
      <h4 id="subject_name" class="text-lg text-gray-700">
        ในหัวข้อ: <span class="font-semibold">{{ subjectTitle }}</span>
      </h4>
    </div>

    <form @submit.prevent="submitSurvey" class="bg-white rounded-lg shadow-md p-6">
      <div class="overflow-x-auto">
        <table class="w-full border-collapse">
          <tbody id="show_question">
            <tr v-for="question in questions" :key="question.que_id" class="border-b">
              <td class="border bg-gray-50 p-4 text-center font-bold w-16">
                <h3 class="text-xl">{{ question.que_num }}</h3>
              </td>
              <td colspan="11" class="border bg-gray-50 p-4 text-left">
                <p class="font-medium">{{ question.que_title }}</p>
                <p class="text-gray-600 text-sm">{{ question.que_title_m }}</p>
              </td>
            </tr>
            <tr class="border-b">
              <td class="border p-4"></td>
              <td v-if="question.que_type === 1" v-for="question in questions" :key="question.que_id" class="border p-2 text-center">
                <div v-for="i in 10" :key="i" class="flex flex-col items-center mb-2">
                  <input
                    :id="`${question.que_num}-${i}`"
                    type="radio"
                    :name="question.que_id"
                    :value="i"
                    class="h-6 w-6"
                    required
                  />
                  <label :for="`${question.que_num}-${i}`" class="text-sm mt-1">{{ i }}</label>
                </div>
                <div class="flex flex-col items-center">
                  <input
                    :id="`${question.que_num}-0`"
                    type="radio"
                    :name="question.que_id"
                    value="0"
                    class="h-6 w-6"
                  />
                  <label :for="`${question.que_num}-0`" class="text-sm mt-1 text-gray-600">ไม่สามารถประเมินได้</label>
                </div>
              </td>
              <td v-else-if="question.que_type === 2" v-for="question in questions" :key="question.que_id" colspan="10" class="border p-4">
                <input
                  type="text"
                  :name="question.que_id"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="กรอกความคิดเห็นของคุณ"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-6 text-center">
        <button
          type="submit"
          :disabled="loading"
          class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
        >
          {{ loading ? 'กำลังบันทึก...' : 'บันทึก' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'default'
})

const route = useRoute()
const personName = ref('')
const subjectTitle = ref('')
const questions = ref([])
const loading = ref(false)

const mockQuestions = [
  {
    que_id: 1,
    que_num: 1,
    que_title: 'ความสามารถในการทำงาน',
    que_title_m: 'Ability to work effectively',
    que_type: 1
  },
  {
    que_id: 2,
    que_num: 2,
    que_title: 'การทำงานเป็นทีม',
    que_title_m: 'Teamwork skills',
    que_type: 1
  },
  {
    que_id: 3,
    que_num: 3,
    que_title: 'ความคิดเห็นเพิ่มเติม',
    que_title_m: 'Additional comments',
    que_type: 2
  }
]

const loadData = () => {
  personName.value = 'สมชาย ใจดี'
  subjectTitle.value = 'การประเมินผลงานประจำปี 2024'
  questions.value = mockQuestions
}

const submitSurvey = () => {
  loading.value = true
  
  setTimeout(() => {
    loading.value = false
    console.log('Survey submitted')
    alert('บันทึกสำเร็จ - Template only')
    navigateTo('/main')
  }, 1500)
}

onMounted(() => {
  loadData()
})

useHead({
  title: 'แบบสำรวจ - FEEDBACK 360'
})
</script>

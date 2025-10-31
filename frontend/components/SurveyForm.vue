<template>
  <div class="bg-white rounded-lg shadow-md p-6">
    <div class="text-center mb-8">
      <h3 class="text-2xl font-bold text-gray-900 mb-4">
        ประเมินคุณ <span class="text-blue-600">{{ personName }}</span>
      </h3>
      <h4 class="text-lg text-gray-700">
        ในหัวข้อ: <span class="font-semibold">{{ subjectTitle }}</span>
      </h4>
    </div>

    <form @submit.prevent="submitSurvey" class="space-y-6">
      <div v-for="question in questions" :key="question.que_id" class="border rounded-lg p-4">
        <div class="mb-4">
          <h4 class="text-lg font-semibold text-gray-900">
            {{ question.que_num }}. {{ question.que_title }}
          </h4>
          <p class="text-gray-600 text-sm">{{ question.que_title_m }}</p>
        </div>
        
        <!-- Radio buttons for score 1-10 -->
        <div v-if="question.que_type === 1" class="space-y-2">
          <div class="grid grid-cols-10 gap-2 mb-4">
            <label
              v-for="i in 10"
              :key="i"
              class="flex flex-col items-center p-2 border rounded-md hover:bg-blue-50 cursor-pointer"
              :class="{ 'bg-blue-100 border-blue-500': form[question.que_id] == i }"
            >
              <input
                :id="`${question.que_id}-${i}`"
                v-model="form[question.que_id]"
                type="radio"
                :name="question.que_id"
                :value="i"
                class="sr-only"
                required
              />
              <span class="text-lg font-semibold">{{ i }}</span>
            </label>
          </div>
          
          <label class="flex items-center p-2 border rounded-md hover:bg-gray-50 cursor-pointer">
            <input
              :id="`${question.que_id}-0`"
              v-model="form[question.que_id]"
              type="radio"
              :name="question.que_id"
              value="0"
              class="mr-2"
            />
            <span class="text-gray-600">ไม่สามารถประเมินได้</span>
          </label>
        </div>
        
        <!-- Text input for comments -->
        <div v-else-if="question.que_type === 2">
          <textarea
            v-model="form[question.que_id]"
            :name="question.que_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            rows="4"
            placeholder="กรอกความคิดเห็นของคุณ"
          ></textarea>
        </div>
      </div>

      <div class="flex justify-center pt-6">
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
const props = defineProps({
  personName: {
    type: String,
    required: true
  },
  subjectTitle: {
    type: String,
    required: true
  },
  questions: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit'])

const form = ref({})

const initializeForm = () => {
  props.questions.forEach(question => {
    form.value[question.que_id] = ''
  })
}

const submitSurvey = () => {
  emit('submit', form.value)
}

onMounted(() => {
  initializeForm()
})

watch(() => props.questions, () => {
  initializeForm()
}, { deep: true })
</script>

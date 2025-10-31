<template>
  <div class="container mx-auto p-6">
    <div class="text-center mb-8">
      <h3 class="text-2xl font-bold text-gray-900 mb-4">เลือกผู้ประเมิน</h3>
      <p class="text-gray-600">เลือกประเภทผู้ประเมินสำหรับ <span class="font-semibold">{{ targetPerson }}</span></p>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
      <div
        v-for="evaluator in evaluators"
        :key="evaluator.type"
        class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer"
        :class="{ 'ring-2 ring-blue-500': selectedType === evaluator.type }"
        @click="selectEvaluator(evaluator.type)"
      >
        <div class="text-center">
          <div class="text-4xl mb-4">
            <i :class="evaluator.icon" class="text-blue-600"></i>
          </div>
          <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ evaluator.title }}</h4>
          <p class="text-gray-600 text-sm">{{ evaluator.description }}</p>
        </div>
      </div>
    </div>

    <div v-if="selectedType" class="mt-8 bg-blue-50 rounded-lg p-6">
      <h4 class="text-lg font-semibold text-blue-900 mb-4">รายละเอียดผู้ประเมิน</h4>
      <p class="text-blue-800 mb-4">{{ getSelectedEvaluator().description }}</p>
      <p class="text-blue-700 text-sm">{{ getSelectedEvaluator().guidelines }}</p>
    </div>

    <div class="mt-8 flex justify-center space-x-4">
      <button
        @click="goBack"
        class="bg-gray-600 text-white px-6 py-3 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
      >
        ย้อนกลับ
      </button>
      <button
        v-if="selectedType"
        @click="startSurvey"
        class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        เริ่มประเมิน
      </button>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'default'
})

const route = useRoute()
const selectedType = ref('')
const targetPerson = ref('สมชาย ใจดี')

const evaluators = ref([
  {
    type: 'self',
    title: 'ประเมินตนเอง',
    description: 'ประเมินความสามารถและพฤติกรรมของตนเอง',
    icon: 'fas fa-user',
    guidelines: 'ให้ประเมินตนเองอย่างตรงไปตรงมาและเป็นกลาง'
  },
  {
    type: 'peer',
    title: 'ประเมินเพื่อนร่วมงาน',
    description: 'ประเมินเพื่อนร่วมงานในระดับเดียวกัน',
    icon: 'fas fa-users',
    guidelines: 'ประเมินจากประสบการณ์การทำงานร่วมกัน'
  },
  {
    type: 'team',
    title: 'ประเมินทีมงาน',
    description: 'ประเมินสมาชิกในทีมที่ดูแล',
    icon: 'fas fa-user-tie',
    guidelines: 'ประเมินจากมุมมองของผู้จัดการหรือหัวหน้า'
  }
])

const selectEvaluator = (type) => {
  selectedType.value = type
}

const getSelectedEvaluator = () => {
  return evaluators.value.find(e => e.type === selectedType.value)
}

const startSurvey = () => {
  console.log('Start survey for type:', selectedType.value)
  alert(`เริ่มประเมินประเภท: ${getSelectedEvaluator().title} - Template only`)
  navigateTo('/main/u_survey_detail')
}

const goBack = () => {
  navigateTo('/main')
}

useHead({
  title: 'เลือกผู้ประเมิน - FEEDBACK 360'
})
</script>

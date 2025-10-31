<template>
  <div class="container mx-auto p-6">
    <h3 class="text-center text-2xl font-bold mb-6">รายการแบบสำรวจ</h3>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="survey in surveys"
        :key="survey.id"
        class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow"
      >
        <div class="flex items-center justify-between mb-4">
          <h4 class="text-lg font-semibold text-gray-900">{{ survey.title }}</h4>
          <span :class="getStatusClass(survey.status)" class="px-2 py-1 rounded-full text-xs font-medium">
            {{ getStatusText(survey.status) }}
          </span>
        </div>
        
        <p class="text-gray-600 mb-4">{{ survey.description }}</p>
        
        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
          <span>วันที่เริ่ม: {{ survey.start_date }}</span>
          <span>วันที่สิ้นสุด: {{ survey.end_date }}</span>
        </div>
        
        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
          <div 
            class="bg-blue-600 h-2 rounded-full" 
            :style="{ width: survey.progress + '%' }"
          ></div>
        </div>
        
        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-600">ความคืบหน้า: {{ survey.progress }}%</span>
          <button
            @click="viewSurvey(survey.id)"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            {{ survey.status === 'completed' ? 'ดูผลลัพธ์' : 'ทำแบบสำรวจ' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="surveys.length === 0" class="text-center py-12">
      <div class="text-gray-500 text-lg">ไม่พบแบบสำรวจ</div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'default'
})

const surveys = ref([
  {
    id: 1,
    title: 'การประเมินผลงานประจำปี 2024',
    description: 'แบบสำรวจการประเมินผลงานพนักงานประจำปี',
    start_date: '01/01/2024',
    end_date: '31/12/2024',
    progress: 75,
    status: 'in_progress'
  },
  {
    id: 2,
    title: 'การประเมินทักษะการทำงานเป็นทีม',
    description: 'ประเมินทักษะการทำงานร่วมกับผู้อื่น',
    start_date: '15/06/2024',
    end_date: '30/06/2024',
    progress: 100,
    status: 'completed'
  },
  {
    id: 3,
    title: 'การประเมินภาวะผู้นำ',
    description: 'ประเมินทักษะภาวะผู้นำและการจัดการ',
    start_date: '01/07/2024',
    end_date: '15/07/2024',
    progress: 0,
    status: 'not_started'
  }
])

const getStatusClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'in_progress':
      return 'bg-blue-100 text-blue-800'
    case 'not_started':
      return 'bg-gray-100 text-gray-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getStatusText = (status) => {
  switch (status) {
    case 'completed':
      return 'เสร็จสิ้น'
    case 'in_progress':
      return 'กำลังดำเนินการ'
    case 'not_started':
      return 'ยังไม่เริ่ม'
    default:
      return 'ไม่ทราบสถานะ'
  }
}

const viewSurvey = (surveyId) => {
  console.log('View survey:', surveyId)
  alert(`ดูแบบสำรวจ ID: ${surveyId} - Template only`)
}

useHead({
  title: 'รายการแบบสำรวจ - FEEDBACK 360'
})
</script>

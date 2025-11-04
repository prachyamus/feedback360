<template>
  <div class="container mx-auto p-6 max-w-5xl">
    <div class="text-center mb-10">
      <img v-if="emp_image" :src="emp_image" alt="Employee Photo"
        class="w-24 h-24 rounded-full object-cover mx-auto border-4 border-blue-500 shadow-lg"
        @error="handleImageError">
      <div v-else class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 mx-auto flex items-center justify-center shadow-inner">
        <i class="fas fa-user text-4xl text-gray-400"></i>
      </div>
      <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mt-4 tracking-tight">{{ emp_name }}</h3>
      <p class="text-gray-600 mt-1">เลือกประเภทผู้ประเมินสำหรับ <span class="font-semibold">{{ emp_name || targetPerson}}</span></p>
    </div>
  </div>

  <ClientOnly>
  <div class="mx-auto w-full max-w-md flex justify-center gap-4">
    <!-- ประเมินด้วยรายการแบบ loop -->
    <div v-for="item in visibleEvaluators" :key="item.id"
      class="bg-white w-full rounded-xl shadow-md p-4 hover:shadow-xl transition-all cursor-pointer border border-gray-100 hover:-translate-y-0.5"
      :class="{ 'ring-2 ring-blue-500': selectedType === item.key }"
      @click="() => { selectedType = item.key; selectEvaluator(item.id) }">
      <div class="text-center space-y-2">
        <div class="text-4xl mb-2">
          <i :class="item.icon + ' text-blue-600'"></i>
        </div>
        <h4 class="text-lg font-semibold text-gray-900">{{ item.title }}</h4>
        <p class="text-gray-600 text-sm">{{ item.description }}</p>
      </div>
    </div>
  </div>
  </ClientOnly>
</template>

<script setup>
import axios from 'axios'

definePageMeta({
  layout: 'default'
})

const config = useRuntimeConfig()
const employeeDataUrl = config.public.apiBaseUrl + '/api/employee_data.php'
const selectedType = ref('')
const targetPerson = ref('Loading...')

//ค่าจาก ERP
const emp_image = ref('')
const emp_name = ref('')
const emp_position = ref('')

//ค่าจาก Cookie
const E_Code = useCookie('E_Code')
//การค่า Get จาก URL
const route = useRoute()
const emp_id = route.query.person_id
const emp_level = route.query.band
const rounded = route.query.rounded

// Normalize เพื่อให้การเปรียบเทียบข้อมูลสะดวกขึ้น (refresh/SSR safe)
const normalizedECode = computed(() => String(E_Code.value || '').trim())
const normalizedEmpId = computed(() => String(emp_id || '').trim())

//สิทธิ์ประเมินตามสิทธิ์ผู้ใช้งาน
const allEvaluators = [
  {
    id: '1',
    key: 'self',
    icon: 'fas fa-user',
    title: 'ประเมินตนเอง',
    description: 'ประเมินความสามารถและพฤติกรรมของตนเอง',
    visibleWhen: () => normalizedECode.value === normalizedEmpId.value
  },
  {
    id: '2',
    key: 'peer',
    icon: 'fas fa-users',
    title: 'ประเมินเพื่อนร่วมงาน',
    description: 'ประเมินเพื่อนร่วมงานในระดับเดียวกัน',
    visibleWhen: () => normalizedECode.value !== normalizedEmpId.value
  },
  {
    id: '3',
    key: 'team',
    icon: 'fas fa-user-tie',
    title: 'ประเมินทีมงาน',
    description: 'ประเมินสมาชิกในทีมที่ดูแล',
    visibleWhen: () => true
  }
]

//กรองรายการประเมินตามสิทธิ์ผู้ใช้งาน
const visibleEvaluators = computed(() => allEvaluators.filter(e => e.visibleWhen()))

//ดึงข้อมูลพนักงานจาก ERP
const loadEmployeeData = async () => {
  try {
    const response = await axios.get(employeeDataUrl + '?emp_id=' + emp_id)
    emp_image.value = 'http://' + response.data.ImagePath
    emp_name.value = response.data.LocalFirstName + ' ' + response.data.LocalLastName
    emp_position.value = response.data.PositionName
  } catch (error) {
    console.error('Error loading employee data:', error)
  }
}

const selectEvaluator = (type) => {
  navigateTo(`/main/u_survey_list?person_id=${emp_id}&bands=${emp_level}&whotype_id=${type}&rounded=${rounded}`)
}

onMounted(() => {
  loadEmployeeData();
})
</script>

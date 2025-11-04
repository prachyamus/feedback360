<template>
    <div class="container mx-auto p-6">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center min-h-64">
        <div class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p class="text-gray-600">กำลังโหลดข้อมูล...</p>
        </div>
      </div>
  
      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
        <div class="flex items-center">
          <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
          <p class="text-red-700">{{ error }}</p>
        </div>
      </div>
  
      <!-- Main Content -->
      <div v-else>
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
        <h3 class="text-center text-3xl font-bold text-blue-600 mb-6">
          รายงานการสำรวจความเห็น 360 องศาเพื่อส่งเสริมพัฒนาการของสมาชิกองค์กร
        </h3>
        <h4 class="text-center text-xl text-gray-700 mb-6">{{ surveyTitle }}</h4>
        
        <table class="w-full border-collapse border border-gray-300">
          <tbody>
            <tr class="border border-gray-300">
              <th class="bg-blue-50 p-4 text-left w-1/4 font-semibold">ชื่อพนักงาน :</th>
              <td class="p-4">{{ employee.emp_fname }} {{ employee.emp_lname }}</td>
            </tr>
            <tr class="border border-gray-300">
              <th class="bg-blue-50 p-4 text-left font-semibold">Overall Rating :</th>
              <td class="p-4 font-semibold text-blue-600">{{ overallRating }}</td>
            </tr>
            <tr class="border border-gray-300">
              <th class="bg-blue-50 p-4 text-left font-semibold">จำนวนผู้ประเมิน :</th>
              <td class="p-4">{{ feedback.total }} คน (ตนเอง {{ feedback.self }}, เพื่อนร่วมงาน {{ feedback.friend }}, ลูกทีม {{ feedback.team }})</td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Important Notice -->
      <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl shadow-lg p-8 mb-6 border-l-4 border-red-500">
        <h4 class="text-red-600 text-center text-2xl font-bold mb-4">
          <i class="fas fa-exclamation-triangle mr-2"></i>สำคัญ อ่านก่อนดูผล
        </h4>
        <div class="space-y-4 text-gray-700 leading-relaxed">
          <p>
            {{ discription }}
          </p>
        </div>
      </div>
  
      <!-- Spider Chart Section -->
      <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
          <div class="lg:col-span-2 flex items-center justify-center">
            <SpiderRadarChart
              :labels="spiderLabels"
              :datasets="spiderDatasets"
              :options="spiderOptions"
              minHeight="360px"
              class="w-full"
            />
          </div>
          <div class="lg:col-span-3">
            <h3 class="text-2xl font-bold text-blue-600 mb-4">
              <i class="fas fa-chart-radar mr-2"></i>ภาพโพรไฟล์ทักษะการสื่อสารแบบแผนภูมิเรดาร์
            </h3>
            <div v-html="chartSummary" class="text-gray-700 leading-relaxed"></div>
          </div>
        </div>
      </div>
  
      <!-- Page Break for Print -->
      <div class="print:break-after-page"></div>
  
      <!-- Overall Summary Section -->
      <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
        <h3 class="text-2xl font-bold text-blue-600 mb-6">
          <i class="fas fa-chart-pie mr-2"></i>สรุปภาพรวมการสะท้อน 360 องศา
        </h3>
        <div v-html="groupSummary" class="mb-6"></div>
  
        <!-- Overall Rating Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 border rounded-lg p-6 mb-6 bg-gradient-to-r from-blue-50 to-indigo-50">
          <div class="lg:col-span-4">
            <h5 class="font-bold text-lg mb-3">Overall Rating :</h5>
            <p class="text-gray-700 leading-relaxed">
              มีการทำงานที่เน้นการมององค์รวมความเป็นทีมและสร้างสรรค์
              ลำดับความสำคัญงานได้อย่างดีเยี่ยมทำงานด้วยความละเอียดคำนึงถึงหลากหลายมุมคิด...
            </p>
          </div>
          <div class="lg:col-span-3">
            <table class="w-full border border-gray-300 text-center">
              <thead class="bg-blue-100">
                <tr>
                  <th class="border border-gray-300 p-3">AVG</th>
                  <th class="border border-gray-300 p-3">Hi</th>
                  <th class="border border-gray-300 p-3">Lo</th>
                </tr>
              </thead>
              <tbody ref="groupAllTable"></tbody>
            </table>
          </div>
          <div class="lg:col-span-5 flex items-center">
            <canvas ref="chartGroupAll" class="w-full"></canvas>
          </div>
        </div>
  
        <!-- Dynamic Group Data -->
        <div ref="summaryGroupData"></div>
      </div>
  
      <!-- Question Details Section -->
      <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
        <h3 class="text-2xl font-bold text-blue-600 mb-6">
          <i class="fas fa-list-check mr-2"></i>ผลประเมินทักษะแยกตามคำถาม
        </h3>
        <div v-html="questionSummary" class="mb-6"></div>
  
        <div class="overflow-x-auto">
          <table class="min-w-full border-collapse border border-gray-300">
            <thead class="bg-gradient-to-r from-blue-100 to-indigo-100">
              <tr>
                <th colspan="2" rowspan="2" class="border border-gray-300 p-4 text-center align-middle font-bold">
                  คำถามที่ใช้
                </th>
                <th colspan="4" class="border border-gray-300 p-4 text-center font-bold">ค่าเฉลี่ย</th>
              </tr>
              <tr>
                <th class="border border-gray-300 p-3 text-center bg-red-50">ตนเอง</th>
                <th class="border border-gray-300 p-3 text-center bg-yellow-50">เพื่อนร่วมงาน</th>
                <th class="border border-gray-300 p-3 text-center bg-green-50">ลูกทีม</th>
                <th class="border border-gray-300 p-3 text-center bg-blue-50">Avg.</th>
              </tr>
            </thead>
            <tbody ref="tableSummary" class="bg-white"></tbody>
          </table>
        </div>
      </div>
  
      <!-- Performance Highlights -->
      <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl shadow-lg p-8 mb-6 border-l-4 border-green-500">
        <h5 class="text-2xl font-bold text-green-600 mb-6">
          <i class="fas fa-trophy mr-2"></i>ศักยภาพซุปเปอร์ฮีโร่ที่ยังซ่อนเร้นอยู่ในตัว
        </h5>
        <table ref="summaryPerfomance" class="w-full border-collapse border border-gray-300"></table>
      </div>
  
      <!-- Areas for Improvement -->
      <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-xl shadow-lg p-8 mb-6 border-l-4 border-red-500">
        <h5 class="text-2xl font-bold text-red-600 mb-6">
          <i class="fas fa-exclamation-circle mr-2"></i>อันตรายที่มองข้ามและอาจทำให้ตกม้า
        </h5>
        <table ref="summaryFalse" class="w-full border-collapse border border-gray-300"></table>
      </div>
  
      <!-- Comments Section -->
      <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
        <h5 class="text-2xl font-bold text-blue-600 mb-6">
          <i class="fas fa-comments mr-2"></i>ข้อเสนอแนะจากคำถามเปิด
        </h5>
        <div ref="summaryText" class="border rounded-lg p-6 bg-gray-50"></div>
      </div>
  
      <!-- Print Button -->
      <div class="flex justify-center mb-8">
        <button
          @click="printReport"
          class="bg-blue-600 text-white px-8 py-4 rounded-lg hover:bg-blue-700 shadow-lg transition-all transform hover:scale-105"
        >
          <i class="fas fa-print mr-2"></i>พิมพ์รายงาน
        </button>
      </div>
    </div>
    </div>
  </template>
  
  <script setup>
  import axios from 'axios'
  import SpiderRadarChart from '@/components/SpiderRadarChart.vue'
  
  definePageMeta({
    layout: 'admin'
  })

  const route = useRoute();
  const sub_id = route.query.sub_id;
  const person_id = route.query.person_id;

  const config = useRuntimeConfig()
  const apiBaseUrl = config.public.apiBaseUrl + '/api/fetch_report_detail.php'

  const discription = ref(`สวัสดีครับทุกคน รายงานที่จัดทำเพื่อเป็นตัวช่วยบนหนทางการพัฒนาตนเองของพวกเรา เชื่อว่าข้อมูลจะมีประโยชน์กับเราแน่นอนเมื่อเราใช้มุมมองสร้างสรรค์ จึงขอยกกรณีศึกษาของตนเองมาแชร์ ส่วนใหญ่เราจะมุ่งในจุดที่เราพยายามปรับปรุงก่อน เช่นที่ผ่านมาพยายามเรื่องการสื่อสารที่กระชับเข้าใจง่าย ทุกครั้งจะสรุปสิ่งที่คุยและวัตถุประสงค์ เราตั้งใจทำมากและคอยคิดวิธีที่เหมาะกับผู้ฟัง ผลที่ได้ปรากฏว่ากลุ่มลูกทีมซึ่งเป็นกลุ่มที่เราใช้ความระวังมากที่สุดกลับสะท้อนคะแนนมาต่ำสุด อย่างแรกที่ต้องทำคือตั้งสิตเราไม่มองว่าใครเป็นผู้ให้ข้อมูลแต่โฟกัสว่าการกระทำอะไรบ้างที่ทำให้เขาได้รับประสบการณ์เช่นนั้น แล้วส่วนใหญ่ก็พบว่าพอจะเดาได้ว่าสถานการณ์แบบไหนบ้างที่เราน่าจะมีความไม่ชัดเจน ก็สามารถปรับแนวทางต่อไปได้ ค่อยๆดูไปแล้วพยายามมองในภาพรวมนะครับ

ส่วนท้ายนี้เป็นโอกาสที่ผู้ให้ข้อมูลเขาจะสื่อสารตรงกับเราได้ ปกติด้วยความที่เป็นคนธรรมดาจิวก็ไปดูส่วนล่างก่อนเพราะธรรมชาติมันสั่งให้เราไปดูสิ่งที่ไม่ดีก่อน อืมอ่านแล้วก็รู้สึกว่าหัวใจเต้นเร็วขึ้น เพราะมีคอมเม้นว่า เราทำหน้าเคร่งเครียดตลอดเวลา ""เมื่อไหร่กัน?!!!"" คือการตอบโต้แว๊บแรก ปกติเราก็พยายามอย่างมากที่จะควบคุมนะ แล้วยังมีเรื่องการระเบิดอารมณ์อีกด้วย ที่ผ่านมาเราแย่ขนาดนั้นเลยหรอ อืมโมเม้นนี้ยังได้ยินเสียงหัวใจเต้นแรงอยู่นะครับ หลังจากนั้นก็เลิกดูผลต่อเพื่อให้ตัวเองตั้งหลักได้ก่อน

กลับบ้านไปเรื่องนี้ก็ยังค้างอยู่ในหัวครับ คนที่บอกว่าเราหน้าเคร่งเครียดตลอดเวลา ""เขาอยู่กับเรานานแค่ไหนถึงพูดแบบนั้นได้?"" ""เราก็หน้าตาแบบนี้ของเราอยู่แล้วจะไปแก้ยังไง"" มีคำถามแบบนี้ไปเรื่อยครับ ต่อมาพอใจเย็นเราก็เกิดสติ ซึ่งอันนี้จิวมักจะเกิดตอนอาบน้ำนะครับ คือเรื่องของเรื่องคำพูดเหล่านี้คนที่เขียนเขาไม่จำเป็นต้องเขียนก็ได้ แต่ที่เขาเขียนคงเพราะว่าเขาหวังดีกับเรา หากเรากลับมุมเป็นตัวเราเองที่จะต้องให้ข้อมูลมันก็คงไม่ใช่เรื่องง่ายและเราคงต้องเลือกคำที่จะใช้อย่างดีเพื่อให้คนที่ได้ข้อมูลไป ได้ใช้มันมาพัฒนาตนเองได้อย่างดีที่สุด พอเข้าใจแบบนี้แล้ว ก็ลองขยายมุมมองโดยจิตนาการว่าหากคำเหล่านี้ออกมาจากปากแม่หรือครูบาอาจาร์ยที่เรานับถือเรายังจะมีความรู้สึกรุนแรงกับมันไหม จิวก็พบว่าตัวเองคิดว่าไม่ แล้วจึงตกผลึกได้ว่าการตอบรับเรามันขึ้นอยู่กับการทำความเข้าใจเจตนาของผู้ให้ข้อมูล ซึ่งบางท่านอาจจะตั้งคำถามว่า ""เจตนา"" ที่แท้เราก็ไม่รู้อยู่ดีใช่ไหม หากมองที่วัตถุปะรสงค์การใช้ข้อมูลเพื่อพัฒนาตัวเราเอง จิวมองว่ากรณีแบบนี้เจตนา ""จริง"" ของผู้ให้ข้อมูลไม่มีนัยยะสำคัญเท่ากับการตีความและให้ความหมายของตัวเราเองเพื่อตัวเราเองครับ เช่นในเคสแย่สุดเขาต้องการแค่ระบายอารมณ์ไม่ได้คิดช่วยเรา แต่เราตีความว่าเขาหวังดีเราก็จะได้สิ่งดีๆจากคำพูดของเขา ได้ไอเดียในการพัฒนาตัวเรา เมื่อคิดได้แบบนี้ก็กลับไปอ่านรายงานอีกทีพบว่าตนรู้สึกว่าความเห็นที่สะท้อนมาก็ตรงดี

สุดท้ายนี้เรื่องผู้ให้ข้อมูล ไม่อยากให้โฟกัสว่าเป็นใครเพราะความสำคัญว่าเขาเป็นใครมันไม่ใช่ประเด็นใหญ่ ""ถ้ารู้เราจะได้ไปปรับความเข้าใจได้"" หากเราได้ข้อมูลและไปทำเช่นนั้นจริง ในกรณีตัวอย่างเรื่องการสื่อสารจิวอาจจะทำให้ลูกทีมคนนั้นเข้าใจจิวมากขึ้น แต่มันก็ไม่ได้ช่วยในการพัฒนาตนเองของจิวเลย มันจึงไม่ตรงวัตถุประสงค์ของพวกเรานะครับ สิ่งที่ควรทำคือการค่อยๆหาแนวทางพัฒนาตนเองในรูปแบบของเราเอง หากเราไปหาหมอแล้วหมอบอกว่าเราน้ำตาลสูงความดันสูงเราคงไม่ได้พยายามไปจำชื่อหมอหรืออธิบายกับหมอว่าเรามีความจำเป็นแบบโน้นแบบนี้ใช่ไหมครับ เพราะสุดท้ายแล้วการทำเช่นนั้นไม่ได้ช่วยให้สุขภาพเราดีขึ้น เรามองว่าหมอหวังดีกับเราเราก็ไปปรับเปลี่ยนพฤติกรรม แต่หากมองว่าหมอตรวจไม่แม่นไม่ตั้งใจเราก็คงประพฤติแบบเดิมไม่ปรับปรุง ประสบการณ์ที่สะท้อนออกมามันก็เป็นไปตามที่เราตีความ เมื่อเข้าใจตรงนี้ได้มันก็รู้สึกง่ายครับ เพราะการกำหนดสติ การตีความการให้ความหมายที่จะสร้างประสบการณ์ที่ตัวเราเองจดจำนั้น สุดท้ายมันขึ้นอยู่กับตัวเราเท่านั้นครับผม`)
  
  const subId = ref(route.query.sub_id || '')
  const personId = ref(route.query.person_id || '')
  const surveyTitle = ref('')
  const employee = ref([''])
  const overallRating = ref(0)
  const feedback = ref({
        total: 0,
        self: 0,
        friend: 0,
        team: 0
    })
  const chartSummary = ref('')
  const groupSummary = ref('')
  const questionSummary = ref('')
  const loading = ref(false)
  const error = ref('')
  
  const chartGroupAll = ref(null)
  const groupAllTable = ref(null)
  const summaryGroupData = ref(null)
  const tableSummary = ref(null)
  const summaryPerfomance = ref(null)
  const summaryFalse = ref(null)
  const summaryText = ref(null)
  
  const spiderlabel = ref([])
  const spiderdata = ref([])
  const spiderOptions = {
    scales: { r: { min: 0, max: 10, ticks: { stepSize: 2 } } },
    plugins: { legend: { display: true, position: 'bottom' } }
  }
  const groupCharts = []
  
  const loadSummaryData = async () => {
    try {
      loading.value = true
      error.value = ''
      
      const response = await axios.get(apiBaseUrl + `?person_id=${person_id}&sub_id=${sub_id}`)
      console.log(response.data.feedback)
      if (response.data && response.data.success) {
        feedback.value = response.data.feedback
        employee.value = response.data.employee
        overallRating.value = response.data.averange.toFixed(2)
        spiderdata.value = response.data.spider
    //     console.log(feedbackCount.value)
    //     employeeName.value = response.data.data.emp_name || ''
    //     overallRating.value = response.data.data.averange 
    //       ? `${parseFloat(response.data.data.averange).toFixed(2)} (ไม่รวมการประเมินตนเอง)`
    //       : '-'
        
    //     const fb = response.data.data.feedback
    //     if (fb) {
    //       feedbackCount.value = `${fb.total} คน (ตนเอง ${fb.self.total}, เพื่อนร่วมงาน ${fb.friend.total}, ลูกทีม ${fb.team.total})`
    //     }
      } else {
        error.value = 'ไม่สามารถโหลดข้อมูลสรุปได้'
      }
    } catch (err) {
      console.error('Error loading summary:', err)
      error.value = 'เกิดข้อผิดพลาดในการโหลดข้อมูล'
    } finally {
      loading.value = false
    }
  }
  
  const loadSubjectData = async () => {
    try {
      const response = await axios.post('/backend/fetch_subject.php', {
        sub_id: sub_id
      })
      
      if (response.success) {
        surveyTitle.value = response.data.sub_title || ''
        chartSummary.value = response.data.sumary_chart || ''
        groupSummary.value = response.data.sumary_group || ''
        questionSummary.value = response.data.sumary_question || ''
      }
    } catch (err) {
      console.error('Error loading subject:', err)
      error.value = 'เกิดข้อผิดพลาดในการโหลดข้อมูลแบบสำรวจ'
    }
  }
  
  const createSpiderChart = async () => {
    try {
      const response = await axios.post('/backend/chart_spider.php', {
        person_id: person_id,
        sub_id: sub_id
      })
      
      if (response.data && response.data.success) {
        spiderLabels.value = response.data.labels || []
        spiderDatasets.value = [
          {
            label: 'ตนเอง',
            data: response.data.self || [],
            backgroundColor: 'rgba(255, 206, 86, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 3
          },
          {
            label: 'ลูกทีม',
            data: response.data.team || [],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 3
          },
          {
            label: 'เพื่อนร่วมงาน',
            data: response.data.friend || [],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 3
          }
        ]
      }
    } catch (error) {
      console.error('Error creating spider chart:', error)
    }
  }
  
  const printReport = () => {
    window.print()
  }
  
  onMounted(async () => {
    if (!subId.value || !personId.value) {
      error.value = 'ไม่พบข้อมูลที่จำเป็น (sub_id หรือ person_id)'
      return
    }
    
    await loadSummaryData()
    await loadSubjectData()
    await createSpiderChart()
  })
  
  onUnmounted(() => {
    groupCharts.forEach(chart => {
      if (chart && typeof chart.destroy === 'function') {
        chart.destroy()
      }
    })
    groupCharts.length = 0
  })
  
  useHead({
    title: 'รายงานผลการประเมิน - FEEDBACK 360'
  })
  </script>
  
  <style scoped>
  @media print {
    .print\:break-after-page {
      page-break-after: always;
    }
  }
  </style>
  
  
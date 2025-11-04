<template>
  <div class="container mx-auto p-6">
    <div class="container mx-auto p-6 max-w-5xl">
      <div class="text-center mb-10">
        <img v-if="emp_image" :src="emp_image" alt="Employee Photo"
          class="w-24 h-24 rounded-full object-cover mx-auto border-4 border-blue-500 shadow-lg"
          @error="handleImageError" />
        <div v-else
          class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 mx-auto flex items-center justify-center shadow-inner">
          <i class="fas fa-user text-4xl text-gray-400"></i>
        </div>
        <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mt-4 tracking-tight">
          {{ emp_name }}
        </h3>
        <p class="text-gray-600 mt-1">
          เลือกประเภทผู้ประเมินสำหรับ
          <span class="font-semibold">{{ emp_name || targetPerson }}</span>
        </p>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <h3 class="text-xl font-bold text-white">รายการแบบสำรวจ</h3>
      </div>

      <div v-if="surveys.length > 0" class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                ลำดับ
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                หัวข้อแบบสำรวจ
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                คำอธิบาย
              </th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                สถานะ
              </th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                จัดการ
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(survey, index) in surveys" :key="survey.sub_id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ index + 1 }}
              </td>
              <td class="px-6 py-4 text-sm font-medium text-gray-900">
                {{ survey.sub_title }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ survey.sub_discrip || "-" }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span :class="getStatusClass(survey.sub_status)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ survey.sub_status === "1" ? "เปิดใช้งาน" : "ปิดใช้งาน" }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <button @click="viewSurvey(survey.sub_id)"
                  class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm transition-colors">
                <i class="fas fa-search mr-1"></i>ดูสรุปผล
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="text-center py-12">
        <div class="text-gray-500 text-lg">ไม่พบแบบสำรวจ</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";

definePageMeta({
  layout: "admin",
});

//ค่าจาก ERP
const emp_image = ref("");
const emp_name = ref("");
const emp_position = ref("");

const surveys = ref([]);

//การค่า Get จาก URL
const route = useRoute();
const emp_id = route.query.person_id;
const emp_level = route.query.band;
const rounded = route.query.rounded;

const config = useRuntimeConfig();
const employeeDataUrl = config.public.apiBaseUrl + "/api/employee_data.php";
const surveysUrl = config.public.apiBaseUrl + "/api/report_user_subject.php";

//ดึงข้อมูลพนักงานจาก ERP
const loadEmployeeData = async () => {
  try {
    const response = await axios.get(employeeDataUrl + "?emp_id=" + emp_id);
    emp_image.value = "http://" + response.data.ImagePath;
    emp_name.value =
      response.data.LocalFirstName + " " + response.data.LocalLastName;
    emp_position.value = response.data.PositionName;
  } catch (error) {
    console.error("Error loading employee data:", error);
  }
};

const loadSurveys = async () => {
  try {
    const rounded = route.query.rounded || "";
    const bands = route.query.bands || "";
    const type = route.query.whotype_id || "";
    const form = new FormData();
    form.append("action", "list-active");
    const res = await axios.get(surveysUrl + "?emp_id=" + emp_id);
    if (res && res.data && res.data.success) {
      surveys.value = res.data.data || [];
    }
  } catch (e) { }
};

const getStatusClass = (status) => {
  return String(status) === "1"
    ? "bg-green-100 text-green-800"
    : "bg-gray-100 text-gray-800";
};

const viewSurvey = (subId) => {

  navigateTo(
    `/admin/report_user_subject_detail?sub_id=${subId}&person_id=${emp_id}`
  );
};

onMounted(() => {
  loadEmployeeData();
  loadSurveys();
});

useHead({
  title: "รายการแบบสำรวจ - FEEDBACK 360",
});

const selectEvaluator = (type) => {
  navigateTo(
    `/main/u_survey_list?person_id=${emp_id}&bands=${emp_level}&whotype_id=${type}&rounded=${rounded}`
  );
};

onMounted(() => {
  loadEmployeeData();
});
</script>
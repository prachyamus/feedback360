<template>
    <div class="container mx-auto p-6">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-blue-600">
          <i class="fas fa-user-shield mr-2"></i>รายชื่อผู้จัดการระบบ
        </h3>
        <button @click="openModal"
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <i class="fas fa-plus mr-2"></i>เพิ่มผู้จัดการระบบ
        </button>
      </div>

      <div class="mb-4 bg-white p-4 rounded-lg shadow-md">
        <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
        <input v-model="searchValue" type="text"
          class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="ค้นหาจากรหัสพนักงาน, ชื่อ-นามสกุล...">
      </div>

      <div class="bg-white rounded-lg shadow-md overflow-hidden p-4">
        <div v-if="loading" class="flex justify-center items-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-2 text-gray-600">กำลังโหลดข้อมูล...</span>
        </div>

        <EasyDataTable
          v-else
          :headers="headers"
          :items="admins"
          :rows-per-page="10"
          :search-value="searchValue"
          buttons-pagination
          theme-color="#2563eb"
          table-class-name="customize-table"
        >
          <template #item-emp_permission="{ emp_permission }">
            <span :class="getPermissionClass(emp_permission)" class="px-2 py-1 rounded-full text-xs font-medium">
              {{ getPermissionText(emp_permission) }}
            </span>
          </template>

          <template #item-actions="item">
            <div class="flex justify-center space-x-2">
              <button @click="editAdmin(item)" class="text-blue-600 hover:text-blue-900">
                <i class="fas fa-edit"></i>
              </button>
              <button @click="deleteAdmin(item.id)" class="text-red-600 hover:text-red-900">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </template>
          
          <template #empty-message>
            <div class="text-center py-8 text-gray-500">
              ไม่พบข้อมูลผู้จัดการระบบ
            </div>
          </template>
        </EasyDataTable>
      </div>


      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">เพิ่ม-แก้ไขผู้ใช้งานระบบ</h3>

            <form @submit.prevent="saveAdmin" class="space-y-4">
              <div>
                <input type="hidden" v-model="form.id" />
                <label class="block text-sm font-medium text-gray-700 mb-1">รหัสพนักงาน</label>
                <input v-model="form.emp_id" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="รหัสพนักงาน" required :readonly="isEdit" @change="fetchUserData" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ-นามสกุล</label>
                <input v-model="form.fullname" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="ชื่อ-นามสกุล" readonly />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">สิทธิ์การใช้งาน</label>
                <select v-model="form.permission"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required>
                  <option value="">โปรดเลือก</option>
                  <option value="1">ทีมงาน</option>
                  <option value="2">ผู้ดูแลระบบ</option>
                  <option value="0">ยกเลิกผู้ใช้งาน</option>
                </select>
              </div>

              <div class="flex justify-end space-x-3 pt-4">
                <button type="button" @click="closeModal"
                  class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                  ยกเลิก
                </button>
                <button type="submit"
                  class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <i class="fas fa-save mr-2"></i>บันทึก
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
const showModal = ref(false)
const isEdit = ref(false)
const loading = ref(false)
const searchValue = ref('')

const headers = [
  { text: 'รหัสพนักงาน', value: 'emp_id', sortable: true, width: 150 },
  { text: 'ชื่อ-นามสกุล', value: 'fullname', sortable: true },
  { text: 'สิทธิ์', value: 'emp_permission', sortable: true, width: 150 },
  { text: 'จัดการ', value: 'actions', width: 100 }
]

const form = ref({
  id: '',
  emp_id: '',
  fullname: '',
  permission: ''
})

const admins = ref([])
const config = useRuntimeConfig()
const apiBaseUrl = config.public.apiBaseUrl + '/api/admin.php';
const employeeDataUrl = config.public.apiBaseUrl + '/api/employee_data.php';


//ดึงรายการผู้ดูแลระบบ
const loadAdmins = async () => {
  try {
    loading.value = true
    const form = new URLSearchParams()
    form.append('action', 'list')
    const response = await axios.post(`${apiBaseUrl}`, form, { withCredentials: true })
    if (response.data.success) {
      admins.value = response.data.data
    }
  } catch (error) {
    console.error('Error loading admins:', error)
  } finally {
    loading.value = false
  }
}

//เปิดหน้าต่างเพิ่ม-แก้ไขผู้ใช้งานระบบ
const openModal = () => {
  isEdit.value = false
  resetForm()
  showModal.value = true
}

//ปิดหน้าต่างเพิ่ม-แก้ไขผู้ใช้งานระบบ
const closeModal = () => {
  showModal.value = false
  resetForm()
}

//รีเซ็ตฟอร์ม
const resetForm = () => {
  form.value = {
    id: '',
    emp_id: '',
    fullname: '',
    permission: ''
  }
}

//แก้ไขผู้ใช้งานระบบ
const editAdmin = async (admin) => {
  isEdit.value = true
  form.value = {
    id: admin.id,
    emp_id: admin.emp_id,
    fullname: admin.fullname,
    permission: admin.permission || admin.emp_permission
  }
  showModal.value = true
}

//ลบผู้ใช้งานระบบ
const deleteAdmin = async (id) => {
  const confirmed = await showConfirm('คุณต้องการลบผู้ใช้งานนี้หรือไม่?')
  if (confirmed) {
    try {
      const form = new URLSearchParams()
      form.append('action', 'delete')
      form.append('id', id)
      const response = await axios.post(apiBaseUrl, form, { withCredentials: true })
      const res = response && response.data !== undefined ? response.data : response

      if (res?.success) {
        toast.success('ลบผู้ใช้งานสำเร็จ')
        loadAdmins()
      }
    } catch (error) {
      console.error('Error deleting admin:', error)
      toast.error('เกิดข้อผิดพลาดในการลบข้อมูล')
    }
  }
}

//บันทึกผู้ใช้งานระบบ
const saveAdmin = async () => {
  try {
    loading.value = true
    const formData = new URLSearchParams()
    formData.append('action', 'save')
    formData.append('id', form.value.id)
    formData.append('emp_id', form.value.emp_id)
    formData.append('emp_permission', form.value.permission)
    const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
    const res = response && response.data !== undefined ? response.data : response

    if (res?.success) {
      toast.success((res.data && res.data.message) || 'บันทึกข้อมูลสำเร็จ')
      closeModal()
      loadAdmins()
    }
  } catch (error) {
    console.error('Error saving admin:', error)
    if (error.message) {
      toast.error(error.message)
    } else {
      toast.error('เกิดข้อผิดพลาดในการบันทึกข้อมูล')
    }
  } finally {
    loading.value = false
  }
}

//ดึงข้อมูลพนักงานจาก ERP
const fetchUserData = async () => {
  if (form.value.emp_id) {
    try {
      const response = await axios.get(employeeDataUrl + '?emp_id=' + form.value.emp_id, { withCredentials: true })
      const res = response && response.data !== undefined ? response.data : response
      console.log(res)
      form.value.fullname = res.LocalFirstName + ' ' + res.LocalLastName
    } catch (error) {
      console.error('Error fetching user from ERP:', error)
    }
  }
}

//ดึงสิทธิ์การใช้งาน
const getPermissionClass = (permission) => {
  switch (permission) {
    case '1':
      return 'bg-blue-100 text-blue-800'
    case '2':
      return 'bg-green-100 text-green-800'
    case '0':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

//ดึงสิทธิ์การใช้งาน
const getPermissionText = (permission) => {
  switch (permission) {
    case '1':
      return 'ทีมงาน'
    case '2':
      return 'ผู้จัดการระบบ'
    case '0':
      return 'ยกเลิก'
    default:
      return 'ไม่ทราบ'
  }
}

onMounted(() => {
  loadAdmins()
})

useHead({
  title: 'จัดการผู้ใช้งานระบบ - FEEDBACK 360'
})
</script>

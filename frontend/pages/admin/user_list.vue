<template>
    <div class="container mx-auto p-6">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-blue-600">
          <i class="fas fa-users mr-2"></i>จัดการผู้ถูกประเมิน
        </h3>
        <button @click="openModal" :disabled="!selectedRound"
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
          <i class="fas fa-plus mr-2"></i>เพิ่มผู้ถูกประเมิน
        </button>
      </div>

      <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">เลือกรอบการประเมิน</label>
            <select v-model="selectedRound" @change="loadUsers"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">โปรดเลือกรอบการประเมิน</option>
              <option v-for="round in rounds" :key="round.r_id" :value="round.r_id">
                {{ round.r_title }}
              </option>
            </select>
          </div>
          
          <div v-if="selectedRound">
            <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
            <input v-model="searchValue" type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="ค้นหาจากชื่อ, รหัสพนักงาน, ตำแหน่ง...">
          </div>
        </div>
      </div>

      <div v-if="selectedRound" class="bg-white rounded-lg shadow-md overflow-hidden p-4">
        <div v-if="loading" class="flex justify-center items-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-2 text-gray-600">กำลังโหลดข้อมูล...</span>
        </div>
        
        <EasyDataTable
          v-else
          :headers="headers"
          :items="users"
          :rows-per-page="10"
          :search-value="searchValue"
          buttons-pagination
          theme-color="#2563eb"
          table-class-name="customize-table"
          hide-rows-per-page
          hide-footer-pagination-info
        >
          <template #item-actions="item">
            <div class="flex justify-center space-x-2">
              <button @click="editUser(item)" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-pencil-alt"></i>
              </button>
              <button @click="deleteUser(item.u_id)" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          </template>
          
          <template #empty-message>
            <div class="text-center py-8 text-gray-500">
              ไม่พบข้อมูลผู้ถูกประเมิน
            </div>
          </template>
        </EasyDataTable>
      </div>

      <div v-else class="bg-white rounded-lg shadow-md p-8 text-center text-gray-500">
        <i class="fas fa-info-circle text-4xl mb-4"></i>
        <p>กรุณาเลือกรอบการประเมินเพื่อแสดงรายการผู้ถูกประเมิน</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ isEdit ? 'แก้ไขผู้ถูกประเมิน' : 'เพิ่มผู้ถูกประเมิน' }}
          </h3>

          <form @submit.prevent="saveUser" class="space-y-4">
            <input type="hidden" v-model="form.u_id" />
            <input type="hidden" v-model="form.rounded" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">รหัสพนักงาน *</label>
                <input v-model="form.emp_id" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="รหัสพนักงาน" required />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ</label>
                <input v-model="form.emp_fname" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="ชื่อ" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">นามสกุล</label>
                <input v-model="form.emp_lname" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="นามสกุล" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อเล่น</label>
                <input v-model="form.emp_nickname" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="ชื่อเล่น" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ตำแหน่ง</label>
                <input v-model="form.emp_position" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="ตำแหน่ง" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ระดับ</label>
                <input v-model="form.emp_level" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="ระดับ" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">สำนัก</label>
                <input v-model="form.emp_div" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="สำนัก" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ฝ่าย</label>
                <input v-model="form.emp_dep" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="ฝ่าย" />
              </div>
            </div>

            <div class="border-t pt-4">
              <h4 class="text-sm font-medium text-gray-700 mb-3">ผู้ประเมิน (Part 1-10)</h4>
              <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <div v-for="i in 10" :key="i">
                  <label class="block text-xs font-medium text-gray-600 mb-1">Part {{ i }}</label>
                  <input v-model="form[`part${i}`]" type="text"
                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    :placeholder="`Part ${i}`" />
                </div>
              </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
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
</template>

<script setup>
import axios from 'axios'
import { useToast } from 'vue-toastification'

definePageMeta({
  layout: 'admin'
})

const toast = useToast()
const { showConfirm } = useConfirm()
const config = useRuntimeConfig()
const apiBaseUrl = config.public.apiBaseUrl + '/api/users.php'
const roundsApiUrl = config.public.apiBaseUrl + '/api/rounds.php'

const selectedRound = ref('')
const rounds = ref([])
const users = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const searchValue = ref('')

const headers = [
  { text: 'ID', value: 'u_id', sortable: true, width: 80 },
  { text: 'รหัสพนักงาน', value: 'emp_id', sortable: true, width: 120 },
  { text: 'ชื่อ', value: 'emp_fname', sortable: true },
  { text: 'นามสกุล', value: 'emp_lname', sortable: true },
  { text: 'ชื่อเล่น', value: 'emp_nickname', sortable: true },
  { text: 'ตำแหน่ง', value: 'emp_position', sortable: true },
  { text: 'ระดับ', value: 'emp_level', sortable: true, width: 100 },
  { text: 'สำนัก', value: 'emp_div', sortable: true },
  { text: 'ฝ่าย', value: 'emp_dep', sortable: true },
  { text: 'จัดการ', value: 'actions', width: 100 }
]

const form = ref({
  u_id: '',
  emp_id: '',
  emp_fname: '',
  emp_lname: '',
  emp_nickname: '',
  emp_position: '',
  emp_level: '',
  emp_div: '',
  emp_dep: '',
  rounded: '',
  part1: '',
  part2: '',
  part3: '',
  part4: '',
  part5: '',
  part6: '',
  part7: '',
  part8: '',
  part9: '',
  part10: ''
})

const loadRounds = async () => {
  try {
    const formData = new URLSearchParams()
    formData.append('action', 'list')
    const response = await axios.post(roundsApiUrl, formData, { withCredentials: true })
    
    if (response.data.success) {
      rounds.value = response.data.data || []
    } else if (Array.isArray(response.data)) {
      rounds.value = response.data
    }
  } catch (err) {
    console.error('Error loading rounds:', err)
    toast.error('เกิดข้อผิดพลาดในการโหลดรอบการประเมิน')
  }
}

const loadUsers = async () => {
  if (!selectedRound.value) return
  
  loading.value = true
  
  try {
    const response = await axios.get(`${apiBaseUrl}?rounded=${selectedRound.value}`, { withCredentials: true })
    
    if (response.data.success) {
      users.value = response.data.data || []
    } else if (Array.isArray(response.data)) {
      users.value = response.data
    }
  } catch (err) {
    console.error('Error loading users:', err)
    toast.error('เกิดข้อผิดพลาดในการโหลดข้อมูล')
  } finally {
    loading.value = false
  }
}

const openModal = () => {
  isEdit.value = false
  resetForm()
  form.value.rounded = selectedRound.value
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const resetForm = () => {
  form.value = {
    u_id: '',
    emp_id: '',
    emp_fname: '',
    emp_lname: '',
    emp_nickname: '',
    emp_position: '',
    emp_level: '',
    emp_div: '',
    emp_dep: '',
    rounded: '',
    part1: '',
    part2: '',
    part3: '',
    part4: '',
    part5: '',
    part6: '',
    part7: '',
    part8: '',
    part9: '',
    part10: ''
  }
}

const editUser = async (user) => {
  isEdit.value = true
  form.value = { ...user }
  showModal.value = true
}

const deleteUser = async (id) => {
  const confirmed = await showConfirm('คุณต้องการลบผู้ถูกประเมินนี้หรือไม่?')
  if (confirmed) {
    try {
      const formData = new URLSearchParams()
      formData.append('action', 'delete')
      formData.append('u_id', id)
      const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
      const res = response && response.data !== undefined ? response.data : response

      if (res?.success) {
        toast.success('ลบผู้ถูกประเมินสำเร็จ')
        loadUsers()
      }
    } catch (error) {
      console.error('Error deleting user:', error)
      toast.error('เกิดข้อผิดพลาดในการลบข้อมูล')
    }
  }
}

const saveUser = async () => {
  try {
    loading.value = true
    const formData = new URLSearchParams()
    formData.append('action', 'save')
    
    Object.keys(form.value).forEach(key => {
      formData.append(key, form.value[key] || '')
    })
    
    const response = await axios.post(apiBaseUrl, formData, { withCredentials: true })
    const res = response && response.data !== undefined ? response.data : response

    if (res?.success) {
      toast.success((res.data && res.data.message) || 'บันทึกข้อมูลสำเร็จ')
      closeModal()
      loadUsers()
    }
  } catch (error) {
    console.error('Error saving user:', error)
    if (error.message) {
      toast.error(error.message)
    } else {
      toast.error('เกิดข้อผิดพลาดในการบันทึกข้อมูล')
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadRounds()
})

useHead({
  title: 'จัดการผู้ถูกประเมิน - FEEDBACK 360'
})
</script>


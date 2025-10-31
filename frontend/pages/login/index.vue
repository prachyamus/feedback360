<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, #194f90 0%, #1a5492 50%, #1c5a95 100%);">
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-40 -right-32 w-80 h-80 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse" style="background-color: #2a5f9a;"></div>
      <div class="absolute -bottom-40 -left-32 w-80 h-80 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse" style="background-color: #3a6fa2; animation-delay: 2s;"></div>
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse" style="background-color: #4a7faa; animation-delay: 4s;"></div>
    </div>

    <div class="relative max-w-md w-full space-y-8">
      <!-- Logo and Title Section -->
      <div class="text-center">
        <div class="mx-auto h-20 w-20 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center shadow-lg mb-6">
          <i class="fas fa-chart-line text-white text-2xl"></i>
        </div>
        <h2 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-white">
          FEEDBACK 360
        </h2>
        <p class="mt-2 text-blue-100">ระบบประเมินผลพนักงานแบบ 360 องศา</p>
      </div>
      
      <!-- Login Form Card -->
      <div class="bg-white/90 backdrop-blur-sm p-8 rounded-2xl shadow-2xl border border-white/30">
        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Username Field -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">
              <i class="fas fa-user mr-2 text-blue-600"></i>รหัสพนักงาน
            </label>
            <div class="relative">
              <input
                v-model="form.username"
                type="text"
                class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50/50"
                placeholder="กรอกรหัสพนักงาน"
                required
              />
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-id-card text-gray-400"></i>
              </div>
            </div>
          </div>

          <!-- Password Field -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">
              <i class="fas fa-lock mr-2 text-blue-600"></i>รหัสผ่าน
            </label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="w-full px-4 py-3 pl-12 pr-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50/50"
                placeholder="กรอกรหัสผ่าน"
                required
              />
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-key text-gray-400"></i>
              </div>
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
              >
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember-me"
                v-model="form.remember"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                จดจำการเข้าสู่ระบบ
              </label>
            </div>
            <a href="#" class="text-sm text-blue-600 hover:text-blue-500 transition-colors">
              ลืมรหัสผ่าน?
            </a>
          </div>

          <!-- Login Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 shadow-lg"
          >
            <span v-if="loading" class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              กำลังเข้าสู่ระบบ...
            </span>
            <span v-else class="flex items-center justify-center">
              <i class="fas fa-sign-in-alt mr-2"></i>
              เข้าสู่ระบบ
            </span>
          </button>

          <!-- ERP Notice -->
          <div class="text-center">
            <small class="text-gray-500 text-xs flex items-center justify-center">
              <i class="fas fa-info-circle mr-1"></i>
              เข้าสู่ระบบด้วยรหัสใช้งาน ERP
            </small>
          </div>
        </form>
      </div>

      <!-- Footer -->
      <div class="text-center">
        <p class="text-xs text-blue-200">
          © 2024 FEEDBACK 360 System. All rights reserved.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: false
})

const form = ref({
  username: '',
  password: '',
  remember: false
})

const loading = ref(false)
const showPassword = ref(false)

const handleLogin = async () => {
  loading.value = true
  
  
  try {
    const config = useRuntimeConfig()
    const response = await $fetch(config.public.apiBaseUrl + '/api/auth.php', {
      method: 'POST',
      credentials: 'include',
      body: {
        emp_id: form.value.username,
        password: form.value.password
      }
    })

    if (response && response.success) {
      // เก็บข้อมูลเป็น cookies ฝั่ง frontend
      const userData = response.data
      const token = userData.token
      
      // ฟังก์ชันตั้งค่า cookie
      const setCookie = (name, value, days = 1) => {
        const expires = new Date()
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000))
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`
      }
      
      // ตั้งค่า cookies ตามข้อมูลที่ได้จาก backend
      setCookie('auth_token', token, form.value.remember ? 30 : 1)
      setCookie('E_Code', userData.EmployeeCode || '', form.value.remember ? 30 : 1)
      setCookie('E_LocalFirstName', userData.LocalFirstName || '', form.value.remember ? 30 : 1)
      setCookie('E_LocalLastName', userData.LocalLastName || '', form.value.remember ? 30 : 1)
      setCookie('E_ImagePath', userData.ImagePath || '', form.value.remember ? 30 : 1)
      setCookie('E_Level', userData.Level || '', form.value.remember ? 30 : 1)
      setCookie('E_Division', userData.DivisionName || '', form.value.remember ? 30 : 1)
      setCookie('admin_permiss', userData.admin_permiss || '1', form.value.remember ? 30 : 1)
      setCookie('adminlevel', userData.admin_permiss || '1', form.value.remember ? 30 : 1)
      
      alert('เข้าสู่ระบบสำเร็จ')
      setTimeout(() => {
        if (userData.admin_permiss && userData.admin_permiss > 1) {
          navigateTo('/admin')
        } else {
          navigateTo('/main')
        }
      }, 600)
    } else {
      alert(response?.message || 'เข้าสู่ระบบไม่สำเร็จ')
    }
  } catch (error) {
    console.error('Login error:', error)
    
    // Handle specific error messages from ERP
    let errorMessage = 'เข้าสู่ระบบไม่สำเร็จ'
    
    if (error.response && error.response.data) {
      const errorData = error.response.data
      if (errorData.message) {
        errorMessage = errorData.message
      } else if (errorData.MessageReturn) {
        errorMessage = errorData.MessageReturn
      }
    }
    
    alert(errorMessage)
  } finally {
    loading.value = false
  }
}

useHead({
  title: 'เข้าสู่ระบบ - FEEDBACK 360'
})
</script>

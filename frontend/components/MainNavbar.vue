<template>
  <nav class="bg-gradient-to-r from-blue-600 to-indigo-700 shadow-lg">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center h-16">
        <!-- Logo and Title -->
        <div class="flex items-center space-x-4">
          <div class="h-10 w-10 bg-white rounded-full flex items-center justify-center">
            <i class="fas fa-chart-line text-blue-600 text-xl"></i>
          </div>
          <div class="text-white">
            <h1 class="text-xl font-bold">FEEDBACK 360</h1>
            <p class="text-xs text-blue-100">ระบบประเมินผลพนักงาน</p>
          </div>
        </div>

        <!-- User Info and Actions -->
        <div class="flex items-center space-x-4">
          <!-- User Info -->
          <div class="hidden md:block text-right text-white">
            <p class="text-sm font-medium">{{ userInfo.name }}</p>
            <p class="text-xs text-blue-100">{{ userInfo.position }}</p>
          </div>

          <!-- User Avatar -->
          <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center overflow-hidden">
            <img v-if="userInfo.image" :src="userInfo.image" :alt="userInfo.name" class="h-full w-full object-cover">
            <i v-else class="fas fa-user text-blue-600"></i>
          </div>

          <!-- Logout Button -->
          <button @click="handleLogout"
            class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center space-x-2">
            <i class="fas fa-sign-out-alt"></i>
            <span class="hidden md:inline">ออกจากระบบ</span>
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
const userInfo = ref({
  name: '',
  position: '',
  image: '',
  code: ''
})

// ดึงข้อมูลจาก cookies
const getCookie = (key) => {
  if (!process.client) return null
  const name = key + '='
  const parts = document.cookie.split(';')
  for (const raw of parts) {
    const c = raw.trim()
    if (c.startsWith(name)) return c.substring(name.length)
  }
  return null
}

const loadUserInfo = () => {
  if (!process.client) return
  const first = getCookie('E_LocalFirstName') || ''
  const last = getCookie('E_LocalLastName') || ''
  const img = getCookie('E_ImagePath') || ''
  userInfo.value = {
    name: (first + ' ' + last).trim(),
    position: getCookie('E_Level') || '',
    image: img ? ('http://' + img) : '',
    code: getCookie('E_Code') || ''
  }
}

// Logout function
const handleLogout = () => {
  if (confirm('คุณต้องการออกจากระบบหรือไม่?')) {
    // ลบ cookies แบบง่ายฝั่ง client (ชั่วคราว)
    if (process.client) {
      const names = ['auth_token','E_Code','E_LocalFirstName','E_LocalLastName','E_ImagePath','E_Level','E_Division','admin_permiss','adminlevel']
      names.forEach(n => document.cookie = n + '=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/')
    }

    alert('ออกจากระบบสำเร็จ')

    // Redirect to login
    setTimeout(() => {
      navigateTo('/login')
    }, 1000)
  }
}

onMounted(() => {
  loadUserInfo()
})
</script>

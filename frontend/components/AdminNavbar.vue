<template>
  <nav class="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center py-4">
        <!-- Logo -->
        <NuxtLink to="/admin" class="flex items-center space-x-4 hover:opacity-90 transition-opacity">
          <h1 class="text-white text-2xl font-bold">
            <i class="fas fa-chart-network mr-2"></i>FEEDBACK 360
          </h1>
          <span class="text-blue-200 text-sm hidden lg:inline">Admin Panel</span>
        </NuxtLink>

        <!-- Navigation Menu - Desktop -->
        <div class="hidden md:flex items-center space-x-2">
          <template v-for="menu in menuItems" :key="menu.id">
            <!-- Simple Link -->
            <NuxtLink v-if="menu.type === 'link'" :to="menu.to"
              class="text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
              <i :class="menu.icon + ' mr-2'"></i>{{ menu.label }}
            </NuxtLink>

            <!-- Dropdown Menu -->
            <div v-else-if="menu.type === 'dropdown'" class="relative" @mouseenter="openDropdown(menu.id)"
              @mouseleave="closeDropdown(menu.id)">
              <button class="text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors flex items-center pointer"
                :class="{ 'bg-blue-700': isDropdownOpen[menu.id] }" @click="toggleDropdown(menu.id)"  >
                <i :class="menu.icon + ' mr-2'"></i>{{ menu.label }}
                <i class="fas fa-chevron-down ml-2 text-xs"></i>
              </button>
              <div v-if="isDropdownOpen[menu.id]" class="absolute left-0 bg-white rounded-lg shadow-xl py-2 z-50 border border-gray-200"
                :class="menu.children.length > 3 ? 'w-64' : 'w-56'">
                <template v-for="(child, index) in menu.children" :key="index">
                  <!-- Link Item -->
                  <NuxtLink v-if="!child.type || child.type === 'link'" :to="child.to"
                    class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                    <i :class="[child.icon, 'w-5 mr-3', child.iconColor]"></i>
                    <span>{{ child.label }}</span>
                  </NuxtLink>

                  <!-- Divider -->
                  <div v-else-if="child.type === 'divider'" class="border-t border-gray-200 my-1"></div>

                  <!-- Info Block -->
                  <div v-else-if="child.type === 'info'" class="px-4 py-2">
                    <div class="text-xs text-gray-500 font-semibold uppercase mb-1">{{ child.title }}</div>
                    <div class="text-xs text-gray-600">
                      <i :class="child.icon + ' mr-1'"></i>
                      {{ child.description }}
                    </div>
                  </div>
                </template>
              </div>
            </div>
          </template>
        </div>

        <!-- User Menu & Logout -->
        <div class="hidden md:flex items-center space-x-4">
          <div class="text-white text-sm flex items-center bg-blue-700 bg-opacity-50 px-3 py-2 rounded-lg">
            <i class="fas fa-user-circle text-lg mr-2"></i>
            <span class="font-medium">{{ userName }}</span>
          </div>
          <button @click="handleLogout"
            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
            <i class="fas fa-sign-out-alt mr-2"></i>ออกจากระบบ
          </button>
        </div>

        <!-- Mobile Menu Button -->
        <button @click="toggleMobileMenu"
          class="md:hidden text-white p-2 rounded-lg hover:bg-blue-700 transition-colors">
          <i :class="showMobileMenu ? 'fas fa-times' : 'fas fa-bars'" class="text-2xl"></i>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div v-if="showMobileMenu" class="md:hidden pb-4 border-t border-blue-500 mt-4 pt-4">
        <div class="flex flex-col space-y-1">
          <!-- User Info Mobile -->
          <div class="bg-blue-700 bg-opacity-50 text-white px-4 py-3 rounded-lg mb-2">
            <i class="fas fa-user-circle text-lg mr-2"></i>
            <span class="font-medium">{{ userName }}</span>
          </div>

          <!-- Dynamic Menu Items -->
          <template v-for="menu in menuItems" :key="'mobile-' + menu.id">
            <!-- Simple Link -->
            <NuxtLink v-if="menu.type === 'link'" :to="menu.to"
              class="text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center"
              @click="toggleMobileMenu">
              <i :class="[menu.icon, 'w-6 mr-3']"></i>
              <span>{{ menu.label }}</span>
            </NuxtLink>

            <!-- Dropdown Section -->
            <template v-else-if="menu.type === 'dropdown'">
              <div class="border-t border-blue-500 my-2"></div>
              <div class="px-4 py-2 text-blue-200 text-xs font-bold uppercase tracking-wider">
                <i :class="menu.icon + ' mr-2'"></i>{{ menu.label }}
              </div>

              <template v-for="(child, childIndex) in menu.children" :key="'mobile-child-' + childIndex">
                <NuxtLink v-if="!child.type || child.type === 'link'" :to="child.to"
                  class="text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors pl-10 flex items-center"
                  @click="toggleMobileMenu">
                  <i :class="[child.icon, 'w-5 mr-3']"></i>
                  <span>{{ child.label }}</span>
                </NuxtLink>
              </template>
            </template>
          </template>

          <!-- Logout -->
          <div class="border-t border-blue-500 my-2"></div>
          <button @click="handleLogout"
            class="text-white px-4 py-3 rounded-lg bg-red-600 hover:bg-red-700 transition-all text-left w-full flex items-center shadow-lg">
            <i class="fas fa-sign-out-alt w-6 mr-3"></i>
            <span class="font-medium">ออกจากระบบ</span>
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useToast } from 'vue-toastification'

const toast = useToast()
const { showConfirm } = useConfirm()
const showMobileMenu = ref(false)
const userName = ref('Admin User')
const isDropdownOpen = ref({})

// Menu Data Structure
const menuItems = ref([
  {
    id: 'home',
    label: 'หน้าหลัก',
    icon: 'fas fa-home',
    to: '/main',
    type: 'link'
  },
  {
    id: 'dashboard',
    label: 'Dashboard',
    icon: 'fas fa-dashboard',
    to: '/admin',
    type: 'link'
  },
  {
    id: 'users',
    label: 'จัดการข้อมูลพิ้นฐาน',
    icon: 'fas fa-cog',
    type: 'dropdown',
    children: [
      {
        label: 'รอบการประเมิน',
        icon: 'fas fa-calendar-alt',
        iconColor: 'text-blue-600',
        to: '/admin/round_list'
      },
      {
        label: 'ผู้ถูกประเมิน',
        icon: 'fas fa-users',
        iconColor: 'text-blue-600',
        to: '/admin/user_list'
      },
      {
        label: 'ผู้ดูแลระบบ',
        icon: 'fas fa-user-shield',
        iconColor: 'text-blue-600',
        to: '/admin/admin_list'
      },
    ]
  },
  {
    id: 'surveys',
    label: 'แบบประเมิน',
    icon: 'fas fa-clipboard-list',
    type: 'link',
    to: '/admin/survey_list'
  },
  {
    id: 'reports',
    label: 'รายงานผล',
    icon: 'fas fa-chart-line',
    type: 'dropdown',
    children: [
      {
        label: 'รายงานผลการประเมิน',
        icon: 'fas fa-chart-pie',
        iconColor: 'text-blue-600',
        to: '/admin/report_list'
      },
      {
        label: 'ผู้ส่งผลประเมิน',
        icon: 'fas fa-users-viewfinder',
        iconColor: 'text-blue-600',
        to: '/admin/emp_list'
      },
      {
        label: 'ข้อมูลดิบการประเมิน',
        icon: 'fas fa-file-alt',
        iconColor: 'text-blue-600',
        to: '/admin/raw_data'
      }
    ]
  },
])

const openDropdown = (menuId) => {
  isDropdownOpen.value[menuId] = true
}

const closeDropdown = (menuId) => {
  isDropdownOpen.value[menuId] = false
}

const toggleDropdown = (menuId) => {
  isDropdownOpen.value[menuId] = !isDropdownOpen.value[menuId]
}

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value
}

const handleLogout = async () => {
  const confirmed = await showConfirm('ต้องการออกจากระบบหรือไม่?', 'ออกจากระบบ')
  if (confirmed) {
    if (process.client) {
      const names = ['auth_token','E_Code','E_LocalFirstName','E_LocalLastName','E_ImagePath','E_Level','E_Division','admin_permiss','adminlevel']
      names.forEach(n => document.cookie = n + '=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/')
    }
    toast.success('ออกจากระบบสำเร็จ')
    setTimeout(() => {
      navigateTo('/login')
    }, 500)
  }
}

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

const getUserNameFromCookie = () => {
  if (!process.client) return
  const firstName = getCookie('E_LocalFirstName') || ''
  const lastName = getCookie('E_LocalLastName') || ''
  userName.value = (firstName + ' ' + lastName).trim() || 'Admin User'
}

// Lifecycle
onMounted(() => {
  getUserNameFromCookie()
})
</script>

<style scoped>
/* Smooth dropdown animation */
@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.absolute {
  animation: fadeInDown 0.2s ease-out;
}

/* Ensure dropdowns appear above other content */
nav .relative {
  z-index: 40;
}

nav .absolute {
  z-index: 50;
}
</style>

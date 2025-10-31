<template>
  <div class="min-h-screen bg-gray-100">
    <AdminNavbar />
    <main class="py-6">
      <slot />
    </main>
    <footer class="bg-white border-t mt-8">
      <div class="container mx-auto px-4 py-4 text-center text-gray-600 text-sm">
        <p>© {{ currentYear }} FEEDBACK 360 System. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import AdminNavbar from '@/components/AdminNavbar.vue'
const currentYear = new Date().getFullYear()
</script>


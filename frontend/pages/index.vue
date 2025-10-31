<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
      <p class="text-gray-600">กำลังเปลี่ยนเส้นทาง...</p>
    </div>
  </div>
</template>

<script setup>
// Redirect to login page
await navigateTo('/login')
</script>

<template>
  <span :class="badgeClass" class="px-2 py-1 rounded-full text-xs font-medium">
    {{ displayText }}
  </span>
</template>

<script setup>
const props = defineProps({
  status: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'default' // default, permission, evaluation
  }
})

const badgeClass = computed(() => {
  if (props.type === 'permission') {
    return getPermissionClass(props.status)
  } else if (props.type === 'evaluation') {
    return getEvaluationClass(props.status)
  } else {
    return getDefaultClass(props.status)
  }
})

const displayText = computed(() => {
  if (props.type === 'permission') {
    return getPermissionText(props.status)
  } else if (props.type === 'evaluation') {
    return getEvaluationText(props.status)
  } else {
    return props.status
  }
})

const getPermissionClass = (status) => {
  switch (status) {
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

const getPermissionText = (status) => {
  switch (status) {
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

const getEvaluationClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'in_progress':
      return 'bg-yellow-100 text-yellow-800'
    case 'not_started':
      return 'bg-red-100 text-red-800'
    case 'fixed':
      return 'bg-orange-100 text-orange-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getEvaluationText = (status) => {
  switch (status) {
    case 'completed':
      return 'ประเมินเสร็จ'
    case 'in_progress':
      return 'กำลังประเมิน'
    case 'not_started':
      return 'ยังไม่เริ่ม'
    case 'fixed':
      return 'ต้องประเมิน'
    default:
      return 'ไม่ทราบ'
  }
}

const getDefaultClass = (status) => {
  switch (status.toLowerCase()) {
    case 'active':
    case 'success':
      return 'bg-green-100 text-green-800'
    case 'pending':
    case 'warning':
      return 'bg-yellow-100 text-yellow-800'
    case 'inactive':
    case 'error':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}
</script>

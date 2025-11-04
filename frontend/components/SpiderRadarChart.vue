<template>
  <div class="relative w-full" :style="{ minHeight: minHeight }">
    <canvas ref="canvasEl" class="w-full h-full"></canvas>
  </div>
</template>

<script setup>
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
  labels: { type: Array, default: () => [] },
  datasets: { type: Array, default: () => [] },
  options: { type: Object, default: () => ({}) },
  minHeight: { type: String, default: '320px' }
})

const canvasEl = ref(null)
let chartInstance = null

const buildChart = () => {
  if (!canvasEl.value) return
  const ctx = canvasEl.value.getContext('2d')
  if (chartInstance) chartInstance.destroy()
  chartInstance = new Chart(ctx, {
    type: 'radar',
    data: { labels: props.labels, datasets: props.datasets },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      ...props.options
    }
  })
}

watch(() => [props.labels, props.datasets, props.options], buildChart, { deep: true })

onMounted(buildChart)
onUnmounted(() => {
  if (chartInstance) chartInstance.destroy()
})
</script>


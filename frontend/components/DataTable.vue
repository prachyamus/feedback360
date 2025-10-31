<template>
  <div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div v-if="showSearch" class="p-4 border-b">
      <input
        v-model="searchKeyword"
        type="text"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        :placeholder="searchPlaceholder"
        @input="handleSearch"
      />
    </div>
    
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              :class="column.class"
            >
              {{ column.label }}
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="(item, index) in filteredData" :key="item.id || index" class="hover:bg-gray-50">
            <td
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-4 whitespace-nowrap text-sm"
              :class="column.class"
            >
              <slot :name="`cell-${column.key}`" :item="item" :value="item[column.key]">
                {{ item[column.key] }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showPagination" class="px-6 py-3 border-t">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
          แสดง {{ (currentPage - 1) * itemsPerPage + 1 }} ถึง {{ Math.min(currentPage * itemsPerPage, totalItems) }} จาก {{ totalItems }} รายการ
        </div>
        <div class="flex space-x-2">
          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
          >
            ก่อนหน้า
          </button>
          <span class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm">{{ currentPage }}</span>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
          >
            ถัดไป
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  data: {
    type: Array,
    default: () => []
  },
  columns: {
    type: Array,
    required: true
  },
  showSearch: {
    type: Boolean,
    default: false
  },
  showPagination: {
    type: Boolean,
    default: false
  },
  searchPlaceholder: {
    type: String,
    default: 'ค้นหา...'
  },
  itemsPerPage: {
    type: Number,
    default: 10
  }
})

const searchKeyword = ref('')
const currentPage = ref(1)

const filteredData = computed(() => {
  let filtered = props.data
  
  if (searchKeyword.value) {
    const keyword = searchKeyword.value.toLowerCase()
    filtered = filtered.filter(item => 
      Object.values(item).some(value => 
        String(value).toLowerCase().includes(keyword)
      )
    )
  }
  
  if (props.showPagination) {
    const start = (currentPage.value - 1) * props.itemsPerPage
    const end = start + props.itemsPerPage
    filtered = filtered.slice(start, end)
  }
  
  return filtered
})

const totalItems = computed(() => {
  let filtered = props.data
  if (searchKeyword.value) {
    const keyword = searchKeyword.value.toLowerCase()
    filtered = filtered.filter(item => 
      Object.values(item).some(value => 
        String(value).toLowerCase().includes(keyword)
      )
    )
  }
  return filtered.length
})

const totalPages = computed(() => Math.ceil(totalItems.value / props.itemsPerPage))

const handleSearch = () => {
  currentPage.value = 1
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}
</script>

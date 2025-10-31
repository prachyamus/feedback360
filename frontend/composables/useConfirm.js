export const useConfirm = () => {
  const showConfirm = (message, title = 'ยืนยันการดำเนินการ') => {
    return new Promise((resolve) => {
      const modalHtml = `
        <div id="confirm-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999] flex items-center justify-center">
          <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
              <div class="flex items-center justify-center w-12 h-12 mx-auto bg-yellow-100 rounded-full">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-medium text-gray-900 text-center mt-4">${title}</h3>
              <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center">${message}</p>
              </div>
              <div class="flex justify-center space-x-3 pt-4">
                <button id="confirm-cancel" class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                  ยกเลิก
                </button>
                <button id="confirm-ok" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                  ยืนยัน
                </button>
              </div>
            </div>
          </div>
        </div>
      `
      
      document.body.insertAdjacentHTML('beforeend', modalHtml)
      
      const modal = document.getElementById('confirm-modal')
      const cancelBtn = document.getElementById('confirm-cancel')
      const okBtn = document.getElementById('confirm-ok')
      
      const cleanup = () => {
        modal.remove()
      }
      
      cancelBtn.addEventListener('click', () => {
        cleanup()
        resolve(false)
      })
      
      okBtn.addEventListener('click', () => {
        cleanup()
        resolve(true)
      })
      
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          cleanup()
          resolve(false)
        }
      })
    })
  }

  return { showConfirm }
}


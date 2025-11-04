import { useToast } from 'vue-toastification'

export const useExport = () => {
  const toast = useToast()

  /**
   * Export data to CSV format
   * @param {Array} data - Array of objects to export
   * @param {String} filename - Output filename without extension
   */
  const exportToCSV = (data, filename = 'export') => {
    if (!data || data.length === 0) {
      toast.warning('ไม่มีข้อมูลสำหรับ Export')
      return
    }

    try {
      const headers = Object.keys(data[0])
      const csvContent = [
        headers.join(','),
        ...data.map(row => headers.map(header => `"${row[header] || ''}"`).join(','))
      ].join('\n')

      const BOM = '\uFEFF'
      const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' })
      const link = document.createElement('a')
      link.href = URL.createObjectURL(blob)
      link.download = `${filename}_${new Date().toISOString().slice(0, 10)}.csv`
      link.click()
      
      toast.success('Export CSV สำเร็จ')
    } catch (error) {
      console.error('CSV Export error:', error)
      toast.error('เกิดข้อผิดพลาดในการ Export CSV')
    }
  }

  /**
   * Export data to Excel format (.xlsx)
   * @param {Array} data - Array of objects to export
   * @param {String} filename - Output filename without extension
   * @param {String} sheetName - Excel sheet name (default: 'Sheet1')
   */
  const exportToExcel = async (data, filename = 'export', sheetName = 'Sheet1') => {
    if (!data || data.length === 0) {
      toast.warning('ไม่มีข้อมูลสำหรับ Export')
      return
    }

    try {
      const XLSX = await import('xlsx')
      
      const worksheet = XLSX.utils.json_to_sheet(data)
      const workbook = XLSX.utils.book_new()
      XLSX.utils.book_append_sheet(workbook, worksheet, sheetName)
      
      XLSX.writeFile(workbook, `${filename}_${new Date().toISOString().slice(0, 10)}.xlsx`)
      
      toast.success('Export Excel สำเร็จ')
    } catch (error) {
      console.error('Excel Export error:', error)
      toast.error('เกิดข้อผิดพลาดในการ Export Excel')
    }
  }

  /**
   * Export data to Excel with multiple sheets
   * @param {Array} sheets - Array of {name, data} objects
   * @param {String} filename - Output filename without extension
   */
  const exportToExcelMultiSheet = async (sheets, filename = 'export') => {
    if (!sheets || sheets.length === 0) {
      toast.warning('ไม่มีข้อมูลสำหรับ Export')
      return
    }

    try {
      const XLSX = await import('xlsx')
      const workbook = XLSX.utils.book_new()
      
      sheets.forEach(sheet => {
        if (sheet.data && sheet.data.length > 0) {
          const worksheet = XLSX.utils.json_to_sheet(sheet.data)
          XLSX.utils.book_append_sheet(workbook, worksheet, sheet.name || 'Sheet')
        }
      })
      
      XLSX.writeFile(workbook, `${filename}_${new Date().toISOString().slice(0, 10)}.xlsx`)
      
      toast.success('Export Excel สำเร็จ')
    } catch (error) {
      console.error('Excel Multi-sheet Export error:', error)
      toast.error('เกิดข้อผิดพลาดในการ Export Excel')
    }
  }

  return {
    exportToCSV,
    exportToExcel,
    exportToExcelMultiSheet
  }
}


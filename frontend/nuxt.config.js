export default defineNuxtConfig({
  devtools: { enabled: true },
  compatibilityDate: '2025-10-31',
  
  ssr: false,
  
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt'
  ],
  
  css: ['~/assets/css/main.css'],
  
  app: {
    baseURL: '/feedback360/',
    head: {
      title: 'FEEDBACK 360',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'FEEDBACK 360 - Employee Evaluation System' }
      ],
      link: []
    }
  },
  
  runtimeConfig: {
    public: {
      apiBaseUrl: 'http://localhost/feedback360/Backend',
      appName: 'FEEDBACK 360'
    }
  },
  
  devServer: {
    port: 3030,
    host: 'localhost'
  },
  
  nitro: {
    prerender: {
      crawlLinks: true,
      fallback: true
    }
  },
  
})

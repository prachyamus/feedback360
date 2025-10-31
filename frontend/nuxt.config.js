export default defineNuxtConfig({
  devtools: { enabled: true },
  
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt'
  ],
  
  css: ['~/assets/css/main.css'],
  
  app: {
    baseURL: '/360/',
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
      apiBaseUrl: 'http://localhost/360/Backend',
      appName: 'FEEDBACK 360'
    }
  },
  
  devServer: {
    port: 8000,
    host: 'localhost'
  },
  
})

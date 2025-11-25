import { createStore } from 'vuex'

export default defineNuxtPlugin((nuxtApp) => {
  const store = createStore({
    state: () => ({}),
    mutations: {},
    actions: {},
  })

  nuxtApp.vueApp.use(store)
  nuxtApp.provide('store', store)
})

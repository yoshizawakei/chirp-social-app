// middleware/auth.js

export default defineNuxtRouteMiddleware(async (to, from) => {
    const nuxtApp = useNuxtApp();
    const store = nuxtApp.vueApp.config.globalProperties.$store;

    const user = store && store.state.auth ? store.state.auth.user : null;

    if (!user) {
        if (to.path !== '/login' && to.path !== '/signup') {
            return navigateTo('/login');
        }
    }
})
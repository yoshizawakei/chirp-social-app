// middleware/auth.js
export default defineNuxtRouteMiddleware(async (to, from) => {
  if (to.path === '/login' || to.path === '/register') {
    return;
  }

  const nuxtApp = useNuxtApp();
  const store = nuxtApp.vueApp.config.globalProperties.$store;

  if (!store) {
    console.error('Store is not initialized in auth middleware.');
    return navigateTo('/login');
  }

  // ★ ここで必ず initAuth を一度待つ
  if (!store.getters['auth/isAuthChecked']) {
    try {
      await store.dispatch('auth/initAuth');
    } catch (e) {
      console.error('initAuth error in middleware:', e);
    }
  }

  const user = store.getters['auth/user'];

  if (!user) {
    console.log('Authentication failed, redirecting to login.');
    return navigateTo('/login');
  }

  console.log('Authentication successful. User ID:', user.uid);
});
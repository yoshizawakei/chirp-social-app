// middleware/auth.js
export default defineNuxtRouteMiddleware(async (to, from) => {
    if (to.path === '/login' || to.path === '/signup') {
        return;
    }

    const nuxtApp = useNuxtApp();
    const store = nuxtApp.vueApp.config.globalProperties.$store;

    if (!store) {
        console.error("Store is not initialized in auth middleware.");
        return navigateTo('/login');
    }

    if (!store.getters['auth/isAuthChecked']) {
        console.log("Middleware waiting for auth check...");

        await new Promise(resolve => {
            const unwatch = store.watch(
                (state, getters) => getters['auth/isAuthChecked'],
                (isAuthChecked) => {
                    if (isAuthChecked) {
                        unwatch();
                        resolve();
                    }
                },

                { immediate: store.getters['auth/isAuthChecked'] }
            );

            setTimeout(() => {
                if (!store.getters['auth/isAuthChecked']) {
                    unwatch();
                    resolve();
                }
            }, 1000); // 1秒待機
        });
    }


    const user = store.getters['auth/user'];

    if (!user) {
        console.log("Authentication failed, redirecting to login.");
        return navigateTo('/login');
    }

    console.log("Authentication successful. User ID:", user.uid);
});
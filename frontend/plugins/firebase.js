import { initializeApp } from "firebase/app";
import { getAuth } from "firebase/auth";

export default defineNuxtPlugin(() => {
    const config = useRuntimeConfig().public;

    const firebaseConfig = {
        apiKey: config.FIREBASE_API_KEY,
        authDomain: config.FIREBASE_AUTH_DOMAIN,
        projectId: config.FIREBASE_PROJECT_ID,
        appId: config.FIREBASE_APP_ID,
    };

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);

    return { provide: { auth } };
});

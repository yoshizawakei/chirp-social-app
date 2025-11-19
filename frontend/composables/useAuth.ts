import { useNuxtApp } from "#app";
import {
    type User,
    signInWithEmailAndPassword,
    createUserWithEmailAndPassword,
    updateProfile,
    onAuthStateChanged,
} from "firebase/auth";

export const useAuth = () => {
    const { $auth } = useNuxtApp();

    // 🔥 型を明示して正しくする（これでエラー解決）
    const user = useState<null | User>("fb_user", () => null);

    const init = () => {
        return new Promise((resolve) => {
        onAuthStateChanged($auth, (u) => {
            user.value = u;
            resolve(u);
        });
        });
    };

    const login = async (email: string, password: string) => {
        const result = await signInWithEmailAndPassword($auth, email, password);
        user.value = result.user;
    };

    const register = async (email: string, password: string, name: string) => {
        const result = await createUserWithEmailAndPassword($auth, email, password);
        await updateProfile(result.user, { displayName: name });
        user.value = result.user;
    };

    const logout = async () => {
        await $auth.signOut();
        user.value = null;
    };

    return {
        user,
        init,
        login,
        register,
        logout,
    };
};

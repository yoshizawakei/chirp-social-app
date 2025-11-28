import { useNuxtApp } from "#app";
import {
  type User,
  signInWithEmailAndPassword,
  createUserWithEmailAndPassword,
  updateProfile,
  onAuthStateChanged,
  signOut
} from "firebase/auth";

export const useAuth = () => {
  const { $auth } = useNuxtApp();

  // ユーザー情報（グローバルState）
  const user = useState<null | User>("fb_user", () => null);

  // Firebase 初期化
  const init = () => {
    return new Promise((resolve) => {
      onAuthStateChanged($auth, (u) => {
        user.value = u;
        resolve(u);
      });
    });
  };

  // ログイン
  const login = async (email: string, password: string) => {
    const result = await signInWithEmailAndPassword($auth, email, password);
    user.value = result.user;
  };

  // 新規登録
  const register = async (email: string, password: string, name: string) => {
    const result = await createUserWithEmailAndPassword($auth, email, password);
    await updateProfile(result.user, { displayName: name });
    user.value = result.user;
  };

  // ログアウト
  const logout = async () => {
    await signOut($auth);
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

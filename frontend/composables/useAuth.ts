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

  // -----------------------------------------
  // ログイン（バリデーション対応）
  // -----------------------------------------
  const login = async (email: string, password: string) => {
    // 入力必須チェック
    if (!email) {
      throw new Error("メールアドレスは必須です。");
    }
    if (!password) {
      throw new Error("パスワードは必須です。");
    }

    const result = await signInWithEmailAndPassword($auth, email, password);
    user.value = result.user;
  };

  // -----------------------------------------
  // 新規登録（バリデーション対応）
  // -----------------------------------------
  const register = async (email: string, password: string, name: string) => {
    // ▼ユーザーネーム
    if (!name) {
      throw new Error("ユーザー名は必須です。");
    }
    if (name.length > 20) {
      throw new Error("ユーザー名は20文字以内で入力してください。");
    }

    // ▼メールアドレス
    if (!email) {
      throw new Error("メールアドレスは必須です。");
    }
    const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailReg.test(email)) {
      throw new Error("メールアドレス形式が正しくありません。");
    }

    // ▼パスワード
    if (!password) {
      throw new Error("パスワードは必須です。");
    }
    if (password.length < 6) {
      throw new Error("パスワードは6文字以上で入力してください。");
    }

    // Firebase 登録処理
    const result = await createUserWithEmailAndPassword($auth, email, password);

    // displayName にユーザー名を保存
    await updateProfile(result.user, { displayName: name });

    user.value = result.user;
  };

  // -----------------------------------------
  // ログアウト
  // -----------------------------------------
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

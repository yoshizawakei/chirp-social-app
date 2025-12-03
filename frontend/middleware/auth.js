// middleware/auth.global.ts
import { useAuth } from "~/composables/useAuth";

export default defineNuxtRouteMiddleware(async (to, from) => {
  // 認証不要のページ
  const publicPages = ["/login", "/register"];

  // もし public page に移動するならチェック不要
  if (publicPages.includes(to.path)) {
    return;
  }

  // Firebase Auth 状態の初期化
  const { user, init } = useAuth();
  await init(); // Firebase のログイン状態を同期

  // 未ログイン → /register へ強制リダイレクト
  if (!user.value) {
    return navigateTo("/register");
  }

  // ログイン済み → 何もしないで続行
});

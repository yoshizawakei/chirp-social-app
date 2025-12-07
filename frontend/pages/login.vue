<template>
  <div class="login-page">
    <!-- 左上ロゴ -->
    <img src="/assets/images/logo.png" alt="SHARE" class="logo" />

    <!-- 右上メニュー -->
    <div class="top-menu">
      <NuxtLink to="/register" class="menu-link">新規登録</NuxtLink>
      <NuxtLink to="/login" class="menu-link">ログイン</NuxtLink>
    </div>

    <!-- 中央カード -->
    <div class="card">
      <h2 class="card-title">ログイン</h2>

      <!-- エラーメッセージ -->
      <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>

      <form @submit.prevent="handleLogin" class="form">
        <input
          v-model="email"
          type="email"
          placeholder="メールアドレス"
          class="input"
        />
        <input
          v-model="password"
          type="password"
          placeholder="パスワード"
          class="input"
        />

        <div class="button-wrap">
          <button type="submit" class="btn-login">ログイン</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from "~/composables/useAuth";

definePageMeta({
  layout: false,
});

const { login } = useAuth();

const email = ref("");
const password = ref("");
const errorMessage = ref("");

const handleLogin = async () => {
  errorMessage.value = "";

  // -----------------------------
  // フロント側バリデーション
  // -----------------------------
  if (!email.value) {
    errorMessage.value = "メールアドレスは必須です。";
    return;
  }
  if (!password.value) {
    errorMessage.value = "パスワードは必須です。";
    return;
  }

  // -----------------------------
  // useAuth.login() 実行
  // -----------------------------
  try {
    await login(email.value, password.value);
    navigateTo("/");
  } catch (err) {
    errorMessage.value = err.message || "ログインに失敗しました。";
  }
};
</script>

<style scoped>
.error-message {
  color: #e53935;
  font-size: 14px;
  margin-bottom: 12px;
  text-align: center;
}

.login-page {
  min-height: 100vh;
  background-color: #0f1923;
  color: #ffffff;
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, "Helvetica Neue",
    Arial, "ヒラギノ角ゴ ProN", "Hiragino Kaku Gothic ProN", "メイリオ",
    Meiryo, sans-serif;
}

.logo {
  position: absolute;
  top: 24px;
  left: 40px;
  height: 40px;
}

.top-menu {
  position: absolute;
  top: 24px;
  right: 40px;
  display: flex;
  gap: 24px;
  font-size: 14px;
}

.menu-link {
  color: #ffffff;
  text-decoration: none;
}
.menu-link:hover {
  text-decoration: underline;
}

.card {
  width: 420px;
  background-color: #ffffff;
  color: #000000;
  border-radius: 6px;
  padding: 32px 40px 36px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
}

.card-title {
  text-align: center;
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 24px;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.input {
  padding: 10px 14px;
  border-radius: 12px;
  border: 1px solid #bdbdbd;
  font-size: 14px;
  outline: none;
}
.input:focus {
  border-color: #7c4dff;
}

.button-wrap {
  margin-top: 12px;
  text-align: center;
}

.btn-login {
  padding: 8px 32px;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  font-size: 14px;
  color: #ffffff;
  background: linear-gradient(90deg, #7c4dff, #4c6fff);
  box-shadow: 0 4px 0 #3b3b3b;
}
.btn-login:hover {
  opacity: 0.9;
}
</style>

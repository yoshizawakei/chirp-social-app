<template>
  <div class="min-h-screen bg-[#0F1923] text-white relative">

    <!-- 右上のメニュー -->
    <div class="absolute top-6 right-8 flex gap-6 text-white text-sm">
      <NuxtLink to="/register" class="hover:opacity-70">新規登録</NuxtLink>
      <NuxtLink to="/login" class="hover:opacity-70">ログイン</NuxtLink>
    </div>

    <!-- ロゴ -->
    <img
      src="/assets/images/logo.png"
      alt="SHARE"
      class="absolute top-6 left-6 w-40"
    />

    <!-- カード中央配置 -->
    <div class="flex items-center justify-center min-h-screen">
      <div class="bg-white text-black w-[420px] rounded-lg shadow-md p-8">

        <h2 class="text-center text-xl font-bold mb-6">新規登録</h2>

        <!-- フォーム -->
        <form @submit.prevent="handleRegister" class="flex flex-col gap-4">

          <input
            v-model="name"
            type="text"
            placeholder="ユーザー ネーム"
            class="border border-gray-400 w-full p-2 rounded-lg"
          />

          <input
            v-model="email"
            type="email"
            placeholder="メールアドレス"
            class="border border-gray-400 w-full p-2 rounded-lg"
          />

          <input
            v-model="password"
            type="password"
            placeholder="パスワード"
            class="border border-gray-400 w-full p-2 rounded-lg"
          />

          <div class="flex justify-center mt-2">
            <button
              type="submit"
              class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white px-6 py-2 rounded-full hover:opacity-90"
            >
              新規登録
            </button>
          </div>

        </form>

      </div>
    </div>

  </div>
</template>

<script setup>
import { useAuth } from "~/composables/useAuth";
const { register } = useAuth();

const name = ref("");
const email = ref("");
const password = ref("");

const handleRegister = async () => {
  if (!name.value || !email.value || !password.value) return;

  await register(email.value, password.value, name.value);

  navigateTo("/");
};
</script>

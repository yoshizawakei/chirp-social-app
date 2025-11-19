<template>
  <div class="min-h-screen bg-[#0F1923] text-white flex">

    <!-- 左サイドバー -->
    <aside class="w-64 p-6 border-r border-gray-600 flex flex-col">
      
      <!-- ロゴ -->
      <h1 class="text-4xl font-black mb-8 tracking-wide">SHARE</h1>

      <!-- メニュー -->
      <nav class="flex flex-col gap-4 mb-10 text-lg">

        <NuxtLink to="/" class="flex items-center gap-3 hover:opacity-80">
          <span class="text-2xl">🏠</span>
          <span>ホーム</span>
        </NuxtLink>

        <button
          @click="logout"
          class="flex items-center gap-3 hover:opacity-80 text-left"
        >
          <span class="text-2xl">🚪</span>
          <span>ログアウト</span>
        </button>

      </nav>

      <!-- シェア（投稿フォーム） -->
      <section class="mt-auto">
        <h2 class="mb-2">シェア</h2>

        <textarea
          v-model="shareText"
          class="w-full bg-transparent border border-gray-500 rounded p-2 h-32 resize-none"
        ></textarea>

        <div class="mt-3 flex justify-center">
          <button
            @click="submitShare"
            class="px-6 py-2 rounded-full bg-gradient-to-r from-purple-500 to-indigo-500 hover:opacity-90"
          >
            シェアする
          </button>
        </div>
      </section>
    </aside>

    <!-- 右側のページコンテンツ -->
    <main class="flex-1 p-8">
      <slot />
    </main>

  </div>
</template>

<script setup>
import { useAuth } from "~/composables/useAuth";

const shareText = ref("");
const { user, logout } = useAuth();

const submitShare = async () => {
  if (!shareText.value.trim()) return;

  const token = await user.value.getIdToken();

  await $fetch("http://localhost/api/posts", {
    method: "POST",
    body: { message: shareText.value },
    headers: { Authorization: `Bearer ${token}` },
  });

  shareText.value = "";
};
</script>

<!-- 
<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
  background-color: #1a1a2e;
  color: white;
}
.sidebar {
  width: 250px;
  background-color: #1a1a2e;
  padding: 20px;
  border-right: 1px solid #33334d;
  position: fixed;
  height: 100%;
  display: flex;
  flex-direction: column;
  z-index: 10;
}
.logo {
  font-size: 24px;
  color: white;
  margin-bottom: 30px;
  font-weight: 800;
}
.navigation {
  display: flex;
  flex-direction: column;
  margin-bottom: 40px;
}
.sidebar-link {
  display: flex;
  align-items: center;
  padding: 12px 10px;
  margin-bottom: 5px;
  color: #b0b0b0;
  font-size: 16px;
  font-weight: 600;
  border-radius: 4px;
  transition: background-color 0.2s, color 0.2s;
  text-decoration: none;
  background: none;
  border: none;
  text-align: left;
  cursor: pointer;
}
.sidebar-link:hover,
.sidebar-link.router-link-active {
  background-color: #24243e;
  color: white;
}
.logout-btn {
  width: 100%;
}
.icon-img {
  width: 24px;
  height: 24px;
  margin-right: 10px;
}
.share-section {
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #33334d;
}
.share-title {
  font-size: 16px;
  color: white;
  margin-bottom: 10px;
  text-align: left;
  font-weight: 600;
}
.share-input {
  width: 100%;
  min-height: 100px;
  background-color: #0d0d18;
  border: 1px solid #44445c;
  color: #e4e4e4;
  padding: 10px;
  border-radius: 4px;
  resize: none;
  box-sizing: border-box;
  margin-bottom: 5px;
}
.char-count {
  font-size: 12px;
  color: #aaa;
  text-align: right;
  margin-bottom: 10px;
}
.share-button {
  width: 100%;
  padding: 10px;
  background-color: #6a40e7;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s;
}
.share-button:disabled {
  background-color: #444;
  cursor: not-allowed;
  opacity: 1;
}
.share-button:hover:not(:disabled) {
  background-color: #5b34d9;
}
.main-content {
  flex-grow: 1;
  margin-left: 300px;
  padding: 20px;
  max-width: 800px;
  width: calc(100% - 250px);
}
</style> -->

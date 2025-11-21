<template>
  <div class="layout">

    <!-- 左サイドバー -->
    <aside class="sidebar">
      <img src="/assets/images/logo.png" alt="SHARE" class="logo" />

      <div class="menu">
        <NuxtLink to="/" class="menu-item">
          <img src="/assets/images/home.png" class="icon" /> ホーム
        </NuxtLink>

        <NuxtLink to="/logout" class="menu-item">
          <img src="/assets/images/logout.png" class="icon" /> ログアウト
        </NuxtLink>
      </div>

      <h3 class="share-title">シェア</h3>

      <textarea
        v-model="shareText"
        class="share-box"
        placeholder="今の気持ちを書こう…"
      />

      <button class="share-btn" @click="sharePost">シェアする</button>
    </aside>

    <!-- メイン表示領域 -->
    <main class="content">
      <slot />
    </main>

  </div>
</template>

<script setup>
import { useAuth } from "~/composables/useAuth";
const { user, init } = useAuth();
const shareText = ref("");

onMounted(() => init());

const config = useRuntimeConfig().public;

const sharePost = async () => {
  if (!shareText.value.trim()) return;

  const token = await user.value.getIdToken();

  await $fetch(`${config.API_URL}/posts`, {
    method: "POST",
    headers: { Authorization: `Bearer ${token}` },
    body: {
      message: shareText.value,
      username: user.value.displayName || "名無し",
    },
  });

  shareText.value = "";
  navigateTo("/", { replace: true });
};
</script>

<style scoped>
.layout {
  display: flex;
  min-height: 100vh;
  background: #0f1923;
  color: white;
  font-family: system-ui, sans-serif;
}

/* サイドバー */
.sidebar {
  width: 260px;
  padding: 24px;
  border-right: 1px solid #555;
}

.logo {
  width: 140px;
  margin-bottom: 40px;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: white;
  font-size: 16px;
  margin-bottom: 20px;
  text-decoration: none;
}

.icon {
  width: 22px;
  height: 22px;
}

.share-title {
  margin-top: 24px;
  margin-bottom: 8px;
  font-size: 16px;
}

.share-box {
  width: 100%;
  height: 140px;
  border-radius: 12px;
  padding: 12px;
  border: none;
  outline: none;
  background: white;
  color: black;
  resize: none;
}

.share-btn {
  margin-top: 12px;
  width: 80%;
  background: #6d28d9;
  color: white;
  padding: 8px 12px;
  border-radius: 20px;
  border: none;
  cursor: pointer;
  display: block;
}
.share-btn:hover {
  opacity: 0.9;
}

/* メイン側 */
.content {
  flex: 1;
  padding: 32px;
}
</style>

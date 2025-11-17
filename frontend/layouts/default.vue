<template>
  <div class="app-layout">
    <aside class="sidebar">
      <h1 class="logo">SHARE</h1>

      <nav class="navigation">
        <NuxtLink to="/" class="sidebar-link">
          <img :src="homeIcon" alt="ホーム" class="icon-img" />ホーム
        </NuxtLink>
        <NuxtLink to="/profile" class="sidebar-link">
          <img :src="profileIcon" alt="プロフィール" class="icon-img" />プロフィール
        </NuxtLink>
        <button @click="logout" class="sidebar-link logout-btn">
          <img :src="logoutIcon" alt="ログアウト" class="icon-img" />ログアウト
        </button>
      </nav>

      <div class="share-section">
        <h3 class="share-title">シェア</h3>

        <textarea
          v-model="message"
          placeholder="今どうしてる？"
          class="share-input"
          :disabled="isPosting"
          maxlength="120"
        ></textarea>

        <p class="char-count">{{ message.length }} / 120</p>

        <button
          class="share-button"
          @click="handleShareClick"
          :disabled="!message.trim() || isPosting || message.length > 120"
        >
          {{ isPosting ? "投稿中..." : "シェアする" }}
        </button>
      </div>
    </aside>

    <main class="main-content">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { useNuxtApp, navigateTo } from "#app";
import { ref } from "vue";
import homeIcon from "~/assets/images/home.png";
import profileIcon from "~/assets/images/profile.png";
import logoutIcon from "~/assets/images/logout.png";

const nuxtApp = useNuxtApp();
const store = nuxtApp.vueApp.config.globalProperties.$store;

const message = ref("");
const isPosting = ref(false);

const logout = async () => {
  if (!confirm("ログアウトしますか？")) return;

  try {
    await store.dispatch("auth/logoutAction");
    await navigateTo("/login");
  } catch (e) {
    console.error("ログアウトエラー:", e);
    alert("ログアウトに失敗しました。");
  }
};

const handleShareClick = async () => {
  const text = message.value.trim();

  // バリデーション
  if (!text || text.length > 120) {
    alert("メッセージを入力するか、文字数を調整してください (120文字以内)。");
    return;
  }

  const user = store.getters["auth/user"];
  if (!user) {
    alert("ログイン状態が確認できません。再度ログインしてください。");
    return;
  }

  isPosting.value = true;

  try {
    await store.dispatch("posts/addPostAction", {
      message: text,
      userId: user.uid,
      userEmail: user.email,
    });

    // ✅ 投稿成功後に入力欄をクリア
    message.value = "";
  } catch (e) {
    console.error("投稿エラー:", e);
    alert("投稿エラー: " + (e.message || "不明なエラーです"));
  } finally {
    // ✅ 必ず「投稿中…」を解除
    isPosting.value = false;
  }
};
</script>

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
</style>

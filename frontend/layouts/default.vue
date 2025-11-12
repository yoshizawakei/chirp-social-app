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
                ></textarea>
                <button 
                    class="share-button" 
                    @click="handleShareClick"
                    :disabled="!message.trim() || isPosting"
                >
                    {{ isPosting ? '投稿中...' : 'シェアする' }}
                </button>
            </div>
        </aside>

        <main class="main-content">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { useNuxtApp, navigateTo } from '#app';
import { ref, computed } from 'vue';
import homeIcon from '~/assets/images/home.png';
import profileIcon from '~/assets/images/profile.png';
import logoutIcon from '~/assets/images/logout.png';

const nuxtApp = useNuxtApp();
const store = nuxtApp.vueApp.config.globalProperties.$store;
const message = ref('');
const isPosting = ref(false);

const isAuthChecked = computed(() => store.getters['auth/isAuthChecked']);
const isLoggedIn = computed(() => store.getters['auth/isLoggedIn']);

// --- ロジック関数 ---

const logout = async () => {
    try {
        await store.dispatch('auth/logoutAction');
        await navigateTo('/login');
    } catch (e) {
        console.error("ログアウトエラー:", e);
    }
}

const handleShareClick = async () => {
    // 1. 投稿メッセージのチェック (早期リターン)
    if (message.value.trim() === '') {
        alert('メッセージを入力してください。');
        return;
    }

    // 2. 認証チェック完了を待つ処理 (最も重要な部分)
    let checkTimeout = 0;
    while (!store.getters['auth/isAuthChecked'] && checkTimeout < 20) {
        await new Promise(resolve => setTimeout(resolve, 50));
        checkTimeout++;
    }

    // 3. 認証状態の最終確認
    const user = store.getters['auth/user'];
    console.log("POST_CHECK: User object status after waiting:", user ? user.uid : 'null/undefined');

    if (!user) {
        alert('投稿に失敗しました: ログイン状態が確認できませんでした。再度ログインしてください。');
        return; 
    }
    
    isPosting.value = true;

    try {
        await store.dispatch('posts/addPostAction', { 
            message: message.value,
            user: user // 認証済みで確認されたユーザー情報を直接渡す
        });
        message.value = '';
    } catch (e) {
        alert('投稿に失敗しました: ' + (e.message || '予期せぬエラー'));
        console.error("投稿エラー:", e);
    } finally {
        isPosting.value = false;
    }
}
</script>

<style scoped>
.app-layout {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 250px;
    background-color: #24243e;
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
    color: #e4e4e4;
    font-size: 16px;
    font-weight: 600;
    border-radius: 4px;
    background: none;
    border: none;
    text-align: left;
    cursor: pointer;
    text-decoration: none;
    transition: background-color 0.2s;
}

.sidebar-link:hover, .sidebar-link.router-link-active {
    background-color: #33334d;
}

.logout-btn {
    width: 100%;
}

.icon-img {
    width: 24px;
    height: 24px;
    margin-right: 10px;
}

.share-title {
    font-size: 16px;
    color: #aaa;
    margin-bottom: 10px;
    text-align: left;
}

.share-input {
    width: 100%;
    min-height: 100px;
    background-color: #1a1a2e;
    border: 1px solid #33334d;
    color: #e4e4e4;
    padding: 10px;
    border-radius: 4px;
    resize: none;
    box-sizing: border-box;
    margin-bottom: 15px;
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
}

.share-button:hover:not(:disabled) {
    background-color: #5b34d9;
}

.main-content {
    flex-grow: 1;
    margin-left: 300px;
    padding: 20px;
    max-width: 800px;
    width: calc(100% - 300px);
}
</style>
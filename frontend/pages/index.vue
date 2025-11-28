<template>
    <div>
        <h1 class="title">ホーム</h1>

        <div v-for="post in posts" :key="post.id" class="post-item">

            <!-- 上段：ユーザー名 & アイコン -->
            <div class="post-header">
                <span class="username">{{ post.username }}</span>

                <div class="icons">

                    <!-- ❤️ -->
                    <button class="icon-btn" @click="toggleLike(post.id)">
                        <img src="/assets/images/heart.png" class="icon-img" />
                        <span>{{ post.likeCount }}</span>
                    </button>

                    <!-- ❌ 削除（モーダルを開く） -->
                    <button
                        v-if="post.userId === currentUserUid"
                        @click="openDeleteModal(post.id)"
                        class="icon-btn"
                    >
                        <img src="/assets/images/cross.png" class="icon-img" />
                    </button>

                    <!-- ↪ 詳細 -->
                    <NuxtLink :to="`/posts/${post.id}`" class="icon-btn">
                        <img src="/assets/images/detail.png" class="icon-img" />
                    </NuxtLink>

                </div>
            </div>

            <!-- メッセージ -->
            <div class="message">{{ post.message }}</div>

            <div class="divider"></div>
        </div>
    </div>

    <!-- ✔ 削除確認モーダル -->
    <ConfirmModal
        :visible="showConfirm"
        message="この投稿を削除しますか？"
        @confirm="confirmDelete"
        @cancel="showConfirm = false"
    />
</template>

<script setup>
import { useAuth } from "~/composables/useAuth";

const { user, init } = useAuth();
onMounted(() => init());

const config = useRuntimeConfig().public;

const posts = ref([]);

// ログイン UID
const currentUserUid = computed(() => user.value?.uid ?? null);

// 初期読み込み
onMounted(async () => {
    posts.value = await $fetch(`${config.API_URL}/posts`);

    posts.value = posts.value.map((p) => ({
        ...p,
        liked: p.likes.includes(currentUserUid.value),
    }));
});

// いいね
const toggleLike = async (id) => {
    const token = await user.value.getIdToken();

    const index = posts.value.findIndex((p) => p.id === id);
    if (index === -1) return;

    const res = await $fetch(`${config.API_URL}/posts/${id}/like`, {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
        body: { userId: user.value.uid },
    });

    posts.value[index].liked = res.liked;
    posts.value[index].likeCount = res.likeCount;
};


// --------------------------
//     削除モーダル部分
// --------------------------
const showConfirm = ref(false);
const deleteTargetId = ref(null);

const openDeleteModal = (id) => {
    deleteTargetId.value = id;
    showConfirm.value = true;
};

const confirmDelete = async () => {
    const token = await user.value.getIdToken();

    await $fetch(`${config.API_URL}/posts/${deleteTargetId.value}`, {
        method: "DELETE",
        headers: { Authorization: `Bearer ${token}` },
        body: { userId: user.value.uid }
    });

    posts.value = posts.value.filter((p) => p.id !== deleteTargetId.value);
    showConfirm.value = false;
};
</script>

<style scoped>
.title {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 20px;
}

.post-item {
    margin-bottom: 16px;
}

.post-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.username {
    font-size: 16px;
    font-weight: bold;
}

.icons {
    display: flex;
    gap: 16px;
    align-items: center;
}

.icon-btn {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    gap: 4px;
    align-items: center;
    color: white;
}

.icon-img {
    width: 22px;
    height: 22px;
}

.message {
    color: #ddd;
    margin-top: 4px;
}

.divider {
    margin-top: 16px;
    border-bottom: 1px solid #555;
}
</style>

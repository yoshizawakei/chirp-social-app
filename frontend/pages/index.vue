<template>
    <div class="text-white">

        <!-- タイトル -->
        <h1 class="text-2xl font-bold mb-4">ホーム</h1>

        <!-- 仕切り線 -->
        <div class="border-t border-gray-500 mb-6"></div>

        <!-- 投稿一覧 -->
        <div v-if="loading" class="text-center py-6 text-gray-400">
        読み込み中...
        </div>

        <div v-else>
        <div
            v-for="post in posts"
            :key="post.id"
            class="py-4 border-b border-gray-500"
        >

            <!-- 上段：ユーザ名 + アイコン群 -->
            <div class="flex justify-between items-center mb-1">

            <!-- ユーザ名 -->
            <div class="text-lg font-bold">{{ post.username }}</div>

            <!-- アイコン群 -->
            <div class="flex items-center gap-4">

                <!-- いいね -->
                <button @click="toggleLike(post)" class="hover:opacity-60 flex items-center gap-1">
                <img src="/assets/images/heart.png" class="w-6 h-6" />
                <span class="text-sm">{{ post.likeCount }}</span>
                </button>

                <!-- 削除（自分の投稿のみ） -->
                <button
                v-if="post.userId === currentUserUid"
                @click="deletePost(post.id)"
                class="hover:opacity-60"
                >
                <img src="/assets/images/cross.png" class="w-6 h-6" />
                </button>

                <!-- 詳細画面へ -->
                <NuxtLink
                :to="`/posts/${post.id}`"
                class="hover:opacity-60"
                >
                <img src="/assets/images/detail.png" class="w-6 h-6" />
                </NuxtLink>

            </div>
            </div>

            <!-- 本文 -->
            <div class="whitespace-pre-wrap text-base text-gray-100">
            {{ post.message }}
            </div>

        </div>
        </div>

    </div>
</template>

<script setup>
import { useAuth } from "~/composables/useAuth";
const config = useRuntimeConfig().public;

const posts = ref([]);
const loading = ref(true);

const { user, init } = useAuth();

// 初期ロード
onMounted(async () => {
    await init();
    await fetchPosts();
});

const currentUserUid = computed(() => user.value?.uid ?? null);

// 投稿取得
const fetchPosts = async () => {
    loading.value = true;
    posts.value = await $fetch(`${config.API_URL}/posts`);
    loading.value = false;
};

// いいね処理
const toggleLike = async (post) => {
    const token = await user.value.getIdToken();
    await $fetch(`${config.API_URL}/posts/${post.id}/like`, {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
    });
    await fetchPosts();
};

// 削除処理
const deletePost = async (id) => {
    const token = await user.value.getIdToken();
    await $fetch(`${config.API_URL}/posts/${id}`, {
        method: "DELETE",
        headers: { Authorization: `Bearer ${token}` },
    });
    await fetchPosts();
};
</script>

<style scoped>
</style>


<template>
  <div class="text-white">

    <!-- タイトル -->
    <h1 class="text-2xl font-bold mb-4">コメント</h1>
    <div class="border-t border-gray-500 mb-6"></div>

    <!-- 投稿ブロック -->
    <div class="border-b border-gray-500 pb-4 mb-6">

      <!-- 上段：ユーザ名 + アイコン -->
      <div class="flex justify-between items-center mb-1">

        <!-- ユーザ名 -->
        <div class="text-lg font-bold">
          {{ post?.username }}
        </div>

        <!-- アイコン群 -->
        <div class="flex items-center gap-4">

          <!-- いいね -->
          <button @click="toggleLike" class="hover:opacity-60 flex items-center gap-1">
            <img src="/assets/images/heart.png" class="w-6 h-6" />
            <span class="text-sm">{{ post?.likeCount }}</span>
          </button>

          <!-- 削除 -->
          <button
            v-if="post?.userId === currentUserUid"
            @click="deletePost"
            class="hover:opacity-60"
          >
            <img src="/assets/images/cross.png" class="w-6 h-6" />
          </button>

          <!-- 戻る（詳細→一覧） -->
          <NuxtLink to="/" class="hover:opacity-60">
            <img src="/assets/images/detail.png" class="w-6 h-6 rotate-180" />
          </NuxtLink>

        </div>
      </div>

      <!-- メッセージ -->
      <div class="whitespace-pre-wrap text-gray-100 text-base">
        {{ post?.message }}
      </div>

    </div>

    <!-- コメント一覧 -->
    <div class="mb-4 border-b border-gray-500 pb-2 text-lg font-bold">
      コメント
    </div>

    <div v-if="loadingComments" class="text-gray-400">読み込み中...</div>

    <div v-else>
      <div
        v-for="c in comments"
        :key="c.id"
        class="py-3 border-b border-gray-500"
      >
        <div class="font-bold mb-1">{{ c.username }}</div>
        <div class="text-gray-100">{{ c.text }}</div>
      </div>
    </div>

    <!-- コメント投稿フォーム -->
    <form @submit.prevent="submitComment" class="mt-6 flex items-center gap-3">

      <input
        v-model="commentText"
        type="text"
        placeholder="コメントを書く..."
        class="flex-1 bg-white text-black rounded-full px-4 py-2"
      />

      <button
        class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white px-6 py-2 rounded-full hover:opacity-90"
      >
        コメント
      </button>

    </form>

  </div>
</template>

<script setup>
import { useAuth } from "~/composables/useAuth";

const route = useRoute();
const config = useRuntimeConfig().public;

const { user, init } = useAuth();

const post = ref(null);
const comments = ref([]);
const loadingComments = ref(true);
const commentText = ref("");

// 初期ロード
onMounted(async () => {
  await init();
  await fetchPost();
  await fetchComments();
});

const currentUserUid = computed(() => user.value?.uid ?? null);

// 投稿詳細取得
const fetchPost = async () => {
  post.value = await $fetch(`${config.API_URL}/posts/${route.params.id}`);
};

// コメント一覧取得
const fetchComments = async () => {
  loadingComments.value = true;
  comments.value = await $fetch(
    `${config.API_URL}/posts/${route.params.id}/comments`
  );
  loadingComments.value = false;
};

// いいね
const toggleLike = async () => {
  const token = await user.value.getIdToken();
  await $fetch(`${config.API_URL}/posts/${route.params.id}/like`, {
    method: "POST",
    headers: { Authorization: `Bearer ${token}` },
  });
  await fetchPost();
};

// コメント投稿
const submitComment = async () => {
  if (!commentText.value.trim()) return;

  const token = await user.value.getIdToken();

  await $fetch(`${config.API_URL}/posts/${route.params.id}/comments`, {
    method: "POST",
    body: { text: commentText.value },
    headers: { Authorization: `Bearer ${token}` },
  });

  commentText.value = "";
  await fetchComments();
};

// 投稿削除
const deletePost = async () => {
  const token = await user.value.getIdToken();
  await $fetch(`${config.API_URL}/posts/${route.params.id}`, {
    method: "DELETE",
    headers: { Authorization: `Bearer ${token}` },
  });
  navigateTo("/");
};
</script>

<style scoped>
</style>

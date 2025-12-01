<template>
    <div>
        <h1 class="title">ホーム</h1>

        <!-- 投稿一覧 -->
        <Message
        v-for="post in posts"
        :key="post.id"
        :post="post"
        :currentUserUid="currentUserUid"
        @like="toggleLike"
        @delete="openDeleteModal"
        />

        <!-- 削除モーダル -->
        <ConfirmModal
        :visible="showConfirm"
        message="この投稿を削除しますか？"
        @confirm="confirmDelete"
        @cancel="showConfirm = false"
        />
    </div>
</template>

<script setup>
import Message from "~/components/Message.vue";
import { useAuth } from "~/composables/useAuth";

const { user, init } = useAuth();
onMounted(() => init());

const config = useRuntimeConfig().public;

const posts = ref([]);
const currentUserUid = computed(() => user.value?.uid ?? null);

const showConfirm = ref(false);
const deleteTargetId = ref(null);

// 初期ロード
onMounted(async () => {
  posts.value = await $fetch(`${config.API_URL}/posts`);

  // いいね状態の整形
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

// 削除モーダル
const openDeleteModal = (id) => {
  deleteTargetId.value = id;
  showConfirm.value = true;
};

const confirmDelete = async () => {
  const token = await user.value.getIdToken();

  await $fetch(`${config.API_URL}/posts/${deleteTargetId.value}`, {
    method: "DELETE",
    headers: { Authorization: `Bearer ${token}` },
    body: { userId: user.value.uid },
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
</style>

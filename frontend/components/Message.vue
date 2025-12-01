<template>
  <div class="post-item">

    <div class="post-header">
      <!-- ユーザー名 -->
      <span class="username">{{ post.username }}</span>

      <div class="icons">

        <!-- ❤️ いいね -->
        <button class="icon-btn" @click="$emit('like', post.id)">
          <img src="/assets/images/heart.png" class="icon-img" />
          <span>{{ post.likeCount }}</span>
        </button>

        <!-- ❌ 削除（自分の投稿のみ） -->
        <button
          v-if="post.userId === currentUserUid"
          class="icon-btn"
          @click="$emit('delete', post.id)"
        >
          <img src="/assets/images/cross.png" class="icon-img" />
        </button>

        <!-- ↪ 詳細 -->
        <NuxtLink :to="`/posts/${post.id}`" class="icon-btn">
          <img src="/assets/images/detail.png" class="icon-img" />
        </NuxtLink>

      </div>
    </div>

    <!-- メッセージ本文 -->
    <div class="message">{{ post.message }}</div>

    <div class="divider"></div>
  </div>
</template>

<script setup>
const props = defineProps({
  post: { type: Object, required: true },
  currentUserUid: { type: String, required: false },
});

defineEmits(["like", "delete"]);
</script>

<style scoped>
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

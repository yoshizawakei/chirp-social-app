<template>
  <div v-if="visible" class="modal-overlay">
    <div class="modal-box">

      <!-- メッセージ -->
      <p class="modal-message">{{ message }}</p>

      <!-- 編集モード：入力欄を表示 -->
      <div v-if="isEditMode" class="input-wrapper">
        <input
          type="text"
          v-model="inputValue"
          class="edit-input"
          placeholder="新しい内容を入力"
        />
      </div>

      <!-- ボタン -->
      <div class="button-group">
        <button class="cancel-btn" @click="$emit('cancel')">キャンセル</button>

        <button class="ok-btn" @click="handleConfirm">
          {{ isEditMode ? "更新する" : "削除する" }}
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  visible: Boolean,
  message: String,
  modelValue: String,
  type: {
    type: String,
    default: "delete", // delete / edit
  }
});

const emit = defineEmits(["update:modelValue", "confirm", "cancel"]);

const isEditMode = computed(() => props.type === "edit");

// 編集用入力
const inputValue = ref(props.modelValue || "");

// v-model 対応
watch(
  () => props.modelValue,
  (v) => {
    inputValue.value = v;
  }
);

const handleConfirm = () => {
  if (isEditMode.value) {
    emit("update:modelValue", inputValue.value);
  }
  emit("confirm");
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-box {
  background: #1e293b;
  padding: 22px;
  border-radius: 10px;
  width: 90%;
  max-width: 360px;
  color: white;
}

.modal-message {
  font-size: 16px;
  margin-bottom: 14px;
}

.input-wrapper {
  margin-bottom: 14px;
}

.edit-input {
  width: 95%;
  padding: 10px;
  border-radius: 6px;
  border: none;
  color: black;
}

.button-group {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.cancel-btn {
  padding: 8px 12px;
  background: #666;
  border-radius: 6px;
  border: none;
  cursor: pointer;
}

.ok-btn {
  padding: 8px 12px;
  background: #d9336f;
  border-radius: 6px;
  border: none;
  cursor: pointer;
}
</style>

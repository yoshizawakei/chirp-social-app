// frontend/composables/useComments.ts
export type Comment = {
    id: number
    postId: number
    userId: string
    username: string
    text: string
    createdAt: string
}

export const useComments = () => {
    const config = useRuntimeConfig()
    const baseURL = config.public.apiBase ?? 'http://localhost'

    const comments = useState<Comment[]>('comments:list', () => [])
    const loading = ref(false)
    const errorMessage = ref('')

    const fetchComments = async (postId: number) => {
        loading.value = true
        try {
        const data = await $fetch<Comment[]>(`${baseURL}/api/posts/${postId}/comments`)
        comments.value = data
        } catch (e: any) {
        errorMessage.value = 'コメントの取得に失敗しました'
        } finally {
        loading.value = false
        }
    }

    const createComment = async (postId: number, userId: string, username: string, text: string) => {
        errorMessage.value = ''

        if (!text) {
        errorMessage.value = 'コメントを入力してください'
        return
        }
        if (text.length > 120) {
        errorMessage.value = 'コメントは120文字以内で入力してください'
        return
        }

        try {
        const comment = await $fetch<Comment>(`${baseURL}/api/posts/${postId}/comments`, {
            method: 'POST',
            body: { userId, username, text },
        })
        comments.value.push(comment)
        } catch (e: any) {
        errorMessage.value = 'コメントの送信に失敗しました'
        }
    }

    return {
        comments,
        loading,
        errorMessage,
        fetchComments,
        createComment,
    }
}

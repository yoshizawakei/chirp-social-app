import type { Ref } from 'vue'

export type Post = {
    id: number
    userId: string
    username: string
    message: string
    createdAt: string
    likes: string[]   // userId の配列
    likeCount: number
    commentsCount: number
}

export const usePosts = () => {
    const config = useRuntimeConfig()
    const baseURL = config.public.apiBase ?? 'http://localhost'

    const posts = useState<Post[]>('posts:list', () => [])
    const loading = ref(false)
    const errorMessage = ref('')

    const fetchPosts = async () => {
        loading.value = true
        errorMessage.value = ''
        try {
        const data = await $fetch<Post[]>(`${baseURL}/api/posts`)
        posts.value = data
        } catch (e: any) {
        errorMessage.value = '投稿の取得に失敗しました'
        } finally {
        loading.value = false
        }
    }

    const createPost = async (userId: string, username: string, message: string) => {
        errorMessage.value = ''

        if (!message || message.length === 0) {
        errorMessage.value = '投稿内容を入力してください'
        return
        }
        if (message.length > 120) {
        errorMessage.value = '投稿内容は120文字以内で入力してください'
        return
        }

        try {
        const newPost = await $fetch<Post>(`${baseURL}/api/posts`, {
            method: 'POST',
            body: {
            userId,
            username,
            message,
            },
        })
        posts.value.unshift({
            ...newPost,
            likes: [],
            likeCount: 0,
            commentsCount: 0,
        })
        } catch (e: any) {
        errorMessage.value = '投稿に失敗しました'
        }
    }

    const deletePost = async (id: number, userId: string) => {
        try {
        await $fetch(`${baseURL}/api/posts/${id}`, {
            method: 'DELETE',
            body: { userId },
        })
        posts.value = posts.value.filter((p) => p.id !== id)
        } catch (e: any) {
        errorMessage.value = '削除に失敗しました'
        }
    }

    const toggleLike = async (id: number, userId: string) => {
        try {
        const res = await $fetch<{ liked: boolean; likeCount: number }>(
            `${baseURL}/api/posts/${id}/like`,
            {
            method: 'POST',
            body: { userId },
            }
        )

        posts.value = posts.value.map((p) =>
            p.id === id
            ? {
                ...p,
                likeCount: res.likeCount,
                likes: res.liked
                    ? [...p.likes, userId]
                    : p.likes.filter((uid) => uid !== userId),
                }
            : p
        )
        } catch (e: any) {
        errorMessage.value = 'いいね処理に失敗しました'
        }
    }

    return {
        posts,
        loading,
        errorMessage,
        fetchPosts,
        createPost,
        deletePost,
        toggleLike,
    }
}

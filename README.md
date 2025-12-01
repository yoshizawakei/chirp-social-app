# SHARE（Twitter風SNSアプリ）

## ■ アプリ概要画像
![トップ画像](images/image.png)

---

## ■ 作成した目的
本アプリケーションは、**Nuxt.js（フロント）× Laravel（API）× Firebase Auth（認証）× Docker（環境構築）** を通して、実践な習得することを目的に作成しました。

---

## ■ アプリケーションURL
http://localhost:3000/

---

## ■ 関連リポジトリ
https://github.com/yoshizawakei/chirp-social-app.git

---

## ■ 機能一覧

### ● 認証
- 新規登録（ユーザーネーム／メール／パスワード）
- ログイン
- ログアウト
- 認証状態保持（Firebase）

### ● 投稿機能
- 投稿一覧表示
- 投稿作成
- 投稿削除（本人のみ）
- 詳細ページへ遷移

### ● いいね機能
- いいね追加
- いいね解除
- いいね数の更新

### ● コメント機能
- コメント一覧表示
- コメント投稿
- コメント編集（本人のみ）
- コメント削除（本人のみ）

### ● UI コンポーネント
- TheHeader（全ページ共通ヘッダー）
- TheSidebar（サイドメニュー）
- Message（投稿一覧表示）
- ConfirmModal（削除・編集の確認モーダル）

---

## ■ 使用技術（実行環境）

### フロントエンド
- Nuxt.js 3.x
- Vue.js 3（Composition API）
- Pinia
- Firebase Authentication
- Vite

### バックエンド
- Laravel 10.x
- PHP 8.x
- Laravel Sanctum
- MySQL 8.x

### 環境構築
- Docker / Docker Compose
  - nginx
  - php-fpm
  - node
  - mysql

---

## ■ テーブル設計

### users テーブル
| カラム名 | 型 | 説明 |
|---------|------|------|
| id | bigint | PK |
| name | string | ユーザーネーム |
| email | string | メールアドレス |
| password | string | パスワード |
| timestamps | - | 作成日時など |

### posts テーブル
| カラム名 | 型 | 説明 |
|---------|------|------|
| id | bigint | PK |
| user_id | bigint | FK（users.id） |
| message | string | 投稿内容 |
| timestamps | - | 作成日時など |

### comments テーブル
| カラム名 | 型 | 説明 |
|----------|------|------|
| id | bigint | PK |
| post_id | bigint | FK（posts.id） |
| user_id | bigint | FK（users.id） |
| text | string | コメント本文 |
| timestamps | - | 作成日時 |

### likes テーブル
| カラム名 | 型 | 説明 |
|----------|------|------|
| id | bigint | PK |
| post_id | bigint | FK |
| user_id | bigint | FK |
| timestamps | - | 作成日時 |

---

## ■ テーブル設計（画像）
![DB](./images/db.png)

---

## ■ ER図（画像）
![ER図](images/ER.png)

---

## ■ 環境構築（ローカル）

### 1. リポジトリをクローン
```
git clone https://github.com/yoshizawakei/chirp-social-app.git
```

---

### 2. Docker コンテナ起動
```
docker-compose up -d --build
```
---

### 3. フロントエンド（Nuxt）
```
docker-compose exec frontend sh
npm install
npm run dev
```

---

### 4. バックエンド（Laravel）
```
docker-compose exec backend bash
composer install
cp .env.example .env
.env.exampleファイルから.envを作成し、環境変数を変更
php artisan key:generate
php artisan migrate --seed
```

---

### 5. Firebase の環境変数設定（Nuxt）
backendディレクトリ直下に`.env`を作成し、以下の内容を記述：

```
NUXT_PUBLIC_FIREBASE_API_KEY=xxxx
NUXT_PUBLIC_FIREBASE_AUTH_DOMAIN=xxxx
NUXT_PUBLIC_FIREBASE_PROJECT_ID=xxxx
NUXT_PUBLIC_FIREBASE_APP_ID=xxxx
```

※Firebase Console にアクセスし、新しいプロジェクトを作成します。
プロジェクト内で Authentication を有効にします。
プロジェクト設定から、ウェブアプリ を追加し、firebaseConfig オブジェクトを取得します。
取得したオブジェクトをxxxの部分に入力します。

---

## ■ テストユーザー（新規登録からご自身で作成してください。）
```
ユーザー名：test1
メール：test1@example.com
パスワード：password

```
ユーザー名：test2ユーザー名
メール：test2@example.com
パスワード：password

```

---

# 以上です。

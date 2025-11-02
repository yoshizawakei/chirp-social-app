// store/auth.js
// Firebase認証状態管理のためのVuexストアモジュール

const defaultUser = { uid: null, name: null, email: null };

export const state = () => ({
  loggedIn: false,
  user: defaultUser,
  authChecked: false, // 認証チェック完了フラグ
});

export const mutations = {
  setUser(state, user) {
    if (user && user.uid) {
      state.user = {
        uid: user.uid,
        name: user.displayName || user.email.split('@')[0], // 名前がない場合はメールアドレスから生成
        email: user.email,
      };
      state.loggedIn = true;
    } else {
      state.user = defaultUser;
      state.loggedIn = false;
    }
  },
  setAuthChecked(state, status) {
      state.authChecked = status;
  }
};

export const actions = {
  // Firebaseの認証状態の変化を監視し、ログイン/ログアウトを検知
  onAuthStateChangedAction({ commit }) {
    return new Promise((resolve) => {
      this.$auth.onAuthStateChanged(user => { // 💡 $auth はプラグインで注入されていることを前提
        commit('setUser', user);
        commit('setAuthChecked', true);
        resolve(user);
      });
    });
  },

  // ログアウト処理
  async logout({ commit }) {
    try {
      await this.$auth.signOut();
      commit('setUser', null);
    } catch (error) {
      console.error('Firebaseログアウトエラー:', error);
      commit('setUser', null);
    }
  },
};

export const getters = {
    isLoggedIn: state => state.loggedIn,
    userName: state => state.user.name,
    userId: state => state.user.uid,
    authChecked: state => state.authChecked,
};
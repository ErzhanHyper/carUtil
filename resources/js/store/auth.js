import { logout } from '@/services/user'
import router from '@/router'
import api from "../api";
export default {
    namespaced: true,

    state: {
        token: null,
        user: null
    },

    getters: {

        authenticated(state) {
            return state.token
        },

        user(state) {
            return state.user
        },
    },

    mutations: {
        SET_TOKEN(state, token) {
            state.token = token
        },

        SET_USER(state, data) {
            state.user = data
        }
    },

    actions: {

         signOut({commit}) {
             logout()
            commit('SET_TOKEN', null)
            commit('SET_USER', null)
        },

        async signIn({dispatch}, credentials) {
            let response = await api.post('login', credentials)
            dispatch('attempt', response.data.data.hash)
        },

        async signInMobile({dispatch}, credentials) {
            let response = await api.post('loginMobile', credentials)
            dispatch('attempt', response.data.data.hash)
        },

        async attempt({commit, state}, token) {
            if (token) {
                commit('SET_TOKEN', token)
            }

            if (!state.token) {
                return
            }

            try {
                 await api.post('/user').then(res => {
                    commit('SET_USER', res.data)
                }).catch(() => {
                     router.push('/login')
                     commit('SET_TOKEN', null)
                     commit('SET_USER', null)
                 })
            } catch (e) {
                commit('SET_TOKEN', null)
                commit('SET_USER', null)
            }
        }
    }
}

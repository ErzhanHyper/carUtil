<template>
    <!--    <q-linear-progress query />-->

    <q-layout view="lHh LpR fFf" v-if="authenticated">

        <q-header elevated class="bg-white text-dark">
            <q-linear-progress indeterminate color="warning" size="md" v-if="contentLoad"/>
            <q-toolbar>
                <q-btn dense flat round icon="menu" @click="toggleLeftDrawer"/>

                <q-toolbar-title class="text-subtitle2 text-weight-bold text-blue-grey-7">
                    ВЭТС/ВЭССХТ
                </q-toolbar-title>

                <!--                <div class="notification-btn flex flex-center q-mr-lg">-->
                <!--                    <q-btn link dense flat round icon="notifications">-->
                <!--                    </q-btn>-->
                <!--                    <q-badge>2</q-badge>-->
                <!--                </div>-->

                <div class="profile-btn flex flex-center">
                    <q-btn dense flat v-if="user">
                        <div class="q-mr-sm flex column text-right">
                                <span class="text-body2 text-capitalize">{{
                                        user.title ?? ((user.profile) ? user.profile.fln : '')
                                    }}</span>
                            <span class="text-caption text-capitalize" v-if="user.role && user.role !== 'liner'">{{
                                    (user.role === 'moderator') ? 'Модератор' : 'Менеджер'
                                }}</span>
                        </div>
                        <q-icon name="person"/>
                        <q-menu anchor="center middle"
                                self="center middle">
                            <q-list style="min-width: 100px">
                                <q-item clickable v-close-popup avatar v-ripple to="/profile">
                                    <q-item-section>Профиль</q-item-section>
                                    <q-item-section class="q-pl-lg">
                                        <q-icon name="person"/>
                                    </q-item-section>
                                </q-item>
                                <q-item clickable v-close-popup avatar v-ripple @click="logout">
                                    <q-item-section>Выйти</q-item-section>
                                    <q-item-section class="q-pl-lg">
                                        <q-icon name="logout"/>
                                    </q-item-section>
                                </q-item>
                            </q-list>
                        </q-menu>
                    </q-btn>
                </div>

            </q-toolbar>
        </q-header>

        <q-drawer show-if-above v-model="leftDrawerOpen" side="left" bordered :width="240" class="bg-primary">
            <MainMenuList/>
        </q-drawer>

        <q-page-container :class="contentLoad ? 'active' : ''">
            <div class="q-pa-md">
                <PageLayout/>
            </div>
        </q-page-container>

    </q-layout>
</template>

<script>
import {ref} from 'vue'
import MainMenuList from "@/Components/MainMenuList.vue";
import Login from "@/Pages/Login.vue";
import PageLayout from "./PageLayout.vue";
import {getUser, logout} from "../services/user";
import {mapGetters, mapActions} from 'vuex'

export default {

    components: {PageLayout, Login, MainMenuList},

    setup() {
        const leftDrawerOpen = ref(false)

        return {
            leftDrawerOpen,
            toggleLeftDrawer() {
                leftDrawerOpen.value = !leftDrawerOpen.value
            }
        }
    },

    data() {
        return {
            contentLoad: true
        }
    },

    computed: {
        ...mapGetters({
            authenticated: 'auth/authenticated',
            user: 'auth/user'
        })
    },

    methods: {
        logout() {
            this.$store.dispatch('auth/signOut')
            this.$router.replace('/login')
        },

    },

    mounted() {
        this.$emitter.on('contentLoaded', (value) => {
            this.contentLoad = value
        })
    }
}
</script>

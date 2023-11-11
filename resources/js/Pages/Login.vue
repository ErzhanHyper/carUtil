<template>
    <q-layout view="lHh LpR fFf" class="login_layout">
        <q-page-container>
            <q-page class=" window-height window-width row justify-center items-center" id="loginPage">
                <div class="column">
                    <div class="row">
                        <h5 class="text-h5 text-white q-my-md">ВЭТС/ВЭССХТ</h5>
                    </div>
                    <div class="row">
                        <q-card square bordered class="q-pa-sm shadow-1"
                                style="max-width: 480px;width: calc(100vw - 40px)">

                            <q-banner dense inline-actions class="text-white bg-pink-5" v-if="showBanner">
                                    <span v-for="(error, i) in errors" :key="i">
                                        <span v-for="(item, index) in error" :key="index">
                                            {{ item }}
                                            <br>
                                        </span>
                                    </span>
                            </q-banner>
                            <q-card-section>

                                <q-form class="q-gutter-md flex justify-between no-wrap">
                                    <q-select :options="options" label="Выберите тип применяемой ЭЦП"
                                              v-model="auth_type"
                                              option-label="name" option-value="code"
                                              style="width: calc(400px - 80px)"/>
                                    <q-btn unelevated color="light-green-7" size="md" label="Войти" push
                                           @click="login" :loading="loading"/>
                                </q-form>
                            </q-card-section>

<!--                            <q-card-section id="mobileAuth">-->

<!--                                <q-form class="q-gutter-md  q-mt-md">-->
<!--                                    <q-input label="ИИН" outlined dense v-model="idnum" :model-value="idnum"  />-->
<!--                                    <q-input label="Пароль" outlined dense type="password" v-model="password" autocomplete="off"-->
<!--                                             :model-value="password"/>-->

<!--                                    <q-btn unelevated color="light-green-7" size="md" label="Войти" push-->
<!--                                           @click="loginMobile" :loading="loading"/>-->
<!--                                </q-form>-->
<!--                            </q-card-section>-->
                        </q-card>
                    </div>
                </div>
            </q-page>
        </q-page-container>
    </q-layout>
</template>


<script>

import {NCALayerClient} from "ncalayer-js-client";
import axios from 'axios';
import {mapActions} from 'vuex';

export default {
    data() {
        return {
            loading: false,
            showBanner: false,
            errors: [],

            idnum: '',
            password: '',

            auth_type: {
                name: 'Файл',
                code: 'PKCS12'
            },
            options: [
                {
                    name: 'Файл',
                    code: 'PKCS12'
                }
            ]
        }
    },

    methods: {

        ...mapActions({
            signIn: 'auth/signIn',
            signInMobile: 'auth/signInMobile',
        }),

        login() {
            this.loading = true
            this.showBanner = false
            this.errors = []
            this.connectAndSign()
        },

        async connectAndSign() {
            const ncalayerClient = new NCALayerClient();

            try {
                await ncalayerClient.connect();
            } catch (error) {
                this.loading = false
                alert(`Не удалось подключиться к NCALayer: ${error.toString()}`);
                return;
            }

            let activeTokens;
            try {
                activeTokens = await ncalayerClient.getActiveTokens();
            } catch (error) {
                console.log(error.toString());
                this.loading = false
                return;
            }

            // getActiveTokens может вернуть несколько типов хранилищ, для простоты проверим первый.
            // Иначе нужно просить пользователя выбрать тип носителя.
            const storageType = activeTokens[0] || NCALayerClient.fileStorageType;

            let base64EncodedSignature;
            try {
                base64EncodedSignature = await ncalayerClient.getKeyInfo(storageType, 'MTEK');
            } catch (error) {
                this.loading = false
                return;
            }

            this.signIn({data: base64EncodedSignature, auth_point: 'liner'}).then(() => {
                this.$router.replace({
                    name: 'preorder'
                })
            }).catch(reject => {
                console.log(reject)
                this.errors = JSON.parse(reject.response.data.error)
                this.showBanner = true
            }).finally(() => {
                this.loading = false
            })
            return base64EncodedSignature;
        },


        // loginMobile() {
        //     this.loading = true
        //     this.showBanner = false
        //     this.errors = []
        //
        //     this.signInMobile({
        //         login: this.idnum,
        //         password: this.password
        //     }).then(() => {
        //         this.$router.replace({
        //             name: 'preorder'
        //         })
        //     }).catch(reject => {
        //         this.errors = JSON.parse(reject.response.data.error)
        //         this.showBanner = true
        //     }).finally(() => {
        //         this.loading = false
        //     })
        // },

    },


    mounted() {

    }
}


</script>

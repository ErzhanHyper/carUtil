<template>
    <q-layout view="lHh LpR fFf">
        <q-page-container>
            <q-page class="bg-blue-grey-10 window-height window-width row justify-center items-center">
                <div class="column">
                    <div class="row">
                        <h5 class="text-h5 text-white q-my-md">ВЭТС/ВЭССХТ</h5>
                    </div>
                    <div class="row">
                        <q-card square bordered class="q-pa-lg shadow-1" style="max-width: 480px;width: 100vw">

                            <q-card-section>
                                <q-form class="q-gutter-md">
                                    <q-select :options="options" label="Выберите тип применяемой ЭЦП"
                                              v-model="auth_type"
                                              option-label="name" option-value="code" @click="getTokens"/>
                                </q-form>
                            </q-card-section>

                            <q-card-actions class="q-px-md">
                                <q-btn unelevated color="light-blue-10" size="md" class="full-width" label="Войти" push
                                       @click="login" :loading="loading"/>
                            </q-card-actions>

                        </q-card>
                    </div>
                    <span class="text-caption text-white text-center">Вход для сотрудников</span>
                </div>
            </q-page>
        </q-page-container>
    </q-layout>
</template>


<script>

import {NCALayerClient} from "ncalayer-js-client";
import {mapActions} from "vuex";

export default {

    name: 'Login',
    data() {
        return {
            loading: false,
            auth_type: {
                name: '',
                code: ''
            },
            options: [
            ]
        }
    },

    methods: {

        ...mapActions({
            signIn: 'auth/signIn',
        }),


        async getTokens(){
            const ncalayerClient = new NCALayerClient();
            this.options = []
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
                console.log(activeTokens)
            } catch (error) {
                console.log(error.toString());
                this.loading = false
                return;
            }

            if(activeTokens[0]){
                this.options.push({
                    name: activeTokens[0],
                    code: activeTokens[0]
                })
            }else{
                this.auth_type = {
                    name: 'Файл',
                    code: NCALayerClient.fileStorageType
                }
            }

        },

        login() {
            this.loading = true
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

            this.signIn({data: base64EncodedSignature, auth_point: 'manager'}).then(() => {
                this.$router.replace({
                    name: 'order'
                })
            }).catch(reject => {
            }).finally(() => {
                this.loading = false
            })
            return base64EncodedSignature;
        }

    },


    mounted() {
        this.getTokens()
    }
}


</script>

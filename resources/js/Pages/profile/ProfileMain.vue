<template>

    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Профиль</div>
    </div>

    <div v-if="show">
        <div class="row q-col-gutter-md">
            <div class="col col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <div class="row q-col-gutter-md">
                    <div class="col col-md-12 col-xs-12">
                        <email-field outlined dense v-model="user.email"/>
                    </div>
                    <div class="col col-md-12 col-xs-12">
                        <phone-field outlined dense v-model="user.phone"/>
                    </div>

<!--                    <template v-else>-->
<!--                        <div class="col col-md-12 col-xs-12">-->
<!--                            <region-field v-model="user.region"/>-->
<!--                        </div>-->
<!--                    </template>-->
                </div>

                <div class="row q-col-gutter-md q-mt-xs">
                    <div class="col col-md-12 col-xs-12">
                        <name-field v-model="user.title" outlined dense :square="false"
                                    label="ФИО" />
                    </div>
                    <div class="col col-md-12 col-xs-12">
                        <name-field v-model="user.for_docs" outlined dense :square="false"
                                    label="Наименование вашей компании (для документов)"  v-if="user.role === 'dealer-light' || user.role === 'dealer-chief'"/>

                        <name-field v-model="user.for_docs" outlined dense :square="false"
                                    label="ФИО в родительном падеже (для документов)"  v-if="user.role === 'moderator' || user.role === 'operator'"/>
                    </div>
                    <div class="col col-md-12 col-xs-12" v-if="user.role === 'dealer-light' || user.role === 'dealer-chief'">
                        <address-field label="Адрес салона или компании (для диллеров)" v-model="user.custom_1" outlined dense/>
                    </div>
                </div>

                <div class="row q-col-gutter-md q-mt-lg">
                <template v-if="user.role === 'operator'">
                    <div class="col col-md-12 col-xs-12">
                        <q-input label="Пароль мобильного приложения" outlined dense
                                 :type="(!isLock) ? 'password' : 'text'"
                                 v-model="user.password">
                            <template v-slot:prepend>
                                <q-icon name="lock"/>
                            </template>
                            <template v-slot:append>
                                <q-btn flat>
                                    <q-icon name="visibility" v-if="isLock" @click="isLock = false"/>
                                    <q-icon name="visibility_off" v-if="!isLock" @click="isLock = true"/>
                                </q-btn>
                            </template>
                        </q-input>
                    </div>

                    <div class="col col-md-12 col-xs-12">
                        <q-input label="Подтверждение пароля мобильного приложения" outlined dense
                                 :type="(!isLock) ? 'password' : 'text'" v-model="user.password_confirm">
                            <template v-slot:prepend>
                                <q-icon name="lock"/>
                            </template>
                            <template v-slot:append>
                                <q-btn flat>
                                    <q-icon name="visibility" v-if="isLock" @click="isLock = false"/>
                                    <q-icon name="visibility_off" v-if="!isLock" @click="isLock = true"/>
                                </q-btn>
                            </template>
                        </q-input>
                    </div>
                </template>
                </div>

            </div>
        </div>

        <div class="q-mt-md">
            <q-btn color="primary" icon="save" label="Сохранить" @click="updateData" :loading="loading"/>
        </div>
    </div>

</template>

<script>
import {getUser, updateProfile} from "../../services/user";
import {Notify} from "quasar";

import EmailField from "../../Components/Fields/EmailField.vue";
import PhoneField from "../../Components/Fields/PhoneField.vue";
import RegionField from "../../Components/Fields/RegionField.vue";
import AddressField from "../../Components/Fields/AddressField.vue";
import NameField from "../../Components/Fields/NameField.vue";

export default {
    components: {RegionField, PhoneField, EmailField, AddressField, NameField},

    data() {
        return {
            isLock: false,
            show: false,
            loading: false,
            user: {
                id: null,
                phone: '',
                email: '',
                password: '',
                password_confirm: '',
                region: ''
            }
        }
    },

    methods: {

        getData() {
            this.$emitter.emit('contentLoaded', true);
            getUser().then(res => {
                this.$emitter.emit('contentLoaded', false);

                let profile = res.profile

                if (res.role === 'liner') {
                    this.user.phone = (profile.phone) ?? ''
                    this.user.email = (profile.email) ?? ''
                } else {
                    this.user.title = res.title
                    this.user.phone = res.phone
                    this.user.email = res.email
                    this.user.for_docs = res.for_docs
                    this.user.custom_1 = res.custom_1
                }
                this.user.id = res.id
                this.user.role = res.role

                this.show = true
            })
        },


        updateData() {
            this.loading = true
            updateProfile(this.user).then(() => {
                this.getData()

                this.user.password = ''
                this.user.password_confirm = ''

                Notify.create({
                    message: 'Данные сохранены',
                    position: 'bottom',
                    type: 'positive'
                })

            }).finally(() => {
                this.loading = false
            })
        }
    },

    created() {
        this.getData()
    }
}
</script>

<style scoped>

</style>

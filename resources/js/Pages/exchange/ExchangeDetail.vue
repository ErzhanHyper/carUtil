<template>
    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between items-center">
        <div class="text-h6 text-primary">
            Переоформление сертификата
            <br>
            <div class="text-overline text-blue-5 text-uppercase" v-if="show && user.role === 'liner' && (item.approve === 0 || item.approve === 1)">{{ item.info }}</div>
            <div class="text-overline text-blue-5 text-uppercase" v-if="show && user.role === 'moderator'">{{ item.status.title }}</div>
        </div>

        <div class="q-gutter-md" >

            <q-btn color="indigo-8"
                   size="12px"
                   dense
                   icon="download"
                   icon-right="description"
                   label="Заявление на перерегистрацию"
                   class="text-weight-bold"
                   :loading="loading1"
                   @click="getExchangeDoc"/>

            <template v-if="show && user.role === 'liner'">
                <q-btn label="Подписать" size="12px" color="positive" icon="gesture" @click="send" v-if="item.canSign" :loading="loading2"/>
                <q-btn label="Отменить" size="12px" color="pink-5" icon="close" class="q-ml-md" v-if="item.canDelete" :loading="loading3" @click="deleteData"/>
            </template>
        </div>
    </div>

    <q-card v-if="show">
        <q-card-section>
            <div class="text-weight-bold q-mt-md">Владелец сертификата</div>
            <div>ФИО: {{ item.certificate ? item.certificate.title_1 : '' }}</div>
            <div>ИИН/БИН: {{ item.certificate ? item.certificate.idnum_1 : '' }}</div>
        </q-card-section>

        <q-card-section>
            <div class="row q-col-gutter">
                <div class="col col-md-4 col-xs-12">
                    <address-field outlined dense label="Адрес владельца" v-model="item.cert_owner_address"
                                   :readonly="item.blocked"/>
                </div>
            </div>
            <div class="row q-col-gutter q-my-md">
                <div class="col col-md-4 col-xs-12">
                    <name-field outlined dense label="ФИО получателя" v-model="item.title" :readonly="item.blocked"/>
                </div>
            </div>

            <div class="row q-col-gutter q-my-md">
                <div class="col col-md-4 col-xs-12">
                    <client-id-field outlined dense label="ИИН/БИН получателя" v-model="item.idnum"
                                     :readonly="item.blocked"/>
                </div>
            </div>

            <div class="row q-col-gutter q-my-md">
                <div class="col col-md-4 col-xs-12">
                    <phone-field outlined dense label="Контактный телефон получателя" v-model="item.phone"
                                 :readonly="item.blocked"/>
                </div>
            </div>

            <div v-if="item.status.id === 0">
                Копии документов, удостоверяющих личность владельца скидочного сертификата (свидетельства о
                государственной регистрации юридического лица, документ, подтверждающий полномочия представителя,
                копия документа, удостоверяющего личность представителя) и лица, которому данный сертификат передается
                прилагаю на
                <input v-model="item.page" style="width: 60px;margin-top: -10px" type="number" min="1"/>
                листах.
            </div>

        </q-card-section>

        <q-card-section>
            <div class="row q-col-gutter q-my-md">
                <div class="col col-md-4 col-xs-12">
                    <exchange-file :data="item" :readonly="item.blocked"/>
                </div>
            </div>
        </q-card-section>
    </q-card>

    <div class="q-mt-md">
        <exchange-action :show="item.status !== 0" :data="item" v-if="user && user.role === 'moderator' && item.approve === 1"/>
    </div>

</template>

<script>
import {deleteExchange, getExchangeById, updateExchange} from "../../services/exchange";
import {signData} from "../../services/sign";

import AddressField from "../../Components/Fields/AddressField.vue";
import NameField from "../../Components/Fields/NameField.vue";
import ClientIdField from "../../Components/Fields/ClientIdField.vue";
import PhoneField from "../../Components/Fields/PhoneField.vue";
import ExchangeFile from "./ExchangeFile.vue";
import ExchangeAction from "./ExchangeAction.vue";
import {mapGetters} from "vuex";
import {Notify} from "quasar";
import {getExchangeApp} from "../../services/document";
import FileDownload from "js-file-download";
import {generateCertificate} from "../../services/certificate";

export default {
    components: {ExchangeAction, PhoneField, ClientIdField, NameField, AddressField, ExchangeFile},
    props: ['id'],

    data() {
        return {
            show: false,
            loading1: false,
            loading2:false,
            loading3:false,
            item: {
                page: 2,
                blocked: true,
                canSign: false,
                canDelete: false,
            }
        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    methods: {

        getExchangeDoc() {
            this.loading1 = true
            getExchangeApp(this.item.id, {responseType: 'arraybuffer'}).then((res) => {
                FileDownload(res, 'exchange.pdf')
            }).finally(() => {
                this.loading1 = false
            })
        },

        getData() {
            this.$emitter.emit('contentLoaded', true);
            getExchangeById(this.id).then(res => {
                this.$emitter.emit('contentLoaded', false);
                if(res && res.id) {
                    this.item = res
                    this.show = true
                }
            })
        },

        send() {
            signData().then(res => {
                if(res){
                    this.loading2 = true
                    updateExchange(this.item.id, {
                        sign: res,
                        title: this.item.title,
                        idnum: this.item.idnum,
                        cert_owner_address: this.item.cert_owner_address,
                        page: this.item.page,
                        phone: this.item.phone,
                    }).then((res) => {
                        if(res) {
                            if (res.success) {
                                this.getData()
                            }
                            if ( res.message && res.message !== '') {
                                Notify.create({
                                    message: res.message,
                                    position: 'bottom',
                                    type: res.success ? 'positive' : 'warning'
                                })
                            }
                        }
                    }).finally(() => {
                        this.loading2 = false
                    })
                }
            })
        },

        deleteData() {
            this.loading3 = true
            deleteExchange(this.item.id).then((res) => {
                if(res && res.success === true){
                    this.$router.push('/certificate')
                }
            }).finally(() => {
                this.loading3 = false
            })
        }
    },

    created() {
        this.getData()
    },

    mounted(){
        this.$emitter.on('ExchangeActionEvent', () => {
            this.getData();
        })
    }
}
</script>

<style scoped>

</style>

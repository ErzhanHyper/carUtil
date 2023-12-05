<template>

    <div class="row q-col-gutter-md" >

        <div class="col col-md-6" v-if="user && (user.role === 'moderator' ||  user.role === 'dealer-chief')">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Отчеты по погашениям
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col q-mr-lg">
                            <q-input type="date" hint="начало" v-model="sell.start"/>
                        </div>
                        <div class="col">
                            <q-input type="date" hint="конец" v-model="sell.end"/>
                        </div>
                    </div>
                    <q-btn label="Запустить" color="blue-grey-8" size="12px" class="q-mt-md" @click="runSell" :loading="loading2"/>
                </q-card-section>
            </q-card>
        </div>

        <div class="col col-md-6" v-if="user && (user.role === 'moderator')">
            <q-card flat bordered >
                <q-card-section class="text-body1">
                    Отчеты по переоформлениям
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col q-mr-lg">
                            <q-input type="date" hint="начало" v-model="exchange.start"/>
                        </div>
                        <div class="col">
                            <q-input type="date" hint="конец" v-model="exchange.end"/>
                        </div>
                    </div>
                    <q-btn label="Запустить" color="blue-grey-8" size="12px" class="q-mt-md" @click="runExchange" :loading="loading3"/>
                </q-card-section>
            </q-card>
        </div>

    </div>

    <div class="row q-col-gutter-md q-mt-md" v-if="user && user.role === 'moderator'">

        <div class="col col-md-6">
            <q-card flat bordered >
                <q-card-section class="text-body1">
                    Отчеты по сертификатам
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col q-mr-lg">
                            <q-input type="date" hint="начало" v-model="cert.start"/>
                        </div>
                        <div class="col">
                            <q-input type="date" hint="конец" v-model="cert.end"/>
                        </div>
                    </div>
                    <q-btn label="Запустить" color="blue-grey-8" size="12px" class="q-mt-md" @click="runCert" :loading="loading1"/>
                </q-card-section>
            </q-card>
        </div>

        <div class="col col-md-6">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Отчеты по действиям
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col q-mr-lg">
                            <q-input type="date" hint="начало" v-model="actions.start"/>
                        </div>
                        <div class="col">
                            <q-input type="date" hint="конец" v-model="actions.end"/>
                        </div>
                    </div>
                    <q-btn label="Запустить" color="indigo-8" size="12px" class="q-mt-md" @click="runAction" :loading="loading4"/>
                </q-card-section>
            </q-card>
        </div>

    </div>
</template>

<script>
import {getActionReport, getCertReport, getExchangeReport, getSellReport} from "../../services/report";
import FileDownload from "js-file-download";
import {mapGetters} from "vuex";

export default {
    data() {
        return{
            loading1:false,
            loading2:false,
            loading3:false,
            loading4:false,

            sell: {
                start: this.$moment().format('YYYY-MM-DD'),
                end: this.$moment().format('YYYY-MM-DD')
            },
            cert: {
                start: this.$moment().format('YYYY-MM-DD'),
                end: this.$moment().format('YYYY-MM-DD')
            },
            exchange: {
                start: this.$moment().format('YYYY-MM-DD'),
                end: this.$moment().format('YYYY-MM-DD')
            },
            actions: {
                start: this.$moment().format('YYYY-MM-DD'),
                end: this.$moment().format('YYYY-MM-DD')
            }
        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    methods: {
        runCert() {
            this.loading1 = true
            getCertReport({params: this.cert, responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'certificate.xlsx')
            }).finally(() => {
                this.loading1 = false
            })
        },

        runSell() {
            this.loading2 = true
            getSellReport({params: this.sell, responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'sell.xlsx')
            }).finally(() => {
                this.loading2 = false
            })
        },

        runExchange() {
            this.loading3 = true
            getExchangeReport({params: this.exchange, responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'exchange.xlsx')
            }).finally(() => {
                this.loading3 = false
            })
        },

        runAction() {
            this.loading4 = true
            getActionReport({params: this.actions, responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'exchange.xlsx')
            }).finally(() => {
                this.loading4 = false
            })
        }
    },

    created() {
    },

    mounted() {

    }
}
</script>

<style scoped>

</style>

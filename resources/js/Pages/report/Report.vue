<template>

    <div class="row q-col-gutter-md">

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
                    <q-btn label="Запустить" color="indigo-8" size="12px" class="q-mt-md" @click="runCert" :loading="loading1"/>
                </q-card-section>
            </q-card>
        </div>

        <div class="col col-md-6">
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
                    <q-btn label="Запустить" color="indigo-8" size="12px" class="q-mt-md" @click="runSell" :loading="loading2"/>
                </q-card-section>
            </q-card>
        </div>

    </div>

    <div class="row q-col-gutter-md q-mt-md">

        <div class="col col-md-6">
            <q-card flat bordered >
                <q-card-section class="text-body1">
                    Отчеты по переоформлениям
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col q-mr-lg">
                            <q-input type="date" hint="начало"/>
                        </div>
                        <div class="col">
                            <q-input type="date" hint="конец"/>
                        </div>
                    </div>
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
                            <q-input type="date" hint="начало"/>
                        </div>
                        <div class="col">
                            <q-input type="date" hint="конец"/>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>

    </div>
</template>

<script>
import {getCertReport, getSellReport} from "../../services/report";
import FileDownload from "js-file-download";

export default {
    data() {
        return{
            loading1:false,
            loading2:false,
            sell: {
                start: '',
                end: ''
            },
            cert: {
                start: '',
                end: ''
            }
        }
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
        }
    },

    created() {
    },

    mounted() {
        setTimeout(() => {
            this.$emitter.emit('contentLoaded', false);
        }, 10)
    }
}
</script>

<style scoped>

</style>

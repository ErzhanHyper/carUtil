<template>

    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Сертификаты</div>

        <div class="flex justify-between">
            <a href="https://recycle.kz/ru/novosti/zheildik-sertifikatyn-shinshi-tl-rsimdeuge-bolady" target="_blank">
                <q-btn color="indigo-8" size="12px" push icon="open_in_new" label="Инструкция" class="q-ml-md text-weight-bold"/>
            </a>
        </div>
    </div>


    <div class="row q-col-gutter-sm">
        <div class="col col-md-7">
            <span class="text-weight-bold text-body1">Мои</span>
            <q-markup-table flat bordered dense >
                <thead>
                <tr>
                    <th class="text-left">№ сертификата</th>
                    <th class="text-left">Дата выдачи</th>
                    <th class="text-left">Срок действия до</th>
                    <th class="text-left">Статус</th>
                    <th class="text-left">Сумма сертификата</th>
                    <th class="text-left">Сертификат</th>
                    <th class="text-left"></th>
                </tr>
                </thead>
                <tbody>
                <template v-if="items.length > 0 && show">
                    <tr v-for="(item, i) in items" :key="i">
                        <td class="text-left">{{ item.id }}</td>
                        <td class="text-left">{{ item.date }}</td>
                        <td class="text-left">{{ item.dateTill }}</td>
                        <td class="text-left">{{ item.status }}</td>
                        <td class="text-left">{{ item.sum }} &#8376;</td>
                        <td class="text-left">
                            <q-btn icon="verified"
                                   color="light-green-8"
                                   size="11px"
                                   label="Скачать"
                                   icon-right="download"
                                   @click="downloadCert(item.id)"
                                   :loading="loading"></q-btn>
                        </td>

                        <td class="text-right">
                            <q-btn icon="verified"

                                   icon-right="sync_alt"
                                   color="pink-10"
                                   size="11px"
                                   label="Переоформить сертификат"
                                   dense
                                   :loading="loading2"
                                   @click="exchangeCert(item.id)"
                                   v-if="item.showExchange">
                            </q-btn>
                            <q-badge v-if="!item.showExchange && item.exchangeStatus != ''">
                                <router-link :to="'/exchange/'+item.exchange.id" v-if="item.exchange">
                                    {{ item.exchangeStatus }}
                                </router-link>
                            </q-badge>
                        </td>
                    </tr>
                </template>
                <div class="q-ma-xs" v-if="show && items.length === 0">Нет записей</div>
                </tbody>
            </q-markup-table>

            <div class="flex justify-center">
                <q-spinner-dots
                    color="primary"
                    size="2em"
                    class="q-ma-xs"
                    v-if="!show"
                />
            </div>
        </div>

        <div class="col col-md-5">
            <span class="text-weight-bold text-body1">Передачи</span>
            <q-markup-table flat bordered dense >
                <thead>
                <tr>
                    <th class="text-left">#</th>
                    <th class="text-left">Дата создания</th>
                    <th class="text-left">ФИО владельца</th>
                    <th class="text-left">ИИН/БИН владельца</th>
                    <th class="text-left">Статус</th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="item in exchanges">
                    <td>
                        <router-link :to="'/exchange/'+item.id" class="text-primary" >
                            <q-icon name="open_in_new" size="xs"/>
                            {{ item.certificate ? item.certificate.id : '-' }}
                        </router-link>
                    </td>
                    <td>{{ item.created }}</td>
                    <td>{{ item.certificate ? item.certificate.title_1 : '' }}</td>
                    <td>{{ item.certificate ? item.certificate.idnum_1 : '' }}</td>
                    <td>
                        <q-badge size="12px" v-if="item.approve === 0">В ожидании подписи</q-badge>
                        <q-badge size="12px" v-if="item.approve === 1">На рассмотрении у модератора</q-badge>
                        <q-badge size="12px" color="positive" v-if="item.approve === 2">Одобрена</q-badge>
                        <q-badge size="12px" color="negative" v-if="item.approve === 3">Отклонена</q-badge>
                    </td>
                </tr>
                <div class="q-ma-xs" v-if="show && exchanges.length === 0">Нет записей</div>

                </tbody>
            </q-markup-table>

            <div class="flex justify-center">
                <q-spinner-dots
                    color="primary"
                    size="2em"
                    class="q-ma-xs"
                    v-if="!show"
                />
            </div>
        </div>
    </div>

</template>

<script>
import {generateCertificate, getCertificateList} from "../../services/certificate";
import FileDownload from "js-file-download";
import {getExchangeList, storeExchange} from "../../services/exchange";
import {Notify} from "quasar";
import {secureData} from "../../services/sign";

export default {

    data() {
        return {
            tab: 1,
            items: [],
            exchanges: [],

            loading: false,
            loading2: false,
            show: false
        }
    },

    methods: {
        getData() {
            this.$emitter.emit('contentLoaded', true);
            getCertificateList().then(res => {
                this.$emitter.emit('contentLoaded', false);
                this.items = res
                this.show = true
            })
            getExchangeList().then(res => {
                this.$emitter.emit('contentLoaded', false);
                this.exchanges = res.items
                this.show = true
            })
        },

        downloadCert(id) {
            secureData().then(res => {
                if(res){
                    this.loading = true
                    generateCertificate(id, {params: res, responseType: 'arraybuffer'}).then(value => {
                        FileDownload(value, 'certificate.pdf')
                    }).finally(() => {
                        this.loading = false
                    })
                }
            }).catch(() => {
                this.loading = false
            })
        },

        exchangeCert(id) {
            this.loading2 = true
            storeExchange({certificateId: id}).then((res) => {
                if(res && res.data && res.data.id) {
                    this.$router.push('/exchange/' + res.data.id)
                }
                Notify.create({
                    message: res.message,
                    position: 'bottom',
                    type: res.success ? 'positive' : 'warning'
                })
            }).finally(() => {
                this.loading2 = false
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

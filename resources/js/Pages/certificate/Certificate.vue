<template>

    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Сертификаты</div>
    </div>


    <q-tabs
        v-model="tab"
        dense
        class="text-grey"
        active-color="primary"
        indicator-color="primary"
        align="justify"
        narrow-indicator
        style="width: 400px"
    >
        <q-tab :name="1" label="Мои сертификаты"/>
        <q-tab :name="2" label="Передача сертификатов"/>
    </q-tabs>

    <q-tab-panels v-model="tab" animated vertical>
        <q-tab-panel :name="1" class="q-pa-none">
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
                    <td class="text-left">{{ item.sum }}</td>
                    <td class="text-left">
                        <q-btn icon="verified" color="indigo-8" dense size="11px"
                               label="Скидочный сертификат"
                               icon-right="download" @click="downloadCert(item.id)" :loading="loading"></q-btn>
                    </td>

                    <td>
                        <q-btn icon="verified" icon-right="sync_alt" color="pink-10" size="11px" label="Переоформить сертификат" dense
                               :loading="loading2" @click="exchangeCert(item.id)" v-if="item.showExchange">
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
        </q-tab-panel>

        <q-tab-panel :name="2" class="q-pa-none">
            <q-markup-table flat bordered dense >
                <thead>
                <tr>
                    <th class="text-left">#</th>
                    <th class="text-left">Дата создания</th>
                    <th class="text-left">ФИО владельца</th>
                    <th class="text-left">ИИН/БИН владельца</th>
                    <th class="text-left"></th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="item in exchanges">
                    <td>
                        <router-link :to="'/exchange/'+item.id" class="text-primary">
                            <q-icon name="open_in_new"/>
                            {{ item.certificate ? item.certificate.id : '-' }}
                        </router-link>
                    </td>
                    <td>{{ item.created }}</td>
                    <td>{{ item.certificate ? item.certificate.title_1 : '' }}</td>
                    <td>{{ item.certificate ? item.certificate.idnum_1 : '' }}</td>
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
        </q-tab-panel>

    </q-tab-panels>

</template>

<script>
import {generateCertificate, getCertificateList} from "../../services/certificate";
import FileDownload from "js-file-download";
import {getExchangeList, storeExchange} from "../../services/exchange";
import {Notify} from "quasar";

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
                this.items = res
                this.show = true
            })
            getExchangeList().then(res => {
                this.exchanges = res.items
                this.show = true
            })
        },

        downloadCert(id) {
            // secureData().then(res => {
            //     if(res){
            this.loading = true
            // validUser().then(() => {
            generateCertificate(id, {responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'certificate.pdf')
            }).finally(() => {
                this.loading = false
            })
            // }).catch(() => {
            //     this.loading = false
            // })
            //     }
            // })
        },

        exchangeCert(id) {
            this.loading2 = true
            storeExchange({certificateId: id}).then((res) => {
                if(res && res.data && res.data.id) {
                    this.$router.push('/exchange/' + res.data.id)
                }
                Notify.create({
                    message: res.message,
                    position: 'bottom-right',
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

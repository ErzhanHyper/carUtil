<template>
    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Предложение</div>
    </div>

    <q-markup-table flat dense wrap-cells bordered>
        <thead>
        <tr>
            <th class="text-left">ФИО</th>
            <th class="text-left">ИИН</th>
            <th class="text-left">Регион</th>
            <th class="text-left">Телефон</th>
            <th class="text-left">Email</th>
            <th class="text-left">Сумма</th>
            <th class="text-left">Дата</th>
            <th class="text-left"></th>
        </tr>
        </thead>
        <tbody>
        <template v-if="items.length > 0">
        <tr v-for="(item, i) in items" :key="i">
            <td>
                <span class="text-subtitle2">
                    {{ item.client.title }}
                </span>
            </td>
            <td>{{ item.client.idnum }}</td>
            <td>{{ item.client.region ? item.client.region.title : '-' }}</td>
            <td>{{ item.client.phone }}</td>
            <td>{{ item.client.email ? item.client.email : '-' }}</td>

            <td><span class="text-subtitle2">{{ item.amount }} &#8376;</span></td>
            <td>{{ item.date }}</td>
            <td class="text-right">
                <div class="q-gutter-sm">
                    <q-chip color="positive" dark square style="top: 2px" v-if="item.signed" label="Подписано"/>
                    <q-btn label="Выбрать" outline color="indigo-8" size="11px" icon="add" @click="acceptTransfer(item.id)" :loading="loading1" v-if="data.canAccept"/>
                    <q-btn icon="gesture" label="Подписать сделку" color="indigo-8" size="sm" @click="signDialog = true" v-if="item.canSign"/>
                    <q-btn icon="close" label="Отменить предложение" color="pink-5" size="sm" @click="closeDeal(item.id)" v-if="item.canClose" :loading="loading2"/>
                </div>

                <!--                <q-chip dark color="green-5" label="Сделка завершена" v-if="item.transfer_order.closed === 2"/>-->
<!--                <template v-if="item.transfer_order && item.transfer_order.transfer_deal_id === item.id">-->
<!--                    <div class="q-gutter-sm">-->
<!--                        <q-btn label="Подписать сделку" color="indigo-8" size="sm"-->
<!--                               @click="signShow(item.transfer_order_id)" v-if="!item.signed"/>-->
<!--                        <q-btn flat unelevated color="blue-10" label="Подписана"-->
<!--                               class="no-pointer-events text-weight-bold" v-if="item.signed"/>-->
<!--                        <q-btn label="Отменить сделку" color="negative" size="sm" @click="closeDeal(item.id)"-->
<!--                               v-if="item.transfer_order.closed !== 2" :loading="loading2"/>-->
<!--                    </div>-->
<!--                </template>-->

            </td>
        </tr>
        </template>

        <template v-else><div class="q-ma-xs">Пока предложений нет</div></template>

        </tbody>
    </q-markup-table>

    <q-dialog v-model="signDialog">
        <q-card style="width: 100%;max-width: 960px;" class="q-pa-none">
            <transfer-term :id="id"/>
            <q-card-actions align="right">
                <q-btn label="Подписать ЭЦП" icon="gesture" color="indigo-8" @click="signTransfer(transfer_id)"
                       :loading="loading"/>
            </q-card-actions>
        </q-card>
    </q-dialog>


</template>

<script>

import {acceptTransferDeal, getTransferDealList, closeTransferDeal, signTransferOrder} from "../../services/transfer";
import {signData} from "../../services/sign";
import TransferTerm from "./TransferTerm.vue";
import {Notify} from "quasar";

export default {
    components: {TransferTerm},
    props: ['id', 'data'],

    data() {
        return {
            items: [],
            show: false,
            signDialog: false,
            transfer_id: null,
            loading: false,
            loading1: false,
            loading2: false,
            select: false,
        }
    },

    methods: {

        getData() {
            this.$emitter.emit('contentLoaded', true);
            this.show = false
            getTransferDealList({params: {transfer_order_id: this.id}}).then(res => {
                if(res && res.items) {
                    this.items = res.items
                }
                this.show = true
            })
        },

        acceptTransfer(id) {
            this.loading1 = true
            acceptTransferDeal(id).then(res => {
                this.getData()
                if (res && res.message !== '') {
                    Notify.create({
                        message: res.message,
                        position: 'bottom-right',
                        type: res.success === true ? 'positive' : 'warning'
                    })
                }
                this.$emitter.emit('TransferDealEvent');
            }).finally(() => {
                this.loading1 = false
            })
        },

        signTransfer() {
            signData().then(res => {
                this.loading = true
                signTransferOrder(this.id, {
                    sign: res,
                }).then(res => {
                    if (res && res.message !== '') {
                        Notify.create({
                            message: res.message,
                            position: 'bottom-right',
                            type: res.success === true ? 'positive' : 'warning'
                        })
                    }
                    this.getData()
                    this.$emitter.emit('TransferDealEvent');
                }).finally(() => {
                    this.loading = false
                    this.signDialog = false
                })
            })
        },

        closeDeal(id) {
            this.loading2 = true
            closeTransferDeal(id).then(() => {
                this.getData()
                this.showAccept = true
                this.$emitter.emit('TransferDealEvent');
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

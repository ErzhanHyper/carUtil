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
                <q-badge color="blue-8" class="q-pa-xs q-mr-sm" dark square style="top: 2px" v-if="item.ownerSigned && !item.receiverSigned" label="В ожидании подписи получателя"/>
                <q-badge color="positive" class="q-pa-xs q-mr-sm" v-if="item.ownerSigned" label="Подписано (Владелец)"></q-badge>
                <q-badge color="positive" class="q-pa-xs q-mr-sm" dark square style="top: 2px" v-if="item.receiverSigned" label="Подписано (Получатель)"/>

                <q-btn label="Выбрать" class="q-mr-sm" outline color="indigo-8" size="11px" icon="add" @click="acceptTransfer(item.id)" :loading="loading1" v-if="data.canAccept"/>
                <q-btn icon="close" class="q-mr-sm" color="pink-5" size="sm" @click="closeDeal(item.id)" v-if="item.canClose" :loading="loading2">
                    <q-tooltip class="bg-indigo" :offset="[10, 10]">
                        Отменить выбор
                    </q-tooltip>
                </q-btn>
            </td>
        </tr>
        </template>

        <template v-else><div class="q-ma-xs">Пока предложений нет</div></template>

        </tbody>
    </q-markup-table>

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
                this.$emitter.emit('contentLoaded', false);
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
                        position: 'bottom',
                        type: res.success === true ? 'positive' : 'warning'
                    })
                }
                this.$emitter.emit('TransferDealEvent');
            }).finally(() => {
                this.loading1 = false
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

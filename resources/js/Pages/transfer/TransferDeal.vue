<template>
    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Предложения по сделке</div>
    </div>

    <q-markup-table flat bordered v-if="show && items.length > 0">
        <thead>
        <tr>
            <th class="text-left">ФИО</th>
            <th class="text-left">ИИН</th>
            <th class="text-left">Сумма</th>
            <th class="text-left">Дата</th>
            <th class="text-left">Статус</th>
            <th class="text-left"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(item, i) in items" :key="i">
            <td>
                <span class="text-subtitle2">
                    {{ (item.liner) ? item.liner.profile.fln : '' }}
                </span>
            </td>
            <td>{{ item.liner.idnum }}</td>
            <td><span class="text-subtitle2">{{ item.amount }} &#8376;</span></td>
            <td>{{ item.date }}</td>
            <td>
                <template v-if="item.transfer_order && item.transfer_order.transfer_deal_id === item.id">
                    <q-chip dark color="green-5" label="Сделка выбрана" v-if="item.transfer_order.closed !== 2"/>
                    <q-chip dark color="green-5" label="Сделка завершена" v-if="item.transfer_order.closed === 2"/>
                </template>
            </td>
            <td class="text-right">
                <q-btn label="Принять сделку" outline color="indigo-8" size="11px" @click="acceptTransfer(item.id)"
                       v-if="showAccept"/>
                <template v-if="item.transfer_order && item.transfer_order.transfer_deal_id === item.id">
                    <div class="q-gutter-sm">
                        <q-btn label="Подписать сделку" color="indigo-8" size="sm" @click="signTransfer(item.transfer_order_id)" v-if="!item.signed"/>
                        <q-btn flat unelevated  color="blue-10" label="Подписана" class="no-pointer-events text-weight-bold" v-if="item.signed"/>
                        <q-btn label="Отменить сделку" color="negative" size="sm" @click="closeDeal(item.id)" v-if="item.transfer_order.closed !== 2"/>
                    </div>
                </template>
            </td>
        </tr>
        </tbody>
    </q-markup-table>

    <template v-else>Нет предложении</template>

</template>

<script>

import {acceptTransferDeal, getTransferDealList, closeTransferDeal, signTransferOrder} from "../../services/transfer";
import {signData} from "../../services/sign";

export default {
    props: ['id'],

    data() {
        return {
            items: [],
            showAccept: true,
            show: false
        }
    },

    methods: {

        getData() {
            this.show = false
            getTransferDealList({
                transfer_order_id: this.id
            }).then(res => {
                this.items = res
                res.map(el => {
                    if (el.showAccept === false) {
                        this.showAccept = false
                    }
                })
                this.show = true
            })
        },

        acceptTransfer(id) {
            acceptTransferDeal(id).then(res => {
                this.getData()
            })
        },

        signTransfer(id){
            signData().then(res => {
                signTransferOrder({
                    sign: res,
                    transfer_order_id: id
                }).then(res => {
                    this.getData()
                })
            })
        },

        closeDeal(id) {
            closeTransferDeal(id).then(() => {
                this.getData()
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

<template>

    <div class="q-gutter-sm text-right" v-if="show">
        <q-btn v-if="item.canSign" class="q-mt-sm" color="indigo-8" icon="gesture" label="Подписать"
               size="12px" @click="showSignDialog"/>

        <q-btn v-if="item.isOwner && item.closed === 0" :to="'/preorder/'+item.order.preorder_id" class="q-mt-sm" color="blue-grey-5"
               icon="open_in_new" label="Перейти к заявке" size="11px"/>

        <q-btn v-if="item.isOwner && item.closed !== 2" :loading="loading2" color="negative"
               icon="close" label="Отменить продажу ТС/СХТ" size="11px"
               @click="removeTransfer"/>
        <q-btn v-if="!item.isOwner && item.closed !== 2 && deal_id" :loading="loading2" color="negative"
               icon="close" label="Отменить предложение"
               size="11px" @click="removeTransferDeal"/>
    </div>

    <transfer-deal v-if="show && item.isOwner" :id="item.id" :data="item"/>

    <template v-if="!item.canDeal">
        <div class="q-mt-md flex justify-between">
            <div class="q-gutter-sm">
                <q-chip v-if="!item.canDeal && item.canSign" class="text-body1" color="blue-1" square>Предложение
                    выбрана
                </q-chip>
                <q-chip v-if="!item.canDeal && item.canSign" class="text-body1" color="blue-1" square>В ожидании
                    подписи
                </q-chip>
            </div>

            <div class="q-gutter-sm">

                <q-btn v-if="item.closed === 2" :loading="loading" class="q-mt-sm q-mr-md" color="deep-orange-10"
                       icon="picture_as_pdf"
                       label="Скачать договор" size="12px" @click="downloadPFS"/>

                <q-btn v-if="item.closed === 2" :to="'/preorder/'+item.order.preorder_id" class="q-mt-sm" color="blue-grey-5"
                       icon="open_in_new" label="Перейти к заявке" size="11px"/>
            </div>

        </div>
    </template>

    <div class="q-mt-lg">
        <div v-if="show && ((item.isOwner && item.closed === 1) || !item.isOwner)" class="row q-col-gutter-md">

            <div class="col col-md-4">

                <q-card v-if="item.isOwner" class="q-mt-md q-mb-md">
                    <q-card-section class="q-gutter-md">
                        <q-input v-model="item.order_id" :readonly="true" dense label="№ заявки" outlined/>
                        <q-input v-model="item.order.created" :readonly="true" dense label="Дата одобрение"
                                 outlined/>
                        <q-input v-model="item.order.client.title" :readonly="true" dense label="ФИО владельца"
                                 outlined/>
                        <q-input v-model="item.order.client.idnum" :readonly="true" dense label="ИИН владельца"
                                 outlined/>
                    </q-card-section>
                </q-card>

                <q-banner v-if="showError" class="q-mb-sm bg-orange-3 q-mt-md">
                    <div v-for="error in errors">
                        <span v-for="e in error">{{ e }}</span>
                    </div>
                </q-banner>

                <client-card v-if="!item.isOwner" :blocked="blocked" :data="item.currentClient" :getClient="getClient"
                             class="q-mb-lg" label="Заполните ваши данные"/>

                <template v-if="!item.isOwner">

                    <q-input v-model="amount" :readonly="!item.canDeal" class="text-body1 text-weight-bold"
                             fill-mask=""
                             input-class="text-right"
                             label="Сумма"
                             mask="#"
                             reverse-fill-mask
                    >
                        <template v-slot:after>
                            &#8376;
                        </template>
                    </q-input>

                    <q-btn v-if="item.canDeal" :loading="loading3" class="text-weight-bold"
                           color="indigo-8" icon="swap_horiz" label="Добавить предложение" push @click="sendData"/>
                </template>

            </div>

            <div class="col col-md-4">
                <q-card v-if="!item.isOwner" class="q-mt-md q-mb-md">
                    <q-card-section class="q-gutter-md">
                        <q-input v-model="item.order_id" :readonly="true" dense label="№ заявки" outlined/>
                        <q-input v-model="item.order.created" :readonly="true" dense label="Дата одобрение"
                                 outlined/>
                        <q-input v-model="item.order.client.title" :readonly="true" dense label="ФИО владельца"
                                 outlined/>
                        <q-input v-model="item.order.client.idnum" :readonly="true" dense label="ИИН владельца"
                                 outlined/>
                    </q-card-section>
                </q-card>

                <car-card :blocked="true" :data="item.order.car" :order_id="item.order.id"
                          :vehicleType="item.vehicleType"/>
            </div>

            <div class="col col-md-4">
                <preorder-file :blocked="true" :onlyPhoto="true" :preorder_id="item.preorder_id"
                               :vehicleType="item.vehicleType"/>
            </div>
        </div>

    </div>

    <transfer-term v-if="show" :id="item.id" :contract="contract" :canGenerate="item.isOwner"/>


</template>

<script>
import {Notify} from "quasar";
import FileDownload from "js-file-download";
import {closeTransfer, deleteTransferDeal, getTransferById, storeTransferDeal} from "../../services/transfer";

import {getTransferContract} from "../../services/document";

import PreorderFile from "../preorder/PreorderFile.vue";
import TransferDeal from "./TransferDeal.vue";
import ClientCard from "@/Pages/client/ClientCard.vue";
import TransferTerm from "./TransferTerm.vue";
import CarCard from "../car/CarCard.vue";

export default {

    props: ['id'],
    components: {TransferDeal, PreorderFile, CarCard, ClientCard, TransferTerm},

    data() {
        return {
            deal_id: null,
            show: false,
            amount: null,
            loading: false,
            loading1: false,
            loading2: false,
            loading3: false,

            showError: false,
            blocked: false,
            signDialog: false,

            errors: [],
            contract: {},
            item: {
                currentClient: {},
                signAccess: false,
                isOwner: false,
                canDeal: true,
                deals: [],
                file: {},
                client: {
                    title: ''
                },
                order: {
                    client: {},
                    car: {}
                }
            }
        }
    },

    methods: {

        showSignDialog(){
            this.$emitter.emit('transferSignDialog', true);
        },

        getClient(value) {
            this.item.client = value
        },

        async getData() {
            this.$emitter.emit('contentLoaded', true);
            this.show = false
            await getTransferById(this.id).then(res => {
                this.$emitter.emit('contentLoaded', false);
                this.contract = res.contract
                let itemData = res.item
                if (itemData) {
                    if (itemData.deal) {
                        this.deal_id = itemData.deal.id
                    }
                    this.amount = itemData.amount
                    this.item = itemData
                    this.blocked = itemData.blocked
                }
                this.show = true
            })
        },

        sendData() {
            this.showError = false
            this.$emitter.emit('ClientCardEvent')
            this.loading3 = true
            storeTransferDeal({
                client: this.item.client,
                transfer_order_id: this.item.id,
                amount: this.amount
            }).then(res => {
                if (res && res.message !== '') {
                    Notify.create({
                        message: res.message,
                        position: 'bottom',
                        type: res.success === true ? 'positive' : 'warning'
                    })
                }

                if (res && res.success === true) {
                    this.blocked = true
                    this.getData()
                }
            }).catch(err => {
                this.errors = JSON.parse(err.message)
                this.showError = true
            }).finally(() => {
                this.loading3 = false
            })
        },


        downloadPFS() {
            this.loading = true
            getTransferContract(this.item.id, {responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'transfer_contract.pdf')
            }).finally(() => {
                this.loading = false
            })
        },

        removeTransfer() {
            this.loading2 = true
            closeTransfer(this.item.id).then((res) => {
                this.$router.push('/preorder')
                if (res && res.message !== '') {
                    Notify.create({
                        message: res.message,
                        position: 'bottom',
                        type: res.success === true ? 'primary' : 'warning'
                    })
                }
            }).finally(() => {
                this.loading2 = false
            })
        },
        removeTransferDeal() {
            this.loading2 = true
            deleteTransferDeal(this.deal_id).then(res => {
                if (res && res.message !== '') {
                    Notify.create({
                        message: res.message,
                        position: 'bottom',
                        type: res.success === true ? 'primary' : 'warning'
                    })
                    this.$router.push('/transfer/order')
                }
            }).finally(() => {
                this.loading = false
            })
        }
    },

    created() {
        this.getData()
    },

    mounted() {
        this.$emitter.on('TransferDealEvent', () => {
            this.getData()
        })
    }
}
</script>

<style scoped>

</style>

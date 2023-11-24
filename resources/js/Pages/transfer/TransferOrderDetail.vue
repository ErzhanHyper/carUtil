<template>

    <div class="q-gutter-sm text-right">
        <q-btn label="Перейти к заявке" color="blue-grey-5" size="11px" class="q-mt-sm"
               icon="open_in_new" :to="'/preorder/'+item.order.preorder_id" v-if="item.isOwner && item.closed === 0"/>

        <q-btn icon="close" color="negative" size="11px"
               label="Отменить продажу ТС/СХТ" @click="removeTransfer" v-if="item.isOwner && item.closed !== 2" :loading="loading2"/>
        <q-btn icon="close" color="negative" size="11px"
               label="Отменить предложение" @click="removeTransferDeal" v-if="!item.isOwner && item.closed !== 2 && deal_id" :loading="loading2" />
    </div>

    <transfer-deal :id="item.id" :data="item" v-if="show && item.isOwner" />

    <template v-if="!item.canDeal">
        <div class="q-mt-md flex justify-between">
            <div class="q-gutter-sm">
                <q-chip class="text-body1" color="blue-1" square v-if="!item.canDeal && item.canSign">Предложение выбрана</q-chip>
                <q-chip class="text-body1" color="blue-1" square v-if="!item.canDeal && item.canSign">В ожидании подписи</q-chip>
            </div>

            <div class="q-gutter-sm">
                <q-btn label="Подписать" color="indigo-8" size="12px" class="q-mt-sm" icon="gesture"
                       @click="signDialog = true" v-if="item.canSign"/>

                <q-btn label="Скачать договор" color="deep-orange-10" size="12px" class="q-mt-sm q-mr-md"
                       icon="picture_as_pdf"
                       @click="downloadPFS" :loading="loading" v-if="item.closed === 2"/>

                <q-btn label="Перейти к заявке" color="blue-grey-5" size="11px" class="q-mt-sm"
                       icon="open_in_new" :to="'/preorder/'+item.order.preorder_id" v-if="item.closed === 2"/>
            </div>

        </div>
    </template>

    <div class="q-mt-lg">
        <div class="row q-col-gutter-md" v-if="show && ((item.isOwner && item.closed === 0 || item.closed === 1) || !item.isOwner)">

            <div class="col col-md-4">

                <q-card class="q-mt-md q-mb-md" v-if="item.isOwner">
                    <q-card-section class="q-gutter-md">
                        <q-input label="№ заявки" v-model="item.order_id" :readonly="true" outlined dense/>
                        <q-input label="Дата одобрение" v-model="item.order.created" :readonly="true" outlined
                                 dense/>
                        <q-input label="ФИО владельца" v-model="item.order.client.title" :readonly="true" outlined
                                 dense/>
                        <q-input label="ИИН владельца" v-model="item.order.client.idnum" :readonly="true" outlined
                                 dense/>
                    </q-card-section>
                </q-card>

                <q-banner class="q-mb-sm bg-orange-3 q-mt-md" v-if="showError">
                    <div v-for="error in errors">
                        <span v-for="e in error">{{ e }}</span>
                    </div>
                </q-banner>

                <client-card :data="item.currentClient" :blocked="blocked" :getClient="getClient" class="q-mb-lg" v-if="!item.isOwner" label="Заполните ваши данные"/>

                <template v-if="!item.isOwner">

                    <q-input label="Сумма" v-model="amount" class="text-body1 text-weight-bold"
                             mask="#"
                             fill-mask=""
                             reverse-fill-mask
                             input-class="text-right"
                             :readonly="!item.canDeal"
                    >
                        <template v-slot:after>
                            &#8376;
                        </template>
                    </q-input>

                    <q-btn label="Добавить предложение" icon="swap_horiz" color="indigo-8"
                           class="text-weight-bold" push @click="sendData" :loading="loading3" v-if="item.canDeal"/>
                </template>

            </div>

            <div class="col col-md-4">
                <q-card class="q-mt-md q-mb-md" v-if="!item.isOwner">
                    <q-card-section class="q-gutter-md">
                        <q-input label="№ заявки" v-model="item.order_id" :readonly="true" outlined dense/>
                        <q-input label="Дата одобрение" v-model="item.order.created" :readonly="true" outlined
                                 dense/>
                        <q-input label="ФИО владельца" v-model="item.order.client.title" :readonly="true" outlined
                                 dense/>
                        <q-input label="ИИН владельца" v-model="item.order.client.idnum" :readonly="true" outlined
                                 dense/>
                    </q-card-section>
                </q-card>

                <car-card :data="item.order.car" :order_id="item.order.id" :vehicleType="item.vehicleType" :blocked="true"/>
            </div>

            <div class="col col-md-4">
                <preorder-file :preorder_id="item.preorder_id" :blocked="true" :onlyPhoto="true" :vehicleType="item.vehicleType"/>
            </div>
        </div>

    </div>


    <q-dialog v-model="signDialog">
        <q-card style="width: 100%;max-width: 960px;">
            <transfer-term :id="item.id"/>
            <q-card-actions align="right">
                <q-btn label="Подписать" icon="gesture" color="indigo-8" @click="signTransfer()"
                       :loading="loading1"/>
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>

<script>
import {Notify} from "quasar";
import FileDownload from "js-file-download";
import {
    closeTransfer,
    deleteTransferDeal,
    getTransferById,
    signTransferOrder,
    storeTransferDeal
} from "../../services/transfer";

import {signData} from "../../services/sign";
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

        getClient(value) {
            this.item.client = value
        },

        getData() {
            this.$emitter.emit('contentLoaded', true);
            this.show = false
            getTransferById(this.id).then(res => {
                this.$emitter.emit('contentLoaded', false);
                if(res) {
                    if(res.deal) {
                        this.deal_id = res.deal.id
                    }
                    this.amount = res.amount
                    this.item = res
                    this.blocked = res.blocked
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

                if(res && res.success === true){
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

        signTransfer() {
            signData().then(res => {
                if(res) {
                    this.loading1 = true
                    signTransferOrder(this.item.id, {
                        sign: res,
                    }).then((res) => {
                        if(res){
                            if (res.message !== '') {
                                Notify.create({
                                    message: res.message,
                                    position: 'bottom',
                                    type: res.success === true ? 'positive' : 'warning'
                                })
                            }
                            if (res.success === true) {
                                this.signDialog = false
                                this.getData()
                                this.$emitter.emit('TransferDealEvent');
                            }
                        }
                    }).finally(() => {
                        this.loading1 = false
                    })
                }
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
                if(res && res.message !== '') {
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
        removeTransferDeal(){
            this.loading2 = true
            deleteTransferDeal(this.deal_id).then(res => {
                if(res && res.message !== '') {
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

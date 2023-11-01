<template>

    <transfer-deal :id="item.id" v-if="show && item.isOwner"/>

    <template v-if="item.dealExist">
        <div class="q-mx-md q-mt-md flex justify-between">
            <div>
                <q-chip class="q-mt-md text-body1" color="blue-1" square v-if="!item.deal">Сделка открыта</q-chip>
                <q-chip class="q-mt-md text-body1" color="green-1" square
                        v-if="item.closed === 1 && item.deal && !item.deal.signed">Сделка
                    выбрана
                </q-chip>
                <q-chip class="q-mt-md text-body1" color="positive" dark square v-if="item.closed === 2">
                    Сделка завершена
                </q-chip>
            </div>

            <div v-if="item.deal">
                <template v-if="item.closed === 1 && item.signAccess && !item.deal.signed">
                    <q-btn label="Подписать сделку" color="indigo-8" size="12px" class="q-mt-sm" icon="gesture"
                           @click="signDialog = true" :loading="loading1"/>
                </template>
                <template v-if="item.closed === 2">
                    <q-btn label="Скачать договор" color="deep-orange-10" size="12px" class="q-mt-sm q-mr-md"
                           icon="picture_as_pdf"
                           @click="downloadPFS" :loading="loading"/>

                    <q-btn label="Перейти к заявке" color="blue-grey-5" size="12px" class="q-mt-sm"
                           icon="open_in_new" :to="'/preorder/'+item.order.preorder_id"/>
                </template>
            </div>

        </div>
    </template>

    <div class="q-mt-lg">
        <div class="row q-col-gutter-md" v-if="show">

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

                <client-card :data="item.client" :blocked="blocked" :getClient="getClient" class="q-mb-lg" v-if="!item.isOwner"/>

                <template v-if="!item.dealExist && !item.isOwner">

                    <q-input label="Сумма сделки" v-model="amount" class="text-body1 text-weight-bold"
                             mask="#"
                             fill-mask=""
                             reverse-fill-mask
                             input-class="text-right"
                    >
                        <template v-slot:after>
                            &#8376;
                        </template>
                    </q-input>

                    <q-btn label="Участвовать в сделке" icon="swap_horiz" color="indigo-8"
                           class="text-weight-bold" push @click="sendData"/>
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

                <car-card :data="item.order.car" :order_id="item.order.id" :categories="item.order.categories" :recycleType="recycleType"
                          :blocked="true"/>
            </div>

            <div class="col col-md-4">
                <preorder-file :data="item.file" :files="item.order.files" :blocked="true" :onlyPhoto="true" :recycleType="recycleType"/>
            </div>
        </div>

    </div>


    <div class="q-gutter-sm q-mb-sm text-right q-mt-lg">
        <q-btn icon="close" color="negative" size="sm"
               label="Отменить продажу ТС" @click="removeTransfer" v-if="item.isOwner && item.closed !== 2"/>
    </div>


    <q-dialog v-model="signDialog">
        <q-card style="width: 100%;max-width: 800px;">
            <transfer-term />
            <q-card-actions align="right">
                <q-btn label="Подписать" icon="gesture" color="indigo-8" @click="signTransfer()"
                       :loading="loading"/>
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>

<script>
import CarCard from "../car/CarCard.vue";
import {
    closeTransfer,
    getTransferItem,
    signTransferOrder,
    storeTransferDeal
} from "../../services/transfer";
import {generateOrderPFS} from "../../services/file";
import {signData} from "../../services/sign";

import {Notify} from "quasar";
import FileDownload from "js-file-download";

import PreorderFile from "../preorder/PreorderFile.vue";
import TransferDeal from "./TransferDeal.vue";
import ClientCard from "@/Pages/client/ClientCard.vue";
import TransferTerm from "./TransferTerm.vue";

export default {

    props: ['id'],
    components: {TransferDeal, PreorderFile, CarCard, ClientCard, TransferTerm},

    data() {
        return {
            show: false,
            amount: null,
            loading: false,
            loading1: false,
            showError: false,
            blocked: false,
            recycleType: null,
            signDialog: false,

            errors: [],
            item: {
                signAccess: false,
                isOwner: false,
                deals: [],
                file: {},
                order: {
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
            this.show = false
            getTransferItem(this.id).then(res => {
                this.item = res
                this.item.file = {
                    preorder_id: res.order.preorder_id
                }

                this.blocked = res.blocked
                this.recycleType = res.recycle_type
                this.show = true
            })
        },

        sendData() {
            this.showError = false
            this.$emitter.emit('ClientCardEvent')

            storeTransferDeal({
                client: this.item.client,
                transfer_order_id: this.item.id,
                amount: this.amount
            }).then(res => {
                if (res.status) {
                    this.getData()
                    Notify.create({
                        message: 'Сделка отправлена владельцу',
                        position: 'bottom-right',
                        type: 'positive'
                    })
                    this.blocked = true
                } else {
                    Notify.create({
                        message: 'Не заполнены поля',
                        position: 'bottom-right',
                        type: 'warning'
                    })
                }
            }).catch(err => {
                this.errors = JSON.parse(err.message)
                this.showError = true
            })
        },

        signTransfer() {
            signData().then(res => {
                this.loading1 = true
                signTransferOrder({
                    sign: res,
                    transfer_order_id: this.item.id
                }).then(() => {
                    this.getData()
                    this.signDialog = false
                }).finally(() => {
                    this.loading1 = false
                })
            })
        },

        downloadPFS() {
            this.loading = true
            generateOrderPFS(this.item.order_id, {responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'pfs.pdf')
                this.loading = false
            })
        },

        removeTransfer() {
            closeTransfer({
                id: this.item.id
            }).then(() => {
                this.$router.push('/transfer/order')
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

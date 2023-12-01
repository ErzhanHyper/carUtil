<template>
    <div v-if="show">
        <q-banner v-if="user.role === 'liner' && (item.order && item.order.status.id !== 3)"
                  class="q-mb-md bg-indigo-1">
            <div>Обработка заявки до 15 дней с момента отправки на рассмотрение</div>
            <div v-if="item.closedDate !== 0">Осталось дней: <span
                class="text-orange-9 text-weight-bold">{{ item.closedDate }}</span></div>
            <div v-else class="text-pink-8 text-weight-bold">Время истекло</div>
        </q-banner>

        <div class="flex justify-between">
            <div class="q-mb-md">
                <span v-if="item.vehicleType"
                      :class="(item.vehicleType === 'car') ? 'text-teal-9' : 'text-orange-9'"
                      class="text-body1 q-mt-sm">
                        {{ (item.vehicleType === 'car') ? 'ВЭТС' : 'ВЭССХТ' }}
                    </span>
                <span :class="'text-'+setStatusColor(item.status.id)"> - {{ item.status.title }}

                    <template
                        v-if="item.booking && item.order && item.order.status.id === 0 && item.order.blocked === 0">
                        | <q-badge class="q-pa-xs" color="green-5">
                            На отправке
                        </q-badge>
                    </template>

                    <span v-if="item.order && item.order.status.id === 3"
                          class="text-green-8 text-overline"> | Сертификат выдан</span>
                </span>
                <div v-if="item.order" class="text-body1 text-weight-bold text-blue-grey-8">
                    № заявки: {{ item.order.id }}
                </div>

            </div>

            <div>
                <preorder-sell v-if="item.order && !item.booking" :order_id="item.order.id"
                               :show="permissions.transferOrder"
                               :transfer="transfer"/>

                <div class="q-gutter-md">
                    <q-btn v-if="permissions.sendToApprove"
                           :loading="loading"
                           color="blue-8"
                           icon="send" label="Отправить"
                           push
                           size="12px"
                           @click="sendData"
                    >
                    </q-btn>
                    <q-btn v-if="permissions.sendToApprove"
                           :disable="blocked"
                           color="negative" icon="delete"
                           label="Удалить заявку"
                           push
                           size="12px"
                           @click="showDeleteDialog = true"
                    >
                    </q-btn>
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <preorder-actions :preorder_id="item.id" :show="permissions.approveOrder"/>
            <order-kap v-if="user.role === 'moderator'" :blocked="item.status.id !== 1"
                       :data="{vin:item.car.vin, grnz: item.car.grnz, iinbin: item.client.idnum}"
                       :preorder_id="item.id"/>
        </div>

        <template
            v-if="item.comment.length > 0 && item.comment[0].text && user.role === 'liner' && (item.status.id === 0 || item.status.id === 4)">
            <q-banner :class="(item.comment[0].action === 'approve') ? 'bg-green-1' : 'bg-purple-1'"
                      dense style="max-width: 380px">
                {{ item.comment[0].text }}
            </q-banner>
        </template>

        <q-banner v-if="showError" class="q-mb-sm bg-orange-1 q-mt-md">
            <div v-for="error in errors">
                <template v-if="error.length > 0">
                    <span v-for="e in error">{{ e }}</span>
                </template>
                <template v-else>
                    {{ error }}
                </template>
            </div>
        </q-banner>

        <booking v-if="!blockedBooking && item.closedDate !== 0 && showBooking" id="preorder_booking"
                 :blocked="blockedBooking" :data="item.booking" :getBooking="getBooking"
                 :options="bookingDates" :preorder_id="item.id" class="q-mt-md"/>

        <div class="row q-col-gutter-md">
            <div class="col col-md-8 col-sm-12 col-xs-12 ">
                <div class="row q-col-gutter-md q-mt-xs">
                    <div class="col col-md-5 col-sm-12 col-xs-12">
                        <client-card :blocked="blocked" :data="item.client" :getClient="getClient"/>
                        <!--                        <client-proxy :item="item" :blocked="blocked" v-if="user.role === 'liner' || item.proxy"/>-->
                    </div>

                    <div class="col col-md-7 col-sm-12 col-xs-12">
                        <car-card :blocked="blockedCar"
                                  :blockedCustom="blocked"
                                  :data="item.car"
                                  :getCar="getCar"
                                  :preorder_id="item.id"
                                  :vehicleType="item.vehicleType">
                        </car-card>
                    </div>
                </div>

                <preorder-history :comments="item.comment"/>
            </div>
            <div v-show="item.car" class="col col-md-4 col-xs-12">
                <preorder-file
                    :blocked="blocked"
                    :client_id="item.client ? item.client.id : null"
                    :data="item.file"
                    :preorder_id="item.id"
                    :transfer="transfer"
                    :vehicleType="item.vehicleType">
                </preorder-file>
            </div>
        </div>

    </div>

    <q-dialog v-model="showDeleteDialog" size="xs">
        <q-card style="width: 600px">
            <q-card-section class="row items-center q-pb-none">
                <div class="text-body1">Вы действительно хотите удалить заявку?</div>
                <q-space/>
                <q-btn v-close-popup dense flat icon="close" round/>
            </q-card-section>
            <q-card-actions class="q-mt-md q-mx-sm q-mb-sm">
                <q-btn :loading="loading" color="pink-5" label="Да" @click="deleteData"/>
                <q-space/>
                <q-btn v-close-popup color="primary" label="Нет"/>
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>

<script>
import {Notify} from "quasar";
import {mapGetters} from "vuex";
import {deletePreorder, getPreorderById, sendOrder} from "../../services/preorder";

import PreorderFile from "@/Pages/preorder/PreorderFile.vue";
import PreorderActions from "@/Pages/preorder/PreorderAction.vue";
import PreorderTimeline from "./PreorderTimeline.vue";
import PreorderHistory from "./PreorderHistory.vue";
import PreorderSell from "./PreorderSell.vue";

import OrderTimeline from "@/Pages/order/OrderTimeline.vue";

import CarCard from "../car/CarCard.vue";
import Booking from "../booking/BookingCard.vue";
import ClientCard from "../client/ClientCard.vue";
import ClientProxy from "../client/ClientProxy.vue";
import OrderKap from "../order/OrderKap.vue";

export default {
    props: ['id'],

    components: {
        OrderKap,
        PreorderSell,
        ClientProxy,
        PreorderHistory,
        PreorderTimeline,
        Booking,
        CarCard,
        PreorderFile,
        OrderTimeline,
        ClientCard,
        PreorderActions
    },

    data() {
        return {
            showDeleteDialog: false,
            disabled: false,

            show: false,
            showFile: false,
            showBooking: false,

            transferOrder: false,
            blocked: true,
            blockedCar: true,
            blockedBooking: true,

            loading: false,
            showError: false,
            errors: [],
            permissions: {
                sendToApprove: false,
                transferOrder: false,
            },

            transfer: {},
            bookingDates: [],
            item: {
                transferShow: false,
                blockedVideo: false,
                recycle_type: null,
                category: null,
                client: {},
                car: {},
                booking: {},
                file: {},
                files: [],
                factory: null,
                proxy: null,
            },

        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    methods: {

        setStatusColor(id) {
            let color = 'blue-grey-5'
            if (id === 1) {
                color = 'blue-5'
            } else if (id === 2) {
                color = 'green-5'
            } else if (id === 3) {
                color = 'pink-5'
            } else if (id === 4) {
                color = 'orange-5'
            }

            return color;
        },

        getBooking(value) {
            this.item.booking = value
        },

        getClient(value) {
            this.item.client = value
        },

        getCar(value) {
            this.item.car = value
        },

        getData() {
            this.$emitter.emit('contentLoaded', true);
            this.showBooking = false
            getPreorderById(this.id).then(res => {
                this.$emitter.emit('contentLoaded', false);

                this.show = true
                this.transfer = res.transfer
                this.blocked = res.permissions.blocked
                this.blockedCar = res.permissions.blockedCar
                this.blockedBooking = res.permissions.blockedBooking
                this.permissions.transferOrder = res.permissions.transferOrder
                this.permissions.sendToApprove = res.permissions.sendToApprove
                this.permissions.approveOrder = res.permissions.approveOrder
                this.item = res.item
                this.bookingDates = res.bookingDates

                this.showBooking = true
            })
        },

        sendData() {
            this.blocked = true
            this.errors = []
            this.showError = false

            this.$emitter.emit('ClientCardEvent')
            this.$emitter.emit('CarCardEvent')

            if ((this.item.status && this.item.status.id === 0) || this.item.status && this.item.status.id === 4) {
                this.loading = true
                sendOrder(this.id, this.item).then(res => {
                    if (res) {
                        if (res.success) {
                            this.getData()
                            Notify.create({
                                message: res.message,
                                position: 'bottom',
                                type: 'positive'
                            })
                        }
                        if (res.message && res.message !== '' && res.success === false) {
                            if (res.message && res.message !== '') {
                                let messages = JSON.parse(res.message)
                                let messageData = []
                                Object.values(messages).map((el, i) => {
                                    if(i > 0) {
                                        messageData.push('<div></div>*' + el[0])
                                    }
                                });
                                this.showNotify(messageData, messages[0])
                            }
                        }
                    }
                }).catch(reject => {
                    if (reject.message && reject.message !== '') {
                        let messages = JSON.parse(reject.message)
                        let messageData = []
                        Object.values(messages).map((el, i) => {
                                messageData.push('<div></div>*' + el[0])
                        });
                        this.showNotify(messageData, '')
                    }

                }).finally(() => {
                    this.loading = false
                    this.blocked = false
                })
            }
        },

        deleteData() {
            this.loading = true
            deletePreorder(this.id).then(res => {
                this.$router.push('/preorder')
                Notify.create({
                    message: 'Заявка удалена',
                    position: 'bottom',
                    type: 'primary'
                })
            }).finally(() => {
                this.loading = false
            })
        },

        showNotify(messages, title) {
            Notify.create({
                caption: messages,
                message: title ?? '',
                position: 'top-right',
                type: 'info',
                html: true,
                timeout: 20000,
                actions: [{icon: 'close', color: 'white'}]
            })
        }
    },

    created() {
        this.getData()
    },

    mounted() {
        this.$emitter.on('preorderActionEvent', () => {
            this.getData()
        })

        this.$emitter.on('preorderSellEvent', () => {
            this.getData()
        })

        this.$emitter.on('BookingCardEvent', () => {
            this.getData()
        })

        this.$emitter.on('CarCategoryEvent', () => {
            this.$emitter.emit('CarCardEvent')
        })
    }

}
</script>

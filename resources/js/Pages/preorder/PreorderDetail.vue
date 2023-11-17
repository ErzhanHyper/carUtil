<template>
    <div v-if="show">
        <q-banner class="q-mb-md bg-indigo-1" v-if="user.role === 'liner' && (item.order && item.order.status.id !== 3)">
            <div>Обработка заявки до 15 дней с момента одобрения</div>
            <div v-if="item.closedDate !== 0">Осталось дней: <span class="text-orange-9 text-weight-bold" >{{ item.closedDate }}</span></div>
            <div v-else class="text-pink-8 text-weight-bold">Время истекло</div>
        </q-banner>

        <div class="flex justify-between">
            <div class="q-mb-md">
                <span :class="(item.vehicleType === 'car') ? 'text-teal-9' : 'text-orange-9'"
                     class="text-body1 q-mt-sm"
                     v-if="item.vehicleType">
                        {{ (item.vehicleType === 'car') ? 'ВЭТС' : 'ВЭССХТ' }}
                    </span>
                    <span :class="'text-'+setStatusColor(item.status.id)"> - {{ item.status.title }}
                    <span class="text-green-8 text-overline"
                          v-if="item.order && item.order.status.id === 3"> | Сертификат выдан</span>
                </span>
                <div class="text-body1 text-weight-bold text-blue-grey-8" v-if="item.order">
                    № заявки: {{ item.order.id }}
                </div>
            </div>

            <div>
                <preorder-sell :transfer="item.transfer" :order_id="item.order.id" :show="permissions.transferOrder" v-if="item.order && !item.booking"/>

                <div class="q-gutter-md" >
                    <q-btn color="blue-8"
                           label="Отправить"
                           icon="send"
                           push size="12px"
                           @click="sendData"
                           :loading="loading"
                           v-if="permissions.sendToApprove"
                    >
                    </q-btn>
                    <q-btn icon="delete"
                           label="Удалить заявку"
                           push size="12px"
                           color="negative"
                           :disable="blocked"
                           @click="showDeleteDialog = true"
                           v-if="permissions.sendToApprove"
                    >
                    </q-btn>
                </div>
            </div>
            </div>

        <div class="flex justify-between">
            <preorder-actions :preorder_id="item.id" :show="permissions.approveOrder" />
            <order-kap :preorder_id="item.id" :data="{vin:item.car.vin, grnz: item.car.grnz, iinbin: item.client.idnum}" :blocked="item.status.id !== 1" v-if="user.role === 'moderator'"/>
        </div>

        <template v-if="item.comment.length > 0 && item.comment[0].text && user.role === 'liner' && (item.status.id === 0 || item.status.id === 4)">
            <q-banner :class="(item.comment[0].action === 'approve') ? 'bg-green-1' : 'bg-purple-1'"
                      style="max-width: 380px" dense>
                {{ item.comment[0].text }}
            </q-banner>
        </template>

        <q-banner class="q-mb-sm bg-orange-1 q-mt-md" v-if="showError">
            <div v-for="error in errors">
                <template v-if="error.length > 0">
                    <span v-for="e in error">{{ e }}</span>
                </template>
                <template v-else>
                    {{ error }}
                </template>
            </div>
        </q-banner>

        <booking class="q-mt-md" :data="item.booking" :preorder_id="item.id" :getBooking="getBooking" :blocked="blockedBooking" id="preorder_booking" v-if="!blockedBooking && item.closedDate !== 0" />

        <div class="row q-col-gutter-md">
            <div class="col col-md-8 col-sm-12 col-xs-12 ">
                <div class="row q-col-gutter-md q-mt-xs">
                    <div class="col col-md-5 col-sm-12 col-xs-12">
                        <client-card :data="item.client" :getClient="getClient" :blocked="blocked"/>
<!--                        <client-proxy :item="item" :blocked="blocked" v-if="user.role === 'liner' || item.proxy"/>-->
                    </div>

                    <div class="col col-md-7 col-sm-12 col-xs-12">
                        <car-card :data="item.car"
                                  :getCar="getCar"
                                  :blocked="blocked"
                                  :vehicleType="item.vehicleType"
                                  :preorder_id="item.id">
                        </car-card>
                    </div>
                </div>

                <preorder-history :comments="item.comment" v-if="user.role === 'moderator'"/>
            </div>
            <div class="col col-md-4 col-xs-12" v-show="item.car">
                <preorder-file
                    :transfer="item.transfer"
                    :data="item.file"
                    :preorder_id="item.id"
                    :client_id="item.client ? item.client.id : null"
                    :blocked="blocked"
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
                <q-btn icon="close" flat round dense v-close-popup/>
            </q-card-section>
            <q-card-actions class="q-mt-md q-mx-sm q-mb-sm">
                <q-btn label="Да" @click="deleteData" color="pink-5" :loading="loading"/>
                <q-space/>
                <q-btn label="Нет" v-close-popup color="primary"/>
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

            transferOrder: false,
            blocked: true,
            blockedBooking: true,

            loading: false,
            showError: false,
            errors: [],
            permissions: {
                sendToApprove: false,
                transferOrder: false,
            },

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
            getPreorderById(this.id).then(res => {
                this.$emitter.emit('contentLoaded', false);
                this.item = res.item
                this.blocked = res.permissions.blocked
                this.blockedBooking = res.permissions.blockedBooking
                this.permissions.transferOrder = res.permissions.transferOrder
                this.permissions.sendToApprove = res.permissions.sendToApprove
                this.permissions.approveOrder = res.permissions.approveOrder
                this.show = true
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
                    if(res) {
                        if (res.success) {
                            this.getData()
                            Notify.create({
                                message: res.message,
                                position: 'bottom',
                                type: 'positive'
                            })
                        }
                        if ( res.message && res.message !== '' && res.success === false) {
                            this.errors.push(res.message)
                            this.showError = true
                        }
                    }
                }).catch(reject => {
                    this.errors = JSON.parse(reject.message)
                    this.showError = true
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

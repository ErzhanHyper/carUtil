<template>

    <div v-if="showData">
        <q-banner class="q-mb-md bg-indigo-1"
                  v-if="user.role === 'liner' && (item.order && item.order.status.id !== 3)">
            Рассмотрение заявки до 15 дней
        </q-banner>

        <q-banner class="q-mb-sm bg-orange-3 q-mt-md" v-if="showError">
            <div v-for="error in errors">
                <span v-for="e in error">{{ e }}</span>
            </div>
        </q-banner>

        <div class="flex justify-between">
            <div>
            <span :class="(item.recycle_type === 1) ? 'text-teal-9' : 'text-orange-9'" class="text-body1 q-mt-sm">
                {{ (item.recycle_type === 1) ? 'ВЭТС' : 'ВЭССХТ' }}
            </span>
                <span :class="'text-'+setStatusColor(item.status.id)"> - {{ item.status.title }}
                <span class="text-green-8 text-overline"
                      v-if="item.order && item.order.status.id === 3"> | Сертификат выдан</span>
            </span>
                <div class="text-body1 text-weight-bold text-blue-grey-8" v-if="item.order">
                    № заявки: {{ item.order.id }}
                </div>
            </div>

            <template v-if="user.role === 'liner'">
                <preorder-sell :show="item.transferShow" :blocked="blocked" :transfer="item.transfer"
                               :order_id="item.order_id"/>
                <q-btn color="blue-8" label="Отправить" @click="sendData" :loading="loading" v-if="!blocked"></q-btn>
            </template>
        </div>

        <template v-if="user.role && user.role === 'moderator'">
            <preorder-actions v-if="item.status && item.status.id === 1" :id="item.id" :disabled="disabled"/>
        </template>

        <template v-if="item.comment.length > 0 && item.comment[0].text && user.role === 'liner'">
            <q-banner :class="(item.comment[0].action === 'approve') ? 'bg-green-1' : 'bg-purple-1'"
                      style="max-width: 380px" dense>
                {{ item.comment[0].text }}
            </q-banner>
        </template>

        <template v-if="(user.role === 'liner' || (user.role === 'moderator' && item.booking.datetime)) && item.status.id === 2">
            <booking class="q-mt-md" :data="item.booking" :getBooking="getBooking" :blocked="blockedBooking" id="preorder_booking"/>
        </template>

        <div class="row q-col-gutter-md">

            <div class="col col-md-8 col-sm-12 col-xs-12 ">
                <div class="row q-col-gutter-md q-mt-xs">
                    <div class="col col-md-5 col-sm-12 col-xs-12">
                        <client-card :data="item.client" :getClient="getClient" :blocked="blocked"/>
                        <client-proxy :item="item" :blocked="blocked" v-if="user.role === 'liner' || item.proxy"/>
                    </div>

                    <div class="col col-md-7 col-sm-12 col-xs-12">
                        <car-card :data="item.car" :getCar="getCar" :categories="item.categories"
                                  :blocked="blocked"
                                  :recycleType="item.recycle_type"
                                  :order_id="item.id"/>
                    </div>
                </div>

                <preorder-history v-if="user.role === 'moderator'" :comments="item.comment"/>
            </div>

            <div class="col col-md-4 col-xs-12" v-if="showFile">
                <PreorderFile :data="item.file" :files="item.files" :blocked="blocked"
                              :blockedVideo="item.blockedVideo" :recycleType="item.recycle_type"/>
            </div>

        </div>

        <div class="q-mt-md text-right" v-if="user && user.role === 'liner' && item.status.id === 0">
            <q-btn icon="delete" label="Удалить заявку" square size="sm" color="negative"
                   @click="showDeleteDialog = true"/>
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
import {deleteOrder, getOrderItem, sendOrder} from "../../services/preorder";

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


export default {
    props: ['id'],

    components: {
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

            showData: false,
            showFile: false,
            blocked: true,
            blockedBooking: true,

            loading: false,
            showError: false,
            order: null,

            errors: [],

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
            getOrderItem(this.id, {}).then(res => {
                this.item = res

                this.item.file = {
                    client_id: res.client_id,
                    preorder_id: res.id,
                    video: res.video,
                    order_id: res.order_id,
                    order: res.order
                }

                if (!this.item.client) {
                    this.item.client = {
                        idnum: this.user.idnum,
                        title: this.user.profile.fln
                    }
                }

                if (this.item.booking) {
                    this.item.booking.preorder_id = this.item.id
                } else {
                    this.item.booking = {
                        preorder_id: this.item.id
                    }
                }

                this.showFile = true
                this.blocked = this.item.blocked
                this.blockedBooking = this.item.blockedBooking
                this.showData = true

            }).finally(() => {
            })
        },

        sendData() {
            this.errors = []
            this.showError = false
            this.$emitter.emit('ClientCardEvent')
            this.$emitter.emit('CarCardEvent')
            this.$emitter.emit('BookingCardEvent')
            if ((this.item.status && this.item.status.id === 0) || this.item.status && this.item.status.id === 4) {
                this.loading = true
                sendOrder(this.id, this.item).then(res => {
                    this.showData = false
                    if (res.status && res.status === 200) {
                        Notify.create({
                            message: 'Отправлено модератору на рассмотрение',
                            position: 'bottom-right',
                            type: 'positive'
                        })
                        this.getData()
                    }
                }).catch(reject => {
                    this.errors = JSON.parse(reject.error)
                    this.showError = true
                }).finally(() => {
                    this.loading = false
                })
            }
        },

        deleteData() {
            this.loading = true
            deleteOrder({
                preorder_id: this.id,
            }).then(res => {
                this.$router.push('/preorder')
                Notify.create({
                    message: 'Заявка удалена',
                    position: 'bottom-right',
                    type: 'info'
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
    }

}
</script>

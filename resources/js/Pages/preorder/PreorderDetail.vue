<template>
    <div v-if="show">
        <div class="flex justify-between q-mb-md">
            <div>
                <span v-if="item.vehicleType"
                      :class="(item.vehicleType === 'car') ? 'text-teal-9' : 'text-orange-9'"
                      class="text-body1 q-mt-sm">
                        {{ (item.vehicleType === 'car') ? 'ВЭТС' : 'ВЭССХТ' }}
                    </span>
                <div v-if="item.order" class="text-body1 text-weight-bold text-blue-grey-8">
                    № заявки: {{ item.order.id }}
                </div>
            </div>
            <div>
                <preorder-sell-action
                    v-if="item.order && !booking"
                    :order_id="item.order.id"
                    :show="permissions.transferOrder"
                    :transfer="transfer"
                />

                <div class="q-gutter-md">

                    <q-btn v-if="permissions.sendToApprove"
                           :disable="blocked"
                           color="pink-5" icon="delete"
                           label="Удалить заявку"
                           push
                           size="12px"
                           @click="showDeleteDialog = true"
                    >
                    </q-btn>
                </div>
            </div>
        </div>

        <q-banner v-if="((!item.order) || (item.order && item.order.status.id !== 3))" class="bg-orange-1">
            <div>Обработка заявки 15 дней с момента отправки на рассмотрение</div>
            <div
                v-if="((item.status.id > 0 && !item.order) || (item.order && item.order.status.id !== 3))">
                <div v-if="closedDays !== 0">Осталось дней: <span
                    class="text-pink-5">{{ closedDays }}</span></div>
                <div v-else class="text-pink-5">Время истекло</div>
            </div>
        </q-banner>

        <preorder-timeline
            v-if="showTimeline"
            :booking="booking"
            :car="car"
            :client="client"
            :data="{sended_dt: item.sended_dt}"
            :history="item.comment"
            :order_status="item.order ? item.order.status : null"
            :permissions="permissions"
            :preorder_id="item.id"
            :preorder_status="item.status"
            :required="client_required && car_required"
            class="q-mb-md"
        />

        <div class="flex justify-end">
            <order-kap v-if="user.role === 'moderator'" :blocked="item.status.id !== 1"
                       :data="{vin: car.vin, grnz: car.grnz, iinbin: client.idnum}"
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

        <div class="row q-col-gutter-md q-mt-xs">
            <div class="col col-md-8 col-sm-12 col-xs-12 ">
                <div class="row q-col-gutter-md">
                    <div class="col col-md-5 col-sm-12 col-xs-12">
                        <client-card :blocked="blocked" :data="client" :getClient="getClient"/>
                        <!--                        <client-proxy :item="item" :blocked="blocked" v-if="user.role === 'liner' || item.proxy"/>-->
                    </div>

                    <div class="col col-md-7 col-sm-12 col-xs-12">
                        <booking v-if="!blockedBooking && closedDays !== 0 && showBooking" id="preorder_booking"
                                 :blocked="blockedBooking" :data="booking" :getBooking="getBooking"
                                 :options="bookingDates" :preorder_id="item.id" class="q-mb-md"/>

                        <car-card v-show="client_required"
                                  :blocked="blockedCar"
                                  :blockedCustom="blocked"
                                  :data="car"
                                  :getCar="getCar"
                                  :preorder_id="item.id"
                                  :vehicleType="item.vehicleType">
                        </car-card>
                    </div>
                </div>

                <q-list bordered class="q-mt-md">
                    <q-expansion-item
                        class="bg-white"
                        expand-separator
                        icon="history"
                        label="История">
                        <preorder-history :comments="item.comment" class="q-pa-md"/>
                    </q-expansion-item>
                </q-list>

            </div>
            <div
                v-show="(car_required && (item.status.id === 0 || item.status.id === 4)) || item.status.id === 1 || item.status.id === 2 || item.status.id === 3"
                class="col col-md-4 col-xs-12">
                <preorder-file
                    :blocked="blocked"
                    :client_id="client ? client.id : null"
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
import {deletePreorder, getPreorderById} from "../../services/preorder";

import PreorderFile from "./PreorderFile.vue";
import PreorderHistory from "./PreorderHistory.vue";
import PreorderSellAction from "./actions/PreorderSellAction.vue";

import CarCard from "../car/CarCard.vue";
import Booking from "../booking/BookingCard.vue";
import ClientCard from "../client/ClientCard.vue";
import ClientProxy from "../client/ClientProxy.vue";
import OrderKap from "../order/OrderKap.vue";
import OrderTimeline from "@/Pages/order/OrderTimeline.vue";
import PreorderTimeline from "./PreorderTimeline.vue";

export default {
    props: ['id'],

    components: {
        PreorderTimeline,
        PreorderSellAction,
        OrderKap,
        ClientProxy,
        PreorderHistory,
        Booking,
        CarCard,
        PreorderFile,
        OrderTimeline,
        ClientCard,
    },

    data() {
        return {
            showDeleteDialog: false,
            disabled: false,
            client_required: false,
            car_required: false,

            show: false,
            showFile: false,
            showBooking: false,
            showTimeline: false,

            transferOrder: false,
            blocked: true,
            blockedCar: true,
            blockedBooking: true,

            loading: false,
            showError: false,
            eventLoad: false,

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
                file: {},
                files: [],
                factory: null,
                proxy: null,
            },

            client: {},
            car: {},
            booking: {},

            closedDays: 0

        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user'
        }),

        client_required() {
            if (this.client &&
                this.client.ud_num &&
                this.client.ud_expired &&
                this.client.region_id &&
                this.client.address &&
                this.client.phone &&
                this.client.year
            ) {
                return true
            }
        },

        car_required() {
            if (this.car &&
                this.car.category_id &&
                this.car.wheels_count &&
                this.car.doors_count &&
                this.car.wheels_protector_count
            ) {
                return true
            }
        }
    },

    methods: {

        getBooking(value) {
            this.booking = value
        },

        getClient(value) {
            this.client = value
        },

        getCar(value) {
            this.car = value
        },

        getData() {
            if (this.eventLoad === false) {
                this.show = false
                this.$emitter.emit('contentLoaded', true);
            }
            this.showBooking = false
            this.showTimeline = false
            getPreorderById(this.id).then(res => {
                if (this.eventLoad === false) {
                    this.$emitter.emit('contentLoaded', false);
                    this.show = true
                }

                this.showTimeline = true
                this.closedDays = res.closedDays
                this.transfer = res.transfer
                this.blocked = res.permissions.blocked
                this.blockedCar = res.permissions.blockedCar
                this.blockedBooking = res.permissions.blockedBooking
                this.permissions.transferOrder = res.permissions.transferOrder
                this.permissions.sendToApprove = res.permissions.sendToApprove
                this.permissions.approveOrder = res.permissions.approveOrder
                this.bookingDates = res.bookingDates

                this.item = res.item
                this.client = res.item.client
                this.car = res.item.car
                this.booking = res.item.booking

                this.showBooking = true
                this.eventLoad = false

            })
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
            this.eventLoad = true
            this.getData()
        })

        this.$emitter.on('preorderSendActionEvent', (value) => {
            this.eventLoad = true
            this.blocked = false
            this.disabled = false
            if (value) {
                this.blocked = true
                this.disabled = true
            }
        })

        this.$emitter.on('preorderSellEvent', () => {
            this.eventLoad = true
            this.getData()
        })

        this.$emitter.on('BookingCardEvent', () => {
            this.eventLoad = true
            this.getData()
        })

        this.$emitter.on('CarCategoryEvent', () => {
            this.$emitter.emit('CarCardEvent')
        })
    }

}
</script>

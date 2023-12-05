<template>
    <div v-if="showData">

        <div class="flex justify-between q-mb-sm">
            <div>
                <span v-if="item.vehicleType"
                      :class="(item.vehicleType === 'car') ? 'text-teal-9' : 'text-orange-9'"
                      class="text-body1 q-mt-sm">
                    {{ (item.vehicleType === 'car') ? 'ВЭТС' : 'ВЭССХТ' }}
                </span>
                <span class="text-body1 text-blue-grey-7"> <b> - №{{ item.id }}</b></span>
                <!--                <div v-if="item.status.id !== 3" :class="'text-'+setStatusColor(item.approve.id)">-->
                <!--                    {{ item.approve.title }}-->
                <!--                </div>-->
                <!--                <div v-if="item.status.id === 5 || item.status.id === 3"-->
                <!--                     :class="'text-'+setStatusColor(item.status.id)">-->
                <!--                    {{ item.status.title }}-->
                <!--                </div>-->
                <div v-if="item.executor" class="text-caption">
                    Исполнитель: {{ item.executor.title }}
                </div>
                <div v-if="item.user" class="text-caption">
                    Менеджер ТБО: {{ item.user.title }}
                </div>
            </div>

            <div>
                <order-execute-action
                    :order_id="item.id"
                    :permissions="{
                    start: permit.can_execute,
                    stop: permit.can_approve && item.executor
                }"/>
            </div>
        </div>

        <order-timeline v-if="showTimeline" :approve="item.approve" :status="item.status" :permit="permit" :order_id="item.id" :history="item.history" class="q-mb-md"/>

        <div class="flex justify-between q-mx-sm">
            <div class="flex">

            </div>
            <div>
                <q-btn
                    v-if="user.role === 'moderator' && item.executor"
                    class="q-mr-md"
                    color="deep-orange-8"
                    icon="search"
                    label="Проверка дубликатов"
                    size="12px"
                    @click="getDuplicate"/>

                <order-kap
                    v-if="user.role === 'moderator' && item.executor"
                    :blocked="!permit.can_approve"
                    :data="{vin:item.car.vin, grnz: item.car.grnz, iinbin: (item.client ? item.client.idnum : '')}"
                    :order_id="item.id"/>
            </div>
        </div>

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

        <div class="row q-col-gutter-md">

            <div class="col col-md-8 col-sm-12 col-xs-12 ">
                <div class="row q-col-gutter-md q-mt-xs">
                    <div class="col col-md-5 col-sm-12 col-xs-12">
                        <client-card :blocked="true" :data="item.client" :getClient="getClient"/>
                        <!--                        <client-proxy :item="item" :blocked="blocked" v-if="user.role === 'liner' || item.proxy"/>-->
                    </div>
                    <div class="col col-md-7 col-sm-12 col-xs-12">
                        <booking v-if="item.booking" :blocked="true" :data="item.booking" :getBooking="getBooking"
                                 class="q-mb-md"/>
                        <car-card :blocked="true" :blockedCustom="true" :data="item.car" :getCar="getCar"
                                  :vehicleType="item.vehicleType"/>
                    </div>
                </div>
                <order-history :items="item.history"/>
            </div>

            <div class="col col-md-4 col-xs-12">
                <order-document
                    v-if="permit.can_send_to_approve"
                    :category="item.car.category"
                    :order_id="item.id"
                    class="q-mb-md"
                />

                <OrderFile
                    v-if="item.client"
                    :blocked="permit.blockedFile" :blockedVideo="permit.blockedVideo"
                    :certificate_id="(item.status.id === 3 && item.approve.id === 3 && item.car.certificate) ? item.car.certificate.id : null"
                    :client_id="item.client.id"
                    :order_id="item.id"
                    :vehicleType="item.vehicleType"
                />

                <template v-if="user && user.role==='moderator'">
                    <q-separator class="q-my-lg"/>

                    <div class="q-px-md q-mt-lg text-body1 text-weight-bold">Файлы предзаявки</div>

                    <PreorderFile
                        v-if="item.client"
                        :blocked="true"
                        :client_id="item.client.id"
                        :preorder_id="item.preorder_id"
                        :transfer="item.transfer"
                        :vehicleType="item.vehicleType"
                    />
                </template>
            </div>
        </div>

        <order-duplicates v-if="user && user.role === 'moderator'" :order_id="item.id"/>

    </div>

</template>

<script>
import {mapGetters} from "vuex";

import {getOrderItem} from "../../services/order";

import ClientCard from "@/Pages/client/ClientCard.vue";
import ClientProxy from "@/Pages/client/ClientProxy.vue";

import OrderTimeline from "@/Pages/order/OrderTimeline.vue";
import SelectFactory from "@/Components/Fields/SelectFactory.vue";
import CarCard from "@/Pages/car/CarCard.vue";
import Booking from "@/Pages/booking/BookingCard.vue";
import PreorderFile from "@/Pages/preorder/PreorderFile.vue"

import OrderExecuteAction from "./actions/OrderExecuteAction.vue";

import OrderKap from "./OrderKap.vue";
import OrderDuplicates from "./OrderDuplicates.vue";
import OrderHistory from "./OrderHistory.vue";
import OrderDocument from "./OrderDocument.vue";
import OrderFile from "./OrderFile.vue";

export default {
    components: {
        OrderDuplicates,
        OrderKap,
        OrderExecuteAction,
        OrderHistory,
        OrderDocument,
        ClientProxy,
        Booking,
        CarCard,
        OrderFile,
        SelectFactory,
        ClientCard,
        OrderTimeline,
        PreorderFile
    },

    props: ['id'],

    data() {
        return {
            showDeleteDialog: false,
            showDuplicatesDialog: false,
            disabled: true,
            showData: false,
            showTimeline: false,
            showFile: false,
            showProxy: false,
            loading: false,
            blocked: true,
            blockedVideo: true,
            showError: false,
            eventLoad: false,

            order: null,
            signHash: '',

            item: {
                client: {},
                car: {},
                preorder: {}
            },

            permit: [],
            errors: [],
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
            } else if (id === 3) {
                color = 'green-5'
            } else if (id === 2) {
                color = 'pink-5'
            } else if (id === 4) {
                color = 'orange-5'
            }

            return color;
        },

        setProxy() {
            if (this.item.proxy === 1) {
                this.showProxy = false
            }

            if (this.item.proxy === 2) {
                this.showProxy = true
            }
        },

        getBooking(value) {
            if (this.item.booking) {
                this.item.booking = value
            }
        },

        getClient(value) {
            this.item.client = value
        },

        getCar(value) {
            this.item.car = value
        },

        getData() {
            if(!this.eventLoad) {
                this.$emitter.emit('contentLoaded', true);
            }
            this.showTimeline = false
            getOrderItem(this.id, {}).then(res => {
                if(!this.eventLoad) {
                    this.$emitter.emit('contentLoaded', false);
                    this.showData = true
                }
                this.showFile = true
                this.showTimeline = true
                this.item = res.item

                if (res.permissions) {
                    this.permit = res.permissions
                }

            })
        },

        getDuplicate() {
            this.$emitter.emit('duplicateDialogEvent')
        }
    },

    created() {
        this.getData()
    },

    mounted() {
        this.$emitter.on('orderFileEvent', () => {
            this.eventLoad = true
            this.getData()
        })
        this.$emitter.on('orderActionEvent', () => {
            this.eventLoad = true
            this.getData()
        })
        this.$emitter.on('VideoSendEvent', () => {
            this.eventLoad = true
            this.getData()
        })
        this.$emitter.on('orderBlockEvent', (value) => {
            this.blockedVideo = value
        })
    }

}
</script>

<style scoped>

</style>

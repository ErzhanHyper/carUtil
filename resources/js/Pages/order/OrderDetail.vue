<template>
    <div v-if="showData">

        <div class="flex justify-between q-mb-sm">
            <div>
                <span :class="(item.vehicleType === 'car') ? 'text-teal-9' : 'text-orange-9'"
                      class="text-body1 q-mt-sm"
                      v-if="item.vehicleType">
                    {{ (item.vehicleType === 'car') ? 'ВЭТС' : 'ВЭССХТ' }}
                </span>
                <span class="text-body1 text-blue-grey-7"> <b> - №{{ item.id }}</b></span>
                <div :class="'text-'+setStatusColor(item.approve.id)">
                    {{ item.approve.title }}
                </div>
                <div :class="'text-'+setStatusColor(item.status.id)"  v-if="item.status.id === 5">
                    {{ item.status.title }}
                </div>
                <div class="text-caption" v-if="item.executor">
                    Исполнитель: {{ item.executor.title }}
                </div>
                <div class="text-caption" v-if="item.user">
                    Менеджер ТБО: {{ item.user.title }}
                </div>
                <div :class="'text-deep-orange'" v-if="item.approve.id === 3 && item.status.id === 4">
                    В ожидании получения видеозаписи ТС/СХТ
                    <q-tooltip class="bg-indigo text-body2" :offset="[10, 10]" v-if="user.role === 'operator'">
                        Зайдите в мобильное приложение и сделайте видеозапись ТС/СХТ и отправьте видеозапись по номеру заявки
                    </q-tooltip>
                </div>

                <q-btn color="positive"
                       push
                       icon="verified"
                       icon-right="download"
                       label="Скидочный сертификат"
                       @click="getCert(item.car.certificate.id)"
                       class="q-mt-md"
                       :loading="loading"
                       v-if="item.status.id === 3 && item.approve.id === 3"
                />

            </div>

            <order-execute-action :order_id="item.id" :permissions="{start: permissions.showExecuteAction, stop: permissions.showApproveAction && item.executor}"/>
        </div>
        <div class="flex justify-between">
            <div class="flex">
                <order-approve-action :order_id="item.id" :show="permissions.showApproveAction" v-if="user.role === 'moderator'"/>
                <order-cert-action :order_id="item.id" :show="permissions.issueCertAction" class="q-ml-xs q-mr-md"/>
                <order-video-action :order_id="item.id" :permissions="{showVideoSendAction: permissions.showVideoSendAction,showVideoRevision: permissions.showVideoRevisionAction}"/>
            </div>
            <div>
                <q-btn label="Проверка дубликатов" color="deep-orange-8" size="12px" icon="search" class="q-mr-md" @click="showDuplicatesDialog = true" v-if="user.role === 'moderator' && item.executor"/>
                <order-kap :order_id="item.id" :data="{vin:item.car.vin, grnz: item.car.grnz, iinbin: item.client.idnum}" :blocked="!permissions.showApproveAction" v-if="user.role === 'moderator' && item.executor"/>
            </div>
            </div>
        <order-send-action :order_id="item.id"
                           :permissions="{
                    showSendToApproveAction: permissions.showSendToApproveAction,
                    showSendToIssueCertAction:  permissions.showSendToIssueCertAction
        }" class="q-mt-sm"/>

        <div class="row q-col-gutter-md">

            <div class="col col-md-8 col-sm-12 col-xs-12 ">
                <div class="row q-col-gutter-md q-mt-xs">
                    <div class="col col-md-5 col-sm-12 col-xs-12">
                        <client-card :data="item.client" :getClient="getClient" :blocked="true"/>
<!--                        <client-proxy :item="item" :blocked="blocked" v-if="user.role === 'liner' || item.proxy"/>-->
                    </div>
                    <div class="col col-md-7 col-sm-12 col-xs-12">
                        <booking class="q-mb-md" :data="item.booking" :getBooking="getBooking" :blocked="true"/>
                        <car-card :data="item.car" :getCar="getCar" :blocked="true" :vehicleType="item.vehicleType"/>
                    </div>
                </div>
                <order-history :items="item.history"/>
            </div>

            <div class="col col-md-4 col-xs-12">
                <order-document v-if="permissions.showSendToApproveAction" class="q-mb-md" :order_id="item.id"/>
                <OrderFile :order_id="item.id" :client_id="item.client.id" :blocked="blocked" :blockedVideo="blockedVideo" :vehicleType="item.vehicleType"/>

                <template v-if="user && user.role==='moderator'">
                    <q-separator class="q-my-lg"/>
                    <div class="q-px-md q-mt-lg text-body1 text-weight-bold">Файлы предзаявки</div>
                    <PreorderFile :preorder_id="item.preorder_id" :blocked="true" :client_id="item.client.id" :vehicleType="item.vehicleType" :transfer="item.transfer"/>
                </template>
            </div>

        </div>


        <q-dialog v-model="showDuplicatesDialog">
            <q-card style="width: 100%;max-width: 1200px">
                <q-card-section class="flex justify-between q-pa-sm">
                    <q-space/>
                    <q-icon name="close" size="sm" flat v-close-popup class="cursor-pointer"/>
                </q-card-section>
                <q-card-section>
                    <div class="row q-col-gutter-md">
                        <div class="col">
                            <div class="text-body1 text-weight-bold text-blue-grey-8">Проверка 1</div>
                            <div class="q-mb-md">
                            <div>Возможные дубликаты по VIN</div>
                                <div v-for="el in duplicates1">
                                    <template v-if="el.length > 0">
                                        <template v-for="car in el">
                                           <div class="text-weight-bold"> <q-icon name="open_in_new" class="q-mr-xs"/>
                                               <a :href="'/order/'+car.order_id" target="_blank" class="text-primary">{{ car.vin }}</a>
                                           </div>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <div class="text-caption">Нет совпадений</div>
                                    </template>
                                </div>
                            </div>

                            <div class="q-mb-md">
                                <div>Возможные дубликаты по номеру кузова</div>
                                <div v-for="el in body_duplicates1">
                                    <template v-if="el.length > 0">
                                        <template v-for="car in el">
                                            <div class="text-weight-bold"> <q-icon name="open_in_new" class="q-mr-xs"/>
                                                <a :href="'/order/'+car.order_id" target="_blank" class="text-primary">{{ car.body_no }}</a>
                                            </div>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <div class="text-caption">Нет совпадений</div>
                                    </template>
                                </div>
                            </div>

                            <div class="q-mb-md">
                                <div>Возможные дубликаты по номеру шасси</div>
                                <div v-for="el in chassis_duplicates1">
                                    <template v-if="el.length > 0">
                                        <template v-for="car in el">
                                            <div class="text-weight-bold"> <q-icon name="open_in_new" class="q-mr-xs"/>
                                                <a :href="'/order/'+car.order_id" target="_blank" class="text-primary">{{ car.chassis_no }}</a>
                                            </div>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <div class="text-caption">Нет совпадений</div>
                                    </template>
                                </div>
                            </div>


                        </div>
                        <div class="col">
                            <div class="text-body1 text-weight-bold text-blue-grey-8">Проверка 2</div>
                            <div class="q-mb-md">
                            <div>Возможные дубликаты по VIN(в этой машине) -> BODY(другие машине)</div>
                                <div v-for="el in duplicates2">
                                    <template v-if="el.length > 0">
                                        <template v-for="car in el">
                                            <div class="text-weight-bold"> <q-icon name="open_in_new" class="q-mr-xs"/>
                                                <a :href="'/order/'+car.order_id" target="_blank" class="text-primary">{{ car.vin }}</a>
                                            </div>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <div class="text-caption">Нет совпадений</div>
                                    </template>
                                </div>
                            </div>
                            <div class="q-mb-md">
                                <div>Возможные дубликаты по BODY(в этой машине) -> VIN(другие машине)</div>
                                <div v-for="el in body_duplicates2">
                                    <template v-if="el.length > 0">
                                        <template v-for="car in el">
                                            <div class="text-weight-bold"> <q-icon name="open_in_new" class="q-mr-xs"/>
                                                <a :href="'/order/'+car.order_id" target="_blank" class="text-primary">{{ car.body_no }}</a>
                                            </div>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <div class="text-caption">Нет совпадений</div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </q-dialog>

    </div>

</template>

<script>
import {mapGetters} from "vuex";
import FileDownload from "js-file-download";

import { getOrderItem} from "../../services/order";
import {generateCertificate} from "../../services/certificate";

import ClientCard from "@/Pages/client/ClientCard.vue";
import ClientProxy from "../client/ClientProxy.vue";
import OrderFile from "@/Pages/order/OrderFile.vue";
import OrderTimeline from "@/Pages/order/OrderTimeline.vue";
import OrderDocument from "./OrderDocument.vue";
import SelectFactory from "@/Components/Fields/SelectFactory.vue";
import CarCard from "@/Pages/car/CarCard.vue";
import Booking from "@/Pages/booking/BookingCard.vue";
import PreorderFile from "@/Pages/preorder/PreorderFile.vue"
import OrderApproveAction from "./actions/OrderApproveAction.vue";
import OrderHistory from "./OrderHistory.vue";
import OrderExecuteAction from "./actions/OrderExecuteAction.vue";
import OrderSendAction from "./actions/OrderSendAction.vue";
import OrderVideoAction from "./actions/OrderVideoAction.vue";
import OrderCertAction from "./actions/OrderCertAction.vue";
import OrderKap from "./OrderKap.vue";

export default {
    components: {
        OrderKap,
        OrderCertAction,
        OrderVideoAction,
        OrderSendAction,
        OrderExecuteAction,
        OrderHistory,
        OrderApproveAction,
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
            showFile: false,
            showProxy: false,
            loading: false,
            blocked: true,
            blockedVideo: true,

            order: null,
            signHash: '',

            item: {
                client: {},
                car: {},
                preorder: {}
            },

            permissions: {
                blockedFiles: true,
                showSendToApproveAction: false,
                showCertAction: false,
                showApproveAction: false,
                showExecuteAction: false,
                showVideoSendAction: false,
                showVideoRevisionAction: false,
                showSendToIssueCertAction: false,
            },

            duplicates1: [],
            duplicates2: [],
            body_duplicates1: [],
            body_duplicates2: [],
            chassis_duplicates1: []
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
            this.$emitter.emit('contentLoaded', true);
            getOrderItem(this.id, {}).then(res => {
                this.showData = true
                this.showFile = true
                this.item = res.item

                this.duplicates1 = res.duplicates1
                this.duplicates2 = res.duplicates2
                this.body_duplicates1 = res.body_duplicates1
                this.body_duplicates2 = res.body_duplicates2
                this.chassis_duplicates1 = res.chassis_duplicates1

                if(res.permissions) {
                    this.permissions.showApproveAction = res.permissions.approveOrder
                    this.permissions.showExecuteAction = res.permissions.executeOrder
                    this.permissions.issueCertAction = res.permissions.issueCert
                    this.permissions.showSendToApproveAction = res.permissions.sendToApprove
                    this.permissions.videoUploaded = res.permissions.videoUploaded
                    this.permissions.showVideoSendAction = res.permissions.uploadVideo
                    this.permissions.showVideoRevisionAction = res.permissions.revisionVideo
                    this.permissions.showSendToIssueCertAction = res.permissions.sendToIssueCert
                    this.blocked = res.permissions.blocked
                    this.blockedVideo = res.permissions.blockedVideo
                }
            })
        },

        getCert(id) {
            this.loading = true
            generateCertificate(id, {responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'certificate.pdf')
            }).finally(() => {
                this.loading = false
            })
        },
    },

    created() {
        this.getData()
    },

    mounted() {
        this.$emitter.on('orderFileEvent', () => {
            this.getData()
        })
        this.$emitter.on('orderActionEvent', () => {
            this.getData()
        })
        this.$emitter.on('VideoSendEvent', () => {
            this.getData()
        })
    }

}
</script>

<style scoped>

</style>

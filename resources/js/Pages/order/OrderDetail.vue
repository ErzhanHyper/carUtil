<template>
    <div v-if="showData">

        <div class="flex justify-between">
            <div>
                <span :class="(item.recycle_type === 'ВЭТС') ? 'text-teal-9' : 'text-orange-9'"
                      class="text-body1 q-mt-sm">
                    {{ (item.recycle_type) ? item.recycle_type : '-' }}
                </span>

                <span class="text-body1 text-blue-grey-7"> <b> - №{{ item.id }}</b></span>

                <div :class="'text-'+setStatusColor(item.approve.id)">
                    {{ item.approve.title }}
                </div>

                <div :class="'text-'+setStatusColor(item.status.id)"  v-if="item.status.id === 5">
                    {{ item.status.title }}
                </div>

                <div class="text-caption" v-if="item.executor">Исполнитель: {{ item.executor.title }}</div>

                <div :class="'text-deep-orange'" v-if="item.approve.id === 3 && item.status.id === 4">
                    В ожидании получения видеозаписи ТС/СХТ
                    <q-tooltip class="bg-indigo text-body2" :offset="[10, 10]" v-if="user.role === 'operator'">
                        Зайдите в мобильное приложение и сделайте видеозапись ТС/СХТ и отправьте видеозапись по номеру заявки
                    </q-tooltip>
                </div>

                <q-space class="q-my-sm" />
                <div v-if="user.role === 'operator' && item.approve.id === 3 && showCameraBtn && !item.videoUploaded">
                <q-btn label="Отправить видеозапись" color="blue-8" icon="videocam" @click="showCamera = true" />
                </div>
            </div>

            <div class="q-mb-md">
                <q-btn :loading="loading"
                       push
                       size="12px"
                       color="blue-8"
                       label="Взять на исполнение"
                       icon="edit"
                       v-if="showModeratorExecute"
                       @click="executeRun"
                >
                </q-btn>
                <q-btn :loading="loading"
                       push
                       size="12px"
                       color="negative"
                       label="Отменить исполнение"
                       icon="close"
                       v-if="showModeratorAction"
                       @click="executeEnd"
                >
                </q-btn>
            </div>
        </div>

        <!--    <OrderTimeline class="q-mt-md"/>-->

        <div class="col col-md-12 q-mt-md"
             v-if="user.role && (user.role === 'operator' || user.role === 'moderator')">
            <template v-if="item.car && item.car.certificate">
                <div class="text-green-8 text-overline">Сертификат выдан</div>
                <q-btn icon="verified" color="green-6" dense label="Скидочный сертификат"
                       icon-right="download"
                       :loading="loading"
                       @click="getCert(item.car.certificate.id)"></q-btn>
            </template>

            <div class="flex justify-between q-mt-md">
                <div class="q-gutter-sm">
                    <q-btn :loading="loading" square size="12px" color="light-green"
                           label="Отправить на рассмотрение модератору" @click="sendData('send_to_moderator')"
                           icon="send" v-if="showOperatorAction"></q-btn>

                    <q-btn :loading="loading" square size="12px" color="light-green"
                           label="Подписать и отправить модератору" @click="sendData('sign_uploaded_video')"
                           icon="send" v-if="user.role === 'operator' && item.status.id === 4 && item.videoUploaded"></q-btn>
                </div>
            </div>
        </div>

        <order-action :order_id="item.id" :showCertAction="showCertAction" :showApproveAction="showModeratorAction" :data="{grnz: item.car.grnz, vin: item.car.vin, iinbin: item.client.idnum}"/>

        <div class="row q-col-gutter-md">

            <div class="col col-md-8 col-sm-12 col-xs-12 ">
                <div class="row q-col-gutter-md q-mt-xs">
                    <div class="col col-md-5 col-sm-12 col-xs-12">
                        <client-card :data="item.client" :getClient="getClient" :blocked="blocked"/>
                        <client-proxy :item="item" :blocked="blocked" v-if="user.role === 'liner' || item.proxy"/>
                    </div>

                    <div class="col col-md-7 col-sm-12 col-xs-12">
                        <booking class="q-mb-md" :data="item.booking" :getBooking="getBooking" :blocked="true"
                                 v-if="user.role === 'moderator' || user.role === 'operator'"/>
                        <car-card :data="item.car" :getCar="getCar" :categories="item.categories" :blocked="blocked" :recycleType="item.recycle_type === 'ВЭТС' ? 1 : 2"/>
                    </div>
                </div>

                <div class="q-mt-md" v-if="item.history && item.history.length > 0">
                    <div class="text-h5">История изменений</div>
                    <q-timeline color="secondary">
                        <template v-for="(text, i) in item.history">
                            <q-timeline-entry class="q-mb-sm" :body="text.created_at">
                                <q-banner :class="(text.action === 'approve') ? 'bg-green-1' : 'bg-purple-1'"
                                          style="max-width: 380px">
                                    <div class="text-subtitle2 text-weight-bold">
                                        <span v-if="text.action === 'approve'">Одобрена</span>
                                        <span v-if="text.action === 'decline'">Отклонена</span>
                                        <span v-if="text.action === 'revision'">На доработку</span>
                                    </div>
                                    {{ text.comment }}
                                </q-banner>
                            </q-timeline-entry>
                        </template>
                    </q-timeline>

                </div>

            </div>

            <div class="col col-md-4 col-xs-12">
                <order-document v-if="showOperatorAction" class="q-mb-md" :order_id="item.id"/>
                <OrderFile :data="item.file" v-if="showFile" :blocked="blockedFiles"
                           :blockedVideo="item.status.id !== 4 || (item.status.id !== 5 && user.role === 'moderator')" :recycleType="item.recycle_type === 'ВЭТС' ? 1 : 2"/>

                <template v-if="user && user.role==='moderator'">
                    <q-separator class="q-my-lg"/>
                    <div class="q-px-md q-mt-lg text-body1 text-weight-bold">Файлы предзаявки</div>
                    <PreorderFile :data="item.file" v-if="showFile" :blocked="true" />
                </template>
            </div>

        </div>
    </div>


    <q-dialog
        v-model="showCamera"
        persistent
        :maximized="maximizedToggle"
        transition-show="slide-up"
        transition-hide="slide-down"
    >
        <camera-record :id="item.id"/>
    </q-dialog>


</template>

<script>
import {mapGetters} from "vuex";
import FileDownload from "js-file-download";

import {
    executeCloseOrder,
    executeRunOrder,
    getOrderItem,
    sendToApproveOrder,
    sendToSignOrder,
} from "../../services/order";
import {signData} from "../../services/sign";
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
import OrderAction from "./OrderAction.vue";
import CameraRecord from "../../Components/CameraRecord.vue";
import {ref} from "vue";

export default {
    components: {
        CameraRecord,
        OrderAction,
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

    setup () {
        return {
            dialog: ref(false),
            maximizedToggle: ref(true)
        }
    },

    data() {
        return {
            showCameraBtn: false,
            showDeleteDialog: false,
            showCamera: false,
            disabled: true,

            showData: false,
            showFile: false,
            showProxy: false,
            blocked: true,
            blockedFiles: true,
            showOperatorAction: false,
            showCertAction: false,
            showModeratorAction: false,
            showModeratorExecute: false,
            loading: false,

            order: null,

            item: {
                executor_uid: '',
                client: {},
                car: {
                    preorder_id: this.id,
                },
                preorder: {}
            },
            signHash: ''
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
            this.showFile = false
            this.showOperatorAction = false
            this.showModeratorAction = false
            this.blockedFiles = true
            this.showCertAction = false
            getOrderItem(this.id, {}).then(res => {
                this.item = res
                if (!res.car) {
                    this.item.car = {
                        preorder_id: res.id
                    }
                }

                if (!res.client) {
                    this.item.client = {
                        idnum: this.user.idnum,
                        title: this.user.profile.fln
                    }
                }

                this.item.file = {
                    client_id: res.client_id,
                    preorder_id: res.preorder_id,
                    order_id: res.id
                }

                if (this.item.setCert) {
                    this.showCertAction = true
                }
                this.showOperatorAction = res.canSend
                this.blockedFiles = !res.canSend
                this.showModeratorAction = res.canApprove
                this.showModeratorExecute = res.canExecute

                this.showData = true
                this.showFile = true
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

        sendData(value) {
            if(value === 'send_to_moderator'){
                this.loading = true
                sendToApproveOrder(this.id).then(res => {
                    this.getData()
                }).finally(() => {
                    this.loading = false
                })
            }else {
                signData().then(res => {
                    if (res) {
                        this.loading = true
                        sendToSignOrder(this.id, {
                            sign: res,
                        }).then(el => {
                            this.getData()
                        }).finally(() => {
                            this.loading = false
                        })
                    }
                })
            }
        },

        executeRun(){
            this.loading = true
            executeRunOrder(this.item.id).then(res => {
                if(res){
                    if(res.success === true){
                        this.getData()
                    }
                }
            }).finally(() => {
                this.loading = false
            })
        },

        executeEnd(){
            this.loading = true
            executeCloseOrder(this.item.id).then(res => {
                if(res){
                    if(res.success === true){
                        this.getData()
                    }
                }
            }).finally(() => {
                this.loading = false
            })
        }

    },

    created() {
        this.getData()
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            this.showCameraBtn = true
        }
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
            this.showCamera = false
        })
    }

}
</script>

<style scoped>

</style>

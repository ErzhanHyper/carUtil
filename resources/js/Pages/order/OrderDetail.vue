<template>
    <q-circular-progress
        indeterminate
        rounded
        size="30px"
        color="primary"
        class="q-ma-md"
        v-if="!showData"
    />

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
                <div :class="'text-deep-orange'" v-if="item.approve.id === 3 && !item.videoUploaded">В
                    ожидании получения видеозаписи ТС
                </div>
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
                           label="Подписать и отправить модератору" @click="sendData()"
                           icon="send" v-if="showOperatorAction"></q-btn>
                </div>
            </div>
        </div>

        <order-action :order_id="item.id" :showCertAction="showCertAction" :showApproveAction="showModeratorAction"
                      v-if="user && user.role === 'moderator'"/>

        <div class="q-mt-md" v-if="item.comment && item.comment.length > 0 && user && user.role === 'liner'">
            <q-banner class="q-mb-sm bg-blue-grey-1">
                <div class="text-caption">Комментарий:</div>
                <div>{{ item.comment[0].action }}</div>
                <div>{{ item.comment[0].text }}</div>
                <div class="text-caption">
                    {{ $moment.unix(item.comment[0].created_at).format('YYYY-MM-DD HH:mm') }}
                </div>
            </q-banner>
        </div>

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
                           :blockedVideo="item.status.id === 3" :recycleType="item.recycle_type === 'ВЭТС' ? 1 : 2"/>

                <template v-if="user && user.role==='moderator'">
                    <q-separator class="q-my-lg"/>
                    <div class="q-px-md q-mt-lg text-body1 text-weight-bold">Файлы предзаявки</div>
                    <PreorderFile :data="item.file" v-if="showFile" :files="item.files" :blocked="blocked"
                                  :blockedVideo="item.blockedVideo"/>
                </template>
            </div>

        </div>
    </div>


</template>

<script>
import {getOrderItem, signOrder} from "../../services/order";
import {signData} from "../../services/sign";

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
import {mapGetters} from "vuex";
import {generateCertificate} from "../../services/certificate";
import FileDownload from "js-file-download";

export default {
    components: {
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

    data() {
        return {
            showDeleteDialog: false,
            disabled: true,

            showData: false,
            showFile: false,
            showProxy: false,
            blocked: true,
            blockedFiles: true,
            showOperatorAction: false,
            showCertAction: false,
            showModeratorAction: false,
            loading: false,

            order: null,

            item: {
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

                this.showFile = true

                if (this.user && this.user.role === 'operator') {
                    if (res.approve && (res.approve.id === 0 || res.approve.id === 4)) {
                        this.showOperatorAction = true
                        this.blockedFiles = false
                    }
                }
                if (res.approve && res.approve.id === 1) {
                    this.showModeratorAction = true
                }

                this.showData = true

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

        sendData() {
            signData().then(res => {
                if (res) {
                    this.loading = true
                    signOrder({
                        sign: res,
                        order_id: this.id
                    }).then(el => {
                        this.getData()
                    }).finally(() => {
                        this.loading = false
                    })
                }
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
    }

}
</script>

<style scoped>

</style>

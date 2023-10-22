<template>


    <div class="flex justify-between" v-if="showData">
        <div>
            <span :class="(item.recycle_type === 'ВЭТС') ? 'text-teal-9' : 'text-orange-9'" class="text-body1 q-mt-sm">
                {{ (item.recycle_type) ? item.recycle_type : '-' }}
            </span> <span class="text-body1 text-blue-grey-7"> <b> - №{{ item.id }}</b></span>
            <div :class="'text-'+setStatusColor(item.approve.id)" v-if="item.approve"> {{ item.approve.title }}</div>
            <div :class="'text-deep-orange'" v-if="item.approve && item.status.id === 2 && !item.videoUploaded">В ожидании получения видеозаписи ТС</div>
        </div>
    </div>

<!--    <OrderTimeline class="q-mt-md"/>-->

    <div class="col col-md-12 q-mt-md"
         v-if="showData && user.role && (user.role === 'operator' || user.role === 'moderator')">
        <template v-if="item.car && item.car.certificate">
            <div class="text-green-8 text-overline">Сертификат выдан</div>
            <q-btn icon="verified" size="sm" dense color="green-5" label="Скидочный сертификат" icon-right="download"
                   @click="getCert(item.car.id)"></q-btn>
        </template>

        <div class="flex justify-between q-mt-md">
            <div class="q-gutter-sm">
                <q-btn :loading="loading" square size="12px" color="light-green"
                       label="Подписать и отправить модератору" @click="sendData()"
                       icon="send" v-if="showOperatorAction"></q-btn>

                <q-btn :loading="loading" size="12px" color="light-green"
                       label="Выдать сертификат"
                       icon="verified" push v-if="showCertAction" @click="giveCert()"></q-btn>

                <template v-if="showModeratorAction">
                    <q-btn :loading="loading" square size="12px" color="light-green"
                           label="Одобрить" @click="sendApprove('approve')"
                           icon="check"></q-btn>

                    <q-btn square size="12px" color="orange-5" label="На доработку" @click="sendApprove('revision')"
                           icon="keyboard_return"></q-btn>

                    <q-btn square size="12px" color="red-5" label="Отклонить" @click="sendApprove('decline')"
                           icon="block"></q-btn>
                </template>
            </div>
            <div class="q-gutter-sm">
                <q-btn square size="12px" color="primary" label="Проверка в КАП" icon="add_task"
                       @click="kapDialog = true" v-if="showModeratorAction"/>
            </div>
        </div>
    </div>

    <div class="q-mt-md" v-if="item.comment && item.comment.length > 0 && showData && user && user.role === 'liner'">
        <q-banner class="q-mb-sm bg-blue-grey-1">
            <div class="text-caption">Комментарий:</div>
            <div>{{ item.comment[0].action }}</div>
            <div>{{ item.comment[0].text }}</div>
            <div class="text-caption">{{
                    $moment.unix(item.comment[0].created_at).format('YYYY-MM-DD HH:mm')
                }}
            </div>
        </q-banner>
    </div>

    <div class="row q-col-gutter-md" v-if="showData">

        <div class="col col-md-8 col-sm-12 col-xs-12 ">
            <div class="row q-col-gutter-md q-mt-xs">
                <div class="col col-md-5 col-sm-12 col-xs-12">

                    <client-card :data="item.client" :getClient="getClient" :blocked="blocked"/>

                    <q-card class="q-mt-md" v-if="blocked && item.proxy && (item.proxy === 1 || item.proxy === 2)">
                        <q-card-section>
                            <div class="row">
                                <div class="col">
                                    <q-select label="Доверенность" square dense outlined
                                              option-label="title" option-value="id"
                                              emit-value map-options class="q-mb-md" :options="proxies"
                                              v-model="item.proxy"
                                              @update:model-value="setProxy()"
                                              :readonly="blocked"

                                    />
                                </div>
                            </div>
                            <div class="row q-col-gutter-md " v-if="showProxy">
                                <div class="col">
                                    <q-input dense square outlined label="ФИО, Наименование владельца"
                                             v-model="item.owner_title" :readonly="blocked"/>
                                </div>
                                <div class="col">
                                    <q-input dense square outlined label="ИИН, БИН владельца"
                                             v-model="item.owner_idnum" :readonly="blocked"/>
                                </div>
                            </div>

                            <div class="row q-col-gutter-md q-mt-xs" v-if="showProxy">
                                <div class="col">
                                    <q-input dense square outlined label="Номер доверенности" v-model="item.proxy_num"
                                             :readonly="blocked"/>
                                </div>
                                <div class="col">
                                    <q-input dense square outlined label="Дата доверенности" type="date"
                                             v-model="item.proxy_date" :readonly="blocked"/>
                                </div>
                            </div>
                        </q-card-section>
                    </q-card>
                </div>

                <div class="col col-md-7 col-sm-12 col-xs-12">
                    <booking class="q-mb-md" :data="item.booking" :getBooking="getBooking" :blocked="disabled"
                             v-if="user.role === 'moderator' || user.role === 'operator'"/>
                    <car-card :data="item.car" :getCar="getCar" v-if="showData" :categories="item.categories"
                              :blocked="blocked"/>
                </div>
            </div>

        </div>

        <div class="col col-md-4 col-xs-12" v-if="showData">
            <order-document v-if="showOperatorAction" class="q-mb-md" :order_id="item.id"/>
            <OrderFile :data="item.file" v-if="showFile" :blocked="blockedFiles"/>
        </div>

    </div>


    <q-dialog v-model="commentDialog">
        <q-card style="width: 800px">
            <q-card-section>
                <q-input type="textarea" v-model="comment" outlined rows="3" label="Комментарий" class="text-body1"/>
            </q-card-section>
            <q-card-actions>
                <q-space/>
                <q-btn label="Отправить" color="primary" @click="sendApproveAction" :loading="loading"/>
            </q-card-actions>
        </q-card>
    </q-dialog>


    <q-dialog v-model="kapDialog">
        <order-kap/>
    </q-dialog>

</template>

<script>
import ClientCard from "@/Pages/client/ClientCard.vue";
import SelectFactory from "@/Components/Fields/SelectFactory.vue";
import OrderFile from "@/Pages/order/OrderFile.vue";
import CarCard from "@/Pages/car/CarCard.vue";
import Booking from "@/Pages/booking/BookingCard.vue";
import OrderKap from "@/Pages/order/OrderKap.vue";
import OrderTimeline from "@/Pages/order/OrderTimeline.vue";
import OrderDocument from "./OrderDocument.vue";

import {getOrderItem, signOrder, approveOrder, storeCertOrder} from "../../services/order";
import {signData} from "../../services/sign";

export default {
    components: {OrderDocument, OrderKap, Booking, CarCard, OrderFile, SelectFactory, ClientCard, OrderTimeline},

    props: ['id'],

    data() {
        return {
            clientDialog: false,
            commentDialog: false,
            showDeleteDialog: false,
            disabled: true,

            kapDialog: false,
            showData: false,
            showFile: false,
            showProxy: false,
            blocked: true,
            blockedFiles: true,
            showOperatorAction: false,
            showCertAction: false,

            showModeratorAction: false,

            loading: false,

            user: JSON.parse(localStorage.getItem('user')),

            factories: [
                {
                    id: 1,
                    title: 'Завод 1'
                },
                {
                    id: 2,
                    title: 'Завод 2'
                }
            ],

            proxies: [
                {
                    id: 1,
                    title: 'Владелец'
                },
                {
                    id: 2,
                    title: 'По доверенности на владельца'
                },
                {
                    id: 3,
                    title: 'По доверенности на доверенное лицо'
                }
            ],

            file_type_car: [],
            banks: [],
            region: [],
            order: null,
            options_category: [],

            item: {
                client: {},
                car: {
                    preorder_id: this.id,
                },
                preorder: {}
            },
            slide: 1,
            comment: '',
            approveAction: '',
            signHash: ''
        }
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
        updateFileType() {
            this.file_type_car.filter(el => {
                if (
                    el.id === 8 ||
                    el.id === 9 ||
                    el.id === 10 ||
                    el.id === 11 ||
                    el.id === 12 ||
                    el.id === 13 ||
                    el.id === 14 ||
                    el.id === 15
                ) {
                    this.options_photo.push(el)
                }
            })
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
                } else if (this.user && this.user.role === 'moderator') {
                    if (res.approve && (res.approve.id === 1)) {
                        this.showModeratorAction = true
                    }
                }


            }).finally(() => {
                this.showData = true
            })
        },

        sendApprove(value) {
            this.approveAction = value
            if (value === 'approve') {
                signData().then(res => {
                    console.log(res)
                    this.signHash = res
                    this.sendApproveAction()
                })
            } else {
                this.commentDialog = true
            }
        },

        sendApproveAction() {
            this.loading = true
            approveOrder({
                comment: this.comment,
                status: this.approveAction,
                sign: this.signHash,
                order_id: this.item.id
            }).then(() => {
                this.getData()
                this.commentDialog = false
            }).finally(() => {
                this.loading = false
            })
        },

        sendData() {
            signData().then(res => {
                signOrder({
                    sign: res,
                    order_id: this.id
                }).then(el => {
                    this.getData()
                })
            })
        },

        giveCert() {
            storeCertOrder({
                order_id: this.item.id,
            }).then(res => {
                this.getData()
            })
        },

        getCert(id) {

        }
    },

    created() {
        this.updateFileType()
        this.getData()
    }

}
</script>

<style scoped>

</style>

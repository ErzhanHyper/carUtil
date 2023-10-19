<template>


    <div class="flex justify-between" v-if="showData">
        <span>
        <span :class="(item.recycle_type === 1) ? 'text-teal-9' : 'text-orange-9'" class="text-body1 q-mt-sm">
            {{ (item.recycle_type === 1) ? 'ВЭТС' : 'ВЭССХТ' }}
        </span>
        <span :class="'text-'+setStatusColor(item.status.id)"> - {{ item.status.title }}</span>
        <div class="text-body1 text-weight-bold text-blue-grey-8" v-if="item.order">№ заявки: {{ item.order.id }}</div>

        </span>

        <div v-if="item.recycle_type && user.role === 'liner'">
            <q-btn color="blue-8" label="Отправить" @click="sendData" v-if="!blocked" :loading="loading"></q-btn>
            <q-space/>
            <template v-if="item.transferShow">
                <div class="q-gutter-sm">
                    <q-btn icon="sell" dense size="md" color="pink-10" label="Выставить ТС на продажу"
                           @click="sellPreorder"></q-btn>
                </div>
            </template>

            <template v-if="!item.transferShow && item.transfer">
                <div class="text-blue-10">
                    ТС выставлен на продажу
                    <q-space/>
                    <div class="text-right">
                        <q-btn flat icon="open_in_new" dense size="sm" color="blue-grey-10" label="Перейти"
                               to="/transfer/order"></q-btn>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="col col-md-12 q-mt-md" v-if="user.role && user.role === 'moderator'">
        <!--        <OrderTimeline/>-->
        <div class="flex justify-between q-mt-md" v-if="item.status && item.status.id === 1">
            <div class="q-gutter-sm">
                <q-btn :loading="loading" square size="12px" color="light-green" label="Одобрить"
                       @click="sendApprove('approve')"
                       icon="send" :disabled="disabled"></q-btn>
                <q-btn square size="12px" color="orange-5" label="На доработку" @click="sendApprove('revision')"
                       icon="keyboard_return"></q-btn>
                <q-btn square size="12px" color="red-5" label="Отклонить" @click="sendApprove('decline')"
                       icon="block"></q-btn>
            </div>
            <div class="q-gutter-sm">
                <!--                <q-btn square size="12px" color="orange-10" label="Найти дубликаты" icon="search"-->
                <!--                       @click="kapDialog = true"/>-->
                <q-btn square size="12px" color="primary" label="Проверка в КАП" icon="add_task"
                       @click="kapDialog = true"/>
            </div>
        </div>
    </div>

    <q-banner class="q-mb-sm bg-orange-3 q-mt-md" v-if="showError">
        <div v-for="error in errors">
            <span v-for="e in error">{{ e }}</span>
        </div>
    </q-banner>

    <div class="row q-col-gutter-md" v-if="showData">

        <div class="col col-md-8 col-sm-12 col-xs-12 ">
            <div class="row q-col-gutter-md q-mt-xs">
                <div class="col col-md-5 col-sm-12 col-xs-12">

                    <client-card :data="item.client" :getClient="getClient" :blocked="blocked"/>

                    <q-card class="q-mt-md">
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
                    <booking class="q-mb-md" :data="item.booking" :getBooking="getBooking"
                             v-if="user.role === 'liner' || user.role === 'moderator'" :blocked="blocked"/>
                    <car-card :data="item.car" :getCar="getCar" v-if="showData" :categories="item.categories"
                              :blocked="blocked" :order_id="item.id"/>
                </div>
            </div>
            <div class="q-mt-md"
                 v-if="item.comment && item.comment.length > 0 && showData">
                <div class="text-h5">История изменений</div>
                <q-timeline color="secondary">
                    <template v-for="(text, i) in item.comment">
                        <q-timeline-entry class="q-mb-sm" :body="text.created_at">
                            <q-banner class="bg-purple-1" style="max-width: 380px">
                                <div class="text-subtitle2 text-weight-bold">
                                    <span v-if="text.action === 'approve'">Одобрено</span>
                                    <span v-if="text.action === 'decline'">Отклонено</span>
                                    <span v-if="text.action === 'revision'">На доработку</span>
                                </div>
                                {{ text.text }}
                            </q-banner>
                        </q-timeline-entry>
                    </template>
                </q-timeline>

            </div>
        </div>

        <div class="col col-md-4 col-xs-12">
            <PreorderFile :data="item.file" v-if="showFile" :files="item.files" :blocked="blocked"/>
        </div>

    </div>


    <div class="q-mt-md text-right" v-if="showData && user && user.role === 'liner'">
        <q-btn icon="delete" label="Удалить заявку" square size="sm" color="negative" @click="showDeleteDialog = true"
               v-if="item.status.id === 0"/>
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

    <q-dialog v-model="kapDialog">
        <order-kap/>
    </q-dialog>

    <q-dialog v-model="commentDialog">
        <q-card style="width: 800px">
            <q-card-section>
                <q-input type="textarea" v-model="comment" outlined rows="3" label="Комментарий"/>
            </q-card-section>

            <q-card-actions>
                <q-space/>
                <q-btn label="Отправить" color="primary" @click="sendApproveAction"/>
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>

<script>
import ClientCard from "../client/ClientCard.vue";
import OrderTimeline from "@/Pages/order/OrderTimeline.vue";
import SelectFactory from "../../Components/Fields/SelectFactory.vue";
import {deleteOrder, getOrderItem, moderatorApproveOrder, sendOrder} from "../../services/preorder";
import PreorderFile from "@/Pages/preorder/PreorderFile.vue";
import CarCard from "../car/CarCard.vue";
import Booking from "../booking/BookingCard.vue";
import {Notify} from "quasar";
import OrderKap from "@/Pages/order/OrderKap.vue";
import PreorderTimeline from "./PreorderTimeline.vue";
import {storeTransfer} from "../../services/transfer";

export default {
    components: {PreorderTimeline, OrderKap, Booking, CarCard, PreorderFile, SelectFactory, OrderTimeline, ClientCard},

    props: ['id'],

    data() {
        return {
            clientDialog: false,
            commentDialog: false,
            showDeleteDialog: false,
            disabled: false,

            kapDialog: false,
            showData: false,
            showFile: false,
            showProxy: false,
            blocked: true,

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

            errors: [],
            showError: false,
            item: {
                recycle_type: null,
                category: null,
                client: {},
                car: {},
                booking: {},
                file: {},
                files: {},
                factory: null,
                proxy: null,
            },
            slide: 1,
            comment: '',
            approveAction: '',
        }
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
                    order_id: res.order_id
                }

                if (!this.item.client) {
                    this.item.client = {
                        idnum: this.user.idnum,
                        title: this.user.profile.fln
                    }
                }

                this.item.files = this.item.car_file

                this.showFile = true

                if (this.user && this.user.role === 'liner') {
                    if (this.item.status.id === 0 || this.item.status.id === 4) {
                        this.blocked = false
                    } else {
                        this.blocked = true
                    }
                }

            }).finally(() => {
                this.showData = true
            })
        },

        sendApprove(value) {
            this.approveAction = value
            if (value === 'approve') {
                this.sendApproveAction()
            } else {
                this.commentDialog = true
            }
        },

        sendApproveAction() {
            this.loading = true
            moderatorApproveOrder(this.item.id, {
                comment: this.comment,
                status: this.approveAction
            }).then(() => {
                this.getData()
                this.commentDialog = false
            }).finally(() => {
                this.loading = false
            })
        },

        sendData() {
            this.errors = []
            this.showError = false
            this.loading = true
            this.$emitter.emit('ClientCardEvent')
            this.$emitter.emit('CarCardEvent')
            this.$emitter.emit('BookingCardEvent')
            if ((this.item.status && this.item.status.id === 0) || this.item.status && this.item.status.id === 4) {
                sendOrder(this.id, this.item).then(res => {
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

        sellPreorder() {
            if (this.item.order) {
                storeTransfer({
                    order_id: this.item.order.id
                }).then(res => {
                    Notify.create({
                        message: 'Транспортное средство выставлена на продажу',
                        position: 'bottom-right',
                        type: 'info'
                    })
                    this.getData()
                })
            }
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

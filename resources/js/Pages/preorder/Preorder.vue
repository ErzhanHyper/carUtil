<template>

    <div class="q-gutter-sm q-mb-md q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Предзаявка</div>

        <div v-if="show && user.role === 'liner'" class="flex justify-between">
            <q-btn class="q-ml-md text-weight-bold"
                color="indigo-8"
                icon="add"
                label="Создать заявку"
                push
                @click="orderDialog = true"
            />
        </div>
    </div>

    <q-card v-if="show && user.role === 'moderator'" bordered class="q-mb-none q-mt-md" flat square>
        <q-card-section>
            <div class="row q-col-gutter-md">
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="filter.vin" dense label="VIN" outlined/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="filter.grnz" dense label="ГРНЗ" outlined/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="filter.title" dense label="ФИО" outlined/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="filter.idnum" dense label="ИИН/БИН" outlined/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-select
                        v-model="filter.status"
                        :options="statuses"
                        dense
                        emit-value
                        label="Статус"
                        map-options
                        option-label="title"
                        option-value="id"
                        outlined
                        transition-hide="jump-up"
                        transition-show="jump-up"
                    />
                </div>
                <div class="col col-md-2 col-sm-2 col-xs-12">
                    <q-btn :loading="loading1" color="blue-8" icon="search" round @click="applyFilter"/>
                    <q-btn :loading="loading2" class="q-ml-sm" color="orange-8" icon="close" round size="sm"
                           @click="resetFilter"/>
                </div>
            </div>
        </q-card-section>
    </q-card>

    <q-markup-table bordered dense flat>
        <thead>
        <tr>
            <th class="text-left">VIN</th>
            <th class="text-left">ГРНЗ</th>
            <th class="text-left">Категория</th>
            <th class="text-left">ФИО/Наименование</th>
            <th class="text-left">ИИН/БИН</th>
            <th class="text-left">Дата создания</th>
            <th class="text-left">Статус</th>
            <th class="text-left"></th>
        </tr>
        </thead>
        <tbody>
        <template v-if="show && items.length > 0">
            <tr v-for="(item, i) in items" :key="i">
                <td class="text-left">
                    <router-link :to="'/preorder/'+item.id">
                        <q-btn color="primary" flat icon="open_in_new" round size="sm" style="margin-bottom: 2px"/>
                        <span class="text-primary">{{ (item.car) ? item.car.vin : '-' }}</span>
                    </router-link>
                </td>
                <td class="text-left">
                    <span class="text-primary">{{ (item.car) ? item.car.grnz : '-' }}</span>
                </td>
                <td class="text-left">
                    <q-chip :color="(item.vehicleType === 'car') ? 'teal-1' : 'orange-1'" size="12px">
                        {{ (item.car) ? (item.car.category ? item.car.category.title_ru + ' | ' : '') : '' }}
                        {{ (item.vehicleType) ? ((item.vehicleType === 'car') ? 'ВЭТС' : 'ВЭССХТ') : '-' }}
                    </q-chip>
                </td>
                <td class="text-left">
                    <template v-if="item.client">
                        <span class="text-body2">{{ item.client.title }}</span>
                    </template>
                    <template v-else>-</template>
                </td>
                <td class="text-left">
                    <template v-if="item.client">
                        <span class="text-body2">{{ item.client.idnum }}</span>
                    </template>
                    <template v-else>-</template>
                </td>
                <td class="text-left"> {{ (item.date) ? item.date : '-' }}</td>
                <td class="text-left">
                    <q-chip :color="setStatusColor(item.status.id)" class="text-overline" dark size="12px"
                            square>
                        {{ item.status.title }}
                    </q-chip>
                </td>
                <td class="text-right">
                    <q-badge v-if="item.order && item.order.status.id === 2 && item.order.approve.id === 3 && !item.order.videoUploaded" class="q-pa-xs"
                             color="deep-orange">
                        В ожидании получения видеозаписи
                        <q-tooltip :offset="[10, 10]" class="bg-indigo text-body2">
                            Зайдите в мобильное приложение и сделайте видеозапись ТС/СХТ и отправьте видеозапись по
                            номеру заявки
                        </q-tooltip>
                    </q-badge>
                    <q-badge v-if="item.order && item.order.status.id === 2 && item.order.approve.id === 3 && item.order.videoUploaded"
                             color="blue--8">
                        Видеозапись отправлена
                        <q-tooltip :offset="[10, 10]" class="bg-indigo text-body2">
                            Ожидайте выдачу сертификата
                        </q-tooltip>
                    </q-badge>
                    <q-badge v-if="item.order && item.order.car && item.order.status.id === 3 && item.order.approve.id === 3" class="q-pa-xs"
                             color="teal-5">
                        <router-link v-if="user.role === 'liner'" to="/certificate">Сертификат выдан</router-link>
                        <template v-else>Сертификат выдан</template>
                    </q-badge>
                    <q-badge v-if="!item.booking && item.order && item.order.status.id === 0 && item.order.blocked === 0" class="q-pa-xs"
                             color="blue-8">
                        На бронировании
                    </q-badge>
                    <q-badge v-if="item.booking && item.order && item.order.status.id === 0 && item.order.blocked === 0" class="q-pa-xs"
                             color="teal-8">
                        В работе
                    </q-badge>
                    <q-badge v-if="item.transfer && item.transfer.closed !== 2" class="q-pa-xs">
                        <router-link :to="'/transfer/order/'+item.transfer.id">Выставлен на продажу</router-link>
                    </q-badge>
                    <q-badge v-if="item.order && (item.order.status.id === 1 || item.order.status.id === 2 || item.order.status.id === 4 || item.order.status.id === 5)"
                             :color="setOrderStatusColor(item.order.status.id)">
                        {{ item.order.status.title }}
                    </q-badge>

                </td>
            </tr>
        </template>
        <div v-if="show && items.length === 0" class="q-ma-xs">Нет записей</div>
        </tbody>
    </q-markup-table>

    <div class="flex justify-center">
        <q-spinner-dots
            v-if="!show"
            class="q-ma-xs"
            color="primary"
            size="2em"
        />
    </div>

    <div class="q-pa-lg flex flex-center">
        <q-pagination
            v-if="items.length > 0"
            v-model="page"
            :max="totalPage"
            :max-pages="10"
            direction-links
            @click="getData()"
        />
    </div>

    <q-dialog v-model="orderDialog">
        <q-card style="width: 600px; max-width: 500px;">
            <q-card-section class="row items-center">
                Создать заявку
                <q-space/>
                <q-btn v-close-popup dense flat icon="close" round/>
            </q-card-section>

            <q-card-section>
                <q-select
                    v-model="item.recycle_type"
                    :model-value="item.recycle_type"
                    :options="recycle_types"
                    emit-value
                    label="Тип заявки"
                    map-options
                    option-label="title"
                    option-value="id"
                    options-selected-class="text-deep-orange"
                    square
                >
                    <template v-slot:option="scope">
                        <q-item v-bind="scope.itemProps">
                            <q-item-section avatar>
                                <q-icon :name="scope.opt.icon"/>
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ scope.opt.title }}</q-item-label>
                                <q-item-label caption>{{ scope.opt.description }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </template>
                </q-select>
            </q-card-section>

            <q-card-section>
                <q-btn :loading="loading" color="primary" icon="add" label="Выбрать" unelevated @click="create()"/>
            </q-card-section>

        </q-card>
    </q-dialog>
</template>

<script>
import {getOrderList, storeOrder} from "../../services/preorder";
import {Notify} from "quasar";
import {mapGetters} from "vuex";

export default {

    data() {
        return {
            data: null,
            sum: null,

            show: false,
            orderDialog: false,
            loading: false,
            loading1: false,
            loading2: false,

            filter: {
                status: 1,
                page: this.page
            },
            recycleTypeRules: {},

            items: [],
            statuses: [
                {
                    id: 1,
                    title: 'На рассмотрении'
                },
                {
                    id: 2,
                    title: 'Одобрена'
                },
                {
                    id: 3,
                    title: 'Отклонена'
                },
                {
                    id: 4,
                    title: 'Возвращена на доработку'
                },
            ],

            page: 1,
            totalPage: 1,

            item: {
                recycle_type: null
            },
            recycle_types: [
                {
                    id: 1,
                    icon: 'recycling',
                    title: 'ВЭТС',
                    description: 'Вышедшее из эксплуатации транспортное средство',
                },
                {
                    id: 2,
                    icon: 'recycling',
                    title: 'ВЭССХТ',
                    description: 'Вышедшей из эксплуатации сельхозтехники',
                }
            ]
        }
    },


    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    methods: {

        setOrderStatusColor(id) {
            let color = 'blue-grey-3'
            if (id === 1) {
                color = 'blue-grey-5'
            } else if (id === 2) {
                color = 'blue-5'
            } else if (id === 3) {
                color = 'teal-5'
            } else if (id === 4) {
                color = 'deep-orange-5'
            } else if (id === 5) {
                color = 'teal-8'
            }

            return color;
        },

        setStatus(id) {
            let result = '';
            if (id === 0) {
                result += 'Новая'
            }

            return result
        },

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

        applyFilter() {
            this.page = 1
            this.getData({params: this.filter})
        },

        resetFilter() {
            this.filter = {
                page: this.page
            }
            this.getData()
        },

        create() {
            if (!this.item.recycle_type) {
                Notify.create({
                    message: 'Выберите тип заявки',
                    position: 'bottom',
                    type: 'warning'
                })
            }

            if (this.item.recycle_type) {
                this.loading = true
                storeOrder({
                    recycle_type: this.item.recycle_type
                }).then(res => {
                    if (res.success === true) {
                        this.orderDialog = false
                        this.$router.push('/preorder/' + res.data.id)
                    }
                }).finally(() => {
                    this.loading = false
                })
            }
        },

        getData() {
            this.$emitter.emit('contentLoaded', true);
            this.filter.page = this.page
            getOrderList({params: this.filter}).then(res => {
                this.$emitter.emit('contentLoaded', false);
                this.items = res.items
                this.totalPage = res.pages
            }).finally(() => {
                this.show = true
            })
        }
    },

    created() {
        this.getData()
    }
}
</script>

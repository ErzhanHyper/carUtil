<template>

    <div class="q-gutter-sm q-mb-md q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Предзаявка</div>

        <div class="flex justify-between" v-if="show && user.role === 'liner'">
            <!--            <q-btn color="purple-10" unelevated icon="tune" class="q-ml-md"/>-->
            <q-btn color="indigo-8" push icon="add" label="Создать заявку" class="q-ml-md text-weight-bold"
                   @click="orderDialog = true"/>
        </div>
    </div>

    <q-card class="q-mb-none q-mt-md" bordered square flat v-if="show && user.role === 'moderator'">
        <q-card-section>
            <div class="row q-col-gutter-md">
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input label="VIN" v-model="filter.vin" outlined dense/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input label="ГРНЗ" v-model="filter.grnz" outlined dense/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input label="ФИО" v-model="filter.title" outlined dense/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input label="ИИН/БИН" v-model="filter.idnum" outlined dense/>
                </div>
                <div class="col col-md-2 col-sm-2 col-xs-12">
                    <q-btn icon="search" round @click="applyFilter" color="blue-8" :loading="loading1"/>
                    <q-btn icon="close" round @click="resetFilter" color="orange-8" size="sm" class="q-ml-sm"
                           :loading="loading2"/>
                </div>
            </div>
        </q-card-section>
    </q-card>

    <q-markup-table flat bordered dense>
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
                    <q-btn icon="open_in_new" size="sm" round flat color="primary" style="margin-bottom: 2px"/>
                    <span class="text-primary">{{ (item.car) ? item.car.vin : '-' }}</span>
                </router-link>
            </td>
            <td class="text-left">
                <span class="text-primary">{{ (item.car) ? item.car.grnz : '-' }}</span>
            </td>
            <td class="text-left">
                <q-chip dark :color="(item.vehicleType === 'car') ? 'teal-8' : 'orange-9'" size="12px">
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
                <q-chip square size="12px" dark :color="setStatusColor(item.status.id)"
                        class="text-overline">
                    {{ item.status.title }}
                </q-chip>
            </td>
            <td class="text-right">
                <q-badge color="deep-orange" class="q-pa-xs" v-if="item.order && item.order.status.id === 2 && item.order.approve.id === 3 && !item.order.videoUploaded"  >
                    В ожидании получения видеозаписи
                    <q-tooltip class="bg-indigo text-body2" :offset="[10, 10]" >
                        Зайдите в мобильное приложение и сделайте видеозапись ТС/СХТ и отправьте видеозапись по номеру заявки
                    </q-tooltip>
                </q-badge>
                <q-badge color="blue--8" v-if="item.order && item.order.status.id === 2 && item.order.approve.id === 3 && item.order.videoUploaded">
                    Видеозапись отправлена
                    <q-tooltip class="bg-indigo text-body2" :offset="[10, 10]" >
                        Ожидайте выдачу сертификата
                    </q-tooltip>
                </q-badge>
                <q-badge color="teal-5" class="q-pa-xs" v-if="item.order && item.order.car && item.order.car.certificate">
                    <router-link to="/certificate">Сертификат выдан</router-link>
                </q-badge>
                <q-badge color="blue-8" class="q-pa-xs" v-if="!item.booking && item.order && item.order.status.id === 0 && item.order.blocked === 0">
                    На бронировании
                </q-badge>
                <q-badge color="green-5" class="q-pa-xs" v-if="item.booking && item.order && item.order.status.id === 0 && item.order.blocked === 0">
                    На отправке
                </q-badge>
                <q-badge v-if="item.transfer && item.transfer.closed !== 2" class="q-pa-xs">
                    <router-link :to="'/transfer/order/'+item.transfer.id">Выставлена на продажу</router-link>
                </q-badge>
                <q-badge :color="setOrderStatusColor(item.order.status.id)" v-if="item.order && (item.order.status.id === 1 || item.order.status.id === 2 || item.order.status.id === 4 || item.order.status.id === 5)">
                    {{ item.order.status.title }}
                </q-badge>
            </td>
        </tr>
        </template>
        <div class="q-ma-xs" v-if="show && items.length === 0">Нет записей</div>
        </tbody>
    </q-markup-table>

    <div class="flex justify-center">
        <q-spinner-dots
            color="primary"
            size="2em"
            class="q-ma-xs"
            v-if="!show"
        />
    </div>

    <div class="q-pa-lg flex flex-center">
        <q-pagination
            v-model="page"
            :max="totalPage"
            :max-pages="10"
            direction-links
            @click="getData()"
            v-if="items.length > 0"
        />
    </div>

    <q-dialog v-model="orderDialog">
        <q-card style="width: 600px; max-width: 500px;">
            <q-card-section class="row items-center">
                Создать заявку
                <q-space/>
                <q-btn icon="close" flat round dense v-close-popup/>
            </q-card-section>

            <q-card-section>
                <q-select
                    square
                    v-model="item.recycle_type"
                    label="Тип заявки"
                    :options="recycle_types"
                    :model-value="item.recycle_type"
                    option-value="id"
                    option-label="title"
                    map-options
                    emit-value
                    options-selected-class="text-deep-orange"
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
                <q-btn color="primary" label="Выбрать" unelevated icon="add" @click="create()" :loading="loading"/>
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
            statuses: [],
            sum: null,

            show: false,
            orderDialog: false,
            loading: false,
            loading1: false,
            loading2: false,

            filter: {
                page: this.page
            },
            recycleTypeRules: {},

            sign_statuses: ['Подписан', 'Не подписан'],
            items: [],

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
                color = 'blue-grey-10'
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

        applyFilter(){
            this.page = 1
            this.getData({params: this.filter})
        },

        resetFilter(){
            this.filter = {
                page: this.page
            }
            this.getData()
        },

        create() {
            if (!this.item.recycle_type) {
                Notify.create({
                    message: 'Выберите тип заявки',
                    position: 'bottom-right',
                    type: 'warning'
                })
            }
            this.loading = true
            storeOrder({
                recycle_type: this.item.recycle_type
            }).then(res => {
                if (res.status === 200) {
                    this.orderDialog = false
                    this.$router.push('/preorder/' + res.data.id)
                }
            }).finally(() => {
                this.loading = false
            })
        },

        getData() {
            this.$emitter.emit('contentLoaded', true);
            getOrderList({params: this.filter}).then(res => {
                this.items = res.items
                this.totalPage = res.pages
            }).finally(()=> {
                this.show = true
            })
        }
    },

    created() {
        this.getData()
    }
}
</script>

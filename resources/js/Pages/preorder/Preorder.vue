<template>

    <div class="q-gutter-sm q-mb-md q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Предзаявка</div>

        <div v-if="show && user.role === 'liner'" class="flex justify-between">
            <preorder-create />
        </div>
    </div>

    <preorder-filter :apply-action="applyFilter" :filter="filter" v-if="user && user.role === 'moderator'" class="q-mb-lg"/>

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

</template>

<script>
import {getOrderList} from "../../services/preorder";
import {mapGetters} from "vuex";
import PreorderCreate from './PreorderCreate.vue'
import PreorderFilter from "./PreorderFilter.vue";
export default {

    components: {
        PreorderFilter,
        PreorderCreate
    },

    data() {
        return {
            data: null,
            sum: null,

            show: false,
            loading1: false,
            loading2: false,

            filter: {
                status: 1,
                page: this.page
            },

            items: [],

            page: 1,
            totalPage: 1,

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

        applyFilter(value) {
            this.items = []
            this.page = 1
            this.filter.page = this.page
            this.filter = value
            this.show = false
            this.totalPage = 1
            getOrderList({params: this.filter}).then(res => {
                this.items = res.items
                this.show = true
                this.totalPage = res.pages

                this.$emitter.emit('FilterApplyEvent')
            })
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

<template>

    <div class="q-gutter-sm q-mb-md flex justify-between items-center">
        <div class="text-h6 text-primary">Предзаявка
            <div class="text-caption text-blue-grey-5">Показано в списке - {{ items.length }}</div>
        </div>

        <div v-if="show && user.role === 'liner'" class="flex justify-between">
            <preorder-create />
        </div>
    </div>

    <main-filter v-if="showFilter" :apply-action="applyFilter" :data="filter" :filters="['vin', 'grnz', 'fio', 'idnum', 'preorder_status']" class="q-mb-md"/>

    <q-scroll-area
        :visible="true"
        :style="'height: calc(100vh - ' + windowHeight + ')'"
    >
    <q-markup-table  dense flat>
        <thead>
        <tr>
            <th class="text-left">VIN</th>
            <th class="text-left">ГРНЗ</th>
            <th class="text-left">Категория</th>
            <th class="text-left">ФИО/Наименование</th>
            <th class="text-left">ИИН/БИН</th>
            <th class="text-left">Дата отправки</th>
            <th class="text-left">Статус</th>
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
                    <q-chip :color="(item.vehicleType === 'car') ? 'blue-grey-5' : 'orange-8'" size="12px" outline>
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
                <td class="text-left"> {{ (item.sended_dt) ? item.sended_dt : '-' }}</td>
                <td class="text-left">
                    {{}}
                    <q-chip :color="setStatusColor(item.globalStatus.id)" class="text-overline" outline size="12px">
                        {{ item.globalStatus.title }}
                    </q-chip>
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
    </q-scroll-area>

    <div class="q-pa-sm flex flex-center">
        <q-pagination
            v-if="totalPage > 1"
            v-model="page"
            :max="totalPage"
            :max-pages="10"
            size="12px"
            direction-links
            @click="getData()"
        />
    </div>

</template>

<script>
import {getOrderList} from "../../services/preorder";
import {mapGetters} from "vuex";
import PreorderCreate from './PreorderCreate.vue'
import MainFilter from "../../Components/MainFilter.vue";

export default {

    components: {
        MainFilter,
        PreorderCreate
    },

    data() {
        return {
            data: null,
            sum: null,
            windowHeight: '200px',

            show: false,
            loading1: false,
            loading2: false,

            filter: {
                status: [1],
                page: this.page
            },
            showFilter: false,

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
        setStatusColor(id) {
            let color = 'blue-grey-5'
            if (id === 1) {
                color = 'blue-5'
            } else if (id === 2) {
                color = 'green-9'
            } else if (id === 3) {
                color = 'pink-5'
            } else if (id === 4) {
                color = 'orange-5'
            } else if (id === 5) {
                color = 'blue-8'
            } else if (id === 6) {
                color = 'teal-5'
            } else if (id === 7) {
                color = 'teal-8'
            } else if (id === 8) {
                color = 'green-5'
            }

            return color;
        },

        applyFilter(value) {
            this.page = 1
            this.filter.page = this.page
            this.filter = value
            this.getData()
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
                if(this.user && this.user.role === 'moderator'){
                    this.showFilter = true
                    this.windowHeight = '260px'
                }
            })
        }
    },

    created() {
        this.getData()
    },

}
</script>

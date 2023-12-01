<template>

    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Заявки</div>

        <div>
            <q-btn color="indigo-8" @click="showFilter = !showFilter" v-if="user && user.role === 'moderator'">
                <q-icon name="sort" ></q-icon>
            </q-btn>
        </div>
    </div>

    <order-filter :filter="filter" :apply-action="applyFilter" :reset-action="resetFilter" v-if="user && user.role === 'moderator' && showFilter"/>

    <q-markup-table flat bordered dense>
        <thead>
        <tr>
            <th class="text-left">ID</th>
            <th class="text-left">Категория</th>
            <th class="text-left">VIN</th>
            <th class="text-left">ГРНЗ</th>
            <th class="text-left">ФИО/Наименование</th>
            <th class="text-left">ИИН/БИН</th>
            <th class="text-left">Регион</th>
            <th class="text-left">Дата создания</th>
            <th class="text-left">Статус</th>
            <th class="text-left">Исполнитель</th>
        </tr>
        </thead>
        <tbody>
        <template v-if="show && items.length > 0">
        <tr v-for="(item, i) in items" :key="i">
            <td class="text-left ">
                <q-btn icon="open_in_new" dense flat :to="'/order/'+item.id" color="primary" :label="item.id"/>
            </td>
            <td class="text-left">
                <q-chip :color="(item.vehicleType === 'car') ? 'teal-1' : 'orange-1'" size="12px"
                        v-if="item.vehicleType">
                    {{ (item.car) ? (item.car.category ? item.car.category.title_ru : '') : '-' }} | {{
                        (item.vehicleType === 'car') ? 'ВЭТС' : 'ВЭССХТ'
                    }}
                </q-chip>
                <q-space />
            </td>
            <td class="text-left">{{ (item.car) ? item.car.vin : '-' }}</td>
            <td class="text-left">{{ (item.car) ? item.car.grnz : '-' }}</td>

            <td class="text-left">{{ (item.client) ? item.client.title : '-' }}</td>
            <td class="text-left">{{ (item.client) ? item.client.idnum : '-' }}</td>
            <td class="text-left">{{ (item.client && item.client.region) ? item.client.region.title : '-' }}</td>
            <td class="text-left">{{ (item.created) ? item.created : '-' }}</td>
            <td class="text-left">
                <div class="q-gutter-sm" >
                    <q-chip square dark size="12px" :color="setApproveColor(item.approve.id)" >
                        {{ item.approve.title }}
                    </q-chip>
                </div>
            </td>
            <td>
                <div style="width: 180px;white-space: normal; font-size: 12px" v-if="item.executor">{{ item.executor.title }}</div>
                <q-space/>
                <q-badge :color="setStatusColor(item.status.id)"
                        v-if="item.status">
                        {{ item.status.title }}
                    <q-tooltip class="bg-indigo text-body2" :offset="[10, 10]" v-if="user.role === 'operator' && item.status.id === 4">
                        Зайдите в мобильное приложение и сделайте видеозапись ТС/СХТ и отправьте видеозапись по номеру заявки
                    </q-tooltip>
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
            :max-pages="10"
            :max="totalPage"
            direction-links
            @click="getData()"
            v-if="totalPage > 10 && items.length > 0"
        />
    </div>

</template>

<script>
import {getOrderList} from "../../services/order";
import {mapGetters} from "vuex";
import {generateCertificate} from "../../services/certificate";
import FileDownload from "js-file-download";
import OrderFilter from "./OrderFilter.vue";

export default {
    components: {OrderFilter},
    data() {
        return {
            data: null,
            show: false,
            showFilter: true,
            orderDialog: false,
            loading: false,

            items: [],
            page: 1,
            totalPage: 1,

            filter: {
                page: 1
            },
            item: {
                approve: '',
                executor: {},
                status: {},
                certificate: {},
                car: {},
                client: {},
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
            authenticated: 'auth/authenticated',
            user: 'auth/user'
        })
    },

    methods: {

        setApproveColor(id) {
            let color = 'blue-grey-5'
            if (id === 1) {
                color = 'blue-5'
            } else if (id === 3) {
                color = 'positive'
            } else if (id === 2) {
                color = 'pink-5'
            } else if (id === 4) {
                color = 'orange-5'
            }

            return color;
        },

        setStatusColor(id) {
            let color = 'blue-grey-8'
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

        applyFilter(value){
            this.page = 1
            this.filter.page = this.page
            this.filter = value
            this.getData()
        },

        resetFilter(value){
            this.page = 1
            this.filter = value
            this.getData()
        },

        create() {
            this.loading = true
            storeOrder({
                recycle_type: this.item.recycle_type
            }).then(res => {
                if (res.status === 200) {
                    this.orderDialog = false
                    this.$router.push('/order/' + res.data.id)
                }
            }).finally(() => {
                this.loading = false
            })
        },

        getCert(id) {
            generateCertificate(id, {responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'certificate.pdf')
            }).finally(() => {
                this.loading = false
            })
        },

        getData() {
            this.filter.page = this.page
            this.$emitter.emit('contentLoaded', true);
            getOrderList({params: this.filter}).then(res => {
                this.$emitter.emit('contentLoaded', false);

                this.show = true
                this.items = res.items
                this.totalPage = res.pages
            })
        }
    },

    created() {
        this.getData()
    }
}
</script>

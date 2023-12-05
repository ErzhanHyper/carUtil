<template>

    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Заявки</div>
        <!--        <div>-->
        <!--            <q-btn v-if="user && user.role === 'moderator'" color="indigo-8" @click="showFilter = !showFilter">-->
        <!--                <q-icon name="sort"></q-icon>-->
        <!--            </q-btn>-->
        <!--        </div>-->
    </div>

    <order-filter v-if="user && user.role === 'moderator' && showFilter" :apply-action="applyFilter" :filter="filter"
                  class="q-mb-md"/>

    <q-scroll-area
        :visible="true"
        style="height: calc(100vh - 270px);"
    >
        <q-markup-table bordered dense flat>
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
                        <q-btn :label="item.id" :to="'/order/'+item.id" color="primary" dense flat icon="open_in_new"/>
                    </td>
                    <td class="text-left">
                        <q-chip v-if="item.vehicleType" :color="(item.vehicleType === 'car') ? 'teal-1' : 'orange-1'"
                                size="12px">
                            {{ (item.car) ? (item.car.category ? item.car.category.title_ru : '') : '-' }} | {{
                                (item.vehicleType === 'car') ? 'ВЭТС' : 'ВЭССХТ'
                            }}
                        </q-chip>
                        <q-space/>
                    </td>
                    <td class="text-left">{{ (item.car) ? item.car.vin : '-' }}</td>
                    <td class="text-left">{{ (item.car) ? item.car.grnz : '-' }}</td>

                    <td class="text-left">{{ (item.client) ? item.client.title : '-' }}</td>
                    <td class="text-left">{{ (item.client) ? item.client.idnum : '-' }}</td>
                    <td class="text-left">{{
                            (item.client && item.client.region) ? item.client.region.title : '-'
                        }}
                    </td>
                    <td class="text-left">{{ (item.created) ? item.created : '-' }}</td>
                    <td class="text-left">
                        <div class="q-gutter-sm">
                            <q-chip :color="setStatusColor(item.globalStatus)" dark size="12px" outline>
                                {{ item.globalStatus }}
                            </q-chip>
                        </div>
                    </td>
                    <td>
                        <div v-if="item.executor" style="width: 180px;white-space: normal; font-size: 12px">
                            {{ item.executor.title }}
                        </div>
                        <div v-else>-</div>
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

    <div class="q-pa-lg flex flex-center">
        <q-pagination
            v-if="totalPage > 1"
            v-model="page"
            :max="totalPage"
            :max-pages="10"
            direction-links
            @click="getData()"
        />
    </div>

</template>

<script>
import {getOrderList} from "../../services/order";
import {mapGetters} from "vuex";
import {generateCertificate} from "../../services/certificate";
import FileDownload from "js-file-download";
import OrderFilter from "./OrderFilter.vue";
import api from "../../api";

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
                    description: 'Вышедшее из эксплуатации сельхозтехника',
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

        setStatusColor(id) {
            let color = 'grey-8'
            if (id === 'Новая заявка') {
                color = 'blue-grey-5'
            } else if (id === 'На рассмотрении') {
                color = 'blue-5'
            } else if (id === 'Одобрено') {
                color = 'teal-8'
            } else if (id === 'Отказано') {
                color = 'pink-5'
            } else if (id === 'В ожидании видеозаписи') {
                color = 'deep-orange-5'
            }else if (id === 'На выдаче сертификата') {
                color = 'blue-grey-8'
            }else if (id === 'Завершено') {
                color = 'green-5'
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

        async applyFilter(value) {
            this.page = 1
            this.filter.page = this.page
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
                this.$emitter.emit('FilterApplyEvent');
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

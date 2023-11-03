<template>

    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Заявки</div>
    </div>

    <div v-if="show">
        <template v-if="items.length > 0">
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
                    <th class="text-left"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, i) in items" :key="i">
                    <td class="text-left">
                        <q-btn icon="open_in_new" dense flat :to="'/order/'+item.id" color="primary" :label="item.id"/>
                    </td>
                    <td class="text-left">
                        <q-chip dark :color="(item.recycle_type === 'ВЭТС') ? 'teal-8' : 'orange-9'" size="12px"
                                v-if="item.recycle_type">
                            {{ (item.car) ? (item.car.category ? item.car.category.title_ru : '') : '-' }} | {{
                                (item.recycle_type) ? item.recycle_type : '-'
                            }}
                        </q-chip>
                    </td>
                    <td class="text-left">{{ (item.car) ? item.car.vin : '-' }}</td>
                    <td class="text-left">{{ (item.car) ? item.car.grnz : '-' }}</td>

                    <td class="text-left">{{ (item.client) ? item.client.title : '-' }}</td>
                    <td class="text-left">{{ (item.client) ? item.client.idnum : '-' }}</td>
                    <td class="text-left">{{ (item.client && item.client.region) ? item.client.region.title : '-' }}</td>
                    <td class="text-left">{{ (item.created) ? item.created : '-' }}</td>
                    <td class="text-left">

                        <div class="q-gutter-sm" v-if="user.role === 'moderator'">
                            <q-chip dark :color="setStatusColor(item.status.id)"
                                    v-if="item.status"
                                    class="text-overline">
                                {{ item.status.title }}
                            </q-chip>
                        </div>

                        <q-badge :color="setApproveColor(item.approve.id)"
                                 v-if="item.approve && user.role === 'operator'">
                            {{ item.approve.title }}
                        </q-badge>
                        <!--                    <q-space class="q-my-xs"/>-->
                        <!--                    <q-badge  :color="(item.signed) ? 'green-5' : 'pink-5'" outline v-if="item.status && item.status.id === 2 && item.signed">-->
                        <!--                        {{ item.signed ? 'Подписано' : 'Не подписано' }}-->
                        <!--                    </q-badge>-->
                    </td>
                    <td class="text-right">
                        <q-btn icon="verified" unelevated dense size="sm" class="text-green-10"
                               label="Скидочный сертификат" icon-right="download" @click="getCert(item.car.certificate.id)"
                               v-if="item.car && item.car.certificate"></q-btn>
                    </td>
                </tr>
                </tbody>
            </q-markup-table>

            <div class="q-pa-lg flex flex-center">
                <q-pagination
                    v-model="page"
                    :max="1"
                    :max-pages="6"
                    boundary-numbers
                    @click="getData()"
                />
            </div>
        </template>

        <template v-else> Нет записей</template>
    </div>

</template>

<script>
import {getOrderList} from "../../services/order";
import {mapGetters} from "vuex";
import {generateCertificate} from "../../services/certificate";
import FileDownload from "js-file-download";

export default {

    data() {
        return {
            data: null,
            sum: null,

            show: false,
            orderDialog: false,
            loading: false,

            sign_statuses: ['Подписан', 'Не подписан'],
            items: [],
            statuses: [],
            page: 1,

            params: {},
            recycleTypeRules: {},
            item: {},

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
                color = 'green-5'
            } else if (id === 2) {
                color = 'pink-5'
            } else if (id === 4) {
                color = 'orange-5'
            }

            return color;
        },

        setStatusColor(id) {
            let color = 'blue-grey-3'
            if (id === 1) {
                color = 'blue-grey-5'
            } else if (id === 2) {
                color = 'blue-5'
            } else if (id === 3) {
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
            this.$emitter.emit('contentLoaded', true);
            getOrderList({page: this.page}).then(res => {
                this.show = true
                this.items = res
            })
        }
    },

    created() {
        this.getData()
    }
}
</script>

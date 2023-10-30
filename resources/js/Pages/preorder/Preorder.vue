<template>

    <div class="q-gutter-sm q-mb-md q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Предварительные заявки</div>

        <div class="flex justify-between" v-if="user && user.role === 'liner'">
            <!--            <q-btn color="purple-10" unelevated icon="tune" class="q-ml-md"/>-->
            <q-btn color="indigo-8" push icon="add" label="Создать заявку" class="q-ml-md text-weight-bold"
                   @click="orderDialog = true"/>
        </div>
    </div>


    <div v-if="show">
        <template v-if="items.length > 0">
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
                        <q-chip dark :color="(item.recycle_type === 1) ? 'teal-8' : 'orange-9'" size="12px">
                            {{ (item.car) ? (item.car.category ? item.car.category.title_ru + ' | ' : '') : '' }}
                            {{ (item.recycle_type) ? ((item.recycle_type === 1) ? 'ВЭТС' : 'ВЭССХТ') : '-' }}
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
                        <q-chip dark :color="setStatusColor(item.status.id)"
                                class="text-overline">
                            {{ item.status.title }}
                        </q-chip>

                        <q-badge v-if="item.transfer && item.transfer.closed !== 2" class="q-ml-md">
                            Выставлена на продажу
                        </q-badge>



                    </td>
                    <td>
                        <q-badge color="deep-orange" v-if="item.order && item.order.status.id === 2 && item.order.approve.id === 3 && !item.order.videoUploaded">
                            В ожидании получения видеозаписи ТС
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

                        <q-badge color="green-8" v-if="item.order && item.order.car && item.order.car.certificate">
                            <router-link to="/certificate">Сертификат выдан</router-link>
                        </q-badge>
                        <!--                    <q-btn icon="verified" unelevated dense size="sm" class="text-green-10" label="Скидочный сертификат"-->
                        <!--                           v-if="item.status.id === 1" icon-right="download"></q-btn>-->
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

        <template v-else> Нет заявок</template>
    </div>


    <q-circular-progress
        indeterminate
        rounded
        size="30px"
        color="primary"
        class="q-ma-md"
        v-if="!show"
    />

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

            params: {},
            recycleTypeRules: {},

            sign_statuses: ['Подписан', 'Не подписан'],
            items: [],

            page: 1,

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
            getOrderList({page: this.page}).then(res => {
                this.items = res
                this.show = true
            })
        }
    },

    created() {
        this.getData()
    }
}
</script>

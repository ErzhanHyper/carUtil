<template>

    <div class="q-gutter-sm q-mb-sm flex justify-between">
        <div class="text-h6 text-primary">Передачи сертификатов</div>
    </div>

    <q-scroll-area
        :visible="true"
        style="height: calc(100vh - 170px);"
    >
        <q-markup-table bordered dense flat>
            <thead>
            <tr>
                <th class="text-left">#</th>
                <th class="text-left">№ сертификата</th>
                <th class="text-left">Новый владелец</th>
                <th class="text-left">ИИН/БИН</th>
                <th class="text-left">Дата создания</th>
                <th class="text-left">Дата отправки</th>
                <th class="text-left">Статус</th>
            </tr>
            </thead>
            <tbody>
            <template v-if="show && items.length > 0">
                <tr v-for="item in items">
                    <td>
                        <router-link :to="'/exchange/'+item.id" class="text-primary">
                            <q-icon name="open_in_new" size="sm"/>
                            {{ item.id }}
                        </router-link>
                    </td>
                    <td>{{ item.certificate ? item.certificate.id : '-' }}</td>
                    <td>{{ item.title }}</td>
                    <td>{{ item.idnum }}</td>
                    <td>{{ item.created }}</td>
                    <td>{{ item.sended_to_approve }}</td>
                    <td>
                        <q-chip v-if="item.status" :color="setStatusColor(item.status.id)" dark size="12px" square>
                            {{ item.status.title }}
                        </q-chip>
                    </td>
                </tr>
            </template>
            <div v-if="show && items.length === 0" class="q-ma-xs">Нет записей</div>
            </tbody>
        </q-markup-table>
    </q-scroll-area>

    <div class="q-pa-sm flex flex-center">
        <q-pagination
            v-if="totalPage > 1"
            v-model="page"
            :max="totalPage"
            :max-pages="10"
            direction-links
            size="12px"
            @click="getData()"
        />
    </div>

</template>

<script>
import {getExchangeList} from "../../services/exchange";

export default {

    data() {
        return {
            filter: {},
            loading1: false,
            loading2: false,
            show: false,
            items: [],
            totalPage: 1,
            page: 1,
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
            }
            return color
        },

        applyFilter() {

        },

        resetFilter() {

        },

        getData() {
            this.$emitter.emit('contentLoaded', true);
            getExchangeList({
                params: {
                    page: this.page
                }
            }).then(res => {
                this.$emitter.emit('contentLoaded', false);
                if (res) {
                    this.show = true
                    this.items = res.items
                    this.totalPage = res.pages
                }
            })
        }
    },

    created() {
        this.getData()
    }
}
</script>

<style scoped>

</style>

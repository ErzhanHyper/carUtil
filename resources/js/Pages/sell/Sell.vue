<template>

    <div class="q-gutter-sm q-mb-md flex justify-between items-center">
        <div class="text-h6 text-primary">Погашения сертификатов</div>
        <div class="flex justify-between">
            <q-btn color="indigo-8" push icon="add" label="Создать" class="q-ml-md text-weight-bold" to="/sell/create" v-if="user && user.role === 'dealer-light'"/>
        </div>
    </div>

<!--    <q-card class="q-mb-none q-mt-md" bordered square flat>-->
<!--        <q-card-section>-->
<!--            <div class="row q-col-gutter-md">-->
<!--                <div class="col col-md-2 col-sm-6 col-xs-12">-->
<!--                    <q-input label="№ погашения" v-model="filter.idnum" outlined dense/>-->
<!--                </div>-->
<!--                <div class="col col-md-2 col-sm-6 col-xs-12">-->
<!--                    <q-input label="№ сертификата" v-model="filter.title" outlined dense/>-->
<!--                </div>-->
<!--                <div class="col col-md-2 col-sm-6 col-xs-12">-->
<!--                    <q-input label="Статус" v-model="filter.title" outlined dense/>-->
<!--                </div>-->
<!--                <div class="col col-md-2 col-sm-6 col-xs-12">-->
<!--                    <q-input label="Дата (с)" v-model="filter.title" outlined dense type="date"/>-->
<!--                </div>-->
<!--                <div class="col col-md-2 col-sm-6 col-xs-12">-->
<!--                    <q-input label="Дата (до)" v-model="filter.title" outlined dense type="date"/>-->
<!--                </div>-->
<!--                <div class="col col-md-2 col-sm-2 col-xs-12">-->
<!--                    <q-btn icon="search" round @click="applyFilter" color="blue-8" :loading="loading1"/>-->
<!--                    <q-btn icon="close" round @click="resetFilter" color="orange-8" size="sm" class="q-ml-sm"-->
<!--                           :loading="loading2"/>-->
<!--                </div>-->
<!--            </div>-->
<!--        </q-card-section>-->
<!--    </q-card>-->

    <q-scroll-area
        :visible="true"
        style="height: calc(100vh - 200px);"
    >
    <q-markup-table flat bordered dense>
        <thead>
        <tr>
            <th class="text-left">#</th>
            <th class="text-left">№ сертификатов</th>
            <th class="text-left">Дата отправки</th>
            <th class="text-left">Дата одобрения</th>
            <th class="text-left">Дата погашения</th>
            <th class="text-left">Сумма</th>
            <th class="text-left">Статус</th>
        </tr>
        </thead>
        <tbody>
            <template v-if="items.length > 0">
                <tr v-for="item in items" >
                    <td><router-link :to="'/sell/'+item.id" class="text-blue-10 text-body2"><q-icon name="open_in_new" size="xs" />{{ item.id }}</router-link></td>
                    <td>
                        <div class="text-body2">№ основного сертификата: <b>{{ item.cert_1 }}</b></div>
                        <div class="text-body2">№ второго сертификата: <b>{{ item.cert_2 }}</b></div>
                        <div class="text-body2">№ третьего сертификата: <b>{{ item.cert_3 }}</b></div>
                        <div class="text-body2">№ четвертого сертификата: <b>{{ item.cert_4 }}</b></div>
                    </td>
                    <td>{{ item.sended_dt ?? '-' }}</td>
                    <td>{{ item.approved_dt ?? '-'  }}</td>
                    <td>{{ item.closed_dt ?? '-' }}</td>
                    <td>{{ item.sum }}</td>
                    <td><q-chip :color="setStatusColor(item.status.id)" v-if="item.status" dark size="12px" square>{{ item.status.title }}</q-chip></td>
                </tr>
            </template>
            <div class="q-ma-xs" v-if="items.length === 0">Нет записей</div>

        </tbody>
    </q-markup-table>
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
import {getSellList} from "../../services/sell";
import {mapGetters} from "vuex";

export default {
    data() {
        return {
            filter: {},
            items: [],
            page: 1,
            totalPage: 1,
            loading1: false,
            loading2: false
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
            if(id === 1){
                color = 'blue-5'
            }else if(id === 2){
                color = 'green-5'
            }else if(id === 3){
                color = 'pink-5'
            }else if(id === 4){
                color = 'deep-orange-5'
            }else if(id === 5){
                color = 'teal-5'
            }
            return color
        },

        applyFilter() {

        },

        resetFilter(){

        },

        getData(){
            this.$emitter.emit('contentLoaded', true);
            getSellList({params: {page: this.page}}).then(res => {
                this.$emitter.emit('contentLoaded', false);
                this.items = res.items
                this.totalPage = res.pages
            })
        }
    },

    created() {
        this.getData()
    },


}
</script>

<style scoped>

</style>

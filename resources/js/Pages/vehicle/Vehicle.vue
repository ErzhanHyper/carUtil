<template>
    <div class="q-gutter-sm q-mb-sm flex justify-between items-center">
        <div class="text-h6 text-primary">Модели ТС</div>
        <div class="flex justify-between">
            <q-btn class="q-ml-md text-weight-bold" color="indigo-8" icon="add" label="Добавить" push
                   to="/vehicle/create"/>
        </div>
    </div>

    <main-filter v-if="showFilter" :apply-action="applyFilter" :data="filter" :filters="['brand', 'class', 'model', 'category', 'manufacture']" class="q-mb-md"/>

    <q-scroll-area
        v-if="show"
        :visible="true"
        style="height: calc(100vh - 240px);"
    >
        <q-markup-table bordered dense flat>
            <thead>
            <tr>
                <th class="text-left">Производитель</th>
                <th class="text-left">Марка</th>
                <th class="text-left">Модель</th>
                <th class="text-left">Категория</th>
                <th class="text-left">Класс</th>
            </tr>
            </thead>
            <tbody>
            <template v-if="items.length > 0">
                <tr v-for="item in items">
                    <td>
                        <router-link :to="'/vehicle/'+item.id" class="text-primary">
                            <q-icon class="q-mr-sm" name="open_in_new" size="18px"/>
                            {{ item.factory }}
                        </router-link>
                    </td>
                    <td>{{ item.brand }}</td>
                    <td>{{ item.model }}</td>
                    <td>{{ item.category }}</td>
                    <td>{{ item.class }}</td>
                </tr>
            </template>
            <tr v-else>
                <td>Нет записей</td>
            </tr>
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
import {getVehicleList} from "../../services/vehicle";
import MainFilter from "../../Components/MainFilter.vue";
import {mapGetters} from "vuex";

export default {
    components: {MainFilter},
    data() {
        return {
            items: [],
            filter: {
                page: this.page
            },
            showFilter: false,

            show: false,
            loading1: false,
            loading2: false,

            page: 1,
            totalPage: 1
        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    methods: {

        async applyFilter(value) {
            this.page = 1
            this.filter.page = this.page
            this.filter = value
            this.getData()
        },

        getData() {
            this.filter.page = this.page
            this.$emitter.emit('contentLoaded', true);
            getVehicleList({params: this.filter}).then(res => {
                this.$emitter.emit('contentLoaded', false);
                this.totalPage = res.pages
                this.items = res.items
                this.show = true
                this.loading1 = false
                this.loading2 = false
            }).finally(() => {
                if(this.user && this.user.role === 'moderator'){
                    this.showFilter = true
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

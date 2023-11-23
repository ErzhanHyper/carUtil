<template>
    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Модели ТС</div>

        <div class="flex justify-between">
            <q-btn color="indigo-8" push icon="add" label="Добавить" class="q-ml-md text-weight-bold" to="/vehicle/create"/>
        </div>
    </div>

    <q-card class="q-mb-none q-mt-md" bordered square flat>
        <q-card-section>
            <div class="row q-col-gutter-md">
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <manufactory-field v-model="filter.manufacture" outlined dense option-value="title"/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input label="Марка" v-model="filter.brand" outlined dense/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input label="Модель" v-model="filter.model" outlined dense/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <category-field outlined dense v-model="filter.category"/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input label="Класс" v-model="filter.class" outlined dense type="number"/>
                </div>
                <div class="col col-md-2 col-sm-2 col-xs-12">
                    <q-btn icon="search" round @click="applyFilter" color="blue-8" :loading="loading1"/>
                    <q-btn icon="close" round @click="resetFilter" color="orange-8" size="sm" class="q-ml-sm"
                           :loading="loading2"/>
                </div>
            </div>
        </q-card-section>
    </q-card>

    <div v-if="show">
        <q-markup-table flat bordered dense >
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
                        <router-link class="text-primary" :to="'/vehicle/'+item.id">
                            <q-icon name="open_in_new" class="q-mr-sm" size="18px"/>{{ item.factory }}
                        </router-link>
                    </td>
                    <td>{{ item.brand }}</td>
                    <td>{{ item.model }}</td>
                    <td>{{ item.category }}</td>
                    <td>{{ item.class }}</td>
                </tr>
            </template>
            <tr v-else><td>Нет записей</td></tr>
            </tbody>

        </q-markup-table>

    </div>

    <div class="q-pa-lg flex flex-center">
        <q-pagination
            v-model="page"
            :min="1"
            :max="totalPage"
            max-pages="10"
            direction-links
            @click="getData()"
            v-if="items.length > 0"
        />
    </div>

</template>

<script>
import {getVehicleList} from "../../services/vehicle";
import ManufactoryField from "../../Components/Fields/ManufactoryField.vue";
import CategoryField from "../../Components/Fields/CategoryField.vue";

export default {
    components: {CategoryField, ManufactoryField},
    data() {
        return {
            items: [],
            filter: {
                page: this.page
            },

            show: false,
            loading1: false,
            loading2: false,

            page: 1,
            totalPage: 1
        }
    },

    methods: {

        applyFilter() {
            this.page = 1
            this.filter.page = this.page
            this.getData()
            this.loading1 = true
        },

        resetFilter() {
            this.page = 1
            this.filter = {}
            this.getData()
            this.loading2 = true
        },

        getData(){
            this.filter.page = this.page
            this.$emitter.emit('contentLoaded', true);
            getVehicleList({params: this.filter}).then(res => {
                this.$emitter.emit('contentLoaded', false);
                this.totalPage = res.pages
                this.items = res.items
                this.show = true
                this.loading1 = false
                this.loading2 = false
            })
        }
    },

    created(){
        this.getData()
    }
}
</script>

<style scoped>

</style>

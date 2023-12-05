<template>
    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Модели ТС</div>
        <div class="flex justify-between">
            <q-btn class="q-ml-md text-weight-bold" color="indigo-8" icon="add" label="Добавить" push
                   to="/vehicle/create"/>
        </div>
    </div>

    <q-card class="q-mb-md q-mt-lg" flat square>
        <q-card-section class="q-pa-none">
            <div class="row q-col-gutter-md">
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <manufactory-field v-model="filter.manufacture" dense option-value="title" outlined clearable/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="filter.brand" dense label="Марка" outlined clearable/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="filter.model" dense label="Модель" outlined clearable/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <category-field v-model="filter.category" dense outlined clearable square="false"/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="filter.class" dense label="Класс" outlined type="number" clearable/>
                </div>
                <div class="col col-lg-1 col-xs-12 col-sm-12">
                    <q-btn :loading="loading1" color="blue-8" icon="search" round @click="applyFilter"/>
                </div>
            </div>
        </q-card-section>
    </q-card>

    <q-scroll-area
        v-if="show"
        :visible="true"
        style="height: calc(100vh - 300px);"
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


    <div class="q-pa-lg flex flex-center">
        <q-pagination
            v-if="items.length > 0"
            v-model="page"
            :max="totalPage"
            :min="1"
            direction-links
            max-pages="10"
            @click="getData()"
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

<template>
    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Модели ТС</div>

        <div class="flex justify-between">
            <q-btn color="indigo-8" push icon="add" label="Добавить" class="q-ml-md text-weight-bold" to="/vehicle/create"/>
        </div>
    </div>

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

export default {
    data() {
        return {
            items: [],
            show: false,
            page: 1,
            totalPage: 1
        }
    },

    methods: {
        getData(){
            this.$emitter.emit('contentLoaded', true);
            getVehicleList({params: {page: this.page}}).then(res => {
                this.totalPage = res.pages
                this.items = res.items
                this.show = true
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

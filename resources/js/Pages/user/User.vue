<template>
    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Пользователи</div>

        <div class="flex justify-between">
            <q-btn class="text-weight-bold" color="indigo-8" icon="add" label="Создать пользователя" push
                   to="/user/create"/>
        </div>
    </div>

    <q-card class="q-mb-md q-mt-lg" flat square>
        <q-card-section class="q-pa-none row q-col-gutter-md">
            <div class="col col-lg-11 col-xs-12 col-sm-12">
                <div class="row q-col-gutter-md">
                    <div class="col col-md-2 col-sm-6 col-xs-12">
                        <q-input v-model="filter.idnum" dense label="ИИН" outlined/>
                    </div>
                    <div class="col col-md-2 col-sm-6 col-xs-12">
                        <q-input v-model="filter.title" dense label="ФИО" outlined/>
                    </div>
                    <div class="col col-md-2 col-sm-6 col-xs-12">
                        <role-field v-model="filter.role" dense outlined/>
                    </div>

                    <div class="col col-md-2 col-sm-6 col-xs-12">
                        <region-field v-model="filter.region" dense emit-value map-options outlined/>
                    </div>

                    <div class="col col-md-2 col-sm-6 col-xs-12">
                        <manufactory-field v-model="filter.manufacture" dense label="Производители (для диллеров)"
                                           outlined/>
                    </div>

                    <div class="col col-md-2 col-sm-6 col-xs-12">
                        <factory-field v-model="filter.factory" :square="false" dense
                                       label="Заводы (для региональных менеджеров)" outlined/>
                    </div>
                </div>
            </div>
            <div class="col col-lg-1 col-xs-12 col-sm-12">
                <q-btn :loading="loading1" color="blue-8" icon="search" round @click="applyFilter"/>
            </div>
        </q-card-section>
    </q-card>

    <q-scroll-area
        :visible="true"
        style="height: calc(100vh - 300px);"
    >
        <q-markup-table bordered dense flat>
            <thead>
            <tr>
                <th class="text-left">Логин</th>
                <th class="text-left">ФИО</th>
                <th class="text-left">Роль</th>
                <th class="text-left">Телефон</th>
                <th class="text-left">Email</th>
                <th class="text-left">Поле (base)</th>
                <th class="text-left">Производитель (диллер)</th>
                <th class="text-left">Регион</th>
            </tr>
            </thead>
            <tbody>
            <template v-if="items.length > 0">
                <tr v-for="(item, i) in items" :key="i">
                    <td>
                        <router-link :to="'/user/'+item.id" class="text-primary text-body2">
                            <q-icon class="q-mr-sm" name="open_in_new" size="sm"/>
                            {{ item.login }}
                        </router-link>
                    </td>
                    <td>{{ item.title }}</td>
                    <td>
                        <q-badge v-if="item.role" :label="item.role_title" size="11px"></q-badge>
                    </td>
                    <td>{{ item.phone }}</td>
                    <td>{{ item.email }}</td>
                    <td>{{ item.base }}</td>
                    <td><span class="text-caption">{{ item.manufacture ? item.manufacture.title : '' }}</span></td>
                    <td>{{ item.region ? item.region.title : '' }}</td>
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
import {getUserCollection} from "../../services/user";
import RoleField from "../../Components/Fields/RoleField.vue";
import ManufactoryField from "../../Components/Fields/ManufactoryField.vue";
import FactoryField from "../../Components/Fields/FactoryField.vue";
import RegionField from "../../Components/Fields/RegionField.vue";

export default {
    components: {RoleField, ManufactoryField, FactoryField, RegionField},
    data() {
        return {
            items: [],
            filter: {
                idnum: '',
                title: '',
                role: '',
                page: this.page
            },
            page: 1,
            totalPage: 1,

            loading1: false,
            loading2: false,
            show: false
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
            getUserCollection({params: this.filter}).then(res => {
                this.$emitter.emit('contentLoaded', false);
                this.items = res.data
                this.totalPage = res.meta.last_page
            }).finally(() => {
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

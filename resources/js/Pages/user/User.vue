<template>
    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Пользователи</div>

        <div class="flex justify-between">
            <q-btn color="indigo-8" push icon="add" label="Создать пользователя" class="q-ml-md text-weight-bold"
                   to="/user/create"/>
        </div>
    </div>

    <q-card class="q-mb-md q-mt-md">
        <q-card-section>
            <div class="row q-col-gutter-md">
                <div class="col col-md-3 col-sm-6 col-xs-12">
                    <q-input label="ИИН" v-model="filter.idnum" outlined dense/>
                </div>
                <div class="col col-md-3 col-sm-6 col-xs-12">
                    <q-input label="ФИО" v-model="filter.title" outlined dense/>
                </div>
                <div class="col col-md-3 col-sm-6 col-xs-12">
                    <role-field v-model="filter.role" outlined dense/>
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
        <q-markup-table flat bordered dense v-if="items.length > 0">
            <thead>
            <tr>
                <th class="text-left">Логин</th>
                <th class="text-left">Телефон</th>
                <th class="text-left">Email</th>
                <th class="text-left">ФИО</th>
                <th class="text-left">Роль</th>
                <th class="text-left">Поле (base)</th>
                <th class="text-left">Адрес</th>
                <th class="text-left">Производитель (диллер)</th>
                <th class="text-left">Регион</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, i) in items" :key="i">
                <td>
                    <router-link :to="'/user/'+item.id" class="text-primary text-body2">
                        <q-icon name="open_in_new" size="sm" class="q-mr-sm"/>
                        {{ item.login }}
                    </router-link>
                </td>
                <td>{{ item.phone }}</td>
                <td>{{ item.email }}</td>
                <td>{{ item.title }}</td>
                <td>{{ item.role }}</td>
                <td>{{ item.base }}</td>
                <td>{{ item.custom_1 }}</td>
                <td>{{ item.custom_2 }}</td>
                <td>{{ item.region ? item.region.title : '' }}</td>
            </tr>
            </tbody>
        </q-markup-table>

        <template v-else>Нет записей</template>

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
import {getUserCollection} from "../../services/user";
import RoleField from "../../Components/Fields/RoleField.vue";

export default {
    components: {RoleField},
    data() {
        return {
            items: [],
            filter: {
                idnum: '',
                title: '',
                role: ''
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
            this.items = []
            this.loading1 = true
            this.getData()
        },

        resetFilter() {
            this.items = []
            this.loading2 = true
            this.filter = {}
            this.getData()
        },

        getData() {
            this.show = false
            getUserCollection({
                idnum: this.filter.idnum,
                title: this.filter.title,
                role: this.filter.role,
                page: this.page
            }).then(res => {
                this.items = res.data
                this.totalPage = res.meta.last_page
                this.show = true
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

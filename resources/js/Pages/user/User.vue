<template>
    <div class="q-gutter-sm q-mb-sm flex justify-between items-center">
        <div class="text-h6 text-primary">Пользователи</div>

        <div class="flex justify-between">
            <q-btn class="text-weight-bold" color="indigo-8" icon="add" label="Добавить" push
                   to="/user/create"/>
        </div>
    </div>

    <main-filter v-if="showFilter" :apply-action="applyFilter" :data="filter" :filters="['idnum', 'fio', 'role', 'region', 'manufacture', 'factory']" class="q-mb-md"/>

    <q-scroll-area
        :visible="true"
        style="height: calc(100vh - 250px);"
    >
        <q-markup-table bordered dense flat>
            <thead>
            <tr>
                <th class="text-left">Логин</th>
                <th class="text-left">Статус</th>
                <th class="text-left">ФИО</th>
                <th class="text-left">Роль</th>
                <th class="text-left">Телефон</th>
                <th class="text-left">Email</th>
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
                    <td>
                        <q-badge :color="item.active ? 'green-5' : 'pink-5'" class="q-mr-xs" rounded/>
                        <span>{{ item.active ? 'Активный' : 'Не активный' }}</span>
                    </td>
                    <td>{{ item.title }}</td>
                    <td>
                        <q-chip v-if="item.role" :label="item.role_title" size="12px" outline color="blue-grey-5"></q-chip>
                    </td>
                    <td>{{ item.phone }}</td>
                    <td>{{ item.email }}</td>
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
import {getUserCollection} from "../../services/user";
import MainFilter from "../../Components/MainFilter.vue";
import {mapGetters} from "vuex";

export default {
    components: {MainFilter},

    data() {
        return {
            items: [],
            filter: {
                idnum: '',
                title: '',
                role: '',
                page: this.page
            },
            showFilter: false,

            page: 1,
            totalPage: 1,

            loading1: false,
            show: false
        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    methods: {
        applyFilter() {
            this.page = 1
            this.filter.page = this.page
            this.loading1 = true
            this.getData()
        },

        getData() {
            this.filter.page = this.page
            this.$emitter.emit('contentLoaded', true);
            getUserCollection({params: this.filter}).then(res => {
                this.$emitter.emit('contentLoaded', false);
                this.$emitter.emit('FilterApplyEvent');
                this.items = res.data
                this.totalPage = res.meta.last_page
            }).finally(() => {
                this.loading1 = false
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

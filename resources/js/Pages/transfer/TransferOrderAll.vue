<template>
    <q-markup-table flat bordered dense>
        <thead>
        <tr>
            <th class="text-left">ID</th>
            <th class="text-left">ФИО</th>
            <th class="text-left">VIN</th>
            <th class="text-left">Категория</th>
            <th class="text-left">Дата</th>
            <th class="text-left"></th>
        </tr>
        </thead>
        <tbody>
        <template v-if="items.length > 0">
            <tr v-for="(item, i) in items" :key="i">
                <td>{{ item.id }}</td>
                <td>{{ item.client.title }}</td>
                <td>{{ item.order.car.vin }}</td>
                <td>{{ item.order.car.category.title_ru }}</td>
                <td>{{ item.date }}</td>
                <td>
                    <q-btn icon="open_in_new" dense flat :to="'/transfer/order/'+item.id" color="primary"
                           label="Открыть"/>
                </td>
            </tr>
        </template>
        <div class="q-ma-xs" v-if="show && items.length === 0">Нет записей</div>
        <q-spinner-dots
            color="primary"
            size="2em"
            class="q-ma-xs"
            v-if="!show"
        />
        </tbody>
    </q-markup-table>
</template>

<script>
import {getTransferList} from "../../services/transfer";
import {mapGetters} from "vuex";

export default {
    data() {
        return {
            tab: 1,
            items: [],

            show: false,
        }
    },

    computed: {
        ...mapGetters({
            authenticated: 'auth/authenticated',
            user: 'auth/user'
        })
    },

    methods: {
        getData() {
            getTransferList({params: {}}).then((res) => {
                this.items = res.items
                this.show = true
            })
        },
    },

    created() {
        this.getData()
    }

}
</script>

<style scoped>

</style>

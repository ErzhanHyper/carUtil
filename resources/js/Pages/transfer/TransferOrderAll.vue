<template>
    <q-markup-table flat bordered dense>
        <thead>
        <tr>
            <th class="text-left">VIN</th>
            <th class="text-left">Категория</th>
            <th class="text-left">ФИО</th>
            <th class="text-left">Дата</th>
        </tr>
        </thead>
        <tbody>
        <template v-if="items.length > 0">
            <tr v-for="(item, i) in items" :key="i">
                <td><router-link :to="'/transfer/order/'+item.id" class="text-primary"><q-icon name="open_in_new" size="sm" />{{ item.order.car.vin }}</router-link></td>
                <td>{{ item.order.car.category.title_ru }}</td>
                <td>{{ item.client.title }}</td>
                <td>{{ item.date }}</td>
            </tr>
        </template>
        <div class="q-ma-xs" v-if="show && items.length === 0">Нет записей</div>
        </tbody>
    </q-markup-table>
    <div class="flex justify-center">
        <q-spinner-dots
            color="primary"
            size="2em"
            class="q-ma-xs"
            v-if="!show"
        />
    </div>
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
                this.$emitter.emit('contentLoaded', false);
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

<template>

    <div v-show="show">
    <template v-if="items.length > 0">
        <q-markup-table flat bordered dense>
            <thead>
            <tr>
                <th class="text-left">ID</th>
                <th class="text-left">ФИО</th>
                <th class="text-left">VIN</th>
                <th class="text-left">Категория</th>
                <th class="text-left">Дата публикации</th>
                <th class="text-left">Статус</th>
                <th class="text-left"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, i) in items" :key="i">
                <td>{{ item.id }}</td>
                <td>{{ item.order.client.title }}</td>
                <td>{{ item.order.car.vin }}</td>
                <td>{{ item.order.car.category.title_ru }}</td>
                <td>{{ item.date }}</td>
                <td>
                    <q-chip color="blue-grey-1" label="Открыта" v-if="item.closed === 0"/>
                    <q-chip color="blue-grey-1" label="В работе" v-if="item.closed === 1"/>
                    <q-chip color="positive" dark label="Завершено" v-if="item.closed === 2"/>
                </td>
                <td>
                    <q-btn icon="open_in_new" dense flat :to="'/transfer/order/'+item.id" color="primary"
                           label="Открыть"/>
                </td>
            </tr>
            </tbody>
        </q-markup-table>
    </template>
    <template v-else>Пусто</template>
    </div>

    <q-circular-progress
        indeterminate
        rounded
        size="30px"
        color="primary"
        class="q-ma-md"
        v-show="!show"
    />
</template>

<script>
import {getTransferCurrentList} from "../../services/transfer";

export default {

    data() {
        return {
            items: [],
            show: false,
        }
    },

    methods: {
        getData() {
            getTransferCurrentList().then((res) => {
                this.items = res
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

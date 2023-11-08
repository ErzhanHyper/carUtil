<template>

    <div class="q-gutter-sm q-mb-md q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Погашения</div>
        <div class="flex justify-between">
            <q-btn color="indigo-8" push icon="add" label="Создать" class="q-ml-md text-weight-bold" to="/sell/create"/>
        </div>
    </div>

    <q-markup-table flat bordered dense>
        <thead>
        <tr>
            <th class="text-left">#</th>
            <th class="text-left">№ сертификатов</th>
            <th class="text-left">Дата отправки</th>
            <th class="text-left">Дата одобрения</th>
            <th class="text-left">Дата погашения</th>
            <th class="text-left">Сумма</th>
            <th class="text-left">Статус</th>
        </tr>
        </thead>
        <tbody>
            <tr v-for="item in items" v-if="items.length > 0">
                <td><router-link :to="'/sell/'+item.id" class="text-blue-10 text-body2"><q-icon name="open_in_new" size="xs" />{{ item.id }}</router-link></td>
                <td>
                    <div class="text-body2">Номер основного сертификата: <b>{{ item.cert_1 }}</b></div>
                    <div class="text-body2">Номер второго сертификата: <b>{{ item.cert_2 }}</b></div>
                    <div class="text-body2">Номер третьего сертификата: <b>{{ item.cert_3 }}</b></div>
                    <div class="text-body2">Номер четвертого сертификата: <b>{{ item.cert_4 }}</b></div>
                </td>
                <td>{{ item.sended_dt ?? '-' }}</td>
                <td>{{ item.approved_dt ?? '-'  }}</td>
                <td>{{ item.closed_dt ?? '-' }}</td>
                <td>{{ item.sum }}</td>
                <td>{{ item.status.title }}</td>
            </tr>
            <tr v-else><td>Нет записей</td></tr>
        </tbody>
    </q-markup-table>

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
import {getSellList} from "../../services/sell";

export default {
    data() {
        return {
            items: [],
            page: 1,
            totalPage: 1
        }
    },

    methods: {
        getData(){
            this.$emitter.emit('contentLoaded', true);
            getSellList({params: {page: this.page}}).then(res => {
                this.items = res.items
                this.totalPage = res.pages
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

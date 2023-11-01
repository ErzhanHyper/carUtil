<template>

    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Передачи сертификатов</div>
    </div>

    <q-markup-table flat bordered dense v-if="items.length > 0">
        <thead>
        <tr>
            <th class="text-left">#</th>
            <th class="text-left">№ сертификата</th>
            <th class="text-left">Новый владелец</th>
            <th class="text-left">ИИН/БИН</th>
            <th class="text-left">Дата создания</th>
            <th class="text-left">Дата отправки</th>
            <th class="text-left">Статус</th>
        </tr>

        <tr v-for="item in items">
            <td>
                <router-link :to="'/exchange/'+item.id" class="text-primary">
                    <q-icon name="open_in_new"/>
                    {{ item.id }}
                </router-link>
            </td>
            <td>{{ item.certificate ? item.certificate.id : '-' }}</td>
            <td>{{ item.title }}</td>
            <td>{{ item.idnum }}</td>
            <td>{{ item.created }}</td>
            <td>{{ item.sended_to_approve }}</td>
            <td>{{ item.status ? item.status.title : '' }}</td>
        </tr>
        </thead>
    </q-markup-table>

    <template v-else>Нет записей</template>

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
import {getExchangeList} from "../../services/exchange";

export default {

    data() {
        return {
            items: [],
            totalPage: 1,
            page: 1,
        }
    },

    methods: {
        getData() {
            getExchangeList({params: {
                page: this.page
            }}).then(res => {
                if(res) {
                    this.items = res.items
                    this.totalPage = res.pages
                }
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

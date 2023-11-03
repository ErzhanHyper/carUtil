<template>

    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Передачи сертификатов</div>
    </div>

    <div v-if="show">
        <q-markup-table flat bordered dense >
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
            </thead>
            <tbody>
                <template v-if="items.length > 0">
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
                    <td><q-chip :color="setStatusColor(item.status.id)" v-if="item.status" dark>{{ item.status.title }}</q-chip></td>
                </tr>
                </template>
                <tr v-else><td>Нет записей</td></tr>
            </tbody>
        </q-markup-table>
    </div>

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
            show: false,
            items: [],
            totalPage: 1,
            page: 1,
        }
    },

    methods: {

        setStatusColor(id) {
            let color = 'blue-grey-5'
            if(id === 1){
                color = 'blue-5'
            }else if(id === 2){
                color = 'green-5'
            }else if(id === 3){
                color = 'pink-5'
            }
            return color
        },

        getData() {
            this.$emitter.emit('contentLoaded', true);
            getExchangeList({params: {
                page: this.page
            }}).then(res => {
                if(res) {
                    this.show = true
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

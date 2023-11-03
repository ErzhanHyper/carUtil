<template>
    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Заводы</div>

        <div class="flex justify-between">
            <q-btn color="indigo-8" push icon="add" label="Добавить" class="q-ml-md text-weight-bold" to="/factory/create"/>
        </div>
    </div>

    <div v-if="show">
    <q-markup-table flat bordered dense >
        <thead>
            <tr>
                <th class="text-left">Название</th>
                <th class="text-left">Адрес</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="items.length > 0">
            <tr v-for="item in items">
                <td>
                    <router-link class="text-primary" :to="'/factory/'+item.id">
                        <q-icon name="open_in_new" class="q-mr-sm" size="18px"/>{{ item.title }}
                    </router-link>
                </td>
                <td>{{ item.address }}</td>
            </tr>
            </template>
            <tr v-else><td>Нет записей</td></tr>
        </tbody>

    </q-markup-table>

    </div>

</template>

<script>
import {getFactoryList} from "../../services/factory";

export default {
    data() {
        return {
            items: [],
            show: false
        }
    },

    methods: {
        getData(){
            this.$emitter.emit('contentLoaded', true);
            getFactoryList().then(res => {
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

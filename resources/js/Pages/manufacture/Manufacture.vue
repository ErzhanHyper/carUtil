<template>
    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Производители</div>
        <div class="flex justify-between">
            <q-btn color="indigo-8" push icon="add" label="Добавить" class="q-ml-md text-weight-bold" to="/manufacture/create"/>
        </div>
    </div>

    <div v-if="show">
        <q-markup-table flat bordered dense >
            <thead>
            <tr>
                <th class="text-left">Название</th>
            </tr>
            </thead>
            <tbody>
            <template v-if="items.length > 0">
                <tr v-for="item in items">
                    <td>
                        <router-link class="text-primary" :to="'/manufacture/'+item.id">
                            <q-icon name="open_in_new" class="q-mr-sm" size="18px"/>{{ item.title }}
                        </router-link>
                    </td>
                </tr>
            </template>
            <tr v-else><td>Нет записей</td></tr>
            </tbody>
        </q-markup-table>

    </div>

</template>

<script>
import {getManufactureList} from "../../services/manufacture";

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
            getManufactureList().then(res => {
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

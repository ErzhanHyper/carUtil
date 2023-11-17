<template>

    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Логи</div>
    </div>

    <q-markup-table flat bordered dense>
        <thead>
        <tr>
            <th class="text-left">События</th>
            <th class="text-left">Объект</th>
            <th class="text-left">Пользователь</th>
            <th class="text-left">Дата</th>
            <th class="text-left">До</th>
            <th class="text-left">После</th>
        </tr>
        </thead>
        <tbody>
            <template v-if="show && items.length > 0">
                <tr v-for="(item, i) in items" :key="i">
                    <td class="text-left">
                        {{ item.event }}
                    </td>
                    <td class="text-left">
                        {{ item.object_type }}
                    </td>
                    <td class="text-left">
                        {{ item.user_id }}
                    </td>
                    <td class="text-left">
                        {{ item.when }}
                    </td>
                    <td class="text-left">
                        <div style="max-width: 300px">
                        {{ item.object_after }}
                        </div>
                    </td>
                    <td class="text-left">
                        <div style="max-width: 300px">
                        {{ item.object_before }}
                        </div>
                    </td>
                </tr>
            </template>
        </tbody>
    </q-markup-table>

</template>

<script>
import {getLogList} from "../../services/log";

export default {
   data(){
       return{
           items: [],
           show:false
       }
   },

    methods:{
       getItems() {
           this.$emitter.emit('contentLoaded', true);
           getLogList().then(res => {
               this.$emitter.emit('contentLoaded', false);
               this.items = res.items.data
               this.show = true
           })
       }
    },

    created() {
       this.getItems()
    }
}
</script>

<style scoped>

</style>

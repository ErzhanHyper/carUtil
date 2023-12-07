<template>
    <q-select dense outlined square v-model="field" label="Категория"
              :options="options"
              map-options
              emit-value
              option-label="title_ru" option-value="id" :readonly="blocked"
              options-cover
              options-dense
              transition-hide="jump-up"
              transition-show="jump-up"
    :loading="options.length === 0"/>
</template>

<script>
import {getCategoryList} from "../../services/category";

export default {
    props: ['data', 'blocked', 'vehicleType'],

    data(){
        return{
            options: [],
            carType: null,
            field: this.data
        }
    },

    methods: {
        getData(){
            getCategoryList({params: {type: this.vehicleType}}).then(res => {
                this.options = res.items
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

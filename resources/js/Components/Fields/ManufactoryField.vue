<template>
    <q-select
        v-model="field"
        clearable
        label="Производители"
        :options="options"
        option-value="id"
        option-label="title"
        :loading="(options.length === 0)"
        emit-value
        map-options
        style="width: 100%"
        hide-hint
        options-cover
        options-dense
        transition-hide="jump-up"
        transition-show="jump-up"
    >
    </q-select>
</template>

<script>
import { getManufactureList} from "../../services/manufacture";

export default {

    props: ['data'],

    data() {
        return{
            field: this.data,
            options: [],
        }
    },

    methods: {
        getItems(){
            getManufactureList().then(res => {
                this.options = res.items
            })
        }
    },

    created() {
        this.getItems()
    }
}
</script>

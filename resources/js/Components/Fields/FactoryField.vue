<template>
    <q-select
        square
        v-model="field"
        label="Завод"
        :options="options"
        :model-value="field"
        option-value="id"
        option-label="title"
        map-options
        emit-value
        clearable
        options-selected-class="text-deep-orange"
        outlined
        dense
        class="q-mb-xs"
        @filter="filterFn"
        use-input
    >
        <template v-slot:option="scope">
            <q-item v-bind="scope.itemProps">
                <q-item-section avatar>
                    <q-icon name="factory"/>
                </q-item-section>
                <q-item-section>
                    <q-item-label>{{ scope.opt.title }}</q-item-label>
                    <q-item-label caption>{{ scope.opt.address }}</q-item-label>
                </q-item-section>
            </q-item>
        </template>
    </q-select>
</template>

<script>
import {getFactoryList} from "../../services/factory";

export default {

    props: ['data'],

    data() {
        return{
            field: this.data,
            options: [],
            items: [],

            showData: false,
        }
    },

    methods: {
        filterFn(val, update) {
            this.options = this.items
            if (val === '') {
                update(() => {
                    this.options.value = this.items
                })
                return
            }
            update(() => {
                const needle = val.toLowerCase()
                this.options = this.items.filter(v => v.title.toLowerCase().indexOf(needle) > -1)

            })
        },

        getItems(){
            getFactoryList().then(res => {
                this.items = res
                this.options = res
            })
        }
    },

    created() {
        this.getItems()
    }
}
</script>

<template>
    <q-select
        square
        v-model="field"
        label="Завод"
        :options="items"
        :model-value="field"
        option-value="id"
        option-label="region_title"
        map-options
        emit-value
        clearable
        options-selected-class="text-deep-orange"
        outlined
        dense
        class="q-mb-xs"
        input-debounce="0"
        :loading="items.length === 0"
    >
        <template v-slot:option="scope">
            <q-item v-bind="scope.itemProps">
                <q-item-section avatar>
                    <q-icon name="factory"/>
                </q-item-section>
                <q-item-section>
                    <q-item-label>{{ scope.opt.region_title }}</q-item-label>
                    <q-item-label caption>{{ scope.opt.title }} | {{ scope.opt.address }}</q-item-label>
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
            items: [],

            showData: false,
        }
    },

    methods: {

        getItems(){
            getFactoryList().then(res => {
                if(res && res.items) {
                    this.items = res.items
                }
            })
        }
    },

    created() {
        this.getItems()
    }
}
</script>

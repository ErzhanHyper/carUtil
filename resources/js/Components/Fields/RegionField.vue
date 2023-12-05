<template>
    <q-select
        v-model="field"
        clearable
        input-debounce="0"
        transition-hide="jump-up"
        transition-show="jump-up"
        label="Регион"
        :options="options"
        option-value="id"
        option-label="title"
        @filter="filterFn"
        :loading="(items.length === 0)"
        emit-value
        map-options
        style="width: 100%"
        v-if="items.length > 0"
    >
        <template v-slot:no-option>
            <q-item>
                <q-item-section class="text-grey">
                    нет результата
                </q-item-section>
            </q-item>
        </template>
    </q-select>
</template>

<script>
import {ref} from 'vue'
import {getRegionList} from "../../services/region";

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
            getRegionList().then(res => {
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

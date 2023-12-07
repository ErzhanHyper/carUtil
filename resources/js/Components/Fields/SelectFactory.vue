<template>
    <q-select
        v-model="field"
        use-input
        clearable
        input-debounce="0"
        label="Завод"
        :options="options"
        option-value="id"
        option-label="title"
        emit-value
        map-options
        @filter="filterFn"
        :loading="(items.length === 0)"
        style="width: 100%"
        options-cover
        options-dense
        transition-hide="jump-up"
        transition-show="jump-up"
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

export default {

    props: ['items', 'data'],

    data() {
        return{
            field: this.data,
            options:this.items
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
        }
    },
}
</script>

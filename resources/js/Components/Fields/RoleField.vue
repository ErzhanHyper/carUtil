<template>
    <q-select
        v-model="field"
        label="Роль"
        :options="items"
        option-label="title"
        option-value="name"
        emit-value
        map-options
        hide-hint
        clearable
        style="width: 100%"
        input-debounce="0"
        options-cover
        options-dense
        transition-hide="jump-up"
        transition-show="jump-up"
    />
</template>

<script>
import {getRoleList} from "../../services/user";

export default {

    props: ['data'],

    data() {
        return {
            field: this.data,
            items: [],
        }
    },

    methods: {
        getItems() {
            getRoleList().then(res => {
                this.items = res
            })
        }
    },

    created() {
        this.getItems()
    }
}
</script>

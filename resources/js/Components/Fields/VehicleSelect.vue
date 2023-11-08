<template>

    <q-select
        v-model="field"
        clearable
        label="Модель, марка"
        :options="options"
        :loading="(options.length === 0)"
        style="width: 100%"
        hide-hint
        options-selected-class="text-deep-orange"
    >
        <template v-slot:option="scope">
            <q-expansion-item
                expand-separator
                group="somegroup"
                header-class="text-weight-bold"
                :label="scope.opt.label"
                :default-opened="hasChild(scope)"
            >
                <template v-for="child in scope.opt.children">
                    <q-item
                        clickable
                        v-ripple
                        v-close-popup
                        @click="getLabel(child)"
                        :class="{ 'bg-light-blue-1': field === child.model }"
                    >
                        <q-item-section>
                            <q-item-label v-html="child.model" class="q-ml-md" ></q-item-label>
                        </q-item-section>
                    </q-item>
                </template>
            </q-expansion-item>
        </template>
    </q-select>
</template>

<script>
import {getVehicleList} from "../../services/vehicle";

export default {
    props: ['data', 'params', 'getParams'],
    data() {
        return{
            field: this.data,
            options: []
        }
    },

    methods: {

        getLabel(value) {
            this.field = value.brand + ', ' + value.model
            this.getParams({
                id: value.id,
                title: this.field
            })
        },

        hasChild (scope) {
            return scope.opt.children.some(c => c.model === this.field)
        },

        getArrayUnique(arr){
            let sorted_arr = arr.slice().sort(); // You can define the comparing function here.
            // JS by default uses a crappy string compare.
            // (we use slice to clone the array so the
            // original array won't be modified)
            let results = [];
            for (let i = 0; i < sorted_arr.length - 1; i++) {
                if (sorted_arr[i + 1] == sorted_arr[i]) {
                    results.push(sorted_arr[i]);
                }
            }
            return results;
        },

        getData(){
            getVehicleList({params: this.params}).then(res => {
                let uniqueBrandArray = [...new Map(res.items.map((item) => [item.brand, item])).values()];
                uniqueBrandArray.map(el => {
                    let arr = []
                    res.items.map(value => {
                        if(value.brand === el.brand) {
                            arr.push(value)
                        }
                    })
                    this.options.push({
                        id: el.id,
                        label: el.brand,
                        children: arr
                    })

                })
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

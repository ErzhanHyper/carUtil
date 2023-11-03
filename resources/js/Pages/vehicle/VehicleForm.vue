<template>
    <div class="row q-col-gutter-md">
        <div class="col col-md-4 q-col-gutter-md">
            <q-select
                v-model="data.factory"
                use-input
                clearable
                input-debounce="0"
                label="Производители"
                :options="options"
                option-value="title"
                option-label="title"
                :loading="(options.length === 0)"
                emit-value
                map-options
                style="width: 100%"
                hide-hint
                outlined
                dense
                square
            >
            </q-select>
            <name-field label="Марка" outlined dense v-model="data.brand"/>
            <name-field label="Модель" outlined dense v-model="data.model"/>
            <q-select dense outlined square v-model="data.category" label="Категория"
                      :options="options_category"
                      map-options
                      emit-value
                      option-label="title_ru" option-value="id"/>
            <name-field label="Класс" outlined dense v-model="data.class"/>
        </div>
    </div>
</template>

<script>
import NameField from "../../Components/Fields/NameField.vue";
import {getManufactureList} from "../../services/manufacture";
import {getCategoryList} from "../../services/category";
export default {
    props: ['data'],
    components: {NameField},

    data() {
        return{
            options: [],
            options_category: []
        }
    },


    created() {
        getManufactureList().then(res => {
            this.options = res.items
        })
        getCategoryList().then(res => {
            this.options_category = res.items
        })
    }
}
</script>

<style scoped>

</style>

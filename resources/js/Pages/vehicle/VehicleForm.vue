<template>
    <div class="row q-col-gutter-md">
        <div class="col col-md-4 q-col-gutter-md">
            <name-field label="Производитель" outlined dense v-model="data.factory"/>
            <name-field label="Марка" outlined dense v-model="data.brand"/>
            <name-field label="Модель" outlined dense v-model="data.model"/>
        </div>
    </div>
</template>

<script>
import AddressField from "../../Components/Fields/AddressField.vue";
import NameField from "../../Components/Fields/NameField.vue";
export default {
    props: ['data'],
    components: {NameField}
}
</script>

<style scoped>

</style>

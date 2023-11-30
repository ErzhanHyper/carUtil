<template>
    <div class="row q-col-gutter-md">
        <div class="col col-md-4 q-col-gutter-md">
            <name-field label="Название" outlined dense v-model="data.title"/>
            <address-field label="Адрес" outlined dense v-model="data.address"/>
            <region-field outlined dense v-model="data.region_id"/>
        </div>
    </div>
</template>

<script>
import AddressField from "../../Components/Fields/AddressField.vue";
import NameField from "../../Components/Fields/NameField.vue";
import RegionField from "../../Components/Fields/RegionField.vue";
export default {
    props: ['data'],
    components: {RegionField, NameField, AddressField}
}
</script>

<style scoped>

</style>

<template>
    <div class="row q-col-gutter-md">
        <div class="col col-md-4 q-col-gutter-md">
            <name-field label="Название" outlined dense v-model="data.title"/>
            <address-field label="Адрес" outlined dense v-model="data.address"/>
        </div>
    </div>
</template>

<script>
import AddressField from "../../Components/Fields/AddressField.vue";
import NameField from "../../Components/Fields/NameField.vue";
export default {
    props: ['data'],
    components: {NameField, AddressField}
}
</script>

<style scoped>

</style>

<template>
    <div class="col col-lg-4 col-md-6 col-sm-12 col-xs-12">
        <div class="row q-col-gutter-md">

            <q-toggle v-model="data.active" label="Активный"/>

            <div class="col col-md-12 col-xs-12">
                <client-id-field v-model="data.login" outlined dense/>
            </div>
            <div class="col col-md-12 col-xs-12">
                <name-field v-model="data.title" outlined dense/>
            </div>

            <div class="col col-md-12 col-xs-12">
                <name-field v-model="data.for_docs" outlined dense
                            label="ФИО в родительном падеже (для документов)"/>
            </div>

            <div class="col col-md-12 col-xs-12">
                <email-field outlined dense v-model="data.email"/>
            </div>

            <div class="col col-md-12 col-xs-12">
                <phone-field outlined dense v-model="data.phone"/>
            </div>

            <div class="col col-md-12 col-xs-12">
                <address-field label="Адрес салона или компании (для диллеров)" v-model="data.custom_1" outlined dense/>
            </div>

        </div>
    </div>

    <div class="col col-lg-4 col-md-6 col-sm-12 col-xs-12">
        <div class="row q-col-gutter-md" style="margin-top: 40px">
            <div class="col col-md-12 col-xs-12">
                <region-field v-model="data.region" :model-value="data.region" emit-value map-options outlined dense square/>
            </div>

            <div class="col col-md-12 col-xs-12">
                <role-field v-model="data.role" :model-value="data.role" outlined dense square/>
            </div>

            <div class="col col-md-12 col-xs-12" v-show="data.role === 'dealer-light' || data.role === 'dealer-chief'">
                <manufactory-field v-model="data.manufacture" :model-value="data.manufacture" outlined dense label="Производители (для диллеров)" square/>
            </div>

            <div class="col col-md-12 col-xs-12" v-show="data.role === 'operator' || data.role === 'operator-chief'">
                <factory-field v-model="data.factory" :model-value="data.factory" outlined dense label="Заводы (для региональных менеджеров)" square/>
            </div>

            <div class="col col-md-12 col-xs-12" v-show="data.role === 'dealer-light' || data.role === 'dealer-chief'">
                <q-input label="Доверенность (для диллеров)" v-model="data.base" outlined dense square/>
            </div>

            <div class="col col-md-12 col-xs-12">
                <client-id-field v-model="data.bin" outlined dense
                                 label="БИН для авторизации диллеров и операторов"/>
            </div>
        </div>
    </div>

</template>

<script>
import EmailField from "../../Components/Fields/EmailField.vue";
import PhoneField from "../../Components/Fields/PhoneField.vue";
import RegionField from "../../Components/Fields/RegionField.vue";
import NameField from "../../Components/Fields/NameField.vue";
import ClientIdField from "../../Components/Fields/ClientIdField.vue";
import AddressField from "../../Components/Fields/AddressField.vue";
import ManufactoryField from "../../Components/Fields/ManufactoryField.vue";
import RoleField from "../../Components/Fields/RoleField.vue";
import FactoryField from "../../Components/Fields/FactoryField.vue";

export default {
    props: ['data', 'getData'],

    components: {
        FactoryField,
        RoleField,
        ManufactoryField, AddressField, ClientIdField, NameField, RegionField, PhoneField, EmailField
    },

    data() {
        return {

        }
    },

    mounted() {

    }
}
</script>

<style scoped>

</style>

<template>
    <div class="col col-lg-4 col-md-6 col-sm-12 col-xs-12">
        <div class="row q-col-gutter-md">

            <q-toggle v-model="item.active" label="Активный"/>

            <div class="col col-md-12 col-xs-12">
                <client-id-field v-model="item.login" outlined dense/>
            </div>
            <div class="col col-md-12 col-xs-12">
                <name-field v-model="item.title" outlined dense/>
            </div>

            <div class="col col-md-12 col-xs-12">
                <name-field v-model="item.for_docs" outlined dense
                            label="ФИО в родительном падеже (для документов)"/>
            </div>

            <div class="col col-md-12 col-xs-12">
                <email-field outlined dense v-model="item.email"/>
            </div>

            <div class="col col-md-12 col-xs-12">
                <phone-field outlined dense v-model="item.phone"/>
            </div>

            <div class="col col-md-12 col-xs-12">
                <address-field label="Адрес салона или компании (для диллеров)" v-model="item.custom_1" outlined dense/>
            </div>

        </div>
    </div>

    <div class="col col-lg-4 col-md-6 col-sm-12 col-xs-12">
        <div class="row q-col-gutter-md" style="margin-top: 40px">
            <div class="col col-md-12 col-xs-12">
                <region-field v-model="item.region" outlined dense square/>
            </div>

            <div class="col col-md-12 col-xs-12">
                <role-field v-model="item.role" outlined dense square/>
            </div>

            <div class="col col-md-12 col-xs-12" v-show="item.role === 'dealer-light' || item.role === 'dealer-chief'">
                <manufactory-field v-model="item.manufactory" outlined dense label="Производители (для диллеров)" square/>
            </div>

            <div class="col col-md-12 col-xs-12" v-show="item.role === 'operator' || item.role === 'operator-chief'">
                <factory-field v-model="item.factory" outlined dense label="Заводы (для региональных менеджеров)" square/>
            </div>

            <div class="col col-md-12 col-xs-12" v-show="item.role === 'dealer-light' || item.role === 'dealer-chief'">
                <q-input label="Доверенность (для диллеров)" v-model="item.base" outlined dense square/>
            </div>

            <div class="col col-md-12 col-xs-12">
                <client-id-field v-model="item.bin" outlined dense
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
            item: {
                active: false,
                title: '',
                login: '',
                bin: ''
            }
        }
    },

    mounted() {
        if (this.data) {
            this.item = this.data
        }

        this.$emitter.on('UserCreateEvent', () => {
            this.getData(this.item)
        })
    }
}
</script>

<style scoped>

</style>

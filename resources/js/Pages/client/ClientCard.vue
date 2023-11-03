<template>

    <q-card>
        <q-card-section class="q-pb-xs">
            <div class="text-body2">Клиент</div>
            <div class="text-body1">{{ item.title }}</div>
        </q-card-section>

        <q-separator dark inset/>

        <q-card-section>
            <div class="row q-col-gutter-md q-mb-md">
                <div class="col col-md-12 col-xs-12">
                    <ClientTypeField :data="item.client_type_id" v-model="item.client_type_id" dense outlined square
                                     :readonly="blocked"/>
                </div>
            </div>

            <div class="row q-col-gutter-md q-mb-none">
                <div class="col col-md-12 col-xs-12">
                    <NameField :data="item.title" v-model="item.title" dense outlined square
                               :rules="[val => !!val || '*']" :readonly="blocked"/>
                </div>
            </div>

            <div class="row q-col-gutter-md q-mb-md">
                <div class="col col-md-6 col-xs-12">
                    <ClientIdField :data="item.idnum" v-model="item.idnum" dense outlined square :readonly="blocked"/>
                </div>
                <div class="col col-md-6 col-xs-12">
                    <ClientUdNumField :data="item.ud_num" v-model="item.ud_num" dense outlined square
                                      :readonly="blocked"/>
                </div>
            </div>

            <div class="row q-col-gutter-md">
                <div class="col col-md-6 col-xs-12">
                    <ClientUdTypeField :data="item.ud_issued_id" v-model="item.ud_issued_id" dense outlined square
                                       :readonly="blocked" hint="орган выдачи удостоверения личности"/>
                </div>
                <div class="col col-md-6 col-xs-12">
                    <DataField :data="item.ud_expired" v-model="item.ud_expired"
                               hint="дата выдачи удостоверения личности" dense outlined square :readonly="blocked"/>
                </div>
            </div>
        </q-card-section>

        <q-card-section class="q-pb-xs">
            <div class="text-body2">Контактные данные</div>
        </q-card-section>
        <q-card-section>
            <div class="q-gutter-md">
                <div class="col col-md-12">
                    <RegionField :data="item.region_id" v-model="item.region_id" dense outlined square
                                 :readonly="blocked"/>
                </div>
                <div class="col col-md-12">
                    <AddressField :data="item.address" v-model="item.address" dense outlined square
                                  :readonly="blocked"/>
                </div>
                <div class="col col-md-12">
                    <PhoneField :data="item.phone" v-model="item.phone" dense outlined square :readonly="blocked"/>
                </div>
                <div class="col col-md-12">
                    <EmailField :data="item.email" v-model="item.email" dense outlined square :readonly="blocked"/>
                </div>
            </div>
        </q-card-section>
    </q-card>

</template>

<script>
import ClientUdNumField from "@/Components/Fields/ClientUdNumField.vue";
import ClientTypeField from "@/Components/Fields/ClientTypeField.vue";
import ClientIdField from "@/Components/Fields/ClientIdField.vue";
import NameField from "@/Components/Fields/NameField.vue";
import RegionField from "@/Components/Fields/RegionField.vue";
import AddressField from "@/Components/Fields/AddressField.vue";
import PhoneField from "@/Components/Fields/PhoneField.vue";
import EmailField from "@/Components/Fields/EmailField.vue";
import ClientUdTypeField from "@/Components/Fields/ClientUdTypeField.vue";
import DataField from "@/Components/Fields/DataField.vue";
import BankField from "@/Components/Fields/BankField.vue";
import BankNumField from "@/Components/Fields/BankNumField.vue";
import {mapGetters} from "vuex";

export default {
    components: {
        BankNumField,
        BankField,
        DataField,
        ClientUdTypeField,
        EmailField, PhoneField, AddressField, RegionField, ClientTypeField, ClientIdField, NameField, ClientUdNumField
    },

    props: ['data', 'getClient', 'blocked'],

    data() {
        return {
            item: {
                region_id: null,
                bank_id: null,
                iban: null,
                ud_issued_id: null,
                ud_num: null,
                idnum: null,
                client_type_id: null,
                title: '',
            },
        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    created() {
        if (this.data) {
            this.item = this.data
            if (this.item.ud_expired) {
                this.item.ud_expired = this.$moment(this.$moment(this.item.ud_expired, 'YYYY-MM-DD').toDate()).format('YYYY-MM-DD')
            }
        }
    },

    mounted() {
        this.$emitter.on('ClientCardEvent', () => {
            this.getClient(this.item)
        })
    }
}
</script>

<style scoped>

</style>

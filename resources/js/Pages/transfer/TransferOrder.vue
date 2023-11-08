<template>
    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Продажа ТС</div>
    </div>

    <q-tabs
        v-model="tab"
        dense
        class="text-grey"
        active-color="primary"
        indicator-color="primary"
        style="width: 400px"
    >
        <q-tab :name="1" label="Все" />
        <q-tab :name="2" label="Мои" />
    </q-tabs>

    <q-tab-panels v-model="tab" animated vertical>
        <q-tab-panel :name="1" class="q-pa-none">
            <transfer-order-all class="q-pt-sm"/>
        </q-tab-panel>
        <q-tab-panel :name="2" class="q-pa-none">
            <transfer-order-current class="q-pt-sm"/>
        </q-tab-panel>
    </q-tab-panels>

    <q-dialog v-model="transferTermsDialog">
        <q-card>
            <q-card-section>
                <div class="text-h6">Общая информация</div>
            </q-card-section>
            <q-separator/>
            <q-card-section style="max-height: 70vh" class="scroll">
                Продажа транспортного средства
            </q-card-section>
            <q-separator/>
            <q-card-actions align="right">
                <q-btn flat label="Закрыть" color="primary" v-close-popup/>
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>

<script>
import {mapGetters} from "vuex";

import TransferOrderCurrent from "./TransferOrderCurrent.vue";
import TransferOrderAll from "./TransferOrderAll.vue";

export default {
    components: {TransferOrderCurrent, TransferOrderAll},
    data() {
        return {
            tab: 1,
            transferTermsDialog: true,
        }
    },

    computed: {
        ...mapGetters({
            authenticated: 'auth/authenticated',
            user: 'auth/user'
        })
    },

    methods: {

    },

    created() {
        this.$emitter.emit('contentLoaded', true);

        if (localStorage.getItem('transfer_terms')) {
            this.transferTermsDialog = false
        } else {
            localStorage.setItem('transfer_terms', JSON.stringify(true))
        }
    }

}
</script>

<style scoped>

</style>

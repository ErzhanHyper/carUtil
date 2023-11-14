<template>
    <div class="q-gutter-sm">
        <q-btn :loading="loading"
               square size="12px"
               color="light-green"
               label="Выдать сертификат"
               icon="verified"
               @click="issueCert()"
               v-if="show">
        </q-btn>
    </div>
</template>

<script>
import {storeCertOrder} from "../../../services/order";

export default {

    props: ['order_id', 'show'],

    data(){
        return{
            loading: false
        }
    },

    methods: {
        issueCert() {
            this.loading = true
            storeCertOrder({
                order_id: this.order_id,
            }).then(res => {
                this.$emitter.emit('orderActionEvent')
            }).finally(() => {
                this.loading = false
            })
        }
    }
}
</script>

<style scoped>

</style>

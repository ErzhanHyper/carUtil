<template>
    <div>
        <q-space/>
        <template v-if="show">
            <div class="q-gutter-sm">
                <q-btn icon="sell" dense size="md" color="pink-10" label="Выставить ТС на продажу"
                       @click="sellPreorder" :loading="loading"></q-btn>
            </div>
        </template>

        <template v-if="!show && transfer && transfer.closed !== 2">
            <div class="text-blue-10">
                ТС выставлен на продажу

                <q-space/>

                <div class="text-right">
                    <q-btn flat
                           icon="open_in_new"
                           dense
                           size="sm"
                           color="blue-grey-10"
                           label="Перейти"
                           :to="'/transfer/order/'+transfer.id">
                    </q-btn>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import {Notify} from "quasar";
import {storeTransfer} from "../../services/transfer";

export default {
    props: ['show', 'blocked', 'transfer', 'order_id'],

    data() {
        return {
            loading: false,
        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    methods: {
        sellPreorder() {
            this.loading = true
            storeTransfer({
                order_id: this.order_id
            }).then(res => {
                this.$emitter.emit('preorderSellEvent')
                if(res && res.message !== '') {
                    Notify.create({
                        message: res.message,
                        position: 'bottom-right',
                        type: res.success === true ? 'positive' : 'warning'
                    })
                }
            }).finally(() => {
                this.loading = false
            })
        }
    }
}
</script>

<style scoped>

</style>

<template>
    <div class="col col-md-12" v-if="show">
        <div class="flex justify-between" >
            <div class="q-gutter-sm">
                <q-btn :loading="loading1"
                       square size="12px"
                       color="light-green"
                       label="Одобрить"
                       @click="send('approve')"
                       icon="send"
                       :disabled="disabled">
                </q-btn>

                <q-btn square size="12px"
                       color="orange-5"
                       icon="message"
                       @click="send('revision')"
                       >
                </q-btn>

                <q-btn square size="12px"
                       color="red-5"
                       label="Отклонить"
                       :loading="loading2"
                       @click="send('decline')"
                       icon="block">
                </q-btn>
            </div>

        </div>
    </div>
</template>

<script>
import {approveExchange, declineExchange} from "../../services/exchange";
import {Notify} from "quasar";

export default {

    props: ['show', 'data'],

    data(){
        return{
            loading1: false,
            loading2: false,
        }
    },

    methods: {
        send(value){
            if(value === 'approve'){
                this.loading1 = true
                approveExchange(this.data.id).then((res) => {
                    Notify.create({
                        message: res.message,
                        position: 'bottom-right',
                        type: res.success ? 'positive' : 'warning'
                    })
                    this.$emitter.emit('ExchangeActionEvent')
                }).finally(() => {
                    this.loading1 = false
                })
            }

            if(value === 'decline'){
                this.loading2 = true
                declineExchange(this.data.id).then((res) => {
                    Notify.create({
                        message: res.message,
                        position: 'bottom-right',
                        type: res.success ? 'positive' : 'warning'
                    })
                    this.$emitter.emit('ExchangeActionEvent')
                }).finally(() => {
                    this.loading2 = false
                })
            }
        }
    }
}
</script>

<style scoped>

</style>

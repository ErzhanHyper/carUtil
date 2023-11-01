<template>
    <div class="col col-md-12" v-if="show">
        <div class="flex justify-between" >
            <div class="q-gutter-sm">
                <q-btn :loading="loading"
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
            loading: false,
        }
    },

    methods: {
        send(value){
            if(value === 'approve'){
                approveExchange(this.data.id).then((res) => {
                    Notify.create({
                        message: res.message,
                        position: 'bottom-right',
                        type: res.success ? 'positive' : 'warning'
                    })
                    this.$emitter.emit('ExchangeActionEvent')
                })
            }

            if(value === 'decline'){
                declineExchange(this.data.id).then((res) => {
                    Notify.create({
                        message: res.message,
                        position: 'bottom-right',
                        type: res.success ? 'positive' : 'warning'
                    })
                    this.$emitter.emit('ExchangeActionEvent')
                })
            }
        }
    }
}
</script>

<style scoped>

</style>

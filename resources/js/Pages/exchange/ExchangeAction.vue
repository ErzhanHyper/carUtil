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
                       :disabled="disabled"
                       >
                </q-btn>

                <q-btn square size="12px"
                       color="orange-5"
                       icon="message"
                       @click="commentDialog = true"
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

    <q-dialog v-model="commentDialog" persistent>
        <q-card style="width: 800px">
            <q-card-section class="flex justify-between q-pb-none">
                <q-space/>
                <q-icon name="close" size="sm" flat v-close-popup class="cursor-pointer"/>
            </q-card-section>
            <q-card-section class="q-pt-sm">
                <q-input type="textarea" v-model="comment" outlined rows="3" label="Комментарий" class="text-body1"/>
            </q-card-section>
            <q-card-actions>
                <q-space/>
                <q-btn label="Отправить" color="blue-8" @click="send('message')" :loading="loading3" />
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>

<script>
import {approveExchange, declineExchange, messageExchange} from "../../services/exchange";
import {Notify} from "quasar";
import {messageSell} from "../../services/sell";

export default {

    props: ['show', 'data'],

    data(){
        return{
            disabled: false,
            loading1: false,
            loading2: false,
            loading3: false,
            commentDialog: false,

            comment: ''
        }
    },

    methods: {
        send(value){
            if(value === 'approve'){
                this.loading1 = true
                approveExchange(this.data.id).then((res) => {
                    Notify.create({
                        message: res.message,
                        position: 'bottom',
                        type: res.success ? 'positive' : 'warning'
                    })
                    this.$emitter.emit('ExchangeActionEvent')
                }).finally(() => {
                    this.loading1 = false
                })
            }

            if(value === 'decline'){
                this.loading2 = true
                this.disabled = true
                declineExchange(this.data.id).then((res) => {
                    Notify.create({
                        message: res.message,
                        position: 'bottom',
                        type: res.success ? 'positive' : 'warning'
                    })
                    this.$emitter.emit('ExchangeActionEvent')
                }).finally(() => {
                    this.loading2 = false
                    this.disabled = false
                })
            }

            if(value === 'message'){
                this.loading3 = true
                messageExchange(this.data.id, {comment: this.comment}).then((res) => {
                    this.commentDialog = false
                    Notify.create({
                        message: res.message,
                        position: 'bottom',
                        type: res.success ? 'positive' : 'warning'
                    })
                }).finally(() => {
                    this.loading3 = false
                })
            }
        }
    }
}
</script>

<style scoped>

</style>

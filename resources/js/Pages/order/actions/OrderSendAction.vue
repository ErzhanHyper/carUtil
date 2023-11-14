<template>
    <div class="q-gutter-sm">
        <q-btn :loading="loading"
               square
               size="12px"
               color="blue-8"
               label="Отправить на рассмотрение модератору"
               @click="sendData('send_to_moderator')"
               icon="send"
               v-if="permissions.showSendToApproveAction">
        </q-btn>
        <q-btn :loading="loading"
                square
                size="12px"
                color="light-green"
                label="Подписать и отправить модератору"
                @click="sendData('sign_uploaded_video')"
                icon="send"
                v-if="permissions.showSendToIssueCertAction">
        </q-btn>
    </div>
</template>

<script>
import {sendToApproveOrder, sendToSignOrder} from "../../../services/order";
import {signData} from "../../../services/sign";

export default {
    props: ['order_id', 'permissions'],

    data(){
        return{
            loading: false,
        }
    },

    methods: {
        sendData(value) {
            if(value === 'send_to_moderator'){
                this.loading = true
                sendToApproveOrder(this.order_id).then(res => {
                    this.$emitter.emit('orderActionEvent')
                }).finally(() => {
                    this.loading = false
                })
            }else {
                signData().then(res => {
                    if (res) {
                        this.loading = true
                        sendToSignOrder(this.order_id, {
                            sign: res,
                        }).then(el => {
                            this.$emitter.emit('orderActionEvent')
                        }).finally(() => {
                            this.loading = false
                        })
                    }
                })
            }
        },
    }
}
</script>

<style scoped>

</style>

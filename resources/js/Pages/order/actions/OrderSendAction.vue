<template>
    <div class="q-gutter-sm">
        <q-btn v-if="permissions.showSendToApproveAction"
               :loading="loading"
               color="blue-8"
               icon="send"
               label="Отправить модератору"
               size="12px"
               push
               @click="sendData('send_to_moderator')">
        </q-btn>
        <q-btn v-if="permissions.showSendToIssueCertAction"
               :loading="loading"
               color="indigo-8"
               icon="send"
               label="Подписать и отправить модератору"
               size="12px"
               push
               @click="sendData('sign_uploaded_video')">
        </q-btn>
    </div>
</template>

<script>
import {sendToApproveOrder, sendToSignOrder} from "../../../services/order";
import {signData} from "../../../services/sign";
import {Notify} from "quasar";

export default {
    props: ['order_id', 'permissions'],

    data() {
        return {
            loading: false,
        }
    },

    methods: {
        sendData(value) {
            this.$emitter.emit('orderBlockEvent', true)
            if (value === 'send_to_moderator') {
                this.loading = true
                sendToApproveOrder(this.order_id).then(res => {
                    if (res.success === true) {
                        this.$emitter.emit('orderActionEvent')
                    }
                    if (res.message && res.message !== '' && res.success === false) {
                        let messages = JSON.parse(res.message)
                        this.showNotify(messages)
                    }
                }).finally(() => {
                    this.loading = false
                })
            } else {
                signData().then(res => {
                    if (res) {
                        setTimeout(() => {
                            this.$emitter.emit('orderBlockEvent', true)
                        }, 10)
                        this.loading = true
                        sendToSignOrder(this.order_id, {
                            sign: res,
                        }).then(el => {
                            if (el.success === true) {
                                this.$emitter.emit('orderActionEvent')
                            }

                        }).finally(() => {
                            this.loading = false
                        })
                    }
                }).finally(() => {
                    this.$emitter.emit('orderBlockEvent', false)
                })
            }
        },

        showNotify(messages) {
            let messageData = []
            Object.values(messages).map((el, i) => {
                if(i > 0) {
                    messageData.push('<div></div>*' + el[0])
                }
            });
            Notify.create({
                caption: messageData,
                message: messages[0],
                position: 'top-right',
                type: 'info',
                html: true,
                timeout: 20000,
                actions: [{icon: 'close', color: 'white'}]
            })
        }

    },

    created() {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            this.permissions.showSendToIssueCertAction = false
        }
    }
}
</script>

<style scoped>

</style>

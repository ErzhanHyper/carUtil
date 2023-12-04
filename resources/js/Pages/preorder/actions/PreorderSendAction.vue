<template>
    <q-btn
        v-if="show"
        :loading="loading"
        color="blue-8"
        icon="send" label="Отправить"
        push
        size="12px"
        @click="sendData"
    >
    </q-btn>
</template>

<script>
import {Notify} from "quasar";
import {sendOrder} from "../../../services/preorder";

export default {
    props: ['preorder_id', 'show', 'car', 'client'],

    data() {
        return {
            loading: false,
        }
    },

    methods: {
        sendData() {
            this.loading = true
            this.$emitter.emit('preorderSendActionEvent', true)

            sendOrder(this.preorder_id, {car: this.car, client: this.client}).then(res => {
                if (res) {
                    if (res.success) {
                        this.$emitter.emit('preorderActionEvent')
                        Notify.create({
                            message: res.message,
                            position: 'bottom',
                            type: 'positive'
                        })
                    }else{
                        this.$emitter.emit('preorderSendActionEvent', false)
                    }
                    if (res.message && res.message !== '' && res.success === false) {
                        if (res.message && res.message !== '') {
                            let messages = JSON.parse(res.message)
                            let messageData = []
                            Object.values(messages).map((el, i) => {
                                if(i > 0) {
                                    messageData.push('<div></div>*' + el[0])
                                }
                            });
                            this.showNotify(messageData, messages[0])
                        }
                    }
                }
            }).catch(reject => {
                this.$emitter.emit('preorderSendActionEvent', false)
                if (reject.message && reject.message !== '') {
                    let messages = JSON.parse(reject.message)
                    let messageData = []
                    Object.values(messages).map((el, i) => {
                        messageData.push('<div></div>*' + el[0])
                    });
                    this.showNotify(messageData, '')
                }

            }).finally(() => {
                this.loading = false
            })
        },

        showNotify(messages, title) {
            Notify.create({
                caption: messages,
                message: title ?? '',
                position: 'top-right',
                type: 'info',
                html: true,
                timeout: 20000,
                actions: [{icon: 'close', color: 'white'}]
            })
        }
    }
}
</script>

<style scoped>

</style>

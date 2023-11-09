<template>
    <div class="col col-md-12">
        <div class="flex justify-between" >
            <div class="q-gutter-sm">
                <q-btn :loading="loading"
                       square size="12px"
                       color="light-green"
                       label="Одобрить"
                       @click="send('approve')"
                       icon="send"
                       :disabled="disabled"
                       v-if="showApproveAction">
                </q-btn>

                <q-btn square size="12px"
                       color="orange-5"
                       label="На доработку"
                       @click="send('revision')"
                       icon="keyboard_return"
                       v-if="showApproveAction">
                </q-btn>

                <q-btn square size="12px"
                       color="red-5"
                       label="Отклонить"
                       @click="send('decline')"
                       icon="block"
                       v-if="showApproveAction">
                </q-btn>

                <q-btn :loading="loading1" size="12px" color="light-green"
                       label="Выдать сертификат"
                       icon="verified" push
                       @click="issueCert()"
                       v-if="showCertAction">
                </q-btn>
            </div>

            <div class="q-gutter-sm">
                <q-btn square size="12px" color="primary" label="Проверка в КАП" icon="add_task" @click="kapDialog = true" v-if="showApproveAction"/>
            </div>

        </div>
    </div>

    <q-dialog v-model="commentDialog">
        <q-card style="width: 800px">
            <q-card-section>
                <q-input type="textarea" v-model="comment" outlined rows="3" label="Комментарий" class="text-body1"/>
            </q-card-section>
            <q-card-actions>
                <q-space/>
                <q-btn label="Отправить" color="primary" @click="declineAction" :loading="loading2" v-if="action === 'decline'"/>
                <q-btn label="Отправить" color="primary" @click="revisionAction" :loading="loading2" v-if="action === 'revision'"/>
            </q-card-actions>
        </q-card>
    </q-dialog>

    <q-dialog v-model="kapDialog" size="md" persistent>
        <order-kap :order_id="order_id" :data="data"/>
    </q-dialog>

</template>

<script>
import {signData} from "../../services/sign";
import {storeCertOrder, declineOrder, revisionOrder, approveOrder} from "../../services/order";
import OrderKap from "@/Pages/order/OrderKap.vue";
import {Notify} from "quasar";

export default {
    props: ['showCertAction', 'showApproveAction', 'order_id', 'data'],

    components: {OrderKap},

    data() {
        return{
            commentDialog: false,
            kapDialog: false,
            loading: false,
            loading1: false,
            loading2: false,
            action: '',
            comment: '',
        }
    },

    methods: {

        send(value) {
            this.action = value
            if (value === 'approve') {
                signData().then(res => {
                    if(res) {
                        this.signHash = res
                        this.approveAction()
                    }
                })
            } else {
                this.commentDialog = true
            }
        },

        approveAction() {
            this.loading = true
            approveOrder({
                comment: this.comment,
                sign: this.signHash,
                order_id: this.order_id
            }).then((res) => {
                if(res) {
                    if (res.success) {
                        this.commentDialog = false
                        this.$emitter.emit('orderActionEvent')
                    }
                    if (res.message !== '') {
                        Notify.create({
                            message: res.message,
                            position: 'bottom-right',
                            type: res.success === true ? 'positive' : 'warning'
                        })
                    }
                }
            }).finally(() => {
                this.loading = false
            })
        },

        declineAction() {
            this.loading2 = true
            declineOrder({
                comment: this.comment,
                order_id: this.order_id
            }).then(() => {
                this.commentDialog = false
                this.$emitter.emit('orderActionEvent')
            }).finally(() => {
                this.loading2 = false
            })
        },

        revisionAction() {
            this.loading2 = true
            revisionOrder({
                comment: this.comment,
                order_id: this.order_id
            }).then(() => {
                this.commentDialog = false
                this.$emitter.emit('orderActionEvent')
            }).finally(() => {
                this.loading2 = false
            })
        },


        issueCert() {
            this.loading1 = true
            storeCertOrder({
                order_id: this.order_id,
            }).then(res => {
                this.$emitter.emit('orderActionEvent')
            }).finally(() => {
                this.loading1 = false
            })
        }
    },

}
</script>


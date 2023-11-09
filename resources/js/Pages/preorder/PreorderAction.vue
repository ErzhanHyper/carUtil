<template>
    <div class="col col-md-12 q-mt-md">
        <div class="flex justify-between q-mt-md" >
            <div class="q-gutter-sm">
                <q-btn :loading="loading" square size="12px" color="light-green" label="Одобрить"
                       @click="send('approve')"
                       icon="send" :disabled="disabled"></q-btn>
                <q-btn square size="12px" color="orange-5" label="На доработку" @click="send('revision')"
                       icon="keyboard_return"></q-btn>
                <q-btn square size="12px" color="red-5" label="Отклонить" @click="send('decline')"
                       icon="block"></q-btn>
            </div>
            <div class="q-gutter-sm">
                <q-btn square size="12px" color="primary" label="Проверка в КАП" icon="add_task" @click="kapDialog = true"/>
            </div>
        </div>
    </div>

    <q-dialog v-model="commentDialog">
        <q-card style="width: 800px">
            <q-card-section>
                <q-input type="textarea" v-model="comment" outlined rows="3" label="Комментарий"/>
            </q-card-section>

            <q-card-actions>
                <q-space/>
                <q-btn label="Отправить" color="primary" @click="declineAction" :loading="loading2" v-if="action === 'decline'"/>
                <q-btn label="Отправить" color="primary" @click="revisionAction" :loading="loading2" v-if="action === 'revision'"/>
            </q-card-actions>
        </q-card>
    </q-dialog>

    <q-dialog v-model="kapDialog" size="md" persistent>
        <order-kap :preorder_id="id" :data="data"/>
    </q-dialog>
</template>

<script>
import {approveOrder, declineOrder, revisionOrder} from "../../services/preorder";
import {Notify} from "quasar";
import OrderKap from "@/Pages/order/OrderKap.vue";

export default {

    props: ['id', 'disabled', 'data'],

    components: {
        OrderKap
    },

    data() {
        return{
            kapDialog: false,
            loading: false,
            loading2: false,
            commentDialog: false,
            comment: '',
            action: ''
        }
    },

    methods: {
        send(value) {
            this.action = value
            if (value === 'approve') {
                this.approveAction()
            } else {
                this.commentDialog = true
            }
        },

        approveAction() {
            this.loading = true
            approveOrder(this.id).then((res) => {
                this.commentDialog = false
                if(res && res.message !== '') {
                    Notify.create({
                        message: res.message,
                        position: 'bottom-right',
                        type: res.success === true ? 'positive' : 'warning'
                    })
                }
                this.$emitter.emit('preorderActionEvent')
            }).finally(() => {
                this.loading = false
            })
        },

        declineAction() {
            this.loading2 = true
            declineOrder(this.id, {
                comment: this.comment,
            }).then(() => {
                this.commentDialog = false
                this.$emitter.emit('preorderActionEvent')
            }).finally(() => {
                this.loading2 = false
            })
        },

        revisionAction() {
            this.loading2 = true
            revisionOrder(this.id, {
                comment: this.comment,
            }).then(() => {
                this.commentDialog = false
                this.$emitter.emit('preorderActionEvent')
            }).finally(() => {
                this.loading2 = false
            })
        },

    }
}
</script>

<style scoped>

</style>

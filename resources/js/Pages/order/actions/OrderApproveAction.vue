<template>
    <div v-if="show">
        <div class="q-gutter-sm">
            <q-btn v-if="show"
                   :disabled="disabled" :loading="loading"
                   color="light-green"
                   icon="send"
                   label="Одобрить"
                   size="12px"
                   square
                   @click="send('approve')">
            </q-btn>

            <q-btn v-if="show" color="orange-5"
                   icon="keyboard_return"
                   label="На доработку"
                   size="12px"
                   square
                   @click="send('revision')">
            </q-btn>

            <!--        <q-btn square size="12px"-->
            <!--               color="red-5"-->
            <!--               label="Отклонить"-->
            <!--               @click="send('decline')"-->
            <!--               icon="block"-->
            <!--               v-if="show">-->
            <!--        </q-btn>-->
        </div>

        <q-dialog v-model="commentDialog" persistent>
            <q-card style="width: 800px">
                <q-card-section class="flex justify-between">
                    <q-space/>
                    <q-icon v-close-popup class="cursor-pointer" flat name="close" size="sm"/>
                </q-card-section>
                <q-card-section>
                    <q-input v-model="comment" class="text-body1" label="Комментарий" outlined rows="3"
                             type="textarea"/>
                </q-card-section>
                <q-card-actions>
                    <q-space/>
                    <q-btn v-if="action === 'decline'" :loading="loading2" color="primary" label="Отправить"
                           @click="declineAction"/>
                    <q-btn v-if="action === 'revision'" :loading="loading2" color="primary" label="Отправить"
                           @click="revisionAction"/>
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>

</template>

<script>
import {signData} from "../../../services/sign";
import {approveOrder, declineOrder, revisionOrder} from "../../../services/order";
import {Notify} from "quasar";
import {mapGetters} from "vuex";

export default {
    props: ['order_id', 'show'],

    data() {
        return {
            commentDialog: false,
            loading: false,
            loading1: false,
            loading2: false,
            action: '',
            comment: '',
        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    methods: {

        send(value) {
            this.action = value
            if (value === 'approve') {
                this.loading = true
                signData().then(res => {
                    if (res) {
                        this.signHash = res
                        this.approveAction()
                    } else {
                        this.loading = false
                    }
                }).catch(() => {
                    this.loading = false
                })
            } else {
                this.commentDialog = true
            }
        },

        approveAction() {
            this.loading = true
            approveOrder(this.order_id, {
                comment: this.comment,
                sign: this.signHash,
            }).then((res) => {
                if (res) {
                    if (res.success) {
                        this.commentDialog = false
                        this.$emitter.emit('orderActionEvent')
                    }
                    if (res.message !== '') {
                        Notify.create({
                            message: res.message,
                            position: 'bottom',
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
            declineOrder(this.order_id, {
                comment: this.comment,
            }).then(() => {
                this.commentDialog = false
                this.$emitter.emit('orderActionEvent')
            }).finally(() => {
                this.loading2 = false
            })
        },

        revisionAction() {
            this.loading2 = true
            revisionOrder(this.order_id, {
                comment: this.comment,
            }).then(() => {
                this.commentDialog = false
                this.$emitter.emit('orderActionEvent')
            }).finally(() => {
                this.loading2 = false
            })
        },
    },

}
</script>


<template>
    <div class="q-gutter-sm">
        <q-btn :loading="loading"
               square size="12px"
               color="light-green"
               label="Одобрить"
               @click="send('approve')"
               icon="send"
               :disabled="disabled"
               v-if="show">
        </q-btn>

        <q-btn square size="12px"
               color="orange-5"
               label="На доработку"
               @click="send('revision')"
               icon="keyboard_return"
               v-if="show">
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
            <q-icon name="close" size="sm" flat v-close-popup class="cursor-pointer"/>
          </q-card-section>
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

</template>

<script>
import {signData} from "../../../services/sign";
import {declineOrder, revisionOrder, approveOrder, revisionVideoOrder} from "../../../services/order";
import {Notify} from "quasar";
import {mapGetters} from "vuex";

export default {
    props: ['order_id', 'show'],

    data() {
        return{
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
                    if(res) {
                        this.signHash = res
                        this.approveAction()
                    }else{
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
                if(res) {
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
            declineOrder(this.order_id,{
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


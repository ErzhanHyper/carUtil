<template>
    <div class="q-gutter-sm" >
        <template v-if="show">
        <q-btn :loading="loading" square size="12px" color="light-green" label="Одобрить"
               @click="send('approve')"
               icon="send" :disabled="disabled"></q-btn>
        <q-btn square size="12px" color="orange-5" label="На доработку" @click="send('revision')"
               icon="keyboard_return"></q-btn>
        <q-btn square size="12px" color="red-5" label="Отклонить" @click="send('decline')"
               icon="block"></q-btn>
        </template>
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

</template>

<script>
import {approveOrder, declineOrder, revisionOrder} from "../../services/preorder";
import {Notify} from "quasar";
import {mapGetters} from "vuex";

export default {
    props: ['preorder_id', 'show'],

    data() {
        return{
            kapDialog: false,
            loading: false,
            loading2: false,
            commentDialog: false,
            comment: '',
            action: '',
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
                this.approveAction()
            } else {
                this.commentDialog = true
            }
        },

        approveAction() {
            this.loading = true
            approveOrder(this.preorder_id).then((res) => {
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
            declineOrder(this.preorder_id, {
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
            revisionOrder(this.preorder_id, {
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

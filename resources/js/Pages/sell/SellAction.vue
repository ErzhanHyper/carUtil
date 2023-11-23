<template>
    <div class="q-gutter-sm">
        <q-btn :loading="loading1"
               square size="12px"
               color="light-green"
               label="Одобрить"
               @click="send('approve')"
               icon="send"
               v-if="permissions.canApprove">
        </q-btn>

        <q-btn square size="12px"
               color="orange-5"
               icon="message"
               @click="commentDialog = true"
               v-if="user && user.role === 'moderator'"
        >
        </q-btn>

        <q-btn square size="12px"
               color="red-5"
               label="Отклонить"
               :loading="loading2"
               @click="send('decline')"
               icon="block"
               v-if="permissions.canDecline"
        >
        </q-btn>

        <q-btn :loading="loading1"
               square size="12px"
               color="blue-8"
               label="Погасить сертификат"
               @click="send('close')"
               icon="verified"
               v-if="permissions.canSell"
        >
        </q-btn>
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
import {Notify} from "quasar";
import {approveSell, closeSell, declineSell, messageSell} from "../../services/sell";
import {mapGetters} from "vuex";

export default {
    props: ['permissions', 'sell_id'],

   data() {
       return{
           loading1: false,
           loading2: false,
           loading3: false,
           commentDialog: false,

           comment: ''
       }
   },

    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    methods: {
        send(value){
            if(value === 'approve'){
                this.loading1 = true
                approveSell(this.sell_id).then((res) => {
                    Notify.create({
                        message: res.message,
                        position: 'bottom',
                        type: res.success ? 'positive' : 'warning'
                    })
                    this.$emitter.emit('SellActionEvent')
                }).finally(() => {
                    this.loading1 = false
                })
            }else if(value === 'decline'){
                this.loading2 = true
                declineSell(this.sell_id).then((res) => {
                    Notify.create({
                        message: res.message,
                        position: 'bottom',
                        type: res.success ? 'positive' : 'warning'
                    })
                    this.$emitter.emit('SellActionEvent')
                }).finally(() => {
                    this.loading2 = false
                })
            }else if(value === 'message'){
                this.loading3 = true
                messageSell(this.sell_id, {comment: this.comment}).then((res) => {
                    this.commentDialog = false
                    Notify.create({
                        message: res.message,
                        position: 'bottom',
                        type: res.success ? 'positive' : 'warning'
                    })
                }).finally(() => {
                    this.loading3 = false
                })
            }else if(value === 'close'){
                this.loading1 = true
                closeSell(this.sell_id).then((res) => {
                    this.commentDialog = false
                    this.$emitter.emit('SellActionEvent')
                    Notify.create({
                        message: res.message,
                        position: 'bottom',
                        type: res.success ? 'positive' : 'warning'
                    })
                }).finally(() => {
                    this.loading1 = false
                })
            }
        }
    },

    created(){
    }
}
</script>

<style scoped>

</style>

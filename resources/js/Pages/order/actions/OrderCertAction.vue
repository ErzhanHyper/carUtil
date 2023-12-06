<template>
    <div class="q-gutter-sm">
        <q-btn :loading="loading"
               :disable="loading1"
               push size="12px"
               color="light-green"
               label="Выдать сертификат"
               icon="verified"
               @click="issueCert()"
               v-if="permissions.showIssueCert">
        </q-btn>
        <q-btn push size="12px"
               :loading="loading1"
               :disable="loading"
               color="orange-5"
               label="На доработку"
               @click="commentDialog = true"
               icon="keyboard_return"
               v-if="permissions.showVideoRevision"
        />
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
                <q-btn label="Отправить" color="primary" @click="revisionVideo" :loading="loading1" />
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>

<script>
import {revisionVideoOrder, storeCertOrder} from "../../../services/order";

export default {

    props: ['order_id', 'show', 'permissions'],

    data(){
        return{
            loading: false,
            loading1: false,

            commentDialog: false,
            comment: '',
        }
    },

    methods: {
        issueCert() {
            this.loading = true
            storeCertOrder({
                order_id: this.order_id,
            }).then(res => {
                this.$emitter.emit('orderActionEvent')
            }).finally(() => {
                this.loading = false
            })
        },

        revisionVideo() {
            this.loading1 = true
            revisionVideoOrder(this.order_id, {
                comment: this.comment,
            }).then(() => {
                this.commentDialog = false
                this.$emitter.emit('orderActionEvent')
            }).finally(() => {
                this.loading1 = false
            })
        },
    }
}
</script>

<style scoped>

</style>

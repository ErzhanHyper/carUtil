<template>
    <div>
        <q-btn :loading="loading"
               push
               size="12px"
               color="blue-8"
               label="Взять на исполнение"
               icon="edit"
               v-if="permissions.start"
               @click="executeRun"
        >
        </q-btn>
        <q-btn :loading="loading"
               push
               size="12px"
               color="pink-5"
               label="Отменить исполнение"
               icon="close"
               v-if="permissions.stop"
               @click="executeEnd"
        >
        </q-btn>
    </div>
</template>

<script>
import {executeCloseOrder, executeRunOrder} from "../../../services/order";

export default {
    props: ['order_id', 'permissions'],
    data(){
        return{
            loading: false,
        }
    },

    methods: {
        executeRun(){
            this.loading = true
            executeRunOrder(this.order_id).then(res => {
                if(res){
                    if(res.success === true){
                        this.$emitter.emit('orderActionEvent')
                    }
                }
            }).finally(() => {
                this.loading = false
            })
        },

        executeEnd(){
            this.loading = true
            executeCloseOrder(this.order_id).then(res => {
                if(res){
                    if(res.success === true){
                        this.$emitter.emit('orderActionEvent')
                    }
                }
            }).finally(() => {
                this.loading = false
            })
        }
    }
}
</script>

<style scoped>

</style>

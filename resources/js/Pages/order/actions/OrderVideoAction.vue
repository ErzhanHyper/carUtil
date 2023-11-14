<template>
    <div class="q-gutter-sm">
        <q-btn square size="12px"
               color="orange-5"
               label="На доработку"
               @click="commentDialog = true"
               icon="keyboard_return"
               v-if="permissions.showVideoRevision"
        />
        <q-btn label="Отправить видеозапись"
               color="blue-8"
               icon="videocam"
               square size="12px"
               v-if="permissions.showVideoSendAction && showCameraBtn"
               @click="cameraDialog = true"
        />
    </div>

    <q-dialog
        v-model="cameraDialog"
        persistent
        :maximized="maximizedToggle"
        transition-show="slide-up"
        transition-hide="slide-down"
    >
        <camera-record :id="order_id"/>
    </q-dialog>

    <q-dialog v-model="commentDialog">
        <q-card style="width: 800px">
            <q-card-section>
                <q-input type="textarea" v-model="comment" outlined rows="3" label="Комментарий" class="text-body1"/>
            </q-card-section>
            <q-card-actions>
                <q-space/>
                <q-btn label="Отправить" color="primary" @click="videoAction" :loading="loading" />
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>

<script>
import {ref} from "vue";
import {revisionVideoOrder} from "@/services/order";
import CameraRecord from "@/Components/CameraRecord.vue";

export default {
    props: ['order_id', 'permissions'],

    components: {
        CameraRecord
    },

    setup () {
        return {
            dialog: ref(false),
            maximizedToggle: ref(true),
        }
    },

    data(){
        return{
            showCameraBtn: false,
            cameraDialog: false,
            commentDialog: false,
            loading: false,
            comment: '',
        }
    },

    methods: {
        videoAction() {
            this.loading = true
            revisionVideoOrder(this.order_id, {
                comment: this.comment,
            }).then(() => {
                this.commentDialog = false
                this.$emitter.emit('orderActionEvent')
            }).finally(() => {
                this.loading = false
            })
        },
    },

    created(){
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            this.showCameraBtn = true
        }
    },

    mounted(){
        this.$emitter.on('VideoSendEvent', () => {
            this.cameraDialog = false
        })
    }
}
</script>

<style scoped>

</style>

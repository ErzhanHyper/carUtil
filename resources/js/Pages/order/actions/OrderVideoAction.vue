<template>
    <div class="q-gutter-sm">
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

            loading: false,
        }
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

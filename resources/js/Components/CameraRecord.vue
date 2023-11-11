<template>

        <q-card >
            <q-bar>
                <q-space />

                <q-btn dense flat icon="minimize" @click="maximizedToggle = false" :disable="!maximizedToggle">
                    <q-tooltip v-if="maximizedToggle" class="bg-white text-primary">Minimize</q-tooltip>
                </q-btn>
                <q-btn dense flat icon="crop_square" @click="maximizedToggle = true" :disable="maximizedToggle">
                    <q-tooltip v-if="!maximizedToggle" class="bg-white text-primary">Maximize</q-tooltip>
                </q-btn>
                <q-btn dense flat icon="close" v-close-popup @click="stopCamera">
                    <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                </q-btn>
            </q-bar>

            <q-card-section class="q-pt-none" style="height: 80vh" id="videoBlock">
                <!--                <button class="btn btn-primary btn-sm" @click="snapshot">Create snapshot</button>-->
                <div v-if="recording" style="position: relative; top: 0px;margin-top:0px;color: #fff;background: rgba(0,0,0,.3);padding: 2px 10px" class="text-center">{{ timer.minutes+ ':' +timer.seconds }}</div>
                <video ref="video" class="camera-stream" style="width: 100%" />
                <div class="text-center flex justify-between items-center q-mx-lg" style="position:relative; top: -70px">
                    <div style="width: 42px">
                        <q-btn fab style="background: rgba(0,0,0,.3)" v-if="showSend" @click="sendData" :loading="loading">
                        <q-icon name="send" size="sm" color="white"/>
                    </q-btn>
                    </div>
                    <q-btn color="white" fab @click="startRecording" v-if="!recording">
                        <q-icon name="fiber_manual_record" color="negative" size="sm"/>
                    </q-btn>
                    <q-btn color="white" fab  @click="stopRecording" v-if="recording">
                        <q-icon name="stop" color="negative" size="sm"/>
                    </q-btn>
                    <q-btn style="background: rgba(0,0,0,.3)" fab size="md">
                        <q-icon name="cameraswitch" size="sm" color="white" @click="switchCamera"/>
                    </q-btn>
                </div>
            </q-card-section>
        </q-card>

</template>

<script>
import { ref } from 'vue'
import Camera from "simple-vue-camera";
import FileDownload from "js-file-download";
import {sendVideoOrder} from "../services/order";
import {Notify} from "quasar";
export default {
    props: ['id'],
    components: {
        Camera
    },

    setup () {
        return {
            dialog: ref(false),
            maximizedToggle: ref(true),
        }
    },

    data() {
        return {
            timer: {
                minutes: 0,
                seconds:0,
                startTime: 0,
                elapsedTime: 0
            },
            loading: false,
            elapsedTime:0,
            recording: false,
            showSend: false,
            mediaStream: null,
            mediaRecorder: null,
            cameraMode: {facingMode: "environment"},
            switch: 'environment',
            recordedBlobs: [],
            imageData: {
                image: '',
                image_orientation: 0,
            },
        }
    },

    methods: {

        switchCamera() {
            if(this.switch === 'environment') {
                this.switch = 'user'
                this.cameraMode = {facingMode: "user"}
            }else {
                this.switch = 'environment'
                this.cameraMode = {facingMode: "environment"}
            }
            this.startCamera()
        },

        startCamera(){
            this.startTimer()
            navigator.mediaDevices.getUserMedia({audio: false, video: this.cameraMode }).then(mediaStream => {
                this.$refs.video.srcObject = mediaStream;
                this.$refs.video.play()
                this.mediaStream = mediaStream
            })
        },

        startRecording() {
            this.showSend = false
            this.recordedBlobs = []
            this.mediaRecorder = new MediaRecorder(this.mediaStream)
            this.recording = true
            this.mediaRecorder.start();
            this.mediaRecorder.ondataavailable = this.handleDataAvailable;
        },

        handleDataAvailable(event) {
            if (event.data && event.data.size > 0) {
                this.recordedBlobs.push(event.data);
            }
        },

        sendData(){
            this.loading = true
            // FileDownload(new Blob(this.recordedBlobs, { type: "video/webm" }))

            let blob = new Blob(this.recordedBlobs, { type: "video/mp4" })
            let formData = new FormData();
            formData.append('voice', blob);
            sendVideoOrder(this.id, formData).then(res => {
                if(res){
                    if(res.success === true){
                        this.$emitter.emit('VideoSendEvent')
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

        stopCamera(){
            this.mediaStream.getTracks().forEach(function(track) {
                track.stop();
            });
        },

        stopRecording() {
            this.timer.seconds = 0
            this.timer.minutes= 0
            this.timer.elapsedTime= 0
            this.timer.startTime= Date.now()
            this.elapsedTime = 0

            this.showSend = true
            this.recording = false
            this.mediaRecorder.stop();
            console.log("Recorded Blobs: ", this.recordedBlobs);
        },

        startTimer() {
            //reset start time
            this.timer.startTime = Date.now();
            // run `setInterval()` and save the ID
            this.timer.intervalId = setInterval(() => {
                //calculate elapsed time
               this.elapsedTime = Date.now() - this.timer.startTime + this.timer.elapsedTime
                //calculate different time measurements based on elapsed time
                this.timer.seconds = parseInt((this.elapsedTime/1000)%60)
                this.timer.minutes = parseInt((this.elapsedTime/(1000*60))%60)
            }, 100);
        }
    },

    mounted(){
        this.startCamera()
    }
}
</script>

<style scoped>

</style>

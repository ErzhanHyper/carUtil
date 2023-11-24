<template>

        <q-card style="background: rgba(0,0,0,.9);">
            <q-bar class="bg-white">
                <q-space />
                <q-btn dense flat icon="close" v-close-popup @click="stopCamera">
                    <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                </q-btn>
            </q-bar>

            <q-card-section class="q-pt-none q-px-none flex column items-start justify-center" style="height: calc(100vh - 32px); " id="videoBlock" >

                <div style="position: absolute;top: 0; width: 100%;height: 80%">
                    <div v-if="recording" style="position: absolute; top: -1px;width:100%;color: #fff;background: rgba(255,0,0,.5);padding: 2px 10px" class="text-center">{{formattedElapsedTime}}</div>
                    <video ref="video" class="camera-stream" style="width: 100%;height: 100%" muted/>
                </div>
                <div style="position: absolute; bottom: 0;left: 0;width: 100%; height: 20%" class="flex items-center" >
                    <div class="text-center flex justify-between items-center q-mx-lg" style="width: 100%">
                        <div style="width: 56px">
                            <q-btn fab style="background: rgba(0,0,0,.3)" v-if="showSend" @click="sendData" :loading="loading" text-color="white">
                            <q-icon name="send" size="sm" color="white"/>
                        </q-btn>
                        </div>
                        <q-btn color="white" fab @click="startRecording" v-if="!recording">
                            <q-icon name="fiber_manual_record" color="negative" size="sm"/>
                        </q-btn>
                        <q-btn color="white" fab  @click="stopRecording" v-if="recording">
                            <q-icon name="stop" color="negative" size="sm"/>
                        </q-btn>
                        <div style="width: 56px">
                            <q-btn style="background: rgba(0,0,0,.3)" fab size="md" v-if="!recording">
                                <q-icon name="cameraswitch" size="sm" color="white" @click="switchCamera"/>
                            </q-btn>
                        </div>
                    </div>
                </div>
            </q-card-section>
        </q-card>

</template>

<script>
import { ref, watchEffect, onMounted } from 'vue'
import Camera from "simple-vue-camera";
import {sendVideoOrder} from "../services/order";
import {Notify} from "quasar";
import { useTimer } from 'vue-timer-hook';
import FileDownload from "js-file-download";

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
            time: new Date(),
            timer: {},
            elapsedTime:0,
            loading: false,
            recording: false,
            showSend: false,
            mediaStream: null,
            mediaRecorder: null,
            cameraMode: "environment",
            recordedBlobs: [],
            imageData: {
                image: '',
                image_orientation: 0,
            },
        }
    },
    methods: {

        switchCamera() {
            if(this.cameraMode === 'environment') {
                this.cameraMode = "user"
            }else {
                this.cameraMode = "environment"
            }
            this.mediaStream.getTracks().forEach(function(track) {
                track.stop();
            });
            this.startCamera()
        },
// {
//     autoGainControl: true,
//         echoCancellation: true,
//     sampleRate: 48000,
//     channelCount: 2,
//     volume: 1.0
// }
        startCamera(){
            navigator.mediaDevices.getUserMedia({audio: false, video: {width: { min: 768, max: 1920,}, facingMode: this.cameraMode, aspectRatio: 16/9 }}).then(mediaStream => {
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
            this.startTimer()
        },

        handleDataAvailable(event) {
            if (event.data && event.data.size > 0) {
                this.recordedBlobs.push(event.data);
            }
        },

        sendData(){
            this.loading = true
            if(this.recordedBlobs.length > 0) {
                let blob = new Blob(this.recordedBlobs, {type: "video/webm"})
                let formData = new FormData();
                formData.append('voice', blob);
                sendVideoOrder(this.id, formData).then(res => {
                    if (res) {
                        if (res.success === true) {
                            this.$emitter.emit('VideoSendEvent')
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
            }
        },

        stopCamera(){
            this.mediaStream.getTracks().forEach(function(track) {
                track.stop();
            });
        },

        stopRecording() {
            this.showSend = true
            this.recording = false
            this.mediaRecorder.stop();
            this.stopTimer()
            this.resetTimer()
        },

        startTimer() {
            this.timer = setInterval(() => {
                this.elapsedTime += 1000;
            }, 1000);
        },
        stopTimer() {
            clearInterval(this.timer);
        },
        resetTimer() {
            this.elapsedTime = 0;
        }

    },

    computed: {
        formattedElapsedTime() {
            const date = new Date(null);
            date.setSeconds(this.elapsedTime / 1000);
            const utc = date.toUTCString();
            return utc.substr(utc.indexOf(":") - 2, 8);
        }
    },

    mounted(){
        this.startCamera()
    }
}
</script>

<style scoped>

</style>

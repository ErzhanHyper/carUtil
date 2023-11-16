<template>
    <q-card flat>
        <q-card-section>
            <q-select
                square v-model="item.file"
                label="Файл"
                :options="filesOptions"
                :model-value="item.file"
                option-label="title"
                option-value="id"
                map-options
                emit-value
                @update:model-value="evt => selectFile(evt)"
                class="q-mb-sm"
                outlined
                dense
                :readonly="blocked"
                v-if="!blocked"
                :loading="loading"
                :disable="loading"
            >
                <template v-slot:before>
                    <q-icon name="folder"/>
                </template>
                <template v-slot:append>
                    <q-btn round dense flat icon="add"/>
                </template>
            </q-select>

<!--            <div class="text-body2">Загруженные файлы</div>-->
        </q-card-section>

        <q-separator inset/>

        <q-card-section>

            <template v-for="(doc, i) in filesDoc" :key="i">
                <div class="flex no-wrap flex-start q-mb-sm text-left relative-position text-deep-orange-10">
                    <q-icon :name="(doc.file_type_id === 29) ? 'videocam' : 'insert_drive_file'" class="q-mr-sm" size="sm" v-if="doc_id !== doc.id"></q-icon>
                    <q-circular-progress indeterminate rounded size="xs" v-if="doc_id === doc.id" color="primary" class="q-mr-xs"/>
                    <a href="#" @click="getOrderFile(doc.id)" class="text-dark" v-if="doc.file_type_id !== 29">
                        {{ getFileTypeTitle(doc.file_type_id) }}
                    </a>
                    <a href="#" @click="showFileDialog = true" class="text-indigo-5 text-weight-bold text-body1" v-if="doc.file_type_id === 29">
                        {{ getFileTypeTitle(doc.file_type_id) }}
                        <div class="text-caption">({{ doc.created_at }})</div>
                    </a>
                    <q-icon name="close" class="q-ml-sm cursor-pointer" size="xs" style="margin-top: 2px" color="negative"
                            v-if="(!blocked || (blocked && doc.file_type_id === 29 && !blockedVideo))" @click="deleteFile({doc: doc, type: 'doc', id: doc.id })">
                    </q-icon>
                </div>
            </template>

            <q-carousel
                animated
                v-model="slide"
                arrows
                infinite
                v-if="filesPhoto.length > 0"
            >
                <q-carousel-slide :name="i+1" class="q-pb-lg relative-position" v-for="(slide, i) in filesPhoto" :key="i">
                    <q-img :src="slide.base64Image" class="full-height cursor-pointer" @click="showImage(slide.base64Image)"/>
                    <div class="text-body1">{{ getFileTypeTitle(slide.file_type_id) }}</div>
                    <q-btn size="xs" dense round icon="close" color="pink-5" class="q-mb-xs"
                           style="position:absolute;right:7px;top:7px" v-if="!blocked"
                           @click="deleteFile({ type: 'photo', id: slide.id })"
                    />
                </q-carousel-slide>

            </q-carousel>

            <input type="file" ref="file_dialog" v-bind:value="uploadedFile" v-on:input="event => uploadFile(event)" :readonly="blocked"
                   v-if="!blocked" class="hidden"/>

        </q-card-section>

    </q-card>

    <q-dialog v-model="showFileDialog">
        <q-card>
            <q-card-section class="flex q-py-sm">
                <div class="text-h6">Видеозапись</div>
                <q-space/>
                <q-icon name="close" size="sm" flat v-close-popup class="cursor-pointer"/>
            </q-card-section>

            <q-separator />

            <q-card-section style="max-height: 80vh" class="scroll">
                <video :src="videoFile" controls style="width: 100%"/>
            </q-card-section>

            <q-separator />
        </q-card>
    </q-dialog>

    <q-dialog v-model="fileDialog">
        <q-card style="width: 100%;max-width:1200px;">
            <q-card-section class="flex justify-between q-py-xs q-px-xs">
                <q-space/>
                <q-icon name="close" size="sm" flat v-close-popup class="cursor-pointer"/>
            </q-card-section>
            <q-card-section >
                <q-img :src="base64Image" />
            </q-card-section>
        </q-card>
    </q-dialog>

</template>

<script>
import {ref} from 'vue'
import {
    deleteOrderFile,
    getFileTypeAgroList,
    getFileTypeList, getOrderFile,
    getOrderFileList, getOrderImage, getOrderVideo,
    storeOrderFile
} from "../../services/file";
import FileDownload from "js-file-download";
import {Notify} from "quasar";

export default {

    props: ['order_id', 'client_id', 'blocked', 'vehicleType', 'blockedVideo'],

    setup() {
        return {
            slide: ref(1),
            pickFile: ref({})
        }
    },

    data() {
        return {
            fileDialog: false,
            base64Image: '',
            doc_id: null,
            videoFile: '',
            showFileDialog: false,

            filesAll: [],
            options_photo: [],
            options_file: [],
            item: {},

            file_type_id: null,
            file_id: null,
            uploadedFile: null,
            loading: false,

            filesDoc: [],
            filesPhoto: [],
            filesOptions: []

        }
    },

    methods: {
        showImage(image){
            this.fileDialog = true
            this.base64Image = image
        },

        getItems() {
            this.loading = true
            this.filesAll = []
            this.filesDoc = []
            this.filesPhoto = []
            this.filesOptions = []
            getOrderFileList({
                order_id: this.order_id
            }).then(res => {
                if (this.vehicleType === 'car') {
                    res.map(el => {
                        if (el.file_type_id === 8 || el.file_type_id === 9 || el.file_type_id === 10 || el.file_type_id === 11 || el.file_type_id === 12 || el.file_type_id === 13 || el.file_type_id === 14 || el.file_type_id === 15) {
                            getOrderImage(el.id, {params: {order_id: this.order_id}}).then((res) => {
                                el.base64Image = 'data:image/jpeg;base64,'+res.data
                                this.filesPhoto.push(el)
                            })
                        } else {
                            this.filesDoc.push(el)
                            if(el.file_type_id === 29){
                                getOrderVideo(el.id, {params: {order_id: this.order_id}}).then((res) => {
                                    this.videoFile = 'data:video/webm;base64,'+res.data
                                })
                            }
                        }
                    })
                }else{
                    res.map(el => {
                        if (el.file_type_id === 4 || el.file_type_id === 5 || el.file_type_id === 6 || el.file_type_id === 7 || el.file_type_id === 8 || el.file_type_id === 9 || el.file_type_id === 10 || el.file_type_id === 11) {
                            getOrderImage(el.id, {params: {order_id: this.order_id}}).then((res) => {
                                el.base64Image = 'data:image/jpeg;base64,'+res.data
                                this.filesPhoto.push(el)
                            })
                        } else {
                            this.filesDoc.push(el)
                            if(el.file_type_id === 29){
                                getOrderVideo(el.id, {params: {order_id: this.order_id}}).then((res) => {
                                    this.videoFile = 'data:video/webm;base64,'+res.data
                                })
                            }
                        }
                    })
                }
            }).finally(() => {
                this.loading = false
            });

            if (this.vehicleType === 'car') {
                getFileTypeList().then(res => {
                    res.forEach(el => {
                        this.filesAll.push(el)
                        if (el.id === 8 || el.id === 9 || el.id === 10 || el.id === 11 || el.id === 12 || el.id === 13 || el.id === 14 || el.id === 15) {
                            this.options_photo.push(el)
                            this.filesOptions.push(el)
                        } else if (el.id !== 4 && el.id !== 29) {
                            this.options_file.push(el)
                            this.filesOptions.push(el)
                        }
                    })
                })
            }else{
                getFileTypeAgroList().then(res => {
                    res.forEach(el => {
                        this.filesAll.push(el)
                        if (el.id === 4 || el.id === 5 || el.id === 6 || el.id === 7 || el.id === 8 || el.id === 9 || el.id === 10 || el.id === 11) {
                            this.options_photo.push(el)
                            this.filesOptions.push(el)
                        }
                        if (el.id === 1 || el.id === 2 || el.id === 3) {
                            this.options_file.push(el)
                            this.filesOptions.push(el)
                        }
                    })
                })
            }
        },

        getFileTypeTitle(id) {
            let title = '';
            this.filesAll.filter(el => {
                if (el.id === id) {
                    title = el.title
                }
            })
            return title
        },

        selectFile(evt) {
            this.$refs.file_dialog.value = this.uploadedFile;
            this.file_type_id = evt
            this.pickFile = this.$refs.file_dialog
            this.pickFile.click();
        },

        deleteFile(value) {
            if (value.type === 'doc') {
                const objWithIdIndex = this.filesDoc.findIndex((obj) => obj.id === value.id);
                if (objWithIdIndex > -1) {
                    this.filesDoc.splice(objWithIdIndex, 1);
                }
            } else if (value.type === 'photo') {
                const objWithIdIndex = this.filesPhoto.findIndex((obj) => obj.id === value.id);
                if (objWithIdIndex > -1) {
                    this.filesPhoto.splice(objWithIdIndex, 1);
                    this.slide = 1
                }
            }
            deleteOrderFile(value.id).then(() => {
                if(value.doc.file_type_id === 29){
                    this.$emitter.emit('orderFileEvent')
                }
            });
        },

        uploadFile(evt) {
            this.loading = true
            storeOrderFile({
                file_type_id: this.file_type_id,
                file: evt.target.files[0],
                order_id: this.order_id,
                client_id: this.client_id
            }).then(() => {
                this.$refs.file_dialog = null
                this.getItems()
                this.file_type_id = null
                this.pickFile = null
                this.file = null
            }).finally(() => {
                this.loading = false
            });
        },

        getOrderFile(id){
            this.doc_id = id
            getOrderFile(id, {params: {order_id: this.order_id}, responseType: 'arraybuffer'}).then((res) => {
                console.log(res)
                let parts = res.headers.get('Content-Disposition').split(';');
                let filename = parts[1].split('=')[1];
                FileDownload(res.data, filename)
            }).finally(() => {
                this.loading = false
                this.doc_id = null
            }).catch(() => {
                Notify.create({
                    message: 'Не удалось открыть файл',
                    position: 'bottom',
                    type: 'warning'
                })
            })
        }
    },

    created() {
        this.getItems()
    },

    mounted(){
        this.$emitter.on('VideoSendEvent', () => {
            this.getItems()
        })
    }
}
</script>

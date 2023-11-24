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

            <div class="flex no-wrap flex-start q-mb-sm text-left relative-position text-weight-bold" v-if="certificate_id">
                <a href="#" class="text-primary" @click="getCert(certificate_id)">
                <q-icon :name="'verified'" class="q-mr-sm" size="sm" v-if="!loading1"></q-icon>
                 <q-circular-progress indeterminate rounded size="xs" v-if="loading1" color="primary" class="q-mr-xs"/>
                Скидочный сертификат
                </a>
            </div>

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

            <template v-if="filesPhoto.length > 0">
            <q-carousel
                animated
                v-model="slide"
                arrows
                infinite
            >
            <template v-for="(slide, i) in filesPhoto" :key="i" v-if="slide">
                <q-carousel-slide :name="i+1" class="q-pb-lg relative-position"  >
                    <q-img :src="slide.base64Image" class="full-height cursor-pointer" @click="showImage(slide.base64Image)"/>
                    <div class="text-body1">{{ getFileTypeTitle(slide.file_type_id) }}</div>
                    <q-btn size="xs" dense round icon="close" color="pink-5" class="q-mb-xs"
                           style="position:absolute;right:7px;top:7px" v-if="!blocked"
                           @click="deleteFile({ type: 'photo', id: slide.id })"
                    />
                </q-carousel-slide>
            </template>

            </q-carousel>
            </template>

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
    deleteOrderFile, getAgroFileImage, getCarFileImage,
    getFileTypeAgroList,
    getFileTypeList, getOrderFile,
    getOrderFileList, getOrderImage, getOrderVideo, getPreOrderFileList,
    storeOrderFile
} from "../../services/file";
import FileDownload from "js-file-download";
import {Notify} from "quasar";
import {generateCertificate} from "../../services/certificate";

export default {

    props: ['order_id', 'client_id', 'blocked', 'vehicleType', 'blockedVideo', 'certificate_id'],

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
            loading1: false,

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
            getOrderFileList({
                order_id: this.order_id
            }).then(res => {
                this.loading = false
                this.filesAll = res.file_types
                this.filesDoc = res.docs
                this.filesPhoto = res.photos
                this.filesOptions = []

                res.file_types.map(el => {
                    if(el.id !== 29){
                        this.filesOptions.push(el)
                    }
                })
                this.filesDoc.map(el => {
                    if(el.file_type_id === 29) {
                        getOrderVideo(el.id, {params: {order_id: this.order_id}}).then((value) => {
                            this.videoFile = 'data:video/webm;base64,' + value.data
                        })
                    }
                })
                if(this.vehicleType === 'agro') {
                    this.filesPhoto.filter(el => {
                        getOrderImage(el.id, {params: {order_id: this.order_id}}).then((res) => {
                            return el.base64Image = 'data:image/jpeg;base64,' + res.data
                        })
                    })
                }else if(this.vehicleType === 'car'){
                    this.filesPhoto.filter(el => {
                        getOrderImage(el.id, {params: {order_id: this.order_id}}).then((res) => {
                            return el.base64Image = 'data:image/jpeg;base64,' + res.data
                        })
                    })
                }

            })
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
            }).catch(() => {
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
        },

        getCert(id) {
            this.loading1 = true
            generateCertificate(id, {responseType: 'arraybuffer'}).then(value => {
                FileDownload(value, 'certificate.pdf')
            }).finally(() => {
                this.loading1 = false
            })
        },
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

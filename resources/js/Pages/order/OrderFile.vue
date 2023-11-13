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
                class="q-mb-lg"
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

            <div class="text-body2">Загруженные файлы</div>
        </q-card-section>

        <q-separator inset/>

        <q-card-section>

            <div class="flex no-wrap flex-start q-mb-sm text-left relative-position text-deep-orange-10"
                 v-for="(doc, i) in filesDoc"
                 :key="i">
                <q-icon :name="(doc.file_type_id === 29) ? 'videocam' : 'insert_drive_file'" class="q-mr-sm" size="sm"></q-icon>
                <a :href="'/storage/uploads/order/files/' + doc.order_id + '/' + doc.original_name" class="text-dark" v-if="doc.file_type_id !== 29">
                    {{ getFileTypeTitle(doc.file_type_id) }}
                </a>
                <a href="#" @click="showFileDialog = true" class="text-indigo-5 text-weight-bold text-body1" v-if="doc.file_type_id === 29">
                    {{ getFileTypeTitle(doc.file_type_id) }}
                </a>
                <q-icon name="close" class="q-ml-sm cursor-pointer" size="xs" style="margin-top: 2px" color="negative"
                        v-if="(!blocked || (blocked && doc.file_type_id === 29)) && !blockedVideo" @click="deleteFile({doc: doc, type: 'doc', id: doc.id })">
                </q-icon>
            </div>

            <q-carousel
                animated
                v-model="slide"
                arrows
                infinite
                v-if="filesPhoto.length > 0"
            >
                <q-carousel-slide :name="i+1" class="q-pb-lg relative-position" v-for="(slide, i) in filesPhoto" :key="i">
                    <q-img :src="'/storage/uploads/order/files/' + slide.order_id + '/' + slide.original_name" class="full-height"/>
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
                <div class="text-h6">Файлы</div>
                <q-space/>
                <q-icon name="close" size="sm" flat v-close-popup class="cursor-pointer"/>
            </q-card-section>

            <q-separator />

            <q-card-section style="max-height: 80vh" class="scroll">
                <video :src="'/storage/uploads/order/files/' + videoFile.order_id + '/' + videoFile.original_name" controls style="width: 100%"/>
            </q-card-section>

            <q-separator />
        </q-card>
    </q-dialog>

</template>

<script>
import {ref} from 'vue'
import {
    deleteOrderFile,
    getFileTypeAgroList,
    getFileTypeList,
    getOrderFileList,
    storeOrderFile
} from "../../services/file";

export default {

    props: ['data', 'blocked', 'recycleType', 'blockedVideo'],

    setup() {
        return {
            slide: ref(1),
            pickFile: ref({})
        }
    },

    data() {
        return {
            videoFile: {},
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
        getItems() {
            this.loading = true
            this.filesAll = []
            this.filesDoc = []
            this.filesPhoto = []
            this.filesOptions = []
            getOrderFileList({
                order_id: this.data.order_id
            }).then(res => {
                if (this.recycleType === 1) {
                    res.map(el => {
                        if (el.file_type_id === 8 || el.file_type_id === 9 || el.file_type_id === 10 || el.file_type_id === 11 || el.file_type_id === 12 || el.file_type_id === 13 || el.file_type_id === 14 || el.file_type_id === 15) {
                            this.filesPhoto.push(el)
                        } else {
                            this.filesDoc.push(el)
                            if(el.file_type_id === 29){
                                this.videoFile = el
                            }
                        }
                    })
                }else{
                    res.map(el => {
                        if (el.file_type_id === 4 || el.file_type_id === 5 || el.file_type_id === 6 || el.file_type_id === 7 || el.file_type_id === 8 || el.file_type_id === 9 || el.file_type_id === 10 || el.file_type_id === 11) {
                            this.filesPhoto.push(el)
                        } else {
                            this.filesDoc.push(el)
                            if(el.file_type_id === 29){
                                this.videoFile = el
                            }
                        }
                    })
                }
            }).finally(() => {
                this.loading = false
            });

            if (this.recycleType === 1) {
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
            deleteOrderFile({
                order_id: this.data.order_id,
                file_id: value.id
            }).then(() => {
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
                order_id: this.item.order_id,
                client_id: this.item.client_id
            }).then(() => {
                this.$refs.file_dialog = null
                this.getItems()
                this.file_type_id = null
                this.pickFile = null
                this.file = null
            }).finally(() => {
                this.loading = false
            });

        }
    },

    created() {
        this.item = this.data
        this.getItems()
    }
}
</script>

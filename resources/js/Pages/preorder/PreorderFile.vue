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
                :loading="loading"
                v-if="!blocked"
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

            <template v-if="!onlyPhoto">
                <div class="flex no-wrap flex-start q-mb-sm text-left relative-position text-deep-orange-10"
                     v-for="(doc, i) in filesDoc"
                     :key="i">
                    <q-icon name="file_copy" class="q-mr-sm" size="sm"></q-icon>
                    <a :href="'/storage/uploads/preorder/files/' + doc.preorder_id + '/' + doc.original_name"
                       class="text-dark">
                        {{ getFileTypeTitle(doc.file_type_id) }}
                    </a>
                    <q-icon name="close" class="q-ml-sm cursor-pointer" size="xs" style="margin-top: 2px"
                            color="negative"
                            v-if="!blocked" @click="deleteFile({type: 'doc', id: doc.id })">
                    </q-icon>
                </div>

                <div class="flex no-wrap flex-start q-mb-sm text-left relative-position text-deep-orange-10"
                     v-if="item.video" v-for="(video, i) in item.video"
                     :key="i">
                    <q-icon name="videocam" class="q-mr-sm" size="sm"></q-icon>
                    <a :href="'/storage/uploads/order/files/' + item.order_id + '/' + video.original_name"
                       class="text-dark">
                        {{ getFileTypeTitle(29) }}
                    </a>
                </div>

                <div class="flex no-wrap flex-start q-mb-sm text-left relative-position text-deep-orange-10"
                     v-if="item.order && item.order.transfer && item.order.transfer.closed === 2">
                    <q-btn flat dense :loading="loading" size="sm">
                        <q-icon name="file_copy" class="q-mr-xs" size="sm"></q-icon>
                    </q-btn>
                    <a href="#" class="text-dark" @click="downloadPFS">
                        Договор купли-продажи вышедшего из эксплуатации транспортного средства/самоходной
                        сельскохозяйственной техники
                    </a>
                </div>


            </template>

            <q-carousel
                animated
                v-model="slide"
                arrows
                infinite
                v-if="filesPhoto.length > 0"
            >
                <q-carousel-slide :name="i+1" class="q-pb-lg relative-position" v-for="(slide, i) in filesPhoto"
                                  :key="i">
                    <q-img :src="'/storage/uploads/preorder/files/' + slide.preorder_id + '/' + slide.original_name" class="full-height"/>
                    <div class="text-body1">{{ getFileTypeTitle(slide.file_type_id) }}</div>
                    <q-btn size="xs" dense round icon="close" color="pink-5" class="q-mb-xs"
                           style="position:absolute;right:7px;top:7px" v-if="!blocked"
                           @click="deleteFile({ type: 'photo', id: slide.id })" />
                </q-carousel-slide>

            </q-carousel>

            <input type="file" ref="file_dialog" v-bind:value="uploadedFile" class="hidden"
                   @change="event => uploadFile(event)" :readonly="blocked"
                   v-if="!blocked"/>

        </q-card-section>

    </q-card>

</template>

<script>
import {ref} from 'vue'
import {
    deleteOrderFile,
    deletePreOrderFile, generateOrderPFS,
    getFileTypeList,
    getFileTypeAgroList,
    getPreOrderFileList,
    storePreOrderFile
} from "../../services/file";
import FileDownload from "js-file-download";
import {getTransferContract} from "../../services/document";

export default {

    props: ['data', 'blocked', 'onlyPhoto', 'blockedVideo', 'recycleType'],

    setup() {
        return {
            slide: ref(1),
            pickFile: ref({})
        }
    },

    data() {
        return {
            filesAll: [],
            filesOptions: [],

            options_photo: [],
            options_file: [],
            item: {},

            uploadedFile: null,
            file_type_id: null,
            file_id: null,

            filesDoc: [],
            filesPhoto: [],

            loading: false,
        }
    },

    methods: {
        getItems() {
            this.filesAll = []
            this.filesDoc = []
            this.filesPhoto = []
            this.filesOptions = []
            getPreOrderFileList({
                preorder_id: this.data.preorder_id
            }).then(res => {
                if (this.recycleType === 1) {
                    res.map(el => {
                        if (el.file_type_id === 8 || el.file_type_id === 9 || el.file_type_id === 10 || el.file_type_id === 11 || el.file_type_id === 12 || el.file_type_id === 13 || el.file_type_id === 14 || el.file_type_id === 15) {
                            this.filesPhoto.push(el)
                        } else {
                            this.filesDoc.push(el)
                        }
                    })
                } else {
                    res.map(el => {
                        if (el.file_type_id === 4 || el.file_type_id === 5 || el.file_type_id === 6 || el.file_type_id === 7 || el.file_type_id === 8 || el.file_type_id === 9 || el.file_type_id === 10 || el.file_type_id === 11) {
                            this.filesPhoto.push(el)
                        } else {
                            console.log(el)
                            this.filesDoc.push(el)
                        }
                    })
                }
            });

            if (this.recycleType === 1) {
                getFileTypeList().then(res => {
                    res.forEach(el => {
                        this.filesAll.push(el)
                        if (el.id === 8 || el.id === 9 || el.id === 10 || el.id === 11 || el.id === 12 || el.id === 13 || el.id === 14 || el.id === 15) {
                            this.options_photo.push(el)
                            this.filesOptions.push(el)
                        }
                        if (el.id === 1 || el.id === 2 || el.id === 5 || el.id === 28) {
                            this.options_file.push(el)
                            this.filesOptions.push(el)
                        }
                    })
                })
            } else {
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
                let objWithIdIndex = this.filesDoc.findIndex((obj) => obj.id === value.id);
                if (objWithIdIndex > -1) {
                    this.filesDoc.splice(objWithIdIndex, 1);
                }
            } else if (value.type === 'photo') {
                let objWithIdIndex = this.filesPhoto.findIndex((obj) => obj.id === value.id);
                if (objWithIdIndex > -1) {
                    this.filesPhoto.splice(objWithIdIndex, 1);
                    this.slide = 1
                }
            }
            deletePreOrderFile({
                preorder_id: this.data.preorder_id,
                file_id: value.id
            }).then(() => {

            });
        },

        uploadFile(evt) {
            this.loading = true
            storePreOrderFile({
                file_type_id: this.file_type_id,
                file: evt.target.files[0],
                preorder_id: this.item.preorder_id,
                client_id: this.item.client_id
            }).then(() => {
                this.getItems()
                this.file_type_id = null
                this.pickFile = null
                this.file = null
            }).finally(() => {
                this.loading = false
            })

        },

        downloadPFS() {
            if(this.item.order && this.item.order.transfer) {
                this.loading = true
                getTransferContract(this.item.order.transfer.id, {responseType: 'arraybuffer'}).then(res => {
                    FileDownload(res, 'transfer_contract.pdf')
                }).finally(() => {
                    this.loading = false
                })
            }
        },
    },

    created() {
        this.item = this.data
        this.getItems()
    }
}
</script>

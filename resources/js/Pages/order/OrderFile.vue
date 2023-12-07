<template>
    <div>
        <q-card flat>
            <q-card-section v-if="!blocked">
                <q-select
                     v-model="item.file"
                    :disable="loading"
                    :loading="loading"
                    :model-value="item.file"
                    :options="filesOptions"
                    :readonly="blocked"
                    class="q-mb-sm"
                    dense
                    emit-value
                    label="Файл"
                    map-options
                    option-label="title"
                    option-value="id"
                    outlined
                    square
                     options-cover
                     options-dense
                     transition-hide="jump-up"
                     transition-show="jump-up"
                    @update:model-value="evt => selectFile(evt)"
                >
                    <template v-slot:before>
                        <q-icon name="folder"/>
                    </template>
                    <template v-slot:append>
                        <q-btn dense flat icon="add" round/>
                    </template>
                </q-select>

                <!--            <div class="text-body2">Загруженные файлы</div>-->
            </q-card-section>
            <div class="q-pa-md" v-if="filesEmpty">Файлы отсутствуют</div>

            <q-separator v-if="!blocked" inset/>

            <q-card-section>

                <div v-if="certificate_id"
                     class="flex no-wrap flex-start q-mb-sm text-left relative-position text-weight-bold">
                    <a class="text-primary" href="#" @click="getCert(certificate_id)">
                        <q-icon v-if="!loading1" :name="'verified'" class="q-mr-sm" size="sm"></q-icon>
                        <q-circular-progress v-if="loading1" class="q-mr-xs" color="primary" indeterminate rounded
                                             size="xs"/>
                        Скидочный сертификат
                    </a>
                </div>

                <template v-for="(doc, i) in filesDoc" :key="i">
                    <div class="flex no-wrap flex-start q-mb-sm text-left relative-position text-deep-orange-10">
                        <q-icon v-if="doc_id !== doc.id" :name="(doc.file_type_id === 29) ? 'videocam' : 'insert_drive_file'"
                                class="q-mr-sm" size="sm"></q-icon>
                        <q-circular-progress v-if="doc_id === doc.id" class="q-mr-xs" color="primary" indeterminate rounded
                                             size="xs"/>
                        <a v-if="doc.file_type_id !== 29" class="text-dark" href="#" @click="getOrderFile(doc.id)">
                            {{ doc.file_type.title }}
                        </a>
                        <a v-if="doc.file_type_id === 29" class="text-indigo-5 text-weight-bold text-body1" href="#"
                           @click="showFileDialog = true">
                            {{ doc.file_type.title }}
                            <div class="text-caption">({{ doc.created_at }})</div>
                        </a>
                        <q-icon v-if="(!blocked || (blocked && doc.file_type_id === 29 && !blockedVideo))" class="q-ml-sm cursor-pointer" color="negative" name="close"
                                size="xs"
                                style="margin-top: 2px"
                                @click="showDeleteDialog({doc: doc, type: 'doc', id: doc.id })">
                        </q-icon>
                    </div>
                </template>

                <q-carousel
                    v-if="filesPhoto.length > 0 && photoFind"
                    v-model="slide"
                    animated
                    arrows
                    control-color="pink-5"
                    infinite
                >
                    <template v-for="(slide, i) in filesPhoto" :key="i">
                        <q-carousel-slide :name="i+1" class="q-pb-lg relative-position">
                            <q-img :src="slide.base64Image" class="full-height cursor-pointer"
                                   @click="showImage(slide.base64Image)"/>
                            <div class="text-body1">{{ slide.file_type.title }}</div>
                            <q-btn v-if="!blocked" class="q-mb-xs" color="pink-5" dense icon="close" round
                                   size="xs" style="position:absolute;right:7px;top:7px"
                                   @click="showDeleteDialog({ type: 'photo', id: slide.id })"
                            />
                        </q-carousel-slide>
                    </template>
                </q-carousel>

                <input v-if="!blocked" ref="file_dialog" :readonly="blocked"
                       class="hidden"
                       type="file"
                       v-bind:value="uploadedFile"
                       @cancel="cancelFileDialog()" @change="event => uploadFile(event)"/>

            </q-card-section>

        </q-card>

        <q-dialog v-model="showFileDialog">
            <q-card>
                <q-card-section class="flex q-py-sm">
                    <div class="text-h6">Видеозапись</div>
                    <q-space/>
                    <q-icon v-close-popup class="cursor-pointer" flat name="close" size="sm"/>
                </q-card-section>

                <q-separator/>

                <q-card-section class="scroll" style="max-height: 80vh">
                    <video :src="videoFile" controls style="width: 100%;max-height: 60vh;"/>
                </q-card-section>

                <q-separator/>
            </q-card>
        </q-dialog>

        <q-dialog v-model="fileDialog">
            <q-card style="width: 100%;max-width:1200px;">
                <q-card-section class="flex justify-between q-py-xs q-px-xs">
                    <q-space/>
                    <q-icon v-close-popup class="cursor-pointer" flat name="close" size="sm"/>
                </q-card-section>
                <q-card-section>
                    <q-img :src="base64Image"/>
                </q-card-section>
            </q-card>
        </q-dialog>

        <q-dialog v-model="deleteDialog" size="xs">
            <q-card style="width: 600px">
                <q-card-section class="row items-center q-pb-none">
                    <div class="text-body1">Вы действительно хотите удалить файл?</div>
                    <q-space/>
                    <q-btn v-close-popup dense flat icon="close" round/>
                </q-card-section>
                <q-card-actions class="q-mt-md q-mx-sm q-mb-sm">
                    <q-btn :loading="loading" color="pink-5" label="Да" @click="deleteFile()"/>
                    <q-space/>
                    <q-btn v-close-popup color="primary" label="Нет"/>
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>

</template>

<script>
import {ref} from 'vue'
import {
    deleteOrderFile,
    getOrderFile,
    getOrderFileList,
    getOrderImage,
    getOrderVideo,
    storeOrderFile
} from "../../services/file";
import FileDownload from "js-file-download";
import {Notify} from "quasar";
import {generateCertificate} from "../../services/certificate";
import {mapGetters} from "vuex";

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
            deleteDialog: false,
            fileDialog: false,
            base64Image: '',
            photoFind: false,
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
            filesOptions: [],
            filesEmpty: false,

        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    methods: {

        showDeleteDialog(value) {
            this.deleteDialog = true
            this.item = value
        },

        showImage(image) {
            this.fileDialog = true
            this.base64Image = image
        },

        getItems() {
            getOrderFileList({
                order_id: this.order_id
            }).then(res => {
                this.filesAll = res.file_types
                this.filesDoc = res.docs
                this.filesPhoto = res.photos
                this.filesOptions = []

                res.file_types.map(el => {
                    if (el.id !== 29) {
                        this.filesOptions.push(el)
                    }
                })
                this.filesDoc.map(el => {
                    if (el.file_type_id === 29) {
                        getOrderVideo(el.id, {params: {order_id: this.order_id}}).then((value) => {
                            if (value.data) {
                                this.videoFile = 'data:video/webm;base64,' + value.data
                            }
                        })
                    }
                })
                if (this.vehicleType === 'agro') {
                    this.filesPhoto.filter(el => {
                        getOrderImage(el.id, {params: {order_id: this.order_id}}).then((value) => {
                            if (value.data) {
                                this.photoFind = true
                                return el.base64Image = 'data:image/jpeg;base64,' + value.data
                            }
                        })
                    })
                } else if (this.vehicleType === 'car') {
                    this.filesPhoto.filter(el => {
                        getOrderImage(el.id, {params: {order_id: this.order_id}}).then((value) => {
                            if (value.data) {
                                this.photoFind = true
                                return el.base64Image = 'data:image/jpeg;base64,' + value.data
                            }
                        })
                    })
                }
            }).finally(() => {
                this.loading = false
                if (this.user && this.user.role === 'moderator' && (this.filesDoc.length === 0 && this.filesPhoto.length === 0 && !this.certificate_id && !this.photoFind)) {
                    this.filesEmpty = true
                }
            });
        },

        selectFile(evt) {
            if (evt) {
                this.$refs.file_dialog.value = this.uploadedFile;
                this.file_type_id = evt
                this.pickFile = this.$refs.file_dialog
                this.pickFile.click();
            }
        },

        cancelFileDialog() {
            this.file_type_id = null
            this.item.file = {
                title: ''
            }
        },

        deleteFile() {
            this.deleteDialog = false
            let value = this.item
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
                this.getItems()
                if (value.doc.file_type_id === 29) {
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
                this.getItems()
            }).catch((res) => {
                this.loading = false
                this.showNotify(JSON.parse(res.message))
            }).finally(() => {
                this.file_type_id = null
                this.item.file = {
                    title: ''
                }
            })
        },

        getOrderFile(id) {
            this.doc_id = id
            getOrderFile(id, {params: {order_id: this.order_id}, responseType: 'arraybuffer'}).then((res) => {
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

        showNotify(messages) {
            let messageData = []
            Object.values(messages).map((el, i) => {
                messageData.push('<div></div>*' + el[0])
            });
            Notify.create({
                message: messageData,
                position: 'top-right',
                type: 'info',
                html: true,
                timeout: 20000,
                actions: [{icon: 'close', color: 'white'}]
            })
        }
    },

    created() {
        this.getItems()
    },

    mounted() {
        this.$emitter.on('VideoSendEvent', () => {
            this.getItems()
        })
    }
}
</script>

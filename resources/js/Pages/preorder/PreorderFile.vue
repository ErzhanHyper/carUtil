<template>
    <q-card flat>

        <q-card-section v-if="!blocked">
            <div v-if="vehicleType === 'car'" class="text-caption text-blue-grey-8">
                <div class="q-mb-xs">* Допускается отсутствие деревянных конструкции ВЭТС категорий N1, N2, N3.</div>
                <div class="q-mb-xs">* Допускается отсутствие шасси на ВЭТС в случае отсутствия при заводской сборке</div>
                <div class="q-mb-xs">* Крупноузловые детали сдаваемого ВЭТС (двигатель, коробка передач, карданный вал и мосты) должны быть установлены на штатных местах надежным способом крепления (болтовое)</div>
                <div class="q-mb-xs">* В случае невозможности крепления способом, предусмотренным изготовителем навесного оборудования ВЭТС генератора, стартера, радиатора, карбюратора, предусмотренных настоящим пунктом на штатных местах, они помещаются в салон и/или багажник сдаваемого ВЭТС</div>
            </div>
            <div v-else class="text-caption">
                <div class="q-mb-xs">* Для ВЭССХТ (комбайн) допускается демонтаж кабины для осуществления транспортировки до пункта приема/переработчика ВЭССХТ</div>
                <div class="q-mb-xs">* Допускается отсутствие деревянных конструкций и шасси на ВЭССХТ в случае отсутствия при заводской сборке</div>
            </div>
        </q-card-section>

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

<!--            <div class="text-body2" v-if="filesDoc.length > 0 || filesPhoto.length > 0 || (transfer && transfer.closed === 2)">Загруженные файлы</div>-->
            <div v-if="user.role === 'moderator' && (filesDoc.length === 0 && filesPhoto.length === 0 && (!transfer))">Файлы отсутствуют</div>
        </q-card-section>

        <q-separator inset/>

        <q-card-section>

            <template v-if="!onlyPhoto">
                <template v-for="(doc, i) in filesDoc" :key="i">

                    <div class="flex no-wrap flex-start q-mb-sm text-left relative-position text-deep-orange-10">
                        <q-icon name="file_copy" class="q-mr-sm" size="sm"  v-if="doc_id !== doc.id"></q-icon>
                        <q-circular-progress indeterminate rounded size="xs" v-if="doc_id === doc.id" color="primary" class="q-mr-xs"/>
                        <a href="#" @click="getFile(doc.id)" class="text-dark">
                            {{ doc.file_type.title }}
                        </a>
                        <q-icon name="close" class="q-ml-sm cursor-pointer" size="xs" style="margin-top: 2px"
                                color="negative"
                                v-if="!blocked" @click="deleteFile({type: 'doc', id: doc.id })">
                        </q-icon>
                    </div>
                </template>

                <div class="flex no-wrap flex-start q-mb-sm text-left relative-position text-deep-orange-10"
                     v-if="transfer && transfer.closed === 2">
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
                control-color="pink-5"
                infinite
                v-if="filesPhoto.length > 0 && photoFind"
            >
                <q-carousel-slide :name="i+1" class="q-pb-lg relative-position" v-for="(slide, i) in filesPhoto"
                                  :key="i">
                    <q-img :src="slide.base64Image" class="full-height cursor-pointer" @click="showImage(slide.base64Image)"/>
                    <div class="text-body1">{{slide.file_type.title }}</div>
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

    <q-dialog v-model="fileDialog">
        <q-card style="width: 100%;max-width:1200px;">
            <q-card-section class="flex justify-between q-py-xs q-px-xs ">
                <q-space/>
                <q-icon name="close" size="sm" flat v-close-popup class="cursor-pointer"/>
            </q-card-section>
            <q-card-section style="max-height: 90vh" class="scroll">
                <q-img :src="base64Image" style="width: 100%; "/>
            </q-card-section>
        </q-card>
    </q-dialog>

</template>

<script>
import {ref} from 'vue'
import {
    deletePreOrderFile, getAgroFile, getAgroFileImage,
    getCarFile, getCarFileImage,
    getPreOrderFileList,
    storePreOrderFile
} from "../../services/file";
import FileDownload from "js-file-download";
import {getTransferContract} from "../../services/document";
import {Notify} from "quasar";
import {mapGetters} from "vuex";

export default {

    props: ['preorder_id', 'client_id', 'blocked', 'onlyPhoto', 'vehicleType', 'transfer'],

    setup() {
        return {
            slide: ref(1),
            pickFile: ref({})
        }
    },

    data() {
        return {
            photoFind: false,
            imageLoaded: false,
            base64Image: '',
            doc_id: null,
            fileDialog: false,

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

    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    methods: {

        showImage(image){
            this.fileDialog = true
            this.base64Image = image
        },

        getItems() {
            getPreOrderFileList({
                preorder_id: this.preorder_id
            }).then(res => {
                this.filesOptions = res.file_types
                this.filesDoc = res.docs
                this.filesPhoto = res.photos

                if(this.vehicleType === 'agro') {
                    this.filesPhoto.filter(el => {
                        getAgroFileImage(el.id, {params: {preorder_id: this.preorder_id}}).then((value) => {
                            if(value.data) {
                                this.photoFind = true
                                return el.base64Image = 'data:image/jpeg;base64,' + value.data
                            }
                        })
                    })
                }else if(this.vehicleType === 'car'){
                    this.filesPhoto.filter(el => {
                        getCarFileImage(el.id, {params: {preorder_id: this.preorder_id}}).then((value) => {
                            if(value.data) {
                                this.photoFind = true
                                return el.base64Image = 'data:image/jpeg;base64,' + value.data
                            }
                        })
                    })
                }

            }).finally(() =>{
                this.loading = false
            })
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
                preorder_id: this.preorder_id,
                file_id: value.id
            }).then(() => {
                this.getItems()
            });
        },

         uploadFile(evt) {
            this.loading = true
             storePreOrderFile({
                file_type_id: this.file_type_id,
                file: evt.target.files[0],
                preorder_id: this.preorder_id,
                client_id: this.client_id
            }).then(() => {
                this.getItems()
                this.file_type_id = null
                this.pickFile = null
                this.$refs.file_dialog.value = null
                this.item.file = null
             }).catch(() => {
            })

        },

        downloadPFS() {
            if(this.transfer) {
                this.loading = true
                getTransferContract(this.transfer.id, {responseType: 'arraybuffer'}).then(res => {
                    FileDownload(res, 'transfer_contract.pdf')
                }).finally(() => {
                    this.loading = false
                })
            }
        },

        getFile(id){
            this.doc_id = id
            // this.fileDialog = true
            if(this.vehicleType === 'car') {
                getCarFile(id, {params: {preorder_id: this.preorder_id}, responseType: 'arraybuffer'}).then((res) => {
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
            else if(this.vehicleType === 'agro') {
                getAgroFile(id, {params: {preorder_id: this.preorder_id}, responseType: 'arraybuffer'}).then((res) => {
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

    },

    created() {
        this.getItems()
    }
}
</script>

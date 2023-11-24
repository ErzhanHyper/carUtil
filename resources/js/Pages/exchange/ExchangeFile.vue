<template>
    <q-select
        square v-model="item.file"
        label="Файл"
        :options="options"
        :model-value="item.file"
        option-label="title"
        option-value="name"
        map-options
        emit-value
        @update:model-value="evt => selectFile(evt)"
        class="q-mb-lg"
        outlined
        dense
        :loading="loading1"
        :readonly="readonly"
        v-if="!readonly"
    >
        <template v-slot:before>
            <q-icon name="folder"/>
        </template>
        <template v-slot:append>
            <q-btn round dense flat icon="add"/>
        </template>
    </q-select>

    <input type="file" ref="file_dialog" v-bind:value="uploadedFile" class="hidden"
           @change="event => uploadFile(event)"/>


    <template v-if="files.length > 0">
        <div class="flex no-wrap flex-start q-mb-sm text-left relative-position text-deep-orange-10"
             v-for="(doc, i) in files"
             :key="i">
            <q-icon name="file_copy" class="q-mr-sm" size="sm" v-if="doc_id !== doc.id"></q-icon>
            <q-circular-progress indeterminate rounded size="xs" v-if="doc_id === doc.id" color="primary" class="q-mr-sm"/>
            <a href="#" @click="downloadFile(doc.id)" class="text-dark">
                {{ getFileTypeTitle(doc.type) }}
            </a>
            <q-icon name="close" class="q-ml-sm cursor-pointer" size="xs" style="margin-top: 2px"
                    color="negative" @click="deleteFile(doc.id)" v-if="!readonly && !loading" >
            </q-icon>
        </div>
    </template>
</template>

<script>
import {ref} from "vue";
import {deleteExchangeFile, downloadExchangeFile, getExchangeFile, storeExchangeFile} from "../../services/exchange";
import {Notify} from "quasar";
import FileDownload from "js-file-download";

export default {

    props: ['data', 'readonly'],

    setup() {
        return {
            slide: ref(1),
            pickFile: ref({})
        }
    },

    data() {
        return {
            doc_id: null,
            loading: false,
            loading1: false,
            files: [],
            item: {},
            options: [
                {
                    name: 'ID_SOURCE',
                    title: 'Удостоверяющие документы/свидетельство владельца(представителя)'
                },
                {
                    name: 'ID_TARGET',
                    title: 'Удостоверяющие документы/свидетельство получателя(представителя)'
                },
                {
                    name: 'PHOTO_SOURCE',
                    title: 'Фото владельца'
                },
                {
                    name: 'PHOTO_TARGET',
                    title: 'Фото получателя'
                },
            ],
            uploadedFile: null,
            type: null,
        }
    },

    methods: {

        getFileTypeTitle(value){
            let title = ''
            this.options.map(el => {
                if(el.name === value){
                    title = el.title
                }
            })

            return title
        },

        selectFile(evt) {
            this.$refs.file_dialog.value = this.uploadedFile;
            this.type = evt
            this.pickFile = this.$refs.file_dialog
            this.pickFile.click();
        },

        uploadFile(evt) {
            this.loading1 = true
            storeExchangeFile(this.data.id,{
                type: this.type,
                file: evt.target.files[0],
            }).then(() => {
                this.getFiles()
                this.type = null
                this.pickFile = null
                this.file = null
            }).catch(err => {
                Notify.create({
                    message: err.file,
                    position: 'bottom',
                    type: 'warning'
                })
            }).finally(() => {
                this.loading1 = false
            })
        },

        downloadFile(id){
            this.doc_id = id
            downloadExchangeFile(id, {params: {exchange_id: this.data.id}, responseType: 'arraybuffer'}).then(res => {
                if(res) {
                    let parts = res.headers.get('Content-Disposition').split(';');
                    let filename = parts[1].split('=')[1];
                    FileDownload(res.data, filename)
                    this.doc_id = null
                }
            })
        },

        getFiles() {
            getExchangeFile(this.data.id).then(res => {
                this.files = res
            }).finally(() => {
                this.loading1 = false
                this.loading = false
            })
        },

        deleteFile(id){
            this.loading = true
            this.loading1 = true
            deleteExchangeFile(id).then(res => {
                this.getFiles()
            })
        }
    },

    created() {
        this.getFiles()
    }
}
</script>

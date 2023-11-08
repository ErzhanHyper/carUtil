<template>
    <q-card flat>
        <q-card-section>
            <q-select
                square v-model="item.file"
                label="Файл"
                :options="options"
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
                 v-for="(doc, i) in items"
                 :key="i">
                <q-icon :name="'insert_drive_file'" class="q-mr-sm" size="sm"></q-icon>
                <a :href="'/storage/uploads/order/files/' + doc.sell_id + '/' + doc.orig_name" class="text-dark">
                    {{ getFileTypeTitle(doc.type) }}
                </a>
                <q-icon name="close" class="q-ml-sm cursor-pointer" size="xs" style="margin-top: 2px" color="negative"
                        v-if="!blocked" @click="deleteFile(doc.id)">
                </q-icon>
            </div>
            <input type="file" ref="file_dialog" v-bind:value="uploadedFile" v-on:input="event => uploadFile(event)" class="hidden" v-if="!blocked"/>

        </q-card-section>

    </q-card>
</template>

<script>
import {deleteSellFile, getSellFilesById, storeSellFile} from "../../services/sell";
import {ref} from "vue";

export default {
   props: ['id', 'blocked'],

    setup() {
        return {
            slide: ref(1),
            pickFile: ref({})
        }
    },

    data(){
       return{
           item: {},
           file_type_id: null,
           options: [
               {
                   id: 'ID',
                   title: 'Удостоверяющие документы'
               },
               {
                   id: 'APP_SELL',
                   title: 'Расписка об использовании'
               },
               {
                   id: 'ACT_SELL',
                   title: 'Акт об использовании'
               },
               {
                   id: 'ID_VEHICLE',
                   title: 'Паспорт ТС'
               },
               {
                   id: 'PHOTO',
                   title: 'Фото заявителя'
               }
           ],
           items: [],
           uploadedFile: null,
           loading: false,
       }
    },

    methods: {

        getFileTypeTitle(value){
            let type = ''
            this.options.map(el => {
                if(value === el.id){
                    type = el.title
                }
            })
            return type
        },

        selectFile(evt) {
            this.$refs.file_dialog.value = this.uploadedFile;
            this.file_type_id = evt
            this.pickFile = this.$refs.file_dialog
            this.pickFile.click();
        },

        deleteFile(id) {
            this.loading = true
            deleteSellFile(id).then(() => {
                this.getData()
            })
        },

        uploadFile(evt) {
            this.loading = true
            storeSellFile({
                type: this.file_type_id,
                file: evt.target.files[0],
                sell_id: this.id
            }).then(res => {
                this.getData()
            })
        },

        getData(){
            getSellFilesById(this.id).then(res => {
                this.items = res.items
                this.loading = false
            })
        }
    },

    created(){
       this.getData()
    }
}
</script>

<style scoped>

</style>

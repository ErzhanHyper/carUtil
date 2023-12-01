<template>
    <q-card flat>
        <q-card-section>
            <div class="text-h6 q-mb-md">Шаблоны документов</div>
            <div class="q-gutter-sm">
                <q-btn :loading="loading1" square size="12px"
                       unelevated color="indigo-1"
                       class="text-indigo-10"

                       label="Сформировать заявление на ТС"
                       icon-right="edit_note"
                        @click="statementDoc"
                />
                <q-btn :loading="loading2" square size="12px"  unelevated color="indigo-1" class="text-indigo-10"  label="Сформировать акт комплектности"
                       icon-right="edit_note" @click="showComplectDialog = true" ></q-btn>
                <q-btn :loading="loading3" square size="12px" unelevated color="indigo-1" class="text-indigo-10"   label="Сформировать акт приема-передачи"
                       icon-right="edit_note" @click="contractDoc"></q-btn>
            </div>
        </q-card-section>
    </q-card>

    <q-dialog v-model="showComplectDialog">
        <q-card>
            <q-card-section>
                <div class="text-h6">Формирования акта комплектности</div>
            </q-card-section>

            <q-separator />

            <q-card-section style="max-height: 70vh" class="scroll">
                <div class="flex column" >
                    <div class="text-center">
                    <q-spinner-dots v-if="complect.length === 0" />
                    </div>
                    <template v-for="(el, i) in complect" :key="i">
                        <q-checkbox :label="el.label" v-model="el.value"/>
                    </template>
                </div>

            </q-card-section>

            <q-separator />

            <q-card-actions align="right">
                <q-btn  label="Сохранить" color="blue-8" v-close-popup @click="genComplectApp" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<script>
import {getComplectApp, getContractDoc, getStatementDoc} from "../../services/document";
import {generateOrderPFS} from "../../services/file";
import FileDownload from "js-file-download";
import {getCategoryComplectList} from "../../services/category";

export default {

    props: ['order_id', 'category'],

    data(){
        return{
            loading1: false,
            loading2: false,
            loading3: false,
            loading4: false,

            showComplectDialog: false,

            item: {},

            complect: [],
        }
    },

    methods: {

        genComplectApp() {
            this.loading2 = true
            getComplectApp(this.order_id, {params: {complect: this.complect}, responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'Акт_комплектности.pdf')
            }).finally(() => {
                this.loading2 = false
            })
        },

        getComplect(){
            getCategoryComplectList({params: {category: this.category.title}}).then(res => {
                res.items.map(el => {
                    this.complect.push({
                        label: el,
                        value: false
                    })
                })
            })
        },

        statementDoc(){
            this.loading1 = true
            getStatementDoc(this.order_id, {responseType: 'arraybuffer'}).then(res => {
                FileDownload(res.data, 'Заявление_на_ТС/СХТ.pdf')
            }).finally(() => {
                this.loading1 = false
            })
        },

        contractDoc(){
            this.loading3 = true
            getContractDoc(this.order_id, {responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'Акт_приема_передачи.pdf')
            }).finally(() => {
                this.loading3 = false
            })
        }
    },

    created(){
        this.getComplect()
    }
}
</script>

<style scoped>

</style>

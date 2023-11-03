<template>
    <q-card flat>
        <q-card-section>
            <div class="text-h6">Шаблоны документов</div>
        </q-card-section>
        <q-card-section>
            <div class="q-gutter-sm">
                <q-btn :loading="loading1" square size="12px"
                       unelevated color="indigo-1"
                       class="text-indigo-10"
                       flat
                       dense
                       label="Сформировать заявление на ТС"
                       icon-right="edit_note"
                        @click="statementDoc"
                />
                <q-btn :loading="loading2" square size="12px" dense unelevated color="indigo-1" class="text-indigo-10" flat label="Сформировать акт комплектности"
                       icon-right="edit_note" @click="showComplectDialog = true"></q-btn>
                <q-btn :loading="loading3" square size="12px" unelevated color="indigo-1" class="text-indigo-10" flat dense label="Сформировать акт приема-передачи"
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
                <div class="flex column">
                    <q-checkbox label="Кузов (Шасси)" v-model="item.id1"/>
                    <q-checkbox label="Крышка капота" v-model="item.id2"/>
                    <q-checkbox label="Крышка багажника" v-model="item.id3"/>
                    <q-checkbox label="Двери" v-model="item.id4"/>
                    <q-checkbox label="Колеса" v-model="item.id5"/>
                    <q-checkbox label="Ограждающие покрытия колес (Крылья)" v-model="item.id6"/>
                    <q-checkbox label="Двигатель с генератором, стартером, карбюратором/системой впрыска" v-model="item.id7"/>
                    <q-checkbox label="Радиатор" v-model="item.id8"/>
                    <q-checkbox label="Редуктор" v-model="item.id9"/>
                    <q-checkbox label="Аккумулятор" v-model="item.id10"/>
                    <q-checkbox label="Коробка передач" v-model="item.id11"/>
                    <q-checkbox label="Раздаточная коробка, мосты и редуктора мостов, карданные валы" v-model="item.id12"/>
                </div>
            </q-card-section>

            <q-separator />

            <q-card-actions align="right">
                <q-btn  label="Сохранить" color="blue-8" v-close-popup />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<script>
import {getContractDoc, getStatementDoc} from "../../services/document";
import {generateOrderPFS} from "../../services/file";
import FileDownload from "js-file-download";

export default {

    props: ['order_id'],

    data(){
        return{
            loading1: false,
            loading2: false,
            loading3: false,

            showComplectDialog: false,

            item: {
                id1: false,
                id2: false,
                id3: false,
                id4: false,
                id5: false,
                id6: false,
                id7: false,
                id8: false,
                id9: false,
                id10: false,
                id11: false,
                id12: false,
            }
        }
    },

    methods: {
        statementDoc(){
            this.loading1 = true
            getStatementDoc(this.order_id, {responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'statement.pdf')
            }).finally(() => {
                this.loading1 = false
            })
        },

        contractDoc(){
            this.loading3 = true
            getContractDoc(this.order_id, {responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'contract.pdf')
            }).finally(() => {
                this.loading3 = false
            })
        }
    }
}
</script>

<style scoped>

</style>

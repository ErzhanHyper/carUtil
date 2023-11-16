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
                       icon-right="edit_note" @click="showComplectDialog = true" ></q-btn>
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
                <div class="flex column" v-if="category.title === 'M1' || category.title === 'M2' || category.title === 'M3'">
                    <q-checkbox label="Кузов (Шасси)" v-model="item.id1"/>
                    <q-checkbox label="Крышка капота" v-model="item.id2"/>
                    <q-checkbox label="Крышка багажника" v-model="item.id3"/>
                    <q-checkbox label="Двери (штатное кол-во)" v-model="item.id4"/>
                    <q-checkbox label="Колеса с шинами (штатное кол-во)" v-model="item.id5"/>
                    <q-checkbox label="Ограждающие покрытия колес (крылья) (штатное кол-во)" v-model="item.id6"/>
                    <q-checkbox label="Двигатель с генератором, стартером, карбюратором/системой впрыска (установлено на штатных местах)" v-model="item.id7"/>
                    <q-checkbox label="Радиатор" v-model="item.id8"/>
                    <q-checkbox label="Редуктор" v-model="item.id9"/>
                    <q-checkbox label="Аккумулятор" v-model="item.id10"/>
                    <q-checkbox label="Коробка передач" v-model="item.id11"/>
                    <q-checkbox label="Раздаточная коробка, мосты и редуктора мостов, карданные валы" v-model="item.id12"/>
                </div>

                <div class="flex column" v-if="category.title === 'N1' || category.title === 'N2' || category.title === 'N3'">
                    <q-checkbox label="Кузов/ фургон/ платформа/ специальное оборудование на шасси (в случае если они предусмотрены штатным оснащением изготовителя)" v-model="item.id1"/>
                    <q-checkbox label="Кабина, шасси, двери (штатное количество)" v-model="item.id2"/>
                    <q-checkbox label="Колеса с шинами (штатное количество)" v-model="item.id3"/>
                    <q-checkbox label="Двигатель с генератором, стартером, карбюратором/системой впрыска" v-model="item.id4"/>
                    <q-checkbox label="Радиатор" v-model="item.id5"/>
                    <q-checkbox label="Редуктор" v-model="item.id6"/>
                    <q-checkbox label="Аккумулятор" v-model="item.id7"/>
                    <q-checkbox label="Коробка передач" v-model="item.id8"/>
                    <q-checkbox label="Раздаточная коробка, мосты и редуктора мостов, крупноузловые детали, подвески ходовой части, карданные валы (в случае если она предусмотрена штатным оснащением изготовителя, установлено на штатных местах)" v-model="item.id9"/>
                </div>

                <div class="flex column" v-if="category.title === 'tractor' || category.title === 'combain'">
                    <q-checkbox label="Кузов/ фургон/ платформа/ специальное оборудование на шасси (в случае если они предусмотрены штатным оснащением изготовителя)" v-model="item.id1"/>
                    <q-checkbox label="Кабина, шасси, двери (штатное количество)" v-model="item.id2"/>
                    <q-checkbox label="Колеса с шинами (штатное количество)" v-model="item.id3"/>
                    <q-checkbox label="Двигатель с генератором, стартером, карбюратором/системой впрыска" v-model="item.id4"/>
                    <q-checkbox label="Радиатор" v-model="item.id5"/>
                    <q-checkbox label="Редуктор" v-model="item.id6"/>
                    <q-checkbox label="Аккумулятор" v-model="item.id7"/>
                    <q-checkbox label="Коробка передач" v-model="item.id8"/>
                    <q-checkbox label="Раздаточная коробка, мосты и редуктора мостов, крупноузловые детали, подвески ходовой части, карданные валы (в случае если она предусмотрена штатным оснащением изготовителя, установлено на штатных местах)" v-model="item.id9"/>
                </div>

            </q-card-section>

            <q-separator />

            <q-card-actions align="right">
                <q-btn  label="Сохранить" color="blue-8" v-close-popup @click="genComplect" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<script>
import {getComplectApp, getContractDoc, getStatementDoc} from "../../services/document";
import {generateOrderPFS} from "../../services/file";
import FileDownload from "js-file-download";

export default {

    props: ['order_id', 'category'],

    data(){
        return{
            loading1: false,
            loading2: false,
            loading3: false,
            loading4: false,

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

        genComplect() {
            this.loading2 = true
            getComplectApp(this.order_id, {params: {complect: this.item}, responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'statement.pdf')
            }).finally(() => {
                this.loading2 = false
            })
        },

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
    },

    created(){
        console.log(this.category)
    }
}
</script>

<style scoped>

</style>

<template>

    <q-btn square size="12px" color="primary" label="Проверка в КАП" icon="add_task" @click="kapDialog = true" />

    <q-dialog v-model="kapDialog" size="md" persistent>
    <q-card class="kap_detail_block" style="width: 100%;max-width: 1400px;">
        <q-card-section class="flex q-py-sm">
            <div class="text-body1">Данные с КАП</div>
            <q-space/>
            <q-icon name="close" size="sm" flat v-close-popup class="cursor-pointer"/>
        </q-card-section>

        <q-separator/>

        <q-card-section style="max-height: 70vh" class="scroll">

            <div class="text-center">
                <q-spinner-dots
                    color="primary"
                    size="3em"
                    v-if="loading || !show"
                />
            </div>

            <div v-if="!loading && !blocked">
                Текущий запрос в КАП:
                <div class="flex q-mb-lg no-wrap" v-if="items.length > 0">
                    <q-markup-table flat bordered separator="cell" >
                    <tbody>
                        <tr style="background-color: #e0e6ed;">
                            <th>Дата операции</th>
                            <th>ГРНЗ</th>
                            <th>Модель</th>
                            <th>Номер двигателя</th>
                            <th>Номер шасси</th>
                            <th>Номер кузова</th>
                            <th>Цвет</th>
                            <th>Дата снятия с учета</th>
                            <th>Категория</th>
                            <th>Объем двигателя</th>
                            <th>Масса</th>
                            <th>Масса без нагрузки</th>
                            <th>VIN</th>
                            <th>Причина снятия с учета</th>
                            <th>ИИН/БИН</th>
                            <th>Фамилия</th>
                            <th>Имя</th>
                            <th>Отчество</th>
                            <th>Статус</th>
                        </tr>
                        <tr v-for="(item, i) in items" :key="i" >
                            <td >{{ item.status_date }}</td>
                            <td >{{ item.grnz }}</td>
                            <td >{{ item.model }}</td>
                            <td >{{ item.engine_no }}</td>
                            <td >{{ item.chassis_no }}</td>
                            <td >{{ item.body_no }}</td>
                            <td >{{ item.color_name }}</td>
                            <td >{{ item.issue_year }}</td>
                            <td >{{ item.category }}</td>
                            <td >{{ item.engine_volume }}</td>
                            <td >{{ item.max_weight }}</td>
                            <td >{{ item.unloaded_weight }}</td>
                            <td >{{ item.vin }}</td>
                            <td >{{ item.unreg_reason }}</td>
                            <td >{{ item.iinbin }}</td>
                            <td >{{ item.lastname }}</td>
                            <td >{{ item.firstname }}</td>
                            <td >{{ item.midname }}</td>
                            <td >{{ item.status }}</td>
                        </tr>
                    </tbody>
                </q-markup-table>
            </div>
            <div class="bg-blue-8 q-pa-md text-white" v-if="!loading && card != ''">
                <span v-html="card"></span>
            </div>
            </div>

            <div class="group q-py-lg" v-if="!blocked">
                <q-select v-model="kap.type" :options="options" option-label="title" option-value="name" emit-value map-options style="width: 200px" outlined dense label="Тип запроса" class="q-mb-md" @update:model-value="selectData" :readonly="loading"></q-select>
                <q-input type="textarea" label="Цель запроса" outlined rows="2" style="width: 400px" v-model="kap.message" :readonly="loading"/>
                <div class="flex no-wrap q-mt-md">
                    <q-input label="Значение" outlined dense style="width: 400px" v-model="kap.value" :readonly="loading"/>
                </div>
                <q-separator class="q-my-lg"/>
            </div>

            <div v-if="history.length > 0 && show" class="q-mt-sm">
                История запросов в КАП:
                <div v-for="el in history">
                    {{ el['created_at'] }} | {{ el['username'] ? el['username'] : '-'}}
                    <div v-html="el['k_status']" class="q-mb-md bg-blue-grey-1 q-pa-sm"></div>
                </div>
            </div>
            <div v-if="history.length === 0 && show">Нет запросов</div>

        </q-card-section>

        <q-card-actions class="q-px-md" v-if="!blocked">
            <q-btn icon="add_task" square color="indigo-8" label="Отправить запрос" :loading="loading" @click="getKapData"/>
            <q-space />
            <q-btn icon-right="download" icon="file_copy" square color="primary" label="Скачать справку" :loading="loading1" v-if="this.card" @click="getRef"/>
        </q-card-actions>
    </q-card>
    </q-dialog>

</template>

<script>
import {checkVehicle, checkVehicleHistory} from "../../services/preorder";
import {getKapReference, getStatementDoc} from "../../services/document";
import FileDownload from "js-file-download";

export default {
    props: ['preorder_id', 'order_id', 'data', 'blocked'],

    data() {
        return {
            kapDialog: false,
            loading: false,
            loading1: false,
            show: false,
            kap_request_id: null,
            kap: {
                type: 'vin',
                message: 'Проверка',
                grnz: this.data.grnz,
                vin: this.data.vin,
                iinbin: this.data.iinbin
            },
            items: [],
            history: [],
            card: '',
            options: [
                {
                    name: 'vin',
                    title: 'VIN'
                },
                {
                    name: 'grnz',
                    title: 'ГРНЗ'
                },
                {
                    name: 'iinbin',
                    title: 'ИИН/БИН'
                },
            ]
        }
    },

    methods: {

        getRef(){
            this.loading1 = true
            getKapReference(this.kap_request_id, {responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'kap_request.pdf')
            }).finally(() => {
                this.loading1 = false
            })
        },

        selectData() {
            if(this.kap.type === 'grnz'){
                this.kap.value = this.data.grnz
            }else if(this.kap.type === 'vin'){
                this.kap.value = this.data.vin
            }else if(this.kap.type === 'iinbin'){
                this.kap.value = this.data.iinbin
            }
        },

        getKapData(){
            this.loading = true
            checkVehicle({ preorder_id: this.preorder_id, order_id: this.order_id, value: this.kap.value, type: this.kap.type, base_on: this.kap.message}).then(res => {
                this.kap_request_id = res.data.id
                if(res.data.card) {
                    this.card = res.data.card
                }
                if(res.data.items) {
                    this.items = res.data.items
                }
                console.log(this.items)

                this.getKapHistory(0)
            }).finally(() => {
                this.loading = false
            })
        },

        getKapHistory(){
            this.history = []
            checkVehicleHistory({ preorder_id: this.preorder_id, order_id: this.order_id}).then(res => {
                this.history = res.items
            }).finally(() => {
                this.show = true
            })
        }

    },

    created() {
        this.selectData()
        this.getKapHistory()
    }
}
</script>

<style scoped>

</style>

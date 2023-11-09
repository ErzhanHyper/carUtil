<template>
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
                    v-if="loading"
                />
            </div>

            <div v-if="!loading && items.length > 0" class="flex q-mb-lg no-wrap">
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
            <div class="group q-py-lg">
                <q-select v-model="kap.type" :options="options" option-label="title" option-value="name" emit-value map-options style="width: 200px" outlined dense label="Тип запроса" class="q-mb-md" @update:model-value="selectData" :readonly="loading"></q-select>
                <q-input type="textarea" label="Цель запроса" outlined rows="2" style="width: 400px" v-model="kap.message" :readonly="loading"/>
                <div class="flex no-wrap q-mt-md">
                    <q-input label="Значение" outlined dense style="width: 400px" v-model="kap.value" :readonly="loading"/>
                </div>
            </div>

        </q-card-section>

        <q-card-actions class="q-px-md">
            <q-btn icon="add_task" square color="indigo-8" label="Отправить запрос" :loading="loading" @click="getKapData"/>
        </q-card-actions>
    </q-card>
</template>

<script>
import {checkVehicle} from "../../services/preorder";

export default {
    props: ['preorder_id', 'order_id', 'data'],

    data() {
        return {
            loading: false,
            kap: {
                type: 'vin',
                message: 'Проверка',
                grnz: this.data.grnz,
                vin: this.data.vin,
                iinbin: this.data.iinbin
            },
            items: [],
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
            checkVehicle({ preorder_id: this.preorder_id, order_id: this.order_id, value: this.kap.value, type: this.kap.type}).then(res => {
                this.items = res.data.items
                this.card = res.data.card
            }).finally(() => {
                this.loading = false
            })
        },

    },

    created() {
        this.selectData()
    }
}
</script>

<style scoped>

</style>

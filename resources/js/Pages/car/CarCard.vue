<template>
    <q-card>
        <q-card-section class="q-pb-xs ">
            <div class="q-gutter-sm flex justify-between">
                <div class="text-body2">ТС</div>
                <q-btn label="Получить данные ТС" color="primary" class="text-overline q-mb-sm" icon="manage_search"
                       @click="search()" v-if="!showFields && kap_data.length === 0" />
            </div>

            <q-select dense outlined square v-model="item.vin" label="VIN"
                      :options="kap_data"
                      map-options
                      emit-value
                      option-label="vin" option-value="id" :readonly="blocked"
                      @update:model-value="setVehicle(item.vin)" v-if="kap_data.length > 0 || showFields"
            class="q-mb-sm q-mt-sm" />

        </q-card-section>

        <q-card-section v-if="showFields">

            <div class="row">
                <div class="col q-mb-lg">
                    <q-select dense outlined square v-model="item.category_id" label="Категория"
                              :options="options_category"
                              map-options
                              emit-value
                              option-label="title_ru" option-value="id" :readonly="blocked"/>
                </div>
            </div>

            <div class="row q-gutter-md q-mb-sm">
                <div class="col">
                    <q-input outlined dense square v-model="item.m_model" label="Марка, модель" :readonly="blocked"/>
                </div>
                <div class="col">
<!--                    <q-input outlined dense square v-model="item.vin" label="VIN" counter class="text-uppercase"-->
<!--                             :readonly="blocked">-->
<!--                        &lt;!&ndash;                        <template v-slot:label>&ndash;&gt;-->
<!--                        &lt;!&ndash;                        <q-icon name="check" size="xs"/>&ndash;&gt;-->
<!--                        &lt;!&ndash;                        </template>&ndash;&gt;-->
<!--                    </q-input>-->

                    <q-input outlined dense square v-model="item.year" label="Год выпуска" counter maxlength="4"
                             type="number" min="0" :readonly="blocked"/>
                </div>
                <div class="col">
                    <q-input outlined dense square v-model="item.grnz" label="ГРНЗ" counter maxlength="8"
                             :readonly="blocked"/>
                </div>
            </div>

            <div class="row q-gutter-md q-mb-sm">
                <div class="col">
                    <q-input outlined dense square v-model="item.body_no" label="Номер кузова" counter
                             :readonly="blocked"/>
                </div>
                <div class="col">
                    <q-input outlined dense square v-model="item.chassis_no" label="Номер шасси" counter
                             :readonly="blocked"/>
                </div>
            </div>

            <div class="row q-gutter-md q-mb-sm">
                <div class="col">
                    <q-input outlined dense square v-model="item.weight" label="Масса (кг)" hint="Без нагрузки"
                             :readonly="blocked"/>
                </div>
            </div>

            <div class="row q-gutter-md q-mb-md">
                <div class="col">
                    <q-input outlined dense square v-model="item.doors_count" label="Количество дверей" type="number"
                             min="0" :readonly="blocked"/>
                </div>
                <div class="col">
                    <q-input outlined dense square v-model="item.wheels_count" label="Количество колес" type="number"
                             min="0" :readonly="blocked"/>
                </div>

            </div>

            <div class="row q-gutter-md q-mb-sm">
                <div class="col">
                    <q-input outlined dense square v-model="item.wheels_protector_count"
                             label="Ограждающие покрытие колес" type="number" min="0" :readonly="blocked"/>
                </div>
            </div>

        </q-card-section>

    </q-card>

</template>

<script>

import {checkVehicle} from "../../services/preorder";

export default {

    props: ['data', 'getCar', 'categories', 'blocked', 'order_id'],

    data() {
        return {
            item: {
                category_id: null,
                vin: '',
                grnz: '',
                m_model: '',
            },

            showFields: false,

            kap_data: [],
            options_category: this.categories
        }
    },

    methods: {

        setVehicle(value) {
            this.item = value
            this.showFields = true
        },

        search() {
            checkVehicle({
                preorder_id: this.order_id,
            }).then(res => {
                if(res) {
                    res.map(el => {
                        this.kap_data.push({
                            m_model: el[2]['MODEL'][0],
                            grnz: el[1]['GRNZ'][0],
                            vin: el[13]['VIN'][0],
                            body_no: el[6]['BODY_NO'][0],
                            year: el[3]['ISSUE_YEAR'][0],
                            weight: el[11]['UNLOADED_WEIGHT'][0],
                            chassis_no: el[5]['CHASSIS_NO'][0]
                        })
                    })
                }
            })
        }
    },

    created() {
        if (this.data && this.data.vin) {
            this.item = this.data
            this.showFields = true
        }
    },

    mounted() {
        this.$emitter.on('CarCardEvent', () => {
            this.getCar(this.item)
        })
    }
}
</script>

<style scoped>

</style>

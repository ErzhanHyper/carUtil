<template>
    <q-card flat bordered>
        <q-card-section class="q-pb-xs ">
            <div class="q-gutter-sm flex justify-between">
                <div class="text-body2">{{ vehicleType === 'car' ? 'ТС' : 'СХТ' }}</div>
                <q-btn label="Получить данные" color="primary" class="text-overline q-mb-sm" icon="manage_search" icon-right="directions_car"
                       :loading="loading"
                       @click="search()" v-if="!showFields && kap_data.length === 0" />
            </div>

            <q-select dense outlined square v-model="item" label="VIN"
                      :options="kap_data"
                      option-label="vin" option-value="id" :readonly="blocked"
                      @update:model-value="setVehicle(item)" v-if="kap_data.length > 0 || showFields"
            class="q-mb-sm q-mt-sm" />

        </q-card-section>

        <q-card-section v-show="showFields">

            <div class="row">
                <div class="col q-mb-lg">
                    <category-field :readonly="blockedCustom" :vehicleType="vehicleType" :data="item.category_id" v-model="item.category_id" @update:model-value="selectCategory()"/>
                </div>
            </div>

            <div class="row q-gutter-md q-mb-sm">
                <div class="col">
                    <q-input outlined dense square v-model="item.m_model" label="Марка, модель" :readonly="blocked || disabled"/>
                </div>
                <div class="col">
<!--                    <q-input outlined dense square v-model="item.vin" label="VIN" counter class="text-uppercase"-->
<!--                             :readonly="blocked">-->
<!--                        &lt;!&ndash;                        <template v-slot:label>&ndash;&gt;-->
<!--                        &lt;!&ndash;                        <q-icon name="check" size="xs"/>&ndash;&gt;-->
<!--                        &lt;!&ndash;                        </template>&ndash;&gt;-->
<!--                    </q-input>-->

                    <q-input outlined dense square v-model="item.year" label="Год выпуска" counter maxlength="4"
                             type="number" min="0" :readonly="blocked || disabled"/>
                </div>
                <div class="col">
                    <q-input outlined dense square v-model="item.grnz" label="ГРНЗ" counter maxlength="8"
                             :readonly="blocked || disabled"/>
                </div>
            </div>

            <div class="row q-gutter-md q-mb-sm">
                <div class="col">
                    <q-input outlined dense square v-model="item.body_no" label="Номер кузова" counter
                             :readonly="blocked || disabled"/>
                </div>
                <div class="col">
                    <q-input outlined dense square v-model="item.chassis_no" label="Номер шасси" counter
                             :readonly="blocked || disabled"/>
                </div>
            </div>

            <div class="row q-gutter-md q-mb-sm">
                <div class="col">
                    <q-input outlined dense square v-model="item.weight" label="Масса (кг)" hint="Без нагрузки"
                             :readonly="blocked || disabled"/>
                </div>
                <div class="col">
                    <q-input outlined dense square v-model="item.color" label="Цвет" hint="Без нагрузки"
                             :readonly="blocked || disabled"/>
                </div>
            </div>

            <div class="row q-gutter-md q-mb-md">
                <div class="col">
                    <q-input outlined dense square v-model="item.doors_count" label="Количество дверей" type="number"
                             :min="0" :rules="doorsRules" :readonly="blockedCustom"/>
                </div>
                <div class="col">
                    <q-input outlined dense square v-model="item.wheels_count" label="Количество колес" type="number"
                             min="0" :readonly="blockedCustom"/>
                </div>

            </div>

            <div class="row q-gutter-md q-mb-sm">
                <div class="col">
                    <q-input outlined dense square v-model="item.wheels_protector_count"
                             label="Ограждающие покрытие колес" type="number" min="0" :readonly="blockedCustom"/>
                </div>
            </div>

        </q-card-section>

    </q-card>

</template>

<script>

import {checkVehicle} from "@/services/preorder";
import CategoryField from "@/Components/Fields/CategoryField.vue";
import {Notify} from "quasar";

export default {
    components: {CategoryField},
    props: ['data', 'getCar', 'blocked', 'blockedCustom', 'vehicleType', 'preorder_id'],
    setup () {
        return {
            doorsRules: [
                val => (val !== null && val !== '') || 'Не больше 5',
                val => (val > 0 && val < 6) || 'Не больше 5'
            ],
        }
    },

    data() {
        return {
            item: {
                category_id: null,
                vin: '',
                grnz: '',
                m_model: '',
                color: ''
            },

            showFields: false,
            loading: false,
            disabled: false,

            kap_data: [],
            options_category: this.categories
        }
    },

    methods: {

        setVehicle(value) {
            this.item = value
            this.showFields = true
        },

        selectCategory(){
            this.$emitter.emit('CarCategoryEvent')
        },

        search() {
            this.loading = true
            checkVehicle({
                preorder_id: this.preorder_id,
            }).then(res => {
                if(res) {
                    if(res.data && res.data.message && res.data.message !== ''){
                        Notify.create({
                            message: res.data.message,
                            position: 'bottom',
                            type: 'primary'
                        })
                    }
                    res.data.items.map((el, i) => {
                        this.kap_data.push({
                            category_id: null,
                            m_model: el.model,
                            grnz: el.grnz,
                            vin: el.vin,
                            body_no: el.body_no,
                            year: el.issue_year,
                            weight: el.unloaded_weight,
                            chassis_no: el.chassis_no,
                            color: el.color_name,
                            engine_no: el.engine_no
                        })
                    })
                    this.disabled = true
                }
            }).finally(() => {
                this.loading = false
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
            if(this.getCar) {
                this.getCar(this.item)
            }
        })
    }
}
</script>

<style scoped>

</style>

<template>
    <q-card class="transparent" flat square>
        <q-card-section class="q-pa-none">

            <div class="flex wrap items-start">

                <q-input
                    v-if="filters.includes('id')"
                    v-model="item.id"
                    class="responsive_field text-uppercase q-mr-sm q-mb-sm"
                    clearable
                    dense
                    label="№"
                    outlined
                    standout="bg-blue-grey-1"
                    style="width: 120px"
                />

                <q-input
                    v-if="filters.includes('vin')"
                    v-model="item.vin"
                    class="responsive_field text-uppercase q-mr-sm q-mb-sm"
                    clearable
                    dense
                    label="VIN"
                    outlined
                    standout="bg-blue-grey-1"
                    style="width: 200px"
                />

                <q-input
                    v-if="filters.includes('grnz')"
                    v-model="item.grnz"
                    class=" responsive_field text-uppercase q-mr-sm q-mb-sm"
                    clearable
                    dense
                    label="ГРНЗ"
                    outlined
                    standout="bg-blue-grey-1"
                    style="width: 140px"
                />

                <q-input
                    v-if="filters.includes('fio')"
                    v-model="item.title"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    label="ФИО"
                    outlined
                    standout="bg-blue-grey-1"
                    style="width: 220px"
                />

                <q-input
                    v-if="filters.includes('idnum')"
                    v-model="item.idnum"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    label="ИИН/БИН"
                    outlined
                    standout="bg-blue-grey-1"
                />

                <q-select
                    v-if="filters.includes('order_type')"
                    v-model="item.type"
                    :options="['ВЭТС', 'ВЭССХТ']"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    label="Тип заявки"
                    outlined
                    style="width: 140px"
                    transition-hide="jump-up"
                    transition-show="jump-up"
                />

                <q-select
                    v-if="filters.includes('order_status')"
                    v-model="item.approve"
                    :options="statuses1"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    emit-value
                    label="Статус одобрения"
                    map-options
                    menu-shrink
                    multiple
                    option-label="title"
                    option-value="id"
                    options-cover
                    options-dense
                    outlined
                    standout="bg-blue-grey-1"
                    style="width: 200px"
                    transition-hide="jump-up"
                    transition-show="jump-up"
                />

                <q-select
                    v-if="filters.includes('order_status')"
                    v-model="item.status"
                    :options="statuses3"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    emit-value
                    label="Статус заявки"
                    map-options
                    menu-shrink
                    multiple
                    option-label="title"
                    option-value="id"
                    options-cover
                    options-dense
                    outlined
                    standout="bg-blue-grey-1"
                    style="width: 200px"
                    transition-hide="jump-up"
                    transition-show="jump-up"
                />

                <q-select
                    v-if="filters.includes('preorder_status')"
                    v-model="item.status"
                    :options="statuses2"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    emit-value
                    label="Статус"
                    map-options
                    menu-shrink
                    multiple
                    option-label="title"
                    option-value="id"
                    options-cover
                    options-dense
                    outlined
                    standout="bg-blue-grey-1"
                    style="width: 280px"
                    transition-hide="jump-up"
                    transition-show="jump-up"
                />

                <manufacture-field
                    v-if="filters.includes('manufacture')"
                    v-model="data.manufacture"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    option-value="title"
                    outlined
                    standout="bg-blue-grey-1"
                    style="width: 280px"
                />

                <q-input
                    v-if="filters.includes('brand')"
                    v-model="data.brand"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    label="Марка"
                    outlined
                    standout="bg-blue-grey-1"
                />

                <q-input
                    v-if="filters.includes('model')"
                    v-model="data.model"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    label="Модель"
                    outlined
                    standout="bg-blue-grey-1"
                />

                <category-field
                    v-if="filters.includes('category')"
                    v-model="data.category"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    outlined
                    square="false"
                    standout="bg-blue-grey-1"
                    style="width: 180px"
                />

                <q-input
                    v-if="filters.includes('class')"
                    v-model="data.class"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    label="Класс"
                    outlined
                    standout="bg-blue-grey-1"
                    type="number"
                />

                <role-field
                    v-if="filters.includes('role')"
                    v-model="data.role"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    outlined
                    standout="bg-blue-grey-1"
                    style="width: 180px"
                />

                <region-field
                    v-if="filters.includes('region')"
                    v-model="data.region"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    emit-value
                    map-options
                    outlined
                    standout="bg-blue-grey-1"
                    style="width: 180px"
                />

                <factory-field
                    v-if="filters.includes('factory')"
                    v-model="data.factory"
                    :square="false"
                    class="responsive_field q-mr-sm q-mb-sm"
                    clearable
                    dense
                    label="Заводы"
                    outlined
                    standout="bg-blue-grey-1"
                    style="width: 180px"
                />

                <q-btn :loading="loading1" color="blue-grey" icon="search" round @click="applyFilter"/>
            </div>
        </q-card-section>
    </q-card>
</template>

<script>

import ManufactureField from "@/Components/Fields/ManufactoryField.vue";
import CategoryField from "@/Components/Fields/CategoryField.vue";
import RoleField from "@/Components/Fields/RoleField.vue";
import FactoryField from "@/Components/Fields/FactoryField.vue";
import RegionField from "@/Components/Fields/RegionField.vue";

export default {
    props: ['data', 'applyAction', 'resetAction', 'filters'],
    components: {RoleField, CategoryField, ManufactureField, FactoryField, RegionField},

    data() {
        return {
            loading1: false,
            item: {},

            statuses1: [
                {
                    id: 1,
                    title: 'На рассмотрении',
                },
                {
                    id: 2,
                    title: 'Отказано',
                },
                {
                    id: 3,
                    title: 'Одобрено',
                },
                {
                    id: 4,
                    title: 'Возвращена на доработку',
                },
            ],

            statuses2: [
                {
                    id: 1,
                    title: 'На рассмотрении'
                },
                {
                    id: 2,
                    title: 'Одобрена'
                },
                {
                    id: 3,
                    title: 'Отклонена'
                },
                {
                    id: 4,
                    title: 'Возвращена на доработку'
                },
            ],
            statuses3: [
                {
                    id: 1,
                    title: 'Открыта'
                },
                {
                    id: 2,
                    title: 'В работе'
                },
                {
                    id: 3,
                    title: 'Завершено'
                },
                {
                    id: 4,
                    title: 'В ожидании видеозаписи'
                },
                {
                    id: 5,
                    title: 'На выдаче сертификата'
                },
            ],
        }
    },

    methods: {
        applyFilter() {
            this.loading1 = true
            this.applyAction(this.item)
        },

        resetFilter() {
            this.item = {}
            this.resetAction(this.item)
        },
    },

    mounted() {
        if (this.data) {
            this.item = this.data
        }

        this.$emitter.on('contentLoaded', (value) => {
            this.loading1 = value
        })
    }
}
</script>

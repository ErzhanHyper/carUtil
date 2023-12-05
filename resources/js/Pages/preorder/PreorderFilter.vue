<template>
    <q-card flat square>
        <q-card-section class="q-pa-none">
            <div class="row q-col-gutter-md">
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="filter.vin" dense label="VIN" outlined clearable standout="bg-blue-grey-1" class="text-uppercase"/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="filter.grnz" dense label="ГРНЗ" outlined clearable standout="bg-blue-grey-1" class="text-uppercase"/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="filter.title" dense label="ФИО" outlined clearable standout="bg-blue-grey-1"/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="filter.idnum" dense label="ИИН/БИН" outlined clearable standout="bg-blue-grey-1"/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-select
                        v-model="filter.status"
                        :options="statuses"
                        dense
                        emit-value
                        label="Статус"
                        map-options
                        option-label="title"
                        option-value="id"
                        outlined
                        clearable
                        transition-hide="jump-up"
                        transition-show="jump-up"
                    />
                </div>
                <div class="col col-md-1 col-sm-2 col-xs-12">
                    <q-btn icon="search" round @click="applyFilter" color="blue-8" :loading="loading1"/>
                </div>
            </div>
        </q-card-section>
    </q-card>
</template>

<script>
export default {
    props: ['filter', 'applyAction'],

    data() {
        return {
            loading1: false,
            loading2: false,
            item: {},
            statuses: [
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
        }
    },

    methods: {
        applyFilter() {
            this.loading1 = true
            this.applyAction(this.item)
        },

        resetFilter() {
            this.item = {}
        },
    },

    mounted() {
        if (this.filter) {
            this.item = this.filter
        }

        this.$emitter.on('FilterApplyEvent',  () => {
            this.loading1 = false
        })
    }

}
</script>

<style scoped>

</style>

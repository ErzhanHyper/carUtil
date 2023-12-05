<template>
    <q-card flat square>
        <q-card-section class="q-pa-none row q-col-gutter-md">
            <div class="col col-lg-11 col-xs-12 col-sm-12">

                <div class="row q-col-gutter-md">
                    <div class="col col-md-2 col-sm-6 col-xs-12">
                        <q-select
                            v-model="item.type"
                            :options="['ВЭТС', 'ВЭССХТ']"
                            clearable
                            dense
                            label="Тип заявки"
                            outlined
                            transition-hide="jump-up"
                            transition-show="jump-up"
                        />
                    </div>
                    <div class="col col-md-2 col-sm-6 col-xs-12">
                        <q-input v-model="item.vin" class="text-uppercase" clearable dense label="VIN" outlined
                                 standout="bg-blue-grey-1"/>
                    </div>
                    <div class="col col-md-2 col-sm-6 col-xs-12">
                        <q-input v-model="item.grnz" class="text-uppercase" clearable dense label="ГРНЗ" outlined
                                 standout="bg-blue-grey-1"/>
                    </div>
                    <div class="col col-md-2 col-sm-6 col-xs-12">
                        <q-input v-model="item.title" clearable dense label="ФИО" outlined standout="bg-blue-grey-1"/>
                    </div>
                    <div class="col col-md-2 col-sm-6 col-xs-12">
                        <q-input v-model="item.idnum" clearable dense label="ИИН/БИН" outlined
                                 standout="bg-blue-grey-1"/>
                    </div>
                    <div class="col col-md-2 col-sm-6 col-xs-12">
                        <q-select
                            v-model="item.approve"
                            :options="statuses"
                            clearable
                            dense
                            emit-value
                            label="Статус"
                            map-options
                            option-label="title" option-value="id"
                            outlined
                            transition-hide="jump-up"
                            transition-show="jump-up"
                        />
                    </div>
                </div>
            </div>

            <div class="col col-lg-1 col-xs-12 col-sm-12 ">
                <q-btn :loading="loading1" color="blue-8" round icon="search" @click="applyFilter"/>
            </div>

        </q-card-section>
    </q-card>
</template>

<script>
export default {
    props: ['filter', 'applyAction', 'resetAction'],

    data() {
        return {
            loading1: false,
            loading2: false,
            item: {},

            statuses: [
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
        if (this.filter) {
            this.item = this.filter
        }

        this.$emitter.on('FilterApplyEvent', () => {
            this.loading1 = false
        })
    }
}
</script>

<style scoped>

</style>

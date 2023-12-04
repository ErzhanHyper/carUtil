<template>
    <q-card bordered class="q-mb-none q-mt-md" flat square>
        <q-card-section>
            <div class="row q-col-gutter-md">
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-select
                        v-model="item.type"
                        :options="['ВЭТС', 'ВЭССХТ']"
                        dense
                        label="Тип заявки"
                        outlined
                        transition-hide="jump-up"
                        transition-show="jump-up"
                    />
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="item.vin" dense label="VIN" outlined/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="item.grnz" dense label="ГРНЗ" outlined/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="item.title" dense label="ФИО" outlined/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="item.idnum" dense label="ИИН/БИН" outlined/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-select
                        v-model="item.approve"
                        :options="statuses"
                        dense
                        emit-value
                        label="Статус"
                        map-options
                        option-label="title"
                        option-value="id" outlined
                        transition-hide="jump-up"
                        transition-show="jump-up"
                    />
                </div>
                <!--                <div class="col col-md-2 col-sm-6 col-xs-12">-->
                <!--                    <q-input label="Дата (с)" v-model="filter.date_start" outlined dense type="date"/>-->
                <!--                </div>-->
                <!--                <div class="col col-md-2 col-sm-6 col-xs-12">-->
                <!--                    <q-input label="Дата (до)" v-model="filter.date_end" outlined dense type="date"/>-->
                <!--                </div>-->
                <div class="col col-md-2 col-sm-2 col-xs-12">
                    <q-btn :loading="loading1" color="blue-8" icon="search" round @click="applyFilter"/>
                    <q-btn :loading="loading2" class="q-ml-sm" color="orange-8" icon="close" round size="sm"
                           @click="resetFilter"/>
                </div>
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
    }
}
</script>

<style scoped>

</style>

<template>
    <q-card class="q-mb-none q-mt-md" bordered square flat>
        <q-card-section>
            <div class="row q-col-gutter-md">
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-select label="Тип заявки" v-model="item.type" outlined dense :options="['ВЭТС', 'ВЭССХТ']"/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input label="VIN" v-model="item.vin" outlined dense/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input label="ГРНЗ" v-model="item.grnz" outlined dense/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input label="ФИО" v-model="item.title" outlined dense/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input label="ИИН/БИН" v-model="item.idnum" outlined dense/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-select
                        label="Статус"
                        v-model="item.approve"
                        :options="statuses"
                        dense
                        emit-value
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
                    <q-btn icon="search" round @click="applyFilter" color="blue-8" :loading="loading1"/>
                    <q-btn icon="close" round @click="resetFilter" color="orange-8" size="sm" class="q-ml-sm"
                           :loading="loading2"/>
                </div>
            </div>
        </q-card-section>
    </q-card>
</template>

<script>
export default {
    props: ['filter', 'applyAction', 'resetAction'],

    data() {
        return{
            loading1: false,
            loading2: false,
            item: {},

            statuses:[
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

        resetFilter(){
            this.item = {}
            this.resetAction(this.item)
        },
    },

    mounted(){
        if(this.filter) {
            this.item = this.filter
        }
    }
}
</script>

<style scoped>

</style>

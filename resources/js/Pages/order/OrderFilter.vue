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
                        clearable
                    />
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="item.vin" dense label="VIN" outlined clearable/>
                </div>
                <div class="col col-md-1 col-sm-6 col-xs-12">
                    <q-input v-model="item.grnz" dense label="ГРНЗ" outlined clearable/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="item.title" dense label="ФИО" outlined clearable/>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12">
                    <q-input v-model="item.idnum" dense label="ИИН/БИН" outlined clearable/>
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
                        clearable
                    />
                </div>
                <div class="col col-md-1 col-sm-1 col-xs-12">
                    <q-btn icon="search" round @click="applyFilter" color="blue-8" :loading="loading1"/>
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

        this.$emitter.on('FilterApplyEvent',  () => {
            this.loading1 = false
        })
    }
}
</script>

<style scoped>

</style>

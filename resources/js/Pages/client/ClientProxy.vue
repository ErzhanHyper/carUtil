<template>
    <q-card class="q-mt-md">
        <q-card-section>
            <div class="row">
                <div class="col">
                    <q-select label="Доверенность" square dense outlined
                              option-label="title" option-value="id"
                              emit-value map-options class="q-mb-md" :options="proxies"
                              v-model="item.proxy"
                              @update:model-value="setProxy()"
                              :readonly="blocked"
                    />
                </div>
            </div>
            <div class="row q-col-gutter-md " v-if="showProxy">
                <div class="col">
                    <q-input dense square outlined label="ФИО, Наименование владельца"
                             v-model="item.owner_title" :readonly="blocked"/>
                </div>
                <div class="col">
                    <q-input dense square outlined label="ИИН, БИН владельца"
                             v-model="item.owner_idnum" :readonly="blocked"/>
                </div>
            </div>

            <div class="row q-col-gutter-md q-mt-xs" v-if="showProxy">
                <div class="col">
                    <q-input dense square outlined label="Номер доверенности"
                             v-model="item.proxy_num"
                             :readonly="blocked"/>
                </div>
                <div class="col">
                    <q-input dense square outlined label="Дата доверенности" type="date"
                             v-model="item.proxy_date" :readonly="blocked"/>
                </div>
            </div>
        </q-card-section>
    </q-card>
</template>

<script>
export default {
    props: ['item', 'blocked'],

    data() {
        return {
            showProxy: false,

            proxies: [
                {
                    id: 1,
                    title: 'Владелец'
                },
                {
                    id: 2,
                    title: 'По доверенности на владельца'
                },
                {
                    id: 3,
                    title: 'По доверенности на доверенное лицо'
                }
            ],
        }
    },

    methods: {
        setProxy() {
            if (this.item.proxy === 1) {
                this.showProxy = false
            }

            if (this.item.proxy === 2) {
                this.showProxy = true
            }
        }
    }
}
</script>

<style scoped>

</style>

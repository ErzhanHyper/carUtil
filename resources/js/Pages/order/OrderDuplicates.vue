<template>
    <q-dialog v-model="dialog">
        <q-card style="width: 100%;max-width: 1200px">
            <q-card-section class="flex justify-between q-pa-sm">
                <q-space/>
                <q-icon v-close-popup class="cursor-pointer" flat name="close" size="sm"/>
            </q-card-section>
            <q-card-section>

                <div v-if="loading1" class="text-center q-mb-md">
                    <q-spinner-dots size="md"/>
                </div>

                <div v-if="!loading1" class="row q-col-gutter-md">
                    <div class="col">
                        <div class="text-body1 text-weight-bold text-blue-grey-8">Проверка 1</div>
                        <div class="q-mb-md">
                            <div>Возможные дубликаты по VIN</div>
                            <div v-for="el in duplicates1">
                                <template v-if="el.length > 0">
                                    <template v-for="car in el">
                                        <div class="text-weight-bold">
                                            <q-icon class="q-mr-xs" name="open_in_new"/>
                                            <a :href="'/order/'+car.order_id" class="text-primary" target="_blank">
                                                {{ car.vin }}
                                            </a>
                                        </div>
                                    </template>
                                </template>
                                <template v-else>
                                    <div class="text-caption">Нет совпадений</div>
                                </template>
                            </div>
                        </div>

                        <div class="q-mb-md">
                            <div>Возможные дубликаты по номеру кузова</div>
                            <div v-for="el in body_duplicates1">
                                <template v-if="el.length > 0">
                                    <template v-for="car in el">
                                        <div class="text-weight-bold">
                                            <q-icon class="q-mr-xs" name="open_in_new"/>
                                            <a :href="'/order/'+car.order_id" class="text-primary" target="_blank">
                                                {{ car.body_no }}</a>
                                        </div>
                                    </template>
                                </template>
                                <template v-else>
                                    <div class="text-caption">Нет совпадений</div>
                                </template>
                            </div>
                        </div>

                        <div class="q-mb-md">
                            <div>Возможные дубликаты по номеру шасси</div>
                            <div v-for="el in chassis_duplicates1">
                                <template v-if="el.length > 0">
                                    <template v-for="car in el">
                                        <div class="text-weight-bold">
                                            <q-icon class="q-mr-xs" name="open_in_new"/>
                                            <a :href="'/order/'+car.order_id" class="text-primary" target="_blank">
                                                {{ car.chassis_no }}
                                            </a>
                                        </div>
                                    </template>
                                </template>
                                <template v-else>
                                    <div class="text-caption">Нет совпадений</div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-body1 text-weight-bold text-blue-grey-8">Проверка 2</div>
                        <div class="q-mb-md">
                            <div>Возможные дубликаты по VIN(в этой машине) -> BODY(другие машине)</div>
                            <div v-for="el in duplicates2">
                                <template v-if="el.length > 0">
                                    <template v-for="car in el">
                                        <div class="text-weight-bold">
                                            <q-icon class="q-mr-xs" name="open_in_new"/>
                                            <a :href="'/order/'+car.order_id" class="text-primary" target="_blank">
                                                {{ car.vin }}
                                            </a>
                                        </div>
                                    </template>
                                </template>
                                <template v-else>
                                    <div class="text-caption">Нет совпадений</div>
                                </template>
                            </div>
                        </div>
                        <div class="q-mb-md">
                            <div>Возможные дубликаты по BODY(в этой машине) -> VIN(другие машине)</div>
                            <div v-for="el in body_duplicates2">
                                <template v-if="el.length > 0">
                                    <template v-for="car in el">
                                        <div class="text-weight-bold">
                                            <q-icon class="q-mr-xs" name="open_in_new"/>
                                            <a :href="'/order/'+car.order_id" class="text-primary" target="_blank">
                                                {{ car.body_no }}
                                            </a>
                                        </div>
                                    </template>
                                </template>
                                <template v-else>
                                    <div class="text-caption">Нет совпадений</div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    </q-dialog>

</template>

<script>
import {getOrderDuplicates} from "../../services/order";

export default {

    props: ['order_id'],
    data() {
        return {
            dialog: false,
            loading1: false,
            duplicates1: [],
            duplicates2: [],
            body_duplicates1: [],
            body_duplicates2: [],
            chassis_duplicates1: []
        }
    },

    methods: {
        getDuplicates() {
            this.loading1 = true
            getOrderDuplicates(this.order_id).then(res => {
                this.duplicates1 = res.duplicates1
                this.duplicates2 = res.duplicates2
                this.body_duplicates1 = res.body_duplicates1
                this.body_duplicates2 = res.body_duplicates2
                this.chassis_duplicates1 = res.chassis_duplicates1
            }).finally(() => {
                this.loading1 = false
            })
        },
    },

    created() {
        this.getDuplicates()
    },

    mounted(){
        this.$emitter.on('duplicateDialogEvent', () => {
            this.dialog = true
        })
    }
}
</script>

<style scoped>

</style>

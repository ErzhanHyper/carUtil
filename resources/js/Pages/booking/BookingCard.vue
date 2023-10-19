<template>
    <q-card>
        <q-card-section class="q-pb-xs">
            <div class="text-body2">Бронирование</div>
        </q-card-section>

        <q-card-section>
            <div class="row q-gutter-md">
                <div class="col">

                    <q-select
                        square
                        v-model="item.factory_id"
                        label="Завод"
                        :options="factories"
                        :model-value="item.factory_id"
                        option-value="id"
                        option-label="title"
                        map-options
                        emit-value
                        clearable
                        options-selected-class="text-deep-orange"
                        outlined
                        dense
                        class="q-mb-md"
                        :readonly="blocked"
                    >
                        <template v-slot:option="scope">
                            <q-item v-bind="scope.itemProps">
                                <q-item-section avatar>
                                    <q-icon name="factory"/>
                                </q-item-section>
                                <q-item-section>
                                    <q-item-label>{{ scope.opt.title }}</q-item-label>
                                    <q-item-label caption>{{ scope.opt.address }}</q-item-label>
                                </q-item-section>
                            </q-item>
                        </template>
                    </q-select>

                    <q-input outlined square dense v-model="item.datetime" v-if="item.factory_id" :readonly="blocked">
                        <template v-slot:prepend>
                            <q-icon name="event" class="cursor-pointer">
                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-date v-model="item.datetime" mask="YYYY-MM-DD HH:mm" :readonly="blocked">
                                        <div class="row items-center justify-end">
                                            <q-btn v-close-popup label="Закрыть" color="primary" flat/>
                                        </div>
                                    </q-date>
                                </q-popup-proxy>
                            </q-icon>
                        </template>

                        <template v-slot:append>
                            <q-icon name="access_time" class="cursor-pointer">
                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="item.datetime" mask="YYYY-MM-DD HH:mm"
                                            :minute-options="minuteOptionsTime"
                                            :hour-options="hourOptionsTime"
                                            format24h
                                            flat
                                            bordered
                                            :readonly="blocked"
                                    >
                                        <div class="row items-center justify-end">
                                            <q-btn v-close-popup label="Закрыть" color="primary" flat/>
                                        </div>
                                    </q-time>
                                </q-popup-proxy>
                            </q-icon>
                        </template>
                    </q-input>

<!--                    <div v-if="item.datetime" class="q-mt-sm">-->
<!--                        <span class="text-caption ">Дата: <b class="text-blue-5 text-body2">{{ $moment(item.datetime).format('YYYY-MM-DD') }}</b></span>-->
<!--                        <br>-->
<!--                        <span class="text-caption ">Время:<b class="text-blue-5 text-body2"> {{ $moment(item.datetime).format('HH:mm') }}</b></span>-->
<!--                    </div>-->

                </div>
            </div>
        </q-card-section>
    </q-card>
</template>

<script>
import {getFactoryList} from "../../services/factory";
import {getBookingOrderList} from "../../services/booking";

export default {

    props: ['data', 'getBooking', 'blocked'],

    data() {
        return {
            item: {},
            factories: [],
            dates: [],

            hourOptionsTime: [9, 10, 11, 12, 14, 15, 16, 17, 18],
            minuteOptionsTime: [0],

        }
    },

    methods: {
        getFactory() {
            getFactoryList().then(res => this.factories = res)
        },

        getDateTime() {
            getBookingOrderList({factory: this.item.factory}).then(res => this.dates = res)
        },

        setFactory() {
            this.getDateTime()
        }
    },

    created() {

        this.item = {
            factory_id: null,
            datetime: null,
            datetime_string: null
        }

        if (this.data) {
            this.item = this.data
            this.item.datetime = this.item.datetime_string
        }

        this.getFactory()
    },

    mounted() {
        this.$emitter.on('BookingCardEvent', () => {
            this.getBooking(this.item)
        })
    }


}
</script>

<style scoped>

</style>

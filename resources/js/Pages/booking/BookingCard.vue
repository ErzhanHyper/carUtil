<template>
    <q-card flat bordered>
        <q-card-section class="q-pb-xs">
            <div class="text-body2">Бронирование</div>
            <q-space />
        </q-card-section>

        <q-card-section>
            <div class="row q-col-gutter-md">
                <div class="col col-md-12 col-xs-12">
                    <factory-field v-model="item.factory_id" :model-value="item.factory_id" :readonly="blocked || disabled" />
                </div>

                <div class="col col-md-12 col-xs-12">
                    <q-input outlined square dense v-model="item.datetime" v-if="item.factory_id" :readonly="blocked" hint="Дата и время">
                        <template v-slot:prepend>
                            <q-icon name="event" class="cursor-pointer">
                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-date v-model="item.datetime" mask="YYYY-MM-DD HH:mm" :readonly="blocked || disabled" v-close-popup>
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
                                            v-close-popup
                                            :readonly="blocked || disabled"
                                    >
                                        <div class="row items-center justify-end">
                                            <q-btn v-close-popup label="Закрыть" color="primary" flat/>
                                        </div>
                                    </q-time>
                                </q-popup-proxy>
                            </q-icon>
                        </template>
                    </q-input>
                </div>

                <div class="col col-md-12 col-xs-12" v-if="!blocked && item.factory_id && item.datetime">
                    <q-btn label="Забронировать" color="indigo-8" push @click="bookingOrder" :loading="loading" :disabled="disabled"/>
                    <q-btn round flat icon="close" color="negative" class="q-ml-xs" @click="closeBooking()" v-if="disabled" :loading="loading"/>
                </div>


            </div>
        </q-card-section>
    </q-card>
</template>

<script>
import {Notify} from "quasar";
import {deleteBookingOrder, getBookingOrderList} from "../../services/booking";
import {bookingOrder} from "../../services/preorder";
import FactoryField from "../../Components/Fields/FactoryField.vue";

export default {
    components: {FactoryField},
    props: ['preorder_id', 'data', 'getBooking', 'blocked'],

    data() {
        return {
            item: {},
            dates: [],

            loading: false,

            hourOptionsTime: [9, 10, 11, 12, 14, 15, 16, 17, 18],
            minuteOptionsTime: [0],

            disabled: true,

        }
    },

    methods: {

        getDateTime() {
            getBookingOrderList({factory: this.item.factory}).then(res => this.dates = res)
        },

        setFactory() {
            this.getDateTime()
        },

        bookingOrder(){
            this.loading = true
            bookingOrder(this.preorder_id, this.item).then(res => {
                this.item.id = res.booking_id
                this.disabled = true
                this.$emitter.emit('BookingCardEvent')
                Notify.create({
                    message: 'Дата и время забронирована',
                    position: 'bottom',
                    type: 'positive'
                })
            }).catch(err => {
                let mess = 'Ошибка бронирование'
                if(err.response.data) {
                    mess = JSON.parse(err.response.data.message).booking
                }
                Notify.create({
                    message: mess,
                    position: 'bottom',
                    type: 'warning'
                })

            }).finally(() => {
                this.loading = false
            })
        },

        closeBooking(){
            this.loading = true
            this.item.preorder_id = this.preorder_id
            deleteBookingOrder(this.item).then(() => {
                this.$emitter.emit('BookingCardEvent')
                this.loading = false
                this.disabled = false
                this.item = {
                    factory_id: null,
                    datetime: null,
                    datetime_string: null
                }
            })
        }
    },

    created() {
        if(this.data){
            this.item = this.data
        }else {
            this.disabled = false
            this.item = {
                factory_id: null,
                datetime: null,
                datetime_string: null
            }
        }
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

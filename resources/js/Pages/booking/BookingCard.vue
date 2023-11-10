<template>
    <q-card flat bordered>
        <q-card-section class="q-pb-xs">
            <div class="text-body2">Бронирование</div>
            <q-space />
            <q-banner class="bg-blue-1 q-mt-md" v-if="!blocked">После бронирования вы можете отнести ТС/СХТ на завод</q-banner>
        </q-card-section>

        <q-card-section>
            <div class="row q-col-gutter-md">
                <div class="col col-md-12 col-xs-12">
                    <factory-field v-model="item.factory_id" :model-value="item.factory_id" :readonly="blocked" :loading="loading" />
                </div>

                <div class="col col-md-12 col-xs-12">
                    <q-input outlined square dense v-model="item.datetime" v-if="item.factory_id" :readonly="blocked" hint="Дата и время">
                        <template v-slot:prepend>
                            <q-icon name="event" class="cursor-pointer">
                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-date v-model="item.datetime" mask="YYYY-MM-DD HH:mm" :readonly="blocked" >
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
                </div>

                <div class="col col-md-12 col-xs-12" v-if="!blocked && item.factory_id && item.datetime">
                    <q-btn label="Забронировать" color="indigo-8" push @click="bookingOrder" :loading="loading" :disabled="disabled"/>
                    <q-btn round flat icon="close" color="negative" class="q-ml-xs" @click="disabled=false" v-if="disabled"/>
                </div>


            </div>
        </q-card-section>
    </q-card>
</template>

<script>
import {Notify} from "quasar";
import {getBookingOrderList} from "../../services/booking";
import {bookingOrder} from "../../services/preorder";
import FactoryField from "../../Components/Fields/FactoryField.vue";

export default {
    components: {FactoryField},
    props: ['data', 'getBooking', 'blocked'],

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
            bookingOrder(this.item.preorder_id, this.item).then(res => {
                this.disabled = true

                Notify.create({
                    message: 'Дата и время забронирована',
                    position: 'bottom-right',
                    type: 'positive'
                })
            }).catch(err => {
                let mess = 'Ошибка бронирование'
                if(err.response.data) {
                    mess = JSON.parse(err.response.data.message).booking
                }
                Notify.create({
                    message: mess,
                    position: 'bottom-right',
                    type: 'warning'
                })

            }).finally(() => {
                this.loading = false
            })
        }
    },

    created() {

        if (this.data) {
            this.item = this.data
            this.item.datetime = this.item.datetime_string

            if(!this.data.datetime || !this.data.factory_id){
                this.disabled = false
            }
        }else{
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

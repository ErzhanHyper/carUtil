<template>

    <div class="q-gutter-sm q-mb-md q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">
            Погашение
        </div>

        <div class="flex justify-between">
            <!--            <q-btn color="purple-10" unelevated icon="tune" class="q-ml-md"/>-->
            <q-btn color="indigo-8" push size="12px" icon="add" label="Запросить одобрение" class="q-ml-md text-weight-bold" v-if="item.canSend" @click="send" :loading="loading1"/>
        </div>
    </div>
    <div v-if="show"><q-chip square :label="item.status.title" color="blue-grey-1" /> </div>

    <div class="row q-col-gutter-lg" v-if="show">

        <div class="col col-md-8">
            <q-card flat>
                <q-card-section class="q-px-none">
                    <div class="q-gutter-md">
                        <q-btn label="Скачать расписку о подтверждении условий использования сертификата" color="indigo-8" size="11px" icon-right="download" icon="description"
                               :loading="loading" @click="getApplication"/>
                    </div>
                </q-card-section>
                <q-card-section class="q-px-none" >
                        <div class="row q-col-gutter-md">
                        <div class="col col-md-6 q-col-gutter-md">
                            <manufacture-field v-model="item.factory" outlined :readonly="true" dense/>
                            <vehicle-select v-model="item.model" :model-value="item.model" outlined  dense :readonly="!item.canEdit" :params="{dealer: true}" :getParams="getVehicle"/>
                        </div>
                            <div class="col col-md-6 q-col-gutter-md">
                                <q-input label="VIN" outlined dense v-model="item.vin" :readonly="!item.canEdit"/>
                                <q-input label="Год выпуска" outlined dense v-model="item.year" :readonly="!item.canEdit"/>
                                <q-input label="Контактный телефон" outlined dense v-model="item.phone" :readonly="!item.canEdit"/>
                                <q-input label="Дата и время отправки на модерацию" outlined dense v-model="item.sended_dt" :readonly="!item.canEdit" v-if="item.sended_at"/>
                            </div>
                        </div>
                </q-card-section>
            </q-card>
        </div>

        <div class="col col-md-4">
            <sell-file :id="item.id" :blocked="!item.canEdit"/>
        </div>

    </div>

    <q-separator class="q-my-lg" />
    <sell-action />
</template>

<script>
import {getSellById, updateSell} from "../../services/sell";
import SellFile from "./SellFile.vue";
import ManufactureField from "../../Components/Fields/ManufactoryField.vue";
import VehicleSelect from "../../Components/Fields/VehicleSelect.vue";
import SellAction from "./SellAction.vue";

import {getExchangeApp, getSellApplication} from "../../services/document";
import FileDownload from "js-file-download";

export default {
    components: {VehicleSelect, ManufactureField, SellFile, SellAction},
    props: ['id'],

    data() {
        return{
            show: false,
            loading: false,
            loading1: false,
            readonly: true,
            item: {
                canEdit: false,
                canSend:false,
                factory: '',
                model: '',
                status: {},
                vehicle_id: null,
            }
        }
    },

    methods: {

        getVehicle(value) {
            this.item.vehicle_id = value.id
            this.item.model = value.title
        },

        send(){
            this.loading1 = true
            this.item.canEdit = false
            updateSell(this.item.id, this.item).then(() => {
                this.getData()
            }).finally(() => {
                this.loading1 = false
            })
        },

        getApplication() {
            this.loading = true
            getSellApplication(this.item.id, {responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'sell_app.pdf')
            }).finally(() => {
                this.loading = false
            })
        },

        getData(){
            this.$emitter.emit('contentLoaded', true);
            getSellById(this.id).then(res => {
                this.$emitter.emit('contentLoaded', false);
                if(res){
                    this.item.id = res.id
                    this.item.phone = res.phone
                    this.item.year = res.year
                    this.item.vin = res.vin
                    this.item.sended_at = res.sended_at
                    this.item.canEdit = res.canEdit
                    this.item.canSend = res.canSend
                    this.item.status = res.status
                    if(res.manufacture) {
                        this.item.factory = res.manufacture.title
                    }
                    if(res.vehicle) {
                        this.item.model = res.vehicle.brand+', '+res.vehicle.model
                    }
                    this.show = true

                }
            })
        }
    },

    created() {
        this.getData()
    }
}
</script>

<style scoped>

</style>

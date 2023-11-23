<template>

    <div class="q-gutter-sm q-mb-md q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">
            Погашение
        </div>

        <div class="flex justify-between">
            <!--            <q-btn color="purple-10" unelevated icon="tune" class="q-ml-md"/>-->
            <q-btn color="indigo-8" push size="12px" icon="add" label="Запросить одобрение" class="q-ml-md text-weight-bold" v-if="item.canSend" @click="send" :loading="loading1"/>
            <q-btn color="indigo-8" push size="12px" icon="add" label="Запросить погашение" class="q-ml-md text-weight-bold" v-if="item.canSendToSell" @click="sendToSell" :loading="loading1"/>
        </div>
    </div>
    <div v-if="show">
        <q-chip :color="setStatusColor(item.status.id)" v-if="item.status" dark square>{{ item.status.title }}</q-chip>
    </div>

    <div class="row q-col-gutter-lg" v-if="show">

        <div class="col col-md-8">
            <q-card flat>
                <q-card-section class="q-px-none">
                    <div class="q-gutter-md">
                        <q-btn label="Скачать расписку о подтверждении условий использования сертификата" color="indigo-8" size="11px" icon-right="download" icon="description"
                               :loading="loading" @click="getApplication" v-if="item.status.id !== 5"/>
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
            <sell-file :id="item.id" :blocked="blockedFile" :status="item.status"/>
        </div>

    </div>

    <q-separator class="q-my-lg" />
    <sell-action :permissions="permissions.approve" :sell_id="item.id"/>

</template>

<script>
import {getSellById, updateSell, updateToGetClose} from "../../services/sell";
import SellFile from "./SellFile.vue";
import ManufactureField from "../../Components/Fields/ManufactoryField.vue";
import VehicleSelect from "../../Components/Fields/VehicleSelect.vue";
import SellAction from "./SellAction.vue";

import {getSellApplication} from "../../services/document";
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
            blockedFile: true,
            permissions: {
              approve: {}
            },
            item: {
                canApprove: false,
                canEdit: false,
                canSend:false,
                canSendToSell: false,
                factory: '',
                model: '',
                status: {},
                vehicle_id: null,
            }
        }
    },

    methods: {

        setStatusColor(id) {
            let color = 'blue-grey-5'
            if(id === 1){
                color = 'blue-5'
            }else if(id === 2){
                color = 'green-5'
            }else if(id === 3){
                color = 'pink-5'
            }else if(id === 4){
                color = 'deep-orange-5'
            }else if(id === 5){
                color = 'teal-5'
            }
            return color
        },

        getVehicle(value) {
            this.item.vehicle_id = value.id
            this.item.model = value.title
        },

        send(){
            this.loading1 = true
            updateSell(this.item.id, this.item).then(() => {
                this.getData()
            }).finally(() => {
                this.loading1 = false
            })
        },

        sendToSell(){
            this.loading1 = true
            updateToGetClose(this.item.id, this.item).then(() => {
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
                    this.item.canSendToSell = res.canSendToSell
                    this.item.status = res.status
                    if(res.manufacture) {
                        this.item.factory = res.manufacture.title
                    }
                    if(res.vehicle) {
                        this.item.model = res.vehicle.brand+', '+res.vehicle.model
                    }

                    if(res.canEdit || res.canSendToSell){
                        this.blockedFile = false
                    }
                    this.show = true

                    this.permissions.approve = {
                        canApprove: res.canApprove,
                        canDecline: res.canDecline,
                        canSell: res.canSell
                    }
                }
            })
        }
    },

    created() {
        this.getData()
    },

    mounted(){
        this.$emitter.on('SellActionEvent', () => {
            this.getData();
        })
    }
}
</script>

<style scoped>

</style>

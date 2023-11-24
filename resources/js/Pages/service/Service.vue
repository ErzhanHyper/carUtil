<template>


    <div class="row q-col-gutter-md">

        <div class="col col-md-6 col-xs-12">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Проверка сертификата
                </q-card-section>
                <q-card-section>
                    <div class="flex no-wrap">
                        <q-input label="№ сертификата" outlined dense style="width: 400px" v-model="cert1.id"/>
                        <q-btn icon="search" square color="indigo-8"  size="12px" label="Поиск" :loading="loading1" @click="checkCert"/>
                    </div>
                </q-card-section>
            </q-card>
        </div>

        <div class="col col-md-6 col-xs-12">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Выкачивание сертификата по номеру договора
                </q-card-section>
                <q-card-section>
                    <div class="flex no-wrap">
                        <q-input label="№ договора" outlined dense style="width: 400px" v-model="order.id"/>
                        <q-btn icon="search" square color="indigo-8"  size="12px" label="Поиск" :loading="loading2" @click="downloadOrderCert"/>
                    </div>
                </q-card-section>
            </q-card>
        </div>
    </div>



    <div class="row q-col-gutter-md q-mt-xs">
        <div class="col col-md-6 col-xs-12">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Выкачивание сертификата по номеру сертификата
                </q-card-section>
                <q-card-section>
                    <div class="flex no-wrap">
                        <q-input label="№ сертификата" outlined dense style="width: 400px" v-model="cert.id"/>
                        <q-btn icon="search" square color="indigo-8"  size="12px" label="Поиск" :loading="loading3" @click="downloadCert"/>

                    </div>
                </q-card-section>
            </q-card>
        </div>

        <div class="col col-md-6 col-xs-12">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Посмотреть договор по номеру
                </q-card-section>
                <q-card-section>
                    <div class="flex no-wrap">
                        <q-input label="№ заявки" outlined dense style="width: 400px"/>
                        <q-btn icon="search" square color="indigo-8"  size="12px" label="Поиск"/>
                    </div>
                </q-card-section>
            </q-card>
        </div>
    </div>


    <div class="row q-col-gutter-md q-mt-xs">
        <div class="col col-md-6 col-xs-12">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Проверка в КАП
                </q-card-section>
                <q-card-section>
                    <q-select v-model="kap.type" :options="options" option-label="title" option-value="name" style="width: 200px" outlined dense label="Тип запроса" class="q-mb-md"></q-select>
                    <div class="flex no-wrap">
                        <q-input label="Значение" outlined dense style="width: 400px" v-model="kap.value"/>
                        <q-btn icon="search" square color="indigo-8" size="12px" label="Поиск"/>
                    </div>
                </q-card-section>
            </q-card>
        </div>
    </div>


    <q-dialog v-model="dialog1" persistent >
        <q-card style="width: 100%;max-width: 800px; " v-if="item">
            <q-card-section class="flex justify-between q-pa-sm">
                <q-space/>
                <q-icon name="close" size="sm" flat v-close-popup class="cursor-pointer"/>
            </q-card-section>

            <q-card-section style="height: 80vh" class="scroll">
            <q-card-section v-if="item.cert">
                <span class="text-subtitle1">Основная информация</span>
                <q-markup-table dense flat bordered separator="cell" >
                    <tr >
                        <th class="text-left">Номер сертификата</th>
                        <td>{{ item.cert.num }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Дата сертификата</th>
                        <td> {{ item.cert.date }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Текущий владелец</th>
                        <td>{{ item.cert.title_1 }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">ИИН текущего владельца</th>
                        <td>{{ item.cert.idnum_1 }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Когда стал владельцем</th>
                        <td>{{ item.cert.date_1 }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Категория / стоимость</th>
                        <td>{{ item.cert.sum }} &#8376;</td>
                    </tr>

                    <tr>
                        <th class="text-left">Заблокирован?</th>
                        <td>{{ item.cert.blocked === 1 ? 'Да' : 'Нет' }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Погашен?</th>
                        <td>{{ item.cert.closed === 1 ? 'Да' : 'Нет' }}</td>
                    </tr>

                </q-markup-table>

            </q-card-section>

            <q-card-section v-if="item.cert">
                <span class="text-subtitle1">Предыдущий хозяин (слот 1)</span>
                <q-markup-table dense flat bordered separator="cell">

                    <tr>
                        <th class="text-left">Дата переоформления</th>
                        <td >{{ item.cert.date_2 ? item.cert.date_2 :  '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">ФИО / название</th>
                        <td>{{ item.cert.title_2 ? item.cert.title_2 : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">ИИН / БИН</th>
                        <td>{{ item.cert.idnum_2 ? item.cert.idnum_2 : '-' }}</td>
                    </tr>

                </q-markup-table>
            </q-card-section>

            <q-card-section v-if="item.cert">
                <span class="text-subtitle1">Предыдущий хозяин (слот 2)</span>
                <q-markup-table dense flat bordered separator="cell">
                    <tr>
                        <th class="text-left">Дата переоформления</th>
                        <td >{{ item.cert.date_3 ? item.cert.date_3 :  '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">ФИО / название</th>
                        <td>{{ item.cert.title_3 ? item.cert.title_3 : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">ИИН / БИН</th>
                        <td>{{ item.cert.idnum_3 ? item.cert.idnum_3 : '-' }}</td>
                    </tr>
                </q-markup-table>
            </q-card-section>

            <q-card-section v-if="item.cert">
                <span class="text-subtitle1">Предыдущий хозяин (слот 3)</span>
                <q-markup-table dense flat bordered separator="cell">
                    <tr>
                        <th class="text-left">Дата переоформления</th>
                        <td >{{ item.cert.date_4 ? item.cert.date_4 :  '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">ФИО / название</th>
                        <td>{{ item.cert.title_4 ? item.cert.title_4 : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">ИИН / БИН</th>
                        <td>{{ item.cert.idnum_4 ? item.cert.idnum_4 : '-' }}</td>
                    </tr>
                </q-markup-table>

            </q-card-section>

            <q-card-section v-if="item.ex2">
                <span class="text-subtitle1">История переоформлений {{ item.ex2.created }}</span>
                <q-markup-table dense flat bordered separator="cell">
                    <tr>
                        <th class="text-left">Время создания заявки</th>
                        <td>{{ item.ex2.created }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Время одобрения заявки</th>
                        <td>{{ item.ex2.approved }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">На кого формляем</th>
                        <td>{{ item.ex2.idnum }}</td>
                    </tr>
                </q-markup-table>
            </q-card-section>

            <q-card-section v-if="item.ex3">
                <span class="text-subtitle1">История переоформлений {{ item.ex3.created }}</span>
                <q-markup-table dense flat bordered separator="cell">
                    <tr>
                        <th class="text-left">Время создания заявки</th>
                        <td>{{ item.ex3.created }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Время одобрения заявки</th>
                        <td>{{ item.ex3.approved }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">На кого формляем</th>
                        <td>{{ item.ex3.idnum }}</td>
                    </tr>
                </q-markup-table>
            </q-card-section>

            <q-card-section v-if="item.ex4">
                <span class="text-subtitle1">История переоформлений {{ item.ex4.created }}</span>
                <q-markup-table dense flat bordered separator="cell">
                    <tr>
                        <th class="text-left">Время создания заявки</th>
                        <td>{{ item.ex4.created }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Время одобрения заявки</th>
                        <td>{{ item.ex4.approved }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">На кого формляем</th>
                        <td>{{ item.ex4.idnum }}</td>
                    </tr>
                </q-markup-table>
            </q-card-section>


            <q-card-section v-if="item.sell">
                <h5>Погашение {{ item.sell.closed_dt }}</h5>
                <q-markup-table dense flat bordered separator="cell">
                    <tr>
                        <th class="text-left">Время создания заявки</th>
                        <td>{{ item.sell.created_dt }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">VIN</th>
                        <td>{{ item.sell.vin }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Год</th>
                        <td>{{ item.sell.year }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Сумма скидки</th>
                        <td>{{ item.sell.sum }} &#8376;</td>
                    </tr>
                    <tr>
                        <th class="text-left">Статус заявки</th>
                        <td>{{ item.sell.status.title }}</td>
                    </tr>
                </q-markup-table>
            </q-card-section>
            </q-card-section>
        </q-card>
    </q-dialog>

</template>

<script>
import {findCertificateById, generateCertificate} from "../../services/certificate";
import FileDownload from "js-file-download";
import {findCertificateByOrderId} from "../../services/order";
import {Notify} from "quasar";

export default {

    data(){
        return{
            kap: { },
            item: {},
            cert1: {
                id: null
            },
            cert: {
                id: null
            },
            order: {
                id:null
            },

            dialog1: false,
            loading1: false,
            loading2: false,
            loading3: false,
            loading4: false,
            loading5: false,

            options: [
                {
                    name: 'vin',
                    title: 'VIN'
                },
                {
                    name: 'grnz',
                    title: 'ГРНЗ'
                },
                {
                    name: 'iinbin',
                    title: 'ИИН/БИН'
                },
            ]
        }
    },

    methods: {
        checkCert(){
            this.loading1 = true
            findCertificateById(this.cert1.id).then(res => {
                if(res.cert || res.sell) {
                    this.cert1 = {
                        id: null
                    }
                    this.item = res
                    this.dialog1 = true
                }else{
                    Notify.create({
                        message: 'Данные не найдены',
                        position: 'bottom',
                        type: 'warning'
                    })
                }
            }).finally(() => {
                this.loading1 = false
            })
        },

        downloadOrderCert(){
            this.loading2 = true
            findCertificateByOrderId(this.order.id, {responseType: 'arraybuffer'}).then(value => {
                this.order = {
                    id: null
                }
                if(value.byteLength > 0) {
                    FileDownload(value, 'certificate.pdf')
                }else{
                    Notify.create({
                        message: 'Данные не найдены',
                        position: 'bottom',
                        type: 'warning'
                    })
                }
            }).finally(() => {
                this.loading2 = false
            }).catch(() => {
                Notify.create({
                    message: 'Данные не найдены',
                    position: 'bottom',
                    type: 'warning'
                })
            })
        },

        downloadCert(){
            this.loading3 = true
            generateCertificate(this.cert.id, {responseType: 'arraybuffer'}).then(value => {
                this.cert = {
                    id: null
                }
                if(value.byteLength > 0) {
                    FileDownload(value, 'certificate.pdf')
                }else{
                    Notify.create({
                        message: 'Данные не найдены',
                        position: 'bottom',
                        type: 'warning'
                    })
                }
            }).finally(() => {
                this.loading3 = false
            }).catch(() => {
                Notify.create({
                    message: 'Данные не найдены',
                    position: 'bottom',
                    type: 'warning'
                })
            })
        }
    },

    mounted() {

    }
}
</script>

<style scoped>

</style>

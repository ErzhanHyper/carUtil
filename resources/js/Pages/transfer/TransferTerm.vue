<template>
    <q-dialog v-model="dialog">

        <q-card style="width: 100%;max-width: 960px;">
            <q-card-section class="scroll q-pa-none" style="max-height: 85vh">

                <div v-if="show && canGenerate" class="q-px-xl q-py-md">
                    <div class="text-center text-subtitle1">Договор купли-продажи вышедшего из эксплуатации <br>транспортного
                        средства/самоходной <br> сельскохозяйственной техники
                    </div>
                    <div style="font-size: 12px;">
                        <br>
                        <table style="width: 100%;font-size: 12px;margin-bottom: 40px;">
                            <tr>
                                <td></td>
                                <td style="text-align: right">г. {{ data['city'] }} {{ data['date'] }} года</td>
                            </tr>
                        </table>

                        <div style="text-align: justify;">
                            <div style="text-indent: 40px">
                                Гражданин Республики Казахстан {{ data['owner_title'] }}, {{ data['owner_year'] }} года
                                рождения, именуемый в дальнейшем
                                «Продавец», действующий на основании _, удостоверения личности № {{
                                    data['owner_ud_num']
                                }}
                                выдано {{ data['owner_ud_expired'] }} г. {{ data['owner_ud_issued'] }} РК, с одной
                                стороны, и
                            </div>

                            <div style="text-indent: 40px">
                                Гражданин Республики Казахстан {{ data['receiver_title'] }}, {{ data['receiver_year'] }}
                                года рождения, именуемый в дальнейшем
                                «Покупатель», действующий на основании _, удостоверения личности №
                                {{ data['receiver_ud_num'] }}
                                выдано {{ data['receiver_ud_expired'] }} г. {{ data['receiver_ud_issued'] }} РК, с
                                другой стороны,
                            </div>

                            <div style="text-indent: 40px">
                                вместе именуемые «Стороны», а отдельно, как указано выше или «Сторона», в соответствии с
                                главами 13, 14 и §1
                                главы 14 Гражданского Кодекса Республики Казахстан (далее ГК РК), действуя согласно
                                части 1-1, 2 ст.152,
                                ст.250, части 2 ст.2 и ст.380 ГК РК, добровольно с целью создания правовых последствий
                                отчуждения и
                                приобретения вышедшего из эксплуатации транспортного средства/вышедшей из эксплуатации
                                самоходной
                                сельскохозяйственной техники (номерного агрегата), не под влиянием обмана, заблуждения,
                                угроз, насилия или
                                стечения обстоятельств заключили настоящий Договор купли-продажи
                                {{ data['recycle_type_title'] }}
                                (далее – Договор) о
                                нижеследующем:
                            </div>

                            <div style="text-indent: 40px">
                                1. Продавец продает в собственность Покупателя вышедшее из эксплуатации транспортное
                                средство/вышедшую из
                                эксплуатации самоходную сельскохозяйственную технику: марка/модель {{ data['model'] }},
                                государственный регистрационный номерной знак при снятии с регистрационного учета
                                {{ data['grnz'] }}, год
                                выпуска {{ data['year'] }}, цвет {{ data['color'] }}, двигатель {{ data['engine_no'] }},
                                кузов № {{ data['body_no'] }}, шасси
                                {{ data['chassis_no'] }} (далее – ВЭТС/ВЭССХТ), а Покупатель приобретает в собственность
                                {{ data['recycle_type_short_title'] }} и
                                обязуется оплатить стоимость {{ data['recycle_type_short_title'] }}, указанную в пункте
                                2 настоящего Договора.
                            </div>

                            <div style="text-indent: 40px">
                                2. Покупатель обязуется оплатить стоимость {{ data['recycle_type_short_title'] }} в
                                размере (сумме)
                                {{ data['amount'] }}
                                ({{ data['amount_text'] }}) тенге в момент подписания настоящего Договора, путем
                                перечисления на
                                банковский счет Продавца либо наличными
                            </div>

                            <div style="text-indent: 40px">
                                3. Право собственности на {{ data['recycle_type_short_title'] }} переходит Покупателю с
                                момента подписания
                                настоящего Договора и полной
                                оплаты стоимости ТС/СХТ.
                            </div>

                            <div style="text-indent: 40px">
                                4. {{ data['recycle_type_short_title'] }}, отчуждаемое/ая по настоящему Договору,
                                принадлежит Продавцу на праве
                                собственности на
                                основании следующих документов:
                            </div>

                            <div style="text-indent: 40px">
                                1) к примеру, <select v-model="item.type">
                                <option>договор купли-продажи</option>
                                <option>меня</option>
                                <option>дарения</option>
                            </select> № <input v-model="item.data1" style="padding: 0"/> от <input v-model="item.data2"
                                                                                                   style="padding: 0"/>
                                и т.п., вступившее в
                                законную силу решение суда <input v-model="item.data3" style="padding: 0"/> от <input
                                v-model="item.data4" style="padding: 0"/>
                                (иные документы, подтверждающие право собственности);


                            </div>

                            <div style="text-indent: 40px">
                                2) Свидетельства о регистрации транспортного средства (СРТС) при снятии с
                                регистрационного учета ТС серия
                                <input v-model="item.data5" style="padding: 0"/>, номер № <input v-model="item.data6"
                                                                                                 style="padding: 0"/>,
                                выдано <input v-model="item.data7" style="padding: 0"/> ;
                            </div>

                            <div style="text-indent: 40px">
                                На момент заключения настоящего Договора Продавец гарантирует, что
                                {{ data['recycle_type_short_title'] }} не
                                заложено, не находится под
                                арестом, иным обременением, не является предметом исков третьих лиц.
                            </div>

                            <div style="text-indent: 40px">
                                6. На момент заключения настоящего Договора Покупатель:
                            </div>

                            <div style="text-indent: 40px">
                                1) осмотрел приобретаемое {{ data['recycle_type_short_title'] }}, дефектов и
                                недостатков, о которых не был
                                предупрежден Продавцом, не
                                обнаружил;
                            </div>

                            <div style="text-indent: 40px">
                                2) качественным и техническим состоянием приобретаемого
                                {{ data['recycle_type_short_title'] }} удовлетворен;
                            </div>

                            <div style="text-indent: 40px">
                                3) соответствие комплектности для сдачи с целью последующей переработки и (или)
                                утилизации и получения
                                скидочного сертификата в порядке, определяемом Оператором расширенных обязательств
                                производителей
                                (импортеров), подтвердил;
                            </div>

                            <div style="text-indent: 40px">
                                4) претензий к внешнему виду и техническому состоянию не имеет.
                            </div>

                            <div style="text-indent: 40px">
                                7. Продавец обязуется передать {{ data['recycle_type_short_title'] }}, указанный в п. 1
                                настоящего Договора, в
                                течении 1 (одного)
                                календарного дня с даты получения денежных средств.
                            </div>

                            <div style="text-indent: 40px">
                                8. Договор составлен на казахском/русском языке в электронной форме в 2 (двух)
                                экземплярах, подписан
                                электронной цифровой подписью Сторон, вступает в силу с даты подписания Сторонами
                                настоящего Договора и
                                прекращается надлежащим исполнением Сторон принятых на себя обязательств.
                            </div>

                            <div style="text-indent: 40px">
                                9. Во всем, что не урегулировано настоящим Договором, Стороны руководствуются
                                действующим законодательством
                                Республики Казахстан.
                            </div>

                            <div style="text-indent: 40px">
                                10. В случае возникновения споров, стороны могут решить их путем переговоров, а в случае
                                невозможности их
                                урегулирования – путем обращения в суды Республики Казахстан по месту нахождения истца.
                            </div>

                            <div style="text-indent: 40px">
                                11. Стороны не имеют права отказаться в одностороннем порядке от исполнения настоящего
                                Договора.
                            </div>

                            <div style="text-indent: 40px">
                                12. Все изменения и дополнения к настоящему Договору действительны, если они совершенны
                                в письменном виде и
                                подписаны уполномоченными представителями обеих Сторон.
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="pdfLink && ready">
                    <q-separator />
                    <div class="text-subtitle1 q-px-lg q-pt-sm q-pb-sm text-center">Договор к подписанию</div>
                    <iframe :src="pdfLink" style="width: 100%; height: calc(100vh - 250px)"></iframe>
                </div>

                <div class="text-center">
                    <q-spinner-dots
                        v-if="!pdfLink && ready"
                        class="q-ma-xs q-mt-xl"
                        color="primary"
                        size="2em"
                    />
                </div>
            </q-card-section>
            <q-card-actions align="center" class="q-py-sm">
                <q-btn v-if="item.type && canGenerate" :loading="loading2" color="blue-8" icon="edit"
                       label="Сформировать договор" @click="generateDoc()"/>
                <q-btn v-if="ready" :loading="loading1" color="indigo-8" icon="gesture"
                       label="Подписать" @click="signTransfer()"/>
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<script>
import {getTransferContract} from "../../services/document";
import {signData} from "../../services/sign";
import {signTransferOrder} from "../../services/transfer";
import {Notify} from "quasar";

const pdf = import('pdfjs')

export default {

    props: ['id', 'contract', 'canGenerate'],

    data() {
        return {
            pdfLink: '',
            dialog: false,
            show: true,
            data: [],
            loading1: false,
            loading2: false,
            ready: false,
            item: {}
        }
    },

    methods: {
        showPFS() {
            getTransferContract(this.id, {params: this.item, responseType: 'arraybuffer'}).then(res => {
                const blob = new Blob([res], {type: 'application/pdf'});
                this.pdfLink = URL.createObjectURL(blob)
                this.ready = true
            }).finally(() => {
                this.loading2 = false
            })
        },

        generateDoc() {
            this.loading2 = true
            this.ready = false
            this.showPFS()
        },

        signTransfer() {
            this.show = false
            signData().then(res => {
                if (res) {
                    this.loading1 = true
                    signTransferOrder(this.id, {
                        sign: res,
                        contractData: this.item
                    }).then((res) => {
                        if (res) {
                            if (res.message !== '') {
                                Notify.create({
                                    message: res.message,
                                    position: 'bottom',
                                    type: res.success === true ? 'positive' : 'warning'
                                })
                            }
                            if (res.success === true) {
                                this.dialog = false
                                this.$emitter.emit('TransferDealEvent');
                            }
                        }
                    }).finally(() => {
                        this.loading1 = false
                        this.show = true
                    })
                }
            }).finally(() => {
                this.show = true
            })
        },
    },

    created() {
        if (this.contract) {
            this.data = this.contract
        }

        if(!this.canGenerate){
            this.ready = true
            this.showPFS()
        }

    },

    mounted() {
        this.$emitter.on('transferSignDialog', (value) => {
            this.dialog = value
        })
    }

}
</script>

<style scoped>

</style>

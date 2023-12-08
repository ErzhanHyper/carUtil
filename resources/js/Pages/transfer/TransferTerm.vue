<template>
    <q-dialog v-model="dialog">

        <q-card style="width: 100%;max-width: 960px;">
            <q-card-section class="scroll q-pa-none" style="max-height: 85vh">

                <div v-if="show && canGenerate" class="q-px-xl q-py-md">

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
                <q-btn v-if="item.type && canGenerate" :loading="loading2" :disabled="!show || loading1" color="blue-8" icon="edit"
                       label="Сформировать договор" @click="generateDoc()"/>
                <q-btn v-if="ready" :loading="loading1" :disabled="!show" color="indigo-8" icon="gesture"
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
            item: {},
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

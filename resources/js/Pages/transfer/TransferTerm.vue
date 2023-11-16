<template>
        <q-card-section class="scroll q-pa-none" style="max-height: 100vh">
            <iframe :src="pdfLink" v-if="pdfLink" style="width: 100%; height: calc(100vh - 120px)"></iframe>
            <div class="text-center">
            <q-spinner-dots
                color="primary"
                size="2em"
                class="q-ma-xs q-mt-xl"
                v-if="!pdfLink"
            />
            </div>
        </q-card-section>

</template>

<script>
import {getTransferContract} from "../../services/document";

const pdf = import('pdfjs')

export default {

    props: ['id'],

    data() {
        return {
            pdfLink: ''
        }
    },

    methods: {
        showPFS() {
            this.loading = true
            getTransferContract(this.id, {responseType: 'arraybuffer'}).then(res => {
                const blob = new Blob([res], { type: 'application/pdf' });
                this.pdfLink = URL.createObjectURL(blob)
            }).finally(() => {
                this.loading = false
            })
        },
    },

    created() {
        this.showPFS()
    }

}
</script>

<style scoped>

</style>

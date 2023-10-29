<template>
    <q-card flat>
        <q-card-section>
            <div class="text-h6">Шаблоны документов</div>
        </q-card-section>
        <q-card-section>
            <div class="q-gutter-sm">
                <q-btn :loading="loading1" square size="12px"
                       unelevated color="indigo-1"
                       class="text-indigo-10"
                       flat
                       dense
                       label="Сформировать заявление на ТС"
                       icon-right="edit_note"
                        @click="statementDoc"
                />
                <q-btn :loading="loading2" square size="12px" dense unelevated color="indigo-1" class="text-indigo-10" flat label="Сформировать акт комплектности"
                       icon-right="edit_note"></q-btn>
                <q-btn :loading="loading3" square size="12px" unelevated color="indigo-1" class="text-indigo-10" flat dense label="Сформировать акт приема-передачи"
                       icon-right="edit_note"></q-btn>
            </div>
        </q-card-section>
    </q-card>
</template>

<script>
import {getStatementDoc} from "../../services/document";
import {generateOrderPFS} from "../../services/file";
import FileDownload from "js-file-download";

export default {

    props: ['order_id'],

    data(){
        return{
            loading1: false,
            loading2: false,
            loading3: false,
        }
    },

    methods: {
        statementDoc(){
            this.loading1 = true
            getStatementDoc({order_id: this.order_id}, {responseType: 'arraybuffer'}).then(res => {
                FileDownload(res, 'statement.pdf')
            }).finally(() => {
                this.loading1 = false
            })
        }
    }
}
</script>

<style scoped>

</style>

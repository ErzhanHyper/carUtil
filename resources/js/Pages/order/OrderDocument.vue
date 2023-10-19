<template>
    <q-card flat>
        <q-card-section>
            <div class="text-h6">Шаблоны документов</div>
        </q-card-section>
        <q-card-section>
            <div class="q-gutter-sm">
                <q-btn :loading="loading1" square size="md"
                       unelevated color="indigo-1"
                       class="text-indigo-10"
                       label="Сформировать заявление на ТС"
                       icon-right="edit_note"
                        @click="statementDoc"
                />
                <q-btn :loading="loading2" square size="md" unelevated color="indigo-1" class="text-indigo-10" label="Сформировать акт комплектности"
                       icon-right="edit_note"></q-btn>
                <q-btn :loading="loading3" square size="md" unelevated color="indigo-1" class="text-indigo-10" label="Сформировать акт приема-передачи"
                       icon-right="edit_note"></q-btn>
            </div>
        </q-card-section>
    </q-card>
</template>

<script>
import {getStatementDoc} from "../../services/document";

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
            getStatementDoc({order_id: this.order_id}).then(res => {
                console.log(res)
            }).finally(() => {
                this.loading1 = false
            })
        }
    }
}
</script>

<style scoped>

</style>

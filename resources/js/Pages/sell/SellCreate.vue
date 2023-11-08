<template>

    <div class="q-gutter-sm q-mb-md q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Погашения</div>

        <div class="flex justify-between q-gutter-md">
            <q-btn label="Заблокировать сертифиакты" color="deep-orange-8" size="12px" push icon="lock" @click="blockCert" :loading="loading"/>
        </div>
    </div>

    <q-card bordered flat >
        <q-card-section style="max-width: 960px">
            <div class="row q-gutter-md q-mb-md" v-for="(item, i) in items">
                <div class="col">
                    <q-input label="Номер сертификата" outlined dense v-model="item.cert_num" type="number" counter/>
                </div>
                <div class="col">
                    <q-input v-model="item.cert_date" outlined dense type="date" hint="Дата выдачи" />
                </div>
                <div class="col">
                    <q-input label="ИИН/БИН владельца" outlined dense v-model="item.cert_idnum" counter type="number"/>
                </div>
                <div class="col">
                    <q-btn icon="close" color="negative" size="sm" dense class="q-mt-sm" v-if="i > 0" @click="removeCert(i)"/>
                </div>
            </div>
            <q-btn label="Добавить сертификат" color="indigo-8" size="11px" icon="add" @click="addCert" v-if="items.length < 4"/>
        </q-card-section>
    </q-card>
</template>

<script>
import {storeSell} from "../../services/sell";

export default {

    data() {
        return {
            loading: false,
            items: [
                {
                    cert_num: '',
                    cert_date: '',
                    cert_idnum: ''
                }
            ]
        }
    },

    methods: {
        addCert() {
            if(this.items.length <= 3){
                this.items.push({
                    cert_num : '',
                    cert_date: '',
                    cert_idnum: ''
                })
            }
        },

        removeCert(i){
            this.items.splice(i, 1);
        },

        blockCert() {
            this.loading = true
            storeSell({certs: JSON.stringify(this.items)}).then(res => {
                if(res && res.success === true){
                    this.$router.push('/sell/'+ res.data.id)
                }
            }).finally(() => {
                this.loading = false
            })
        }
    }
}
</script>

<style scoped>

</style>

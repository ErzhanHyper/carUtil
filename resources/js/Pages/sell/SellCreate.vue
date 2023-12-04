<template>

    <div class="q-gutter-sm q-mb-md q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Погашения</div>

        <div class="flex justify-between q-gutter-md">
            <q-btn :loading="loading" color="deep-orange-8" icon="lock" label="Заблокировать сертифиакты" push
                   size="12px" @click="blockCert"/>
        </div>
    </div>


    <q-card bordered flat>
        <q-card-section style="max-width: 960px">

            <div class="q-gutter-y-md" style="max-width: 350px">
                <q-option-group
                    v-model="panel"
                    :options="[
                      { label: 'Транспортное средство', value: 'ts' },
                      { label: 'Сельхозтехника', value: 'sxt' },
                    ]"
                    inline
                />

                <q-tab-panels v-model="panel" animated class="shadow-2 rounded-borders">
                    <q-tab-panel name="mails">
                        <div class="text-h6">Транспортное средство</div>
                    </q-tab-panel>

                    <q-tab-panel name="alarms">
                        <div class="text-h6">Сельхозтехника</div>
                    </q-tab-panel>

                </q-tab-panels>
            </div>

            <div v-for="(item, i) in items" class="row q-gutter-md q-mb-md">
                <div class="col">
                    <q-input v-model="item.cert_num" counter dense label="Номер сертификата" outlined type="number"/>
                </div>
                <div class="col">
                    <q-input v-model="item.cert_date" dense hint="Дата выдачи" outlined type="date"/>
                </div>
                <div class="col">
                    <q-input v-model="item.cert_idnum" counter dense label="ИИН/БИН владельца" outlined type="number"/>
                </div>
                <div class="col">
                    <q-btn v-if="i > 0" class="q-mt-sm" color="negative" dense icon="close" size="sm"
                           @click="removeCert(i)"/>
                </div>
            </div>
            <q-btn v-if="(panel === 'ts') ? items.length < 2 : items.length < 4" color="indigo-8" icon="add"
                   label="Добавить сертификат" size="11px"
                   @click="addCert"/>
        </q-card-section>
    </q-card>
</template>

<script>
import {ref} from 'vue'
import {storeSell} from "../../services/sell";

export default {
    setup() {
        return {
            panel: ref('ts')
        }
    },

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

    watch: {
        panel: {
            handler() {
                if (this.panel === 'ts' && this.items.length > 1) {
                    this.items.splice(2, 2);
                }
            }
        }
    },

    methods: {
        addCert() {
            if (this.panel === 'ts') {
                if (this.items.length <= 1) {
                    this.items.push({
                        cert_num: '',
                        cert_date: '',
                        cert_idnum: ''
                    })
                }
            } else {
                if (this.items.length <= 3) {
                    this.items.push({
                        cert_num: '',
                        cert_date: '',
                        cert_idnum: ''
                    })
                }
            }
        },

        removeCert(i) {
            this.items.splice(i, 1);
        },

        blockCert() {
            this.loading = true
            storeSell({certs: JSON.stringify(this.items)}).then(res => {
                if (res && res.success === true) {
                    this.$router.push('/sell/' + res.data.id)
                }
            }).finally(() => {
                this.loading = false
            })
        }
    },

    mounted() {

    }
}
</script>

<style scoped>

</style>

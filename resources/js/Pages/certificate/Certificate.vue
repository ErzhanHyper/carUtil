<template>

    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Сертификаты</div>
    </div>


    <q-tabs
        v-model="tab"
        dense
        class="text-grey"
        active-color="primary"
        indicator-color="primary"
        align="justify"
        narrow-indicator
        style="width: 400px"
        v-if="show"
    >
        <q-tab :name="1" label="Мои сертификаты"/>
        <q-tab :name="2" label="Передача сертификатов"/>
    </q-tabs>

    <q-tab-panels v-model="tab" animated v-if="show">
        <q-tab-panel :name="1">
            <q-markup-table flat bordered dense v-if="items.length > 0">
                <thead>
                <tr>
                    <th class="text-left">№ сертификата</th>
                    <th class="text-left">Дата выдачи</th>
                    <th class="text-left">Срок действия до</th>
                    <th class="text-left">Статус</th>
                    <th class="text-left">Сумма сертификата</th>
                    <th class="text-left"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, i) in items" :key="i">
                    <td class="text-left">{{ item.id }}</td>
                    <td class="text-left">{{ item.date }}</td>
                    <td class="text-left">{{ item.dateTill }}</td>
                    <td class="text-left">{{ item.status }}</td>
                    <td class="text-left">{{ item.sum }}</td>
                    <td class="text-left">
                        <q-btn icon="verified" unelevated dense size="sm" class="text-green-10"
                               label="Скидочный сертификат"
                               icon-right="download" @click="downloadCert(item.id)" :loading="loading"></q-btn>
                    </td>
                </tr>
                </tbody>
            </q-markup-table>

            <template v-else>Нет записей</template>
        </q-tab-panel>

        <q-tab-panel :name="2">
            Нет записей
        </q-tab-panel>

    </q-tab-panels>


    <q-circular-progress
        indeterminate
        rounded
        size="30px"
        color="primary"
        class="q-ma-md"
        v-if="!show"
    />


</template>

<script>
import {generateCertificate, getCertificateList} from "../../services/certificate";
import {secureData} from "../../services/sign";
import FileDownload from "js-file-download";
import {validUser} from "../../services/user";

export default {

    data() {
        return {
            tab: 1,
            items: [],
            loading: false,
            show: false
        }
    },

    methods: {
        getData() {
            getCertificateList().then(res => {
                this.items = res
                this.show = true
            })
        },

        downloadCert(id) {
            // secureData().then(res => {
            //     if(res){
            //         this.loading = true
            //         validUser().then(() => {
                        generateCertificate(id, {responseType: 'arraybuffer'}).then(res => {
                            FileDownload(res, 'certificate.pdf')
                        }).finally(() => {
                            this.loading = false
                        })
            //         }).catch(() => {
            //             this.loading = false
            //         })
            //     }
            // })
        }
    },

    created() {
        this.getData()
    }

}
</script>

<style scoped>

</style>

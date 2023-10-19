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
    >
        <q-tab :name="1" label="Мои сертификаты"/>
        <q-tab :name="2" label="Передача сертификатов"/>
    </q-tabs>

    <q-tab-panels v-model="tab" animated>
        <q-tab-panel :name="1">
            <q-markup-table flat bordered dense v-if="items.length > 0">
                <thead>
                <tr>
                    <th class="text-left">№ сертификата</th>
                    <th class="text-left">Дата выдачи</th>
                    <th class="text-left">ФИО владельца</th>
                    <th class="text-left">ИИН/БИН владельца</th>
                    <th class="text-left"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, i) in items" :key="i">
                    <td class="text-left"> {{ item.id }}</td>
                    <td class="text-left"> {{ item.date }}</td>
                    <td class="text-left"></td>
                </tr>
                </tbody>
            </q-markup-table>

            <template v-else> Нет записей</template>
        </q-tab-panel>

        <q-tab-panel :name="2">
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
        </q-tab-panel>

    </q-tab-panels>


</template>

<script>
import {getCertificateList} from "../../services/certificate";

export default {

    data() {
        return {
            tab: 1,
            items: []
        }
    },

    methods: {
        getData() {
            getCertificateList().then(res => {
                this.items = res
            })
        }
    },

    created() {
        this.getData()
    }

}
</script>

<style scoped>

</style>

<template>

    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Погашения</div>
    </div>

    <q-markup-table flat bordered dense>
        <thead>
        <tr>
            <th class="text-left">#</th>
            <th class="text-left">№ сертификата</th>
            <th class="text-left">Дата отправки</th>
            <th class="text-left">Дата одобрения</th>
            <th class="text-left">Дата погашения</th>
            <th class="text-left">Сумма</th>
            <th class="text-left">Статус</th>
        </tr>

        <tr v-for="item in items" v-if="items.length > 0">
        </tr>

        <tr v-else><td>Нет записей</td></tr>

        </thead>
    </q-markup-table>
</template>

<script>
export default {
    data() {
        return {
            items: []
        }
    }
}
</script>

<style scoped>

</style>

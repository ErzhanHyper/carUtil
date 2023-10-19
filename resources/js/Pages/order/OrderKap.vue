<template>
    <q-card class="kap_detail_block">
        <q-card-section class="flex q-py-sm">
            <div class="text-body1">Данные с КАП</div>
            <q-space/>
            <q-icon name="close" size="sm" flat v-close-popup class="cursor-pointer"/>
        </q-card-section>

        <q-separator/>

        <q-card-section style="max-height: 50vh" class="scroll">

            <div class="text-center">
                <q-spinner
                    color="primary"
                    size="3em"
                    v-if="loading"
                />
            </div>

            <div v-if="!loading">
                <q-markup-table dense flat bordered separator="cell">
                    <tbody>
                    <tr>
                        <td>ИИН / БИН</td>
                        <td>988321212320</td>
                    </tr>
                    <tr>
                        <td>VIN</td>
                        <td>654fsd5f4sdf</td>
                    </tr>
                    <tr>
                        <td>ГРНЗ</td>
                        <td>545FDF01</td>
                    </tr>
                    <tr>
                        <td>Год выпуска</td>
                        <td>2022</td>
                    </tr>
                    <tr>
                        <td>Номер кузова</td>
                        <td>222244</td>
                    </tr>
                    <tr>
                        <td>СЕРИЯ И НОМЕР СРТС</td>
                        <td>222244</td>
                    </tr>
                    <tr>
                        <td>СТАТУС КАРТОЧКИ</td>
                        <td>222244</td>
                    </tr>
                    <tr>
                        <td>ПРИЧИНА СНЯТИЯ С УЧЕТА ТС</td>
                        <td>утилизация (выбраковка,списание)</td>
                    </tr>
                    <tr>
                        <td>КАТЕГОРИЯ</td>
                        <td>B</td>
                    </tr>
                    </tbody>
                </q-markup-table>
            </div>


        </q-card-section>
    </q-card>
</template>

<script>
export default {
    name: "OrderKap",

    data() {
        return {
            loading: true,
            items: [
                {
                    date: 168902343,
                    vin: '23423GSGDSG34523345',
                    grnz: '158KUP01'
                },
            ]
        }
    },

    created() {
        setTimeout(() => {
            this.loading = false
        }, 1000)
    }
}
</script>

<style scoped>

</style>

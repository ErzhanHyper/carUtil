<template>


    <div class="row q-col-gutter-md">

        <div class="col col-md-6 col-xs-12">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Проверка сертификата
                </q-card-section>
                <q-card-section>
                    <div class="flex no-wrap">
                        <q-input label="№ сертификата" outlined dense style="width: 400px"/>
                        <q-btn icon="search" square color="indigo-8"  size="12px" label="Поиск"/>
                    </div>
                </q-card-section>
            </q-card>
        </div>

        <div class="col col-md-6 col-xs-12">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Выкачивание сертификата
                </q-card-section>
                <q-card-section>
                    <div class="flex no-wrap">
                        <q-input label="№ сертификата" outlined dense style="width: 400px"/>
                        <q-btn icon="search" square color="indigo-8"  size="12px" label="Поиск"/>
                    </div>
                </q-card-section>
            </q-card>
        </div>
    </div>



    <div class="row q-col-gutter-md q-mt-xs">
        <div class="col col-md-6 col-xs-12">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Выкачивание сертификата
                </q-card-section>
                <q-card-section>
                    <div class="flex no-wrap">
                        <q-input label="№ договора" outlined dense style="width: 400px"/>
                        <q-btn icon="search" square color="indigo-8"  size="12px" label="Поиск"/>
                    </div>
                </q-card-section>
            </q-card>
        </div>

        <div class="col col-md-6 col-xs-12">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Посмотреть договор по номеру
                </q-card-section>
                <q-card-section>
                    <div class="flex no-wrap">
                        <q-input label="№ заявки" outlined dense style="width: 400px"/>
                        <q-btn icon="search" square color="indigo-8"  size="12px" label="Поиск"/>
                    </div>
                </q-card-section>
            </q-card>
        </div>
    </div>


    <div class="row q-col-gutter-md q-mt-xs">
        <div class="col col-md-6 col-xs-12">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Проверка в КАП
                </q-card-section>
                <q-card-section>
                    <q-select v-model="kap.type" :options="options" option-label="title" option-value="name" style="width: 200px" outlined dense label="Тип запроса" class="q-mb-md"></q-select>
                    <div class="flex no-wrap">
                        <q-input label="Значение" outlined dense style="width: 400px" v-model="kap.value"/>
                        <q-btn icon="search" square color="indigo-8" size="12px" label="Поиск"/>
                    </div>
                </q-card-section>
            </q-card>
        </div>
    </div>

</template>

<script>
export default {

    data(){
        return{
            kap: { },

            options: [
                {
                    name: 'vin',
                    title: 'VIN'
                },
                {
                    name: 'grnz',
                    title: 'ГРНЗ'
                },
                {
                    name: 'iinbin',
                    title: 'ИИН/БИН'
                },
            ]
        }
    }
}
</script>

<style scoped>

</style>

<template>

    <div class="row q-col-gutter-md">

        <div class="col col-md-6">
            <q-card flat bordered >
                <q-card-section class="text-body1">
                    Отчеты по сертификатам
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col q-mr-lg">
                            <q-input type="date" hint="начало"/>
                        </div>
                        <div class="col">
                            <q-input type="date" hint="конец"/>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>

        <div class="col col-md-6">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Отчеты по погашениям
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col q-mr-lg">
                            <q-input type="date" hint="начало"/>
                        </div>
                        <div class="col">
                            <q-input type="date" hint="конец"/>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>

    </div>

    <div class="row q-col-gutter-md q-mt-md">

        <div class="col col-md-6">
            <q-card flat bordered >
                <q-card-section class="text-body1">
                    Отчеты по переоформлениям
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col q-mr-lg">
                            <q-input type="date" hint="начало"/>
                        </div>
                        <div class="col">
                            <q-input type="date" hint="конец"/>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>

        <div class="col col-md-6">
            <q-card flat bordered>
                <q-card-section class="text-body1">
                    Отчеты по действиям
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col q-mr-lg">
                            <q-input type="date" hint="начало"/>
                        </div>
                        <div class="col">
                            <q-input type="date" hint="конец"/>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>

    </div>
</template>

<script>
export default {
    name: "Report"
}
</script>

<style scoped>

</style>

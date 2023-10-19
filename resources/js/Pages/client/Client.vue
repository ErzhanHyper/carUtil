<template>

    <q-card flat bordered>
        <q-card-section>
            <div class="text-h6">Фильтр</div>
        </q-card-section>

        <q-card-section>
            <div class="row">
                <div class="col q-mr-lg">
                    <q-input label="ФИО"/>
                </div>
                <div class="col q-mr-lg">
                    <q-input label="ИИН"/>
                </div>
                <div class="col">
                    <q-input label="Телефон"/>
                </div>
                <div class="col">
                    <q-btn icon="search" flat round color="primary" size="lg"/>
                </div>
            </div>
        </q-card-section>
    </q-card>

    <q-markup-table dense>
        <thead>
        <tr>
            <th class="text-left">ФИО</th>
            <th class="text-left">ИИН</th>
            <th class="text-left">Телефон</th>
            <th class="text-left">Регион</th>
            <th class="text-left">Действия</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(item, i) in items" :key="i">
            <td class="text-left">{{ item.title }}</td>
            <td class="text-left">{{ item.idnum }}</td>
            <td class="text-left">{{ item.phone ? item.phone : '-' }}</td>
            <td class="text-left">{{ item.region.title }}</td>
            <td>
                <Link :href="'/client/'+item.id">
                    <q-btn icon="open_in_new" unelevated round dense size="md" class="text-primary"></q-btn>
                </Link>
            </td>
        </tr>
        </tbody>
    </q-markup-table>

    <div class="q-pa-lg flex flex-center">
        <q-pagination
            v-model="page"
            :max="5"
            direction-links
        />
    </div>

</template>

<script>

import {Link} from '@inertiajs/vue3'

export default {
    components: {
        Link
    },

    props: ['data'],

    data() {
        return {
            items: this.data.data,
            page: 1
        }
    },

    created() {
        console.log(this.data)
    }
}
</script>

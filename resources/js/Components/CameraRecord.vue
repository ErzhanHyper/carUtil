<template>

        <q-card class="bg-primary text-white">
            <q-bar>
                <q-space />

                <q-btn dense flat icon="minimize" @click="maximizedToggle = false" :disable="!maximizedToggle">
                    <q-tooltip v-if="maximizedToggle" class="bg-white text-primary">Minimize</q-tooltip>
                </q-btn>
                <q-btn dense flat icon="crop_square" @click="maximizedToggle = true" :disable="maximizedToggle">
                    <q-tooltip v-if="!maximizedToggle" class="bg-white text-primary">Maximize</q-tooltip>
                </q-btn>
                <q-btn dense flat icon="close" v-close-popup>
                    <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                </q-btn>
            </q-bar>

            <q-card-section>
                <div class="text-h6">Камера</div>
            </q-card-section>

            <q-card-section class="q-pt-none">
                Начать запись
            </q-card-section>
        </q-card>

</template>

<script>
import { ref } from 'vue'

export default {
    setup () {
        return {
            dialog: ref(false),
            maximizedToggle: ref(true)
        }
    }
}
</script>

<style scoped>

</style>

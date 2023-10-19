<template>
    <q-stepper
        v-model="step"
        ref="stepper"
        color="primary"
        header-class="text-bold"
        flat
        bordered
    >

        <q-step
            :name="1"
            title="Формирование заявки"
            caption="Optional"
            icon="create_new_folder"
            :done="step > 2"
        >
        </q-step>

        <q-step
            :name="2"
            title="На рассмотрении у модератора"
            icon="settings"
            :done="step > 3"
        >
            For each
        </q-step>

    </q-stepper>

</template>

<script>
import {ref} from 'vue'

export default {
    setup() {
        return {
            step: ref(1)
        }
    }
}
</script>

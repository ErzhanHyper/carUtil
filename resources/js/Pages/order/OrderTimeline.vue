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
            title="На рассмотрении у модератора"
            caption="Optional"
            icon="create_new_folder"
            :done="step > 2"
        >
            3 дня согласно правилам
            <br>
            Взята в работу: 2023-09-02
        </q-step>

        <q-step
            :name="2"
            title="На рассмотрении у оператора"
            icon="settings"
            :done="step > 3"
        >
            For each
        </q-step>

        <q-step
            :name="3"
            title="На одобрении у модератора"
            icon="add_comment"
            disable
        >
            Try out different ad text to see what brings
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

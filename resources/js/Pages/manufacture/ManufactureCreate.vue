<template>
    <q-card>
        <q-card-section>
            <manufacture-form :data="item"/>
            <div class="q-gutter-md q-mt-md">
                <q-btn :loading="loading" label="Добавить" icon="edit" color="primary" @click="createData"/>
            </div>
        </q-card-section>
    </q-card>
</template>

<script>
import {storeManufacture} from "../../services/manufacture";
import ManufactureForm from "./ManufactureForm.vue";

export default {
    components: {ManufactureForm},

    data() {
        return{
            loading: false,
            item : {}
        }
    },

    methods: {
        createData() {
            this.loading = true
            storeManufacture(this.item).then((res) => {
                if(res && res.success === true) {
                    this.$router.push('/manufacture')
                }
            }).finally(() => {
                this.loading = false
            })
        }
    },
}
</script>

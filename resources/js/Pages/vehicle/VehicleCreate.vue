<template>
    <q-card>
        <q-card-section>
            <vehicle-form :data="item"/>
            <div class="q-gutter-md q-mt-md">
                <q-btn :loading="loading" label="Добавить" icon="edit" color="primary" @click="createData"/>
            </div>
        </q-card-section>
    </q-card>
</template>

<script>
import {storeVehicle} from "../../services/vehicle";
import VehicleForm from "./VehicleForm.vue";

export default {
    components: {VehicleForm},

    data() {
        return{
            loading: false,
            item : {}
        }
    },

    methods: {
        createData() {
            this.loading = true
            storeVehicle(this.item).then((res) => {
                if(res && res.success === true) {
                    this.$router.push('/vehicle')
                }
            }).finally(() => {
                this.loading = false
            })
        }
    },
}
</script>

<template>
    <q-card>
        <q-card-section>
            <factory-form :data="item"/>
            <div class="q-gutter-md q-mt-md">
                <q-btn :loading="loading" label="Добавить" icon="edit" color="primary" @click="createData"/>
            </div>
        </q-card-section>
    </q-card>
</template>

<script>
import FactoryForm from "./FactoryForm.vue"
import {storeFactory} from "../../services/factory";

export default {
    components: {FactoryForm},

    data() {
        return{
            loading: false,
            item : {}
        }
    },

    methods: {
        createData() {
            this.loading = true
            storeFactory(this.item).then((res) => {
                if(res && res.success === true) {
                    this.$router.push('/factory')
                }
            }).finally(() => {
                this.loading = false
            })
        }
    },
}
</script>

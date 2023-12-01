<template>
    <q-card>
        <q-card-section>
            <manufacture-form :data="item" />
            <div class="q-gutter-md q-mt-md">
                <q-btn :loading="loading" label="Сохранить" icon="edit" color="blue-8" @click="updateData"/>
<!--                <q-btn :loading="loading" label="Удалить" icon="delete" color="pink-5" @click="showDeleteDialog = true"/>-->
            </div>
        </q-card-section>
    </q-card>

    <q-dialog v-model="showDeleteDialog" size="xs">
        <q-card style="width: 600px">
            <q-card-section class="row items-center q-pb-none">
                <div class="text-body1">Вы действительно хотите удалить завод?</div>
                <q-space/>
                <q-btn icon="close" flat round dense v-close-popup/>

                <div class="text-body2 text-red">Учтите что завод может быть привязан к заявке</div>

            </q-card-section>
            <q-card-actions class="q-mt-md q-mx-sm q-mb-sm">
                <q-btn label="Да" @click="deleteData" color="pink-5" :loading="loading"/>
                <q-space/>
                <q-btn label="Нет" v-close-popup color="primary"/>
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<script>
import {Notify} from "quasar";
import {deleteManufacture, getManufactureById, updateManufacture} from "../../services/manufacture";
import ManufactureForm from "./ManufactureForm.vue";
export default {
    props: ['id'],
    components: {ManufactureForm},

    data() {
        return{
            show: false,
            loading: false,
            showDeleteDialog: false,

            item: {}
        }
    },

    methods: {
        getData(){
            this.$emitter.emit('contentLoaded', true);
            getManufactureById(this.id).then(res => {
                this.$emitter.emit('contentLoaded', false);
                this.show = true
                if(res && res.id) {
                    this.item = res
                }
            })
        },

        updateData(){
            this.loading = true
            updateManufacture(this.item.id, this.item).then(() => {
                Notify.create({
                    message: 'Изменено',
                    position: 'bottom',
                    type: 'info'
                })
            }).finally(() =>{
                this.loading = false
            })
        },

        deleteData() {
            this.loading = true
            deleteManufacture(this.item.id).then(() => {
                this.$router.push('/manufacture')
                Notify.create({
                    message: 'Удалено',
                    position: 'bottom',
                    type: 'negative'
                })
            }).finally(() =>{
                this.loading = false
            })
        }
    },

    created(){
        this.getData()
    }
}
</script>

<style scoped>

</style>

<template>

    <q-btn class="q-ml-md text-weight-bold"
           color="indigo-8"
           icon="add"
           label="Создать заявку"
           push
           @click="dialog = true"
    />

    <q-dialog v-model="dialog">
        <q-card style="width: 600px; max-width: 500px;">
            <q-card-section class="row items-center">
                Создать заявку
                <q-space/>
                <q-btn v-close-popup dense flat icon="close" round/>
            </q-card-section>

            <q-card-section>
                <q-select
                    v-model="item.recycle_type"
                    :model-value="item.recycle_type"
                    :options="recycle_types"
                    emit-value
                    label="Тип заявки"
                    map-options
                    option-label="title"
                    option-value="id"
                    options-selected-class="text-deep-orange"
                    square
                >
                    <template v-slot:option="scope">
                        <q-item v-bind="scope.itemProps">
                            <q-item-section avatar>
                                <q-icon :name="scope.opt.icon"/>
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ scope.opt.title }}</q-item-label>
                                <q-item-label caption>{{ scope.opt.description }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </template>
                </q-select>
            </q-card-section>

            <q-card-section>
                <q-btn :loading="loading" color="indigo-8" icon="add" label="Выбрать" unelevated @click="create()"/>
            </q-card-section>

        </q-card>
    </q-dialog>
</template>

<script>
import {Notify} from "quasar";
import {storeOrder} from "../../services/preorder";

export default {

    data() {
        return{
            dialog: false,
            loading: false,
            item: {
                recycle_type: null
            },

            recycle_types: [
                {
                    id: 1,
                    icon: 'recycling',
                    title: 'ВЭТС',
                    description: 'Вышедшее из эксплуатации транспортное средство',
                },
                {
                    id: 2,
                    icon: 'recycling',
                    title: 'ВЭССХТ',
                    description: 'Вышедшей из эксплуатации сельхозтехники',
                }
            ]
        }
    },

    methods: {
        create() {
            if (!this.item.recycle_type) {
                Notify.create({
                    message: 'Выберите тип заявки',
                    position: 'bottom',
                    type: 'warning'
                })
            }

            if (this.item.recycle_type) {
                this.loading = true
                storeOrder({
                    recycle_type: this.item.recycle_type
                }).then(res => {
                    if (res.success === true) {
                        this.dialog = false
                        this.$router.push('/preorder/' + res.data.id)
                    }
                }).finally(() => {
                    this.loading = false
                })
            }
        },
    }
}
</script>

<style scoped>

</style>

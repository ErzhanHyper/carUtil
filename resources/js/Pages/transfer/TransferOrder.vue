<template>
    <div class="q-gutter-sm q-mb-sm q-mt-xs flex justify-between">
        <div class="text-h6 text-primary">Продажа ТС</div>
        <div class="flex justify-between" v-if="user && user.role === 'liner'">
            <q-btn color="blue-6" unelevated icon="add" label="Создать заявку" class="q-ml-md text-weight-bold"
                   @click="orderDialog = true"/>
        </div>
    </div>

    <q-tabs
        v-model="tab"
        dense
        class="text-grey"
        active-color="primary"
        indicator-color="primary"
        align="justify"
        style="width: 300px"
    >
        <q-tab :name="1" label="Все сделки"/>
        <q-tab :name="2" label="Мои сделки"/>
<!--        <q-tab :name="3" label="Завершенные сделки"/>-->
    </q-tabs>

    <q-tab-panels v-model="tab" animated>
        <q-tab-panel :name="1">
            <template v-if="items.length > 0">
                <q-markup-table flat bordered dense>
                    <thead>
                    <tr>
                        <th class="text-left">ID</th>
                        <th class="text-left">ФИО</th>
                        <th class="text-left">VIN</th>
                        <th class="text-left">Категория</th>
                        <th class="text-left">Дата публикации</th>
                        <th class="text-left"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, i) in items" :key="i">
                        <td>{{ item.id }}</td>
                        <td>{{ item.order.client.title }}</td>
                        <td>{{ item.order.car.vin }}</td>
                        <td>{{ item.order.car.category.title_ru }}</td>
                        <td>{{ item.date }}</td>
                        <td>
                            <q-btn icon="open_in_new" dense flat :to="'/transfer/order/'+item.id" color="primary"
                                   label="Открыть"/>

                        </td>
                    </tr>
                    </tbody>
                </q-markup-table>
            </template>
            <template v-else>Пусто</template>
        </q-tab-panel>

        <q-tab-panel :name="2">
            <transfer-order-current />
        </q-tab-panel>

<!--        <q-tab-panel :name="3">-->
<!--            Lorem ipsum dolor sit amet consectetur adipisicing elit.-->
<!--        </q-tab-panel>-->
    </q-tab-panels>

    <q-dialog v-model="transferTermsDialog">
        <q-card>
            <q-card-section>
                <div class="text-h6">Общая информация</div>
            </q-card-section>

            <q-separator/>

            <q-card-section style="max-height: 70vh" class="scroll">
                Продажа транспортного средства
            </q-card-section>

            <q-separator/>

            <q-card-actions align="right">
                <q-btn flat label="Закрыть" color="primary" v-close-popup/>
            </q-card-actions>
        </q-card>
    </q-dialog>


</template>

<script>
import {getTransferList} from "../../services/transfer";
import TransferOrderCurrent from "./TransferOrderCurrent.vue";
import {mapGetters} from "vuex";

export default {
    components: {TransferOrderCurrent},
    data() {
        return {
            tab: 1,
            transferTermsDialog: true,
            items: [],
        }
    },

    computed: {
        ...mapGetters({
            authenticated: 'auth/authenticated',
            user: 'auth/user'
        })
    },

    methods: {
        getData() {
            getTransferList().then((res) => {
                this.items = res
            })
        },
    },

    created() {
        this.getData()

        if (localStorage.getItem('transfer_terms')) {
            this.transferTermsDialog = false
        } else {
            localStorage.setItem('transfer_terms', JSON.stringify(true))
        }
    }

}
</script>

<style scoped>

</style>

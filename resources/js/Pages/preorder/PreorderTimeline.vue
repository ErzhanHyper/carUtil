<template>
    <q-stepper
        ref="stepper"
        v-model="step"
        bordered
        color="primary"
        flat
        done-color="green-4"
    >
        <q-step
            :done="step > 1"
            :name="1"
            caption="на скидочный сертификат"
            icon="create_new_folder"
            title="Формирование заявки"
        >
            <preorder-send-action
                :car="car"
                :client="client"
                :preorder_id="preorder_id"
                :show="permissions.sendToApprove && required"
            />
        </q-step>

        <q-step
            :done="step > 2"
            :name="2"
            :caption="preorder_status.title"
            icon="settings"
            title="На рассмотрении у модератора"
        >
            <preorder-approve-action
                :preorder_id="preorder_id"
                :show="permissions.approveOrder"
            />
            <preorder-send-action
                :car="car"
                :client="client"
                :preorder_id="preorder_id"
                :show="permissions.sendToApprove && required"
            />
        </q-step>

        <q-step
            :done="step > 3"
            :name="3"
            icon="settings"
            title="На бронировании"
        >
        </q-step>

        <q-step
            :done="step > 4"
            :name="4"
            icon="settings"
            title="На рассмотрении у менеджера завода"
        >

        </q-step>

        <q-step
            :done="step > 5"
            :caption="order_status ? order_status.title : null"
            :name="5"
            icon="verified"
            title="На выдаче сертификата"
        >
        </q-step>
    </q-stepper>

</template>

<script>
import {ref} from 'vue'
import PreorderApproveAction from "./actions/PreorderApproveAction.vue";
import PreorderSendAction from "./actions/PreorderSendAction.vue";

export default {
    props: ['preorder_status', 'order_status', 'preorder_id', 'permissions', 'car', 'client', 'required'],

    components: {
        PreorderApproveAction,
        PreorderSendAction,
    },

    setup() {
        return {
            step: ref(1)
        }
    },

    methods: {
        setStatusColor(id) {
            let color = 'blue-grey-5'
            if (id === 1) {
                color = 'blue-5'
            } else if (id === 2) {
                color = 'green-5'
            } else if (id === 3) {
                color = 'pink-5'
            } else if (id === 4) {
                color = 'orange-5'
            }

            return color;
        },

        setOrderStatusColor(id) {
            let color = 'blue-grey-5'
            if (id === 1) {
                color = 'blue-5'
            } else if (id === 3) {
                color = 'green-5'
            } else if (id === 2) {
                color = 'pink-5'
            } else if (id === 4) {
                color = 'orange-5'
            }

            return color;
        },
    },

    created() {
        if (this.preorder_status.id === 0 &&  !this.order_status) {
            this.step = 1
        } else if (this.preorder_status.id > 0 &&  !this.order_status) {
            this.step = 2
        }

        if(this.order_status) {
            console.log(this.order_status.id)
            if (this.preorder_status.id === 2 && this.order_status.id === 0) {
                this.step = 3
            } else if (this.preorder_status.id === 2 && (this.order_status.id === 1 || this.order_status.id === 2 || this.order_status.id === 4)) {
                this.step = 4
            } else if (this.preorder_status.id === 2 && this.order_status.id === 5) {
                this.step = 5
            } else if (this.preorder_status.id === 2 && this.order_status.id === 3) {
                this.step = 6
            }
        }
    }
}
</script>

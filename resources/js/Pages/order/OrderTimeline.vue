<template>
    <q-stepper
        ref="stepper"
        v-model="step"
        color="primary"
        done-color="green-4"
        flat
        bordered
    >

        <q-step
            :done="step > 1"
            :name="1"
            icon="create_new_folder"
            title="На рассмотрении у менеджера завода"
        >
            <order-send-action
                :order_id="order_id"
                :permissions="{
                showSendToApproveAction: permit.can_send_to_approve,
                showSendToIssueCertAction:  permit.can_send_to_issue_cert
            }"
                class="q-mt-sm"
            />
        </q-step>

        <q-step
            :caption="approve ? approve.title : ''"
            :done="step > 2"
            :name="2"
            icon="settings"
            title="На одобрении у модератора"
        >
            <order-approve-action
                :order_id="order_id"
                :show="permit.can_approve"
            />
        </q-step>

        <q-step
            :done="step > 3"
            :name="3"
            icon="videocam"
            :caption="step >3 ? 'Отправлена' : ''"
            title="В ожидании получения видеозаписи"
        >
            <div v-if="user && user.role === 'moderator'">
                Получение видеозаписи через мобильное приложение от менеджера завода
            </div>
            <order-video-action
                :order_id="order_id"
                :permissions="{
                        showVideoSendAction: permit.can_upload_video,
                        showVideoRevision: permit.can_return_back
                    }"/>

            <order-send-action
                :order_id="order_id"
                :permissions="{
                showSendToApproveAction: permit.can_send_to_approve,
                showSendToIssueCertAction:  permit.can_send_to_issue_cert
            }"
                class="q-mt-sm"/>
        </q-step>

        <q-step
            :done="step > 4"
            :name="4"
            icon="verified"
            title="На выдаче сертификата"
            :caption="approve.id === 3 && status.id === 3 ? status.title : ''"
        >
            <order-cert-action
                v-if="permit.can_issue_cert"
                :order_id="order_id"
                :permissions="{showVideoRevision: permit.can_return_back, showIssueCert: permit.can_issue_cert}"
                class="q-ml-xs q-mr-md"/>
        </q-step>

    </q-stepper>

</template>

<script>
import {ref} from 'vue'
import OrderSendAction from "./actions/OrderSendAction.vue";
import OrderApproveAction from "./actions/OrderApproveAction.vue";
import OrderVideoAction from "./actions/OrderVideoAction.vue";
import OrderCertAction from "./actions/OrderCertAction.vue";
import {mapGetters} from "vuex";

export default {
    props: ['status', 'approve', 'permit', 'order_id'],

    components: {
        OrderSendAction,
        OrderApproveAction,
        OrderVideoAction,
        OrderCertAction,
    },

    setup() {
        return {
            step: ref(1)
        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },

    created() {
        if (this.status.id === 0 && (this.approve.id === 0 || this.approve.id === 1 || this.approve.id === 4)) {
            this.step = 1
        } else if (this.status.id === 1 || this.status.id === 2 && this.approve.id !== 4) {
            this.step = 2
        } else if (this.status.id === 4) {
            this.step = 3
        } else if (this.status.id === 5) {
            this.step = 4
        } else if (this.status.id === 3) {
            this.step = 5
        }
    }
}
</script>

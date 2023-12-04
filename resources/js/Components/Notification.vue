<template>
    <q-btn class="q-mr-md" size="sm" round color="blue-grey-8">
        <q-icon color="white" name="notifications"/>
        <q-badge color="deep-orange" floating rounded style="margin-top: 5px" v-if="count > 0">{{ count }}</q-badge>

        <q-menu v-model="show" style="margin-top:4px!important; width: 300px">
            <div class="no-wrap q-px-sm">
                <div class="flex justify-between items-center q-pa-sm">
                    <q-icon color="blue-grey-5" name="notifications"/>
                    <div>Уведомления</div>
                    <q-btn :loading="loading" dense flat size="sm" v-if="count > 0" >
                        <q-icon color="pink-4" name="delete_sweep" @click="clearAll()">
                            <q-tooltip>
                                Очистить все
                            </q-tooltip>
                        </q-icon>
                    </q-btn>
                    <div v-else></div>
                </div>
                <q-separator color="blue-grey-1"/>

                <template v-for="item in items" v-if="items.length > 0">
                    <q-banner class="q-px-xs flex items-center">
                        <template v-slot:avatar>
                            <q-icon color="blue-3" name="edit_note"/>
                        </template>
                        <router-link :to="'/preorder/'+item.preorder_id" class="text-blue-grey-8 text-weight-bold">
                        <span class="text-caption">
                            Заявка №{{ item.preorder_id }}
                            <br>
                            <span :class="item.action === 'APPROVED' ? 'text-green-5' : 'text-blue-grey-5'">{{ item.action_title }}</span>
                            <span v-if="item.comment"><br>{{ item.comment }}</span>
                        </span>
                        </router-link>
                        <br>
                        <div class="text-right" style="font-size: 11px">{{ item.time }}</div>
                    </q-banner>
                </template>

                <div v-if="items.length === 0" class="text-caption">Нет записей</div>
            </div>
        </q-menu>
    </q-btn>
</template>

<script>
import {getNotificationList} from "../services/notification";

export default {
    name: "Notification",

    data() {
        return {
            show: false,
            loading: false,

            count: 0,
            items: [
                {
                    title: '250167',
                    text: '#Шалдыбаев Ержан(moderator): взял(а) заявку на исполнение',
                    time: '28.11.2024 10:06'
                },
                {
                    title: '250167',
                    text: '#Шалдыбаев Ержан(moderator): Одобрил заявку',
                    time: '28.11.2024 10:06'
                },
                {
                    title: '250165',
                    text: '#Tes Tesov(operator): подписал(а) заявку',
                    time: '28.11.2024 10:06'
                }
            ]
        }
    },

    methods: {
        getData() {
            getNotificationList().then(res => {
                this.items = res
                this.count = res.length
            })
        },

        clearAll() {

        }
    },

    created() {
        this.getData()
    }
}
</script>

<style scoped>

</style>

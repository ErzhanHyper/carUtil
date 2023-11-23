<template>

    <q-banner dense inline-actions class="text-white bg-pink-5" v-if="showBanner" >
            <span v-for="(error, i) in errors" :key="i">
                <span v-for="(item, index) in error" :key="index">
                    {{ item }}
                    <br>
                </span>
            </span>
    </q-banner>

    <div class="row q-col-gutter-md">
        <user-form :data="item"/>
    </div>
    <div class="q-mt-md">
        <q-btn color="primary" icon="save" label="Сохранить" @click="store" :loading="loading"/>
    </div>
</template>

<script>
import UserForm from "./UserForm.vue";
import {storeUser} from "../../services/user";
import {Notify} from "quasar";

export default {
    components: {UserForm},

    data() {
        return {
            item: {},
            errors: [],
            loading: false,
            showBanner: false,
        }
    },

    methods: {

        store() {
            this.showBanner = false
            this.errors = []
            this.loading = true
            this.$emitter.emit('UserCreateEvent')
            storeUser(this.item).then(res => {
                Notify.create({
                    message: 'Пользователь успешно добавлен',
                    position: 'bottom',
                    type: 'positive'
                })
                this.$router.push('/user')
            }).finally(() => {
                this.loading = false
            }).catch((reject) => {
                console.log(reject)
                this.errors = reject.response.data
                this.showBanner = true
            })
        }
    }
}
</script>

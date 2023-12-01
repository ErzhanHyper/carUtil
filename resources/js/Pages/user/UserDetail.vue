<template>
    <div class="row q-col-gutter-md">
        <user-form :data="item" v-if="show"/>
    </div>

    <div class="q-mt-md">
        <q-btn color="primary" icon="save" label="Сохранить" @click="updateData" :loading="loading"/>
    </div>
</template>

<script>
import {getUserById, updateUser} from "../../services/user";
import UserForm from "./UserForm.vue";
import {Notify} from "quasar";

export default {
    props: ['id'],

    components: {
        UserForm,
    },

    data() {
        return {
            show: false,
            loading: false,
            item: {
                phone: '',
                email: '',
                login: '',
                name: '',
                active: 0
            }
        }
    },

    methods: {
        getData() {
            this.$emitter.emit('contentLoaded', true);
            getUserById(this.id).then(res => {
                this.$emitter.emit('contentLoaded', false);
                this.item = res
                this.item.factory = res.factory ? res.factory.id : null
                this.item.region = res.region ? res.region.id : null
                this.item.manufacture = res.manufacture ? res.manufacture.id : null

                this.show = true
            })
        },

        updateData(){
            this.loading = true
            updateUser(this.item.id, this.item).then(() => {
                this.getData()
                Notify.create({
                    message: 'Данные пользователя успешно были изменены',
                    position: 'bottom',
                    type: 'positive'
                })
            }).finally(() => {
                this.loading = false
            })
        }
    },

    created() {
        this.getData()
    }

}
</script>

<style scoped>

</style>

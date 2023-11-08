<template>
    <div class="col col-md-12" v-if="show">
        <div class="flex justify-between" >
            <div class="q-gutter-sm">
                <q-btn :loading="loading1"
                       square size="12px"
                       color="light-green"
                       label="Одобрить"
                       @click="send('approve')"
                       icon="send"
                       :disabled="disabled">
                </q-btn>

                <q-btn square size="12px"
                       color="orange-5"
                       icon="message"
                       @click="send('revision')"
                >
                </q-btn>

                <q-btn square size="12px"
                       color="red-5"
                       label="Отклонить"
                       :loading="loading2"
                       @click="send('decline')"
                       icon="block">
                </q-btn>
            </div>

        </div>
    </div>
</template>

<script>
export default {
   data() {
       return{
           loading1: false,
           loading2: false,
           show: false,
       }
   },

    methods: {
        send(value) {
            this.show = true
        }
    },

    created(){
       this.send()
    }
}
</script>

<style scoped>

</style>

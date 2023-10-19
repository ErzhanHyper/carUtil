<template>
    <q-input
        type="textarea"
        label="Адрес"
        v-model="field"
        rows="2"
    />
</template>

<script>
export default {
    props: ['data'],
    data(){
        return{
            field: this.data
        }
    }
}
</script>

<style scoped>

</style>

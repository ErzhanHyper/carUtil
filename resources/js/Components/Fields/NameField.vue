<template>
    <q-input square v-model="field" label="ФИО" />
</template>

<script>
export default {
    props: ['data'],
    data() {
        return{
            field: this.data
        }
    }
}
</script>

<style scoped>

</style>

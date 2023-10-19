<template>
    <q-input v-model="field" label="Контактный телефон"/>
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

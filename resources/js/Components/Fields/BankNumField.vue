<template>
    <q-input square v-model="field" label="Номер банковского счета"  hint="20 символов"/>
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

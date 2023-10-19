<template>
    <q-input square v-model="field" label="Номер удостоверения личности" maxlength="7" counter/>
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

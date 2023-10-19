<template>
    <q-input square v-model="field" label="ИИН" maxlength="12" counter/>
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

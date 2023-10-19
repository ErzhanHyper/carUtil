<template>
    <q-input v-model="field" label="Email"/>
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

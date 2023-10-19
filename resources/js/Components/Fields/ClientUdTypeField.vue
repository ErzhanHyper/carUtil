<template>
    <q-select square v-model="field" :options="options" option-value="id" option-label="title" label="Выдано" map-options emit-value/>
</template>

<script>
export default {
    props: ['data'],
    data() {
        return{
            field: this.data,
            options: [
                {
                    id: 1,
                    title: 'МВД РК'
                },
                {
                    id: 2,
                    title: 'МЮ РК'
                },
                {
                    id: 3,
                    title: 'Другое'
                }
            ]
        }
    }
}
</script>

<style scoped>

</style>

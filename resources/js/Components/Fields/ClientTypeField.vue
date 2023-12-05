<template>
    <q-select square v-model="field" :options="options" option-value="id" option-label="title" label="Физ./Юр. лицо" map-options emit-value
              transition-hide="jump-up"
              transition-show="jump-up"
    />
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
                    title: 'Физическое лицо'
                },
                {
                    id: 2,
                    title: 'Юридическое лицо - плательщик НДС'
                },
                {
                    id: 3,
                    title: 'Юридическое лицо - неплательщик НДС'
                }
            ]
        }
    }
}
</script>

<style scoped>

</style>

<template>
    <ClientCard :data="data" :banks="banks" :region="region"/>
</template>
<script>
import ClientCard from "./ClientCard.vue";
export default {

    components: {ClientCard},

    props: ['data', 'banks', 'region'],

}
</script>

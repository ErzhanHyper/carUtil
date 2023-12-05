<template>
    <div class="q-mt-md" v-if="items && items.length > 0">
        <div class="text-h5">История изменений</div>
        <q-timeline color="secondary">
            <template v-for="(text, i) in items">
                <q-timeline-entry class="q-mb-sm" :body="text.created_at" >
                    <q-banner :class="(text.action === 'APPROVED') ? 'bg-green-1' : 'bg-purple-1'"
                              style="max-width: 380px">
                        <div class="text-overline text-weight-bold">
                            <span>{{ text.action_title }}</span>
                        </div>
                        {{ text.comment }}
                    </q-banner>
                </q-timeline-entry>
            </template>
        </q-timeline>
    </div>
    <div v-else>
        Нет записей
    </div>
</template>

<script>
export default {
    props: ['items'],


}
</script>

<style scoped>

</style>

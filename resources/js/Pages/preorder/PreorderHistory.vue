<template>
    <div class="q-mt-md"
         v-if="comments.length > 0">
        <div class="text-h5">История изменений</div>
        <q-timeline color="secondary">
            <template v-for="(text, i) in comments">
                <q-timeline-entry class="q-mb-sm" :body="text.created_at">
                    <q-banner :class="(text.action === 'approve') ? 'bg-green-1' : 'bg-purple-1'"
                              style="max-width: 380px">
                        <div class="text-subtitle2 text-weight-bold">
                            <span v-if="text.action === 'approve'">Одобрена</span>
                            <span v-if="text.action === 'decline'">Отклонена</span>
                            <span v-if="text.action === 'revision'">На доработку</span>
                        </div>
                        {{ text.text }}
                    </q-banner>
                </q-timeline-entry>
            </template>
        </q-timeline>

    </div>
</template>

<script>
export default {
    props: ['comments'],
}
</script>

<style scoped>

</style>

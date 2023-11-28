<template>
    <div class="q-mt-md" v-if="comments && comments.length > 0">
        <div class="text-h5">История изменений</div>
        <q-timeline color="secondary">
            <template v-for="(text, i) in comments">
                <q-timeline-entry class="q-mb-sm" :body="text.created_at" >
                    <q-banner :class="(text.action === 'approve') ? 'bg-green-1' : 'bg-purple-1'"
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
</template>

<script>
export default {
    props: ['comments'],
}
</script>

<style scoped>

</style>

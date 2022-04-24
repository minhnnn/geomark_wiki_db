<template>
    <div v-if="links.length > 3">
        <ul class="pagination pagination-sm m-0 float-right">
            <template v-for="(link, key) in links">
                <li v-if="link.url === null" :key="key" class="page-item">
                    <a class="page-link" href="#" v-html="link.label" />
                </li>
                <li v-else :key="`link-${key}`" class="page-item" :class="{ 'active': link.active }">
                    <a class="page-link" :href="link.url" v-html="link.label"/>
                </li>
            </template>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        links: Array,
    },
}
</script>

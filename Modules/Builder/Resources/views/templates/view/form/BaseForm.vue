<template>
    <form @submit.prevent="submitForm()">
        $INPUTCONTENT$
        <button>{{ submitName }}</button>
    </form>
</template>

<script setup>
export default {
    name: "BaseForm",
    props: {
        submitName: {
            type: String,
            default: 'submit'
        },
        submitRouteName: {
            type: String,
            default: null
        }
    },
    data() {
        return {
            form: {
                $FORMDATA$
            }
        }
    },
    methods: {
        submitForm() {
            if(this.submitRouteName) {
                $inertia.post(this.$backendRoute(this.submitRouteName));
            }
        }
    }
}
</script>

<style scoped>

</style>

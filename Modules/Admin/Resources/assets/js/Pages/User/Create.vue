<template>
    <div class="content-body">
        <div class="card card-primary card-outline">
            <div class="card-body pad table-responsive">
                <form @submit.prevent="submitForm">
                    <!--email Input -->
                    <div class="form-group">
                        <text-input
                            v-model="form.email"
                            :error="form.errors.email"
                            label="email"
                        ></text-input>
                    </div>

                    <!--name Input -->
                    <div class="form-group">
                        <text-input
                            v-model="form.name"
                            :error="form.errors.name"
                            label="name"
                        ></text-input>
                    </div>

                    <!--password Input -->
                    <div class="form-group">
                        <text-input
                            v-model="form.password"
                            :error="form.errors.password"
                            label="password"
                        ></text-input>
                    </div>
                    <loading-button :loading="form.processing" type="submit">Create user</loading-button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import TextInput from "../../Shared/TextInput";
import LoadingButton from "../../Shared/LoadingButton";
export default {
    name: "Create",
    components: {LoadingButton, TextInput},
    data() {
        return {
            form: this.$inertia.form({
                email:null,
                name:null,
                password:null,
            })
        }
    },
    methods: {
        submitForm() {
            this.form.post(this.$backendRoute('admin.user.store'));
        }
    }
}
</script>

<style scoped>

</style>

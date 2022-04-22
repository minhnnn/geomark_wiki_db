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
                            class="form-control col-md-6"
                            label="email"
                        ></text-input>
                    </div>

                    <!--name Input -->
                    <div class="form-group">
                        <text-input
                            v-model="form.name"
                            :error="form.errors.name"
                            class="form-control col-md-6"
                            label="name"
                        ></text-input>
                    </div>

                    <!--password Input -->
                    <div class="form-group">
                        <text-input
                            v-model="form.password"
                            :error="form.errors.password"
                            class="form-control col-md-6"
                            label="password"
                        ></text-input>
                    </div>
                    <loading-button :loading="form.processing" type="submit">Edit user</loading-button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>

import TextInput from "../../Shared/TextInput";
import LoadingButton from "../../Shared/LoadingButton";
export default {
    name: "Edit.vue",
    components: {LoadingButton, TextInput},
    props: {
        user: Object
    },
    data() {
        return {
            form: this.$inertia.form({
                email: this.user.email,
                name: this.user.name,
                password: this.user.password,
            })
        }
    },
    methods: {
    submitForm() {
        this.form.patch(this.$backendRoute('admin.user.update', this.user.id));
    }
}
}
</script>

<style scoped>

</style>

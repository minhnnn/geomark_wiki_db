<template>
    <div class="content-body">
        <div class="card card-primary card-outline">
            <div class="card-body pad table-responsive">
                <form @submit.prevent="submitForm">
                    
                         <text-input
                            v-model="form.email"
                            :error="form.errors.email"
                            label="email"
                        ></text-input>
                    
                         <textarea-input
                            v-model="form.name"
                            :error="form.errors.name"
                            label="name"
                        ></textarea-input>
                    
                         <text-input
                            v-model="form.password"
                            :error="form.errors.password"
                            label="password"
                            :type="'number'"
                        ></text-input>
                    
                    <button class="btn btn-success mt-3" :loading="form.processing" type="submit">Edit</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>

import TextInput from "../../Shared/TextInput";
import TextareaInput from "../../Shared/TextareaInput";

export default {
    name: "Edit",
    components: {TextInput, TextareaInput},
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

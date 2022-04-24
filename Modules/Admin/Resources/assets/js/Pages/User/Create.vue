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
                    
                    <button class="btn btn-success mt-3" :loading="form.processing" type="submit">Create</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>

import TextInput from "../../Shared/TextInput";
import TextareaInput from "../../Shared/TextareaInput";

export default {
    name: "Create",
    components: {TextInput, TextareaInput},
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

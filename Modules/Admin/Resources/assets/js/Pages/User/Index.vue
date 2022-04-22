<template>
    <div style="min-height: 2080.12px;">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Users</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>email</th>
                                        <th>name</th>
                                        <th>password</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(user, index) in allUser.data">
                                        <td>{{(allUser.total - 1) * allUser.per_page + index + 1}}</td>
                                        <td>{{user.email}}</td>
                                        <td>{{user.name}}</td>
                                        <td>{{user.password}}</td>
                                        <td>
                                            <Link :href="$backendRoute('admin.user.edit', user.id)" class="btn btn-primary">Edit</Link>
                                            <button type="button" @click="deleteUser(user)" class="btn btn-danger">Delete</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer clearfix">
                                <ul class="pagination pagination-sm m-0 float-right">
                                    <li class="page-item"><a class="page-link" href="#">«</a></li>
                                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item"><a class="page-link" href="#">»</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    name: "Index",
    props: {
        allUser: Object,
    },
    methods: {
        deleteUser(user) {
            if (confirm('Are you sure you want to delete this contact?')) {
                this.$inertia.delete(this.$backendRoute('admin.user.destroy', user.id))
            }
        }
    },
}
</script>

<style scoped>

</style>

<template>
    <!--$NAME$ Input -->
    <div class="form-group">
        <label for="$KEY$">$NAME$</label>
        <input
            id="$KEY$"
            name="$KEY$"
            type="number"
            :value="$$OBJECT$.$KEY$"
            class="form-control col-md-6"
        >
    </div>
</template>

<script>
export default {
    name: "NumberInput"
}
</script>

<style scoped>

</style>

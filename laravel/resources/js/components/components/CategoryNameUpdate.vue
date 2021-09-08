<template>
    <div class="font-bold"
        v-if="!show"
        @click="showInput">
        {{ this.category.name }}
    </div>
    <div v-else>
        <input 
            v-model="category_name"
            @blur="updateName"
            @keyup.enter="updateName"
            ref="input">
    </div>
</template>

<script>
export default {
    name: 'CategoryNameUpdate',
    props: ['category'],
    data: function() {
        return {
            show: false,
            category_name: '',
        }
    },
    methods: {
        showInput: function() {
            this.category_name = this.category.name;
            this.show = true;
            this.$nextTick(function () {
                    this.focusInput();
            })
            // Vue.nextTick(() => {
            //     this.focusInput();
            // });
        },
        focusInput: function() {
            this.$refs.input.focus();
        },
        updateName: function() {
            this.$emit('category-name-updated', this.category_name, this.category.id);
            this.show = false;
        }
    }
}
</script>
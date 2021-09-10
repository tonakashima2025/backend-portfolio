<template>
    <div class="font-bold"
        v-if="!show"
        @click="showInput">
        {{ this.category.name }}
    </div>
    <div v-else>
        <!-- <input 
            v-model="category_name"
            @blur="updateName"
            @keyup.enter="updateName"
            ref="input">         -->
        <input 
            v-model="category_name"
            @keyup.enter="updateName"
            ref="input">        
        <button
            class="mx-2 px-2 py-1 bg-red-500 hover:bg-red-700 text-white rounded-lg mr-2 font-bold text-xs"
            @click="deleteCategory">
            削除
        </button>

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
            });
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
        },
        
        // カテゴリー削除
        deleteCategory: function() {
            this.$emit('category-delete', this.category.id);
            this.show = false;
        },

    }
}
</script>
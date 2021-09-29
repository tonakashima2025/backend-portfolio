<template>
    <div class="kanban">
        <div id="trello-content" class="flex">
            <div
                v-for="(category,index) in displayCategories"
                :key="index"
                style="width:400px"
            >
                <div 
                    class="bg-gray-200 m-2 p-2 text-sm"
                    @dragstart.self="dragCategory(category)"
                    @dragover="dragOverCategory(category)"
                    draggable="true"
                >
                    <category-name-update
                        :category="category"
                        @category-name-updated="categoryNameUpdate"
                        @category-delete="categoryDelete">
                    </category-name-update>
                    <div
                        v-for="(task,index) in category.tasks"
                        :key="index"
                        class="m-2 bg-white p-2"
                        @dragstart="dragTask(task)"
                        @dragover="dragOverTask(task)"
                        draggable="true"
                        @click="openModal(category,task)"
                    >
                        {{ task.name }} 
                    </div>
                    <task-add @task-added="taskAdd" :category_id="category.id"></task-add>
                </div>
            </div>
            <div style="min-width:400px">
                <div class="bg-gray-200 m-2 p-2 text-sm">
                    <div
                        v-if="!show_category_input"
                        @click="showInputCategory"
                    >
                        カテゴリーを追加
                    </div>
                    <div v-else>
                        <input 
                            type="text"
                            class="w-full p-2" 
                            placeholder="新しいカテゴリー名を追加してください"
                            v-model="category_name"
                            ref="inputCategory"
                        >
                        <div class="flex m-2">
                            <button 
                                class="px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded-lg mr-2 font-bold"
                                @click="categoryAdd"
                            >
                                追加    
                            </button>
                            <button 
                                class="px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded-lg font-bold"
                                @click="closeCategoryInput"
                            >
                                キャンセル
                            </button>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="base" v-show="modal">
                <div class="overlay" v-show="modal" @click="modal=false">
                </div>
                <div class="content" v-show="modal">
                    <div class="text-xs">カテゴリー{{ category.name }}</div>
                    <div class="my-4">
                        <label class="text-xs">
                            タスク名
                        </label>
                        <input class="border rounded-lg px-4 py-2 text-xs" v-model="form.name">
                    </div>
                    <div class="my-4">
                        <label class="text-xs">
                            担当者
                        </label>
                        <input class="border rounded-lg px-4 py-2 text-xs" v-model="form.incharge_user">
                    </div>
                    <div class="my-4">
                        <label class="text-xs">
                            開始日
                        </label>
                        <input type="date" class="border rounded-lg px-4 py-2 text-xs" v-model="form.start_date">
                    </div>
                    <div class="my-4">
                        <label class="text-xs">
                            終了締切日
                        </label>
                        <input type="date" class="border rounded-lg px-4 py-2 text-xs" v-model="form.end_date">
                    </div>
                    <button 
                        class="px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded-lg mr-2 font-bold text-xs"
                        @click="taskUpdate"
                    >
                        更新
                    </button>
                    <button 
                        class="px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded-lg mr-2 font-bold text-xs"
                        @click="taskDelete"
                    >
                        削除
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import CategoryNameUpdate from './components/CategoryNameUpdate.vue'
import TaskAdd from './components/TaskAdd.vue'

export default {
    name: 'Kanban',
    components: {
        CategoryNameUpdate,
        TaskAdd,
    },
    data: function() {
        return {
            // DBデータ
            categories: [],
            tasks: [],
            category: {},
            task: {},
            type: '',
            category_name: '',
            show_category_input: false,
            modal: false,
            form:{
                id:'',
                category_id:'',
                name:'',
                start_date:'',
                end_date:'',
                incharge_user:'',
                percentage:'',
                sort: '',
            },
        }
    },
    methods: {
        // API接続（カテゴリー・タスク一覧取得）
        getCategories() {
            axios.get('/api/categories')
                .then((res) => {
                    this.categories = res.data;
                });
        },
        getTasks() {
            axios.get('/api/tasks')
                .then((res) => {
                    this.tasks = res.data;
                });
        },
        // ドラッグ＆ドロップ
        dragCategory: function(category) {
            this.category = category;
            this.type = "typeCategory";
        },
        dragTask: function(task) {
            this.task = task;
            this.type = "typeTask";
        },
        dragOverCategory: function(overCategory) {
            if (overCategory.id !== this.category.id && this.type === 'typeCategory') {
                let deleteIndex;
                let addIndex;
                this.categories.forEach((category,index) => {
                    if(category.id === this.category.id) {
                        deleteIndex = index;
                    }
                });
                this.categories.forEach((category,index) => {
                    if(category.id === overCategory.id) {
                        addIndex = index;
                    }
                });
                this.categories.splice(deleteIndex, 1);
                this.categories.splice(addIndex, 0, this.category);
            } else {
                if (this.task.category_id !== overCategory.id && this.type === 'typeTask') {
                    let tasks = this.tasks.filter(task => task.category_id === overCategory.id);
                    if (tasks.length === 0) {
                        this.task.category_id = overCategory.id;
                    }
                }
            }
        },
        dragOverTask: function(overTask) {
            if (overTask.id !== this.task.id && this.type === 'typeTask') {
                let deleteIndex;
                let addIndex;
                this.tasks.forEach((task,index) => {
                    if(task.id === this.task.id) {
                        deleteIndex = index;
                    }
                });
                this.tasks.forEach((task,index) => {
                    if(task.id === overTask.id) {
                        addIndex = index;
                    }
                });
                this.tasks.splice(deleteIndex, 1);
                this.task.category_id = overTask.category_id;
                console.log(this.task);
                this.tasks.splice(addIndex, 0, this.task);
                this.tasks.forEach((task,index) => {
                    task.sort = index;
                });
                this.tasks.forEach((task) => {
                    // API接続（カテゴリー区分・ソートカラム更新）
                    axios.put('/api/tasks/' + task.id, task)
                        .then((res) => {
                            this.$router.go({name: 'kanban', force: true});
                        });
                });
            }
        },
        // カテゴリー追加
        showInputCategory: function() {
            this.show_category_input=true;
            this.$nextTick(function () {
                this.focusInputCategory();
            })
        },
        focusInputCategory: function() {
            this.$refs.inputCategory.focus();
        },
        categoryAdd: function() {
            if (this.category_name !=='') {
                this.category = {
                    name: this.category_name,
                    collapsed: 1
                },
                this.categories.push(this.category);
                this.apiCategoryAdd();
                this.show_category_input = false;
            }
        },
        // API接続（カテゴリー追加）
        apiCategoryAdd: function() {
            axios.post('/api/categories', this.category)
                .then((res) => {
                    this.$router.go({name: 'kanban', force: true});
                });
        },
        closeCategoryInput: function() {
            this.category_name = '';
            this.show_category_input = false;
        },
        // カテゴリー名更新
        categoryNameUpdate: function(category_name, category_id) {
            let update_category = this.categories.find(element => element.id === category_id);
            update_category.name = category_name;
            // API接続（カテゴリー名更新）
            axios.put('/api/categories/' + category_id, update_category)
                .then((res) => {
                    this.$router.push({name: 'kanban'}, () => {});
                });
        },
        // カテゴリー削除
        categoryDelete: function(category_id) {
            let deleteIndex;
            this.categories.map((category,index) => {
                if(category.id === category_id) {
                    deleteIndex = index;
                }
            });
            this.categories.splice(deleteIndex, 1);

            // API接続（カテゴリー削除）
            axios.delete('/api/categories/' + category_id)
                .then((res) => {
                    this.$router.push({name: 'kanban'}, () => {});
                });
            

        },
        // タスク追加
        taskAdd: function(task_name, category_id) {
            this.task = {
                category_id: category_id,
                name: task_name,
            },
            this.tasks.push(this.task);
            this.apiTaskAdd();
        },
        // API接続（タスク追加）
        apiTaskAdd: function() {
            axios.post('/api/tasks', this.task)
                .then((res) => {
                    this.$router.go({name: 'kanban', force: true});
                });
        },
        // タスク更新
        taskUpdate() {
            let update_task = this.tasks.find(task => task.id === this.form.id);
            // Object.assign(task, this.form);
            // update_task.id = this.form.id;
            // update_task.category_id = this.form.category_id;
            update_task.name = this.form.name;
            update_task.start_date = this.form.start_date;
            update_task.end_date = this.form.end_date;
            update_task.incharge_user = this.form.incharge_user;
            update_task.percentage = this.form.percentage;

            this.modal = false;

            // API接続（タスク更新）
            axios.put('/api/tasks/' + update_task.id, update_task)
                .then((res) => {
                    this.$router.push({name: 'kanban'}, () => {});
                });
        },
        // タスク削除
        taskDelete() {
            let deleteIndex;
            this.tasks.map((task,index) => {
                if(task.id === this.form.id) {
                    deleteIndex = index;
                }
            });
            this.tasks.splice(deleteIndex, 1);

            // API接続（タスク削除）
            axios.delete('/api/tasks/' + this.form.id)
                .then((res) => {
                    this.$router.push({name: 'kanban'}, () => {});
                });
            
            this.modal = false;
        },
        openModal(category, task) {
            this.modal = true;
            this.category = category;
            // Object.assign(this.form, task);
            this.form.id = task.id;
            this.form.category_id = task.category_id;
            this.form.name = task.name;
            this.form.start_date = task.start_date;
            this.form.end_date = task.end_date;
            this.form.incharge_user = task.incharge_user;
            this.form.percentage = task.percentage;
        },
    },
    mounted() {
        // API接続（カテゴリー・タスク一覧取得）
        this.getCategories();
        this.getTasks();
    },
    computed: {
        displayCategories: function() {
            let categories = [];
            let tasks = "";
            this.categories.map(category => { 
                tasks = this.tasks.filter(task => task.category_id === category.id);
                categories.push({
                    id: category.id,
                    name: category.name,
                    tasks: tasks
                })
            })
            return categories;
        }
    }
}
</script>

<style scoped>
.base {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    margin-top: 50px;
}
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: gray;
    opacity: 0.5;
}
.content {
    background-color: white;
    position: relative;
    border-radius: 10px;
    padding: 40px;
}
</style>
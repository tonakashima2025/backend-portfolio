<template>
    <div class="gantt">
        <div id="gantt-header" class="h-12 p-2 flex items-center" ref="header">
            <button class="bg-indigo-700 hover:bg-indigo-900 text-white py-2 px-4 rounded-lg flex text-xs ml-4"
                @click="addTask">
                ＋ 
                <!-- <svg class="w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg> -->
                <span class="font-bold text-xs">タスク追加</span>
            </button>
        </div>
        <div id="gantt-content" class="flex">
            <div id="gantt-task">
                <div id="gantt-task-title" class="flex items-center bg-green-600 text-white h-20" ref="task">
                    <div class="border-t border-r border-b flex items-center justify-center font-bold text-xs w-48 h-full">
                        タスク
                    </div>
                    <div class="border-t border-r border-b flex items-center justify-center font-bold text-xs w-24 h-full">
                        開始日
                    </div>
                    <div class="border-t border-r border-b flex items-center justify-center font-bold text-xs w-24 h-full">
                        完了期限日
                    </div>
                    <div class="border-t border-r border-b flex items-center justify-center font-bold text-xs w-16 h-full">
                        担当
                    </div>
                    <div class="border-t border-r border-b flex items-center justify-center font-bold text-xs w-12 h-full">
                        進捗
                    </div>
                </div>
                <div id="gantt-task-list" class="overflow-y-hidden"
                    :style="`height:${calendarViewHeight}px`">
                    <div v-for="(task,index) in displayTasks" 
                        :key="index" 
                        class="flex h-10 border-b"
                        draggable="true"
                        @dragstart="dragTask(task)"
                        @dragover.prevent="dragOverTask(task)">
                        <template v-if="task.cat === 'category'">
                            <div class="flex items-center font-bold w-full text-sm pl-2 flex justify-between items-center bg-green-100">
                                <span>{{ task.name }}</span>
                                <div class="pr-4" @click="toggleCategory(task.id)">
                                    <span v-if="task.collapsed">
                                        ∨ 
                                        <!-- <svg class="w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg> -->
                                    </span>
                                    <span v-else>
                                        ∧ 
                                        <!-- <svg class="w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg> -->
                                    </span>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="border-r flex items-center font-bold w-48 text-sm pl-4"
                                @click="editTask(task)">
                                {{ task.name }}
                            </div>
                            <div class="border-r flex items-center justify-center w-24 text-sm"
                                @click="editTask(task)">
                                {{ task.start_date }}
                            </div>
                            <div class="border-r flex items-center justify-center w-24 text-sm"
                                @click="editTask(task)">
                                {{ task.end_date }}
                            </div>
                            <div class="border-r flex items-center justify-center w-16 text-sm"
                                @click="editTask(task)">
                                {{ task.incharge_user }}
                            </div>
                            <div class="flex items-center justify-center w-12 text-sm"
                                @click="editTask(task)">
                                {{ task.percentage }}%
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div id="gantt-calendar" class="overflow-x-scroll overflow-y-hidden border-l"
                :style="`width:${calendarViewWidth}px`" ref="calendar">
                <div id="gantt-date" class="h-20">
                    <div id="gantt-year-month" class="relative h-8">
                        <div v-for="(calendar,index) in calendars" :key="index">
                            <div class="bg-indigo-700 text-white border-b border-r border-t h-8 absolute font-bold text-sm flex items-center justify-center"
                                :style="`width:${calendar.calendar*block_size}px;left:${calendar.start_block_number*block_size}px`">
                                {{ calendar.date }}
                            </div>
                        </div>
                    </div>
                    <div id="gantt-day" class="relative h-12">
                        <div v-for="(calendar,index) in calendars" :key="index">
                            <div v-for="(day,index) in calendar.days" :key="index">
                                <div class="border-r h-12 absolute flex items-center justify-center flex-col font-bold text-xs"
                                    :style="`width:${block_size}px;left:${day.block_number*block_size}px;`"
                                    :class="{'bg-blue-100': day.dayOfWeek === '土', 'bg-red-100': day.dayOfWeek === '日', 'bg-red-600 text-yellow-500': calendar.year === today.year() && calendar.month === today.month() && day.day === today.date()}">
                                    <span>{{ day.day }}</span>
                                    <span>{{ day.dayOfWeek }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="gantt-height" class="relative">
                        <div v-for="(calendar,index) in calendars" :key="index">
                            <div v-for="(day,index) in calendar.days" :key="index">
                                <div class="border-r border-b absolute"
                                    :class="{'bg-blue-100': day.dayOfWeek === '土', 'bg-red-100': day.dayOfWeek === '日'}"
                                    :style="`width:${block_size}px;left:${day.block_number*block_size}px;height:${calendarViewHeight}px`">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="gantt-bar-area" class="relative"
                    :style="`width:${calendarViewWidth}px;height:${calendarViewHeight}px`">
                    <div v-for="(bar,index) in taskBars" :key="index">
                        <div class="rounded-lg absolute h-5 bg-yellow-100"
                            v-if="bar.task.cat === 'task'"
                            :style="bar.style"
                            @mousedown="mouseDownMove(bar.task)">
                            <div class="w-full h-full" style="pointer-events:none;">
                                <div class="h-full bg-yellow-500 rounded-l-lg"
                                style="pointer-events:none;"
                                :style="`width:${bar.task.percentage}%`"
                                :class="{ 'rounded-r-lg': bar.task.percentage === 100 }">
                                </div>
                            </div>
                            <div class="absolute w-2 h-2 bg-gray-300 border border-black"
                                style="top:6px;left:-6px;cursor:col-resize;"
                                @mousedown="mouseDownChange(bar.task, 'left')">
                            </div>
                            <div class="absolute w-2 h-2 bg-gray-300 border border-black"
                                style="top:6px;right:-6px;cursor:col-resize;"
                                @mousedown="mouseDownChange(bar.task, 'right')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="base" v-show="show">
                <div class="overlay" v-show="show" @click="show=false">
                </div>
                <div class="content" v-show="show">
                    <h2 class="font-bold" v-if="create_mode">タスク追加</h2>
                    <h2 class="font-bold" v-else>タスク更新</h2>
                    <div class="my-4">
                        <label for="" class="text-xs mr-2">カテゴリー：</label>
                        <select name="" id="" class="text-xs border px-4 py-2 rounded-lg"
                            v-model="form.category_id">
                            <option 
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <!-- <div class="my-4">
                        <label for="" class="text-xs mr-2">ID：</label>
                        <input type="number" class="text-xs border rounded-lg px-4 py-2"
                            v-model="form.id">
                    </div> -->
                    <div class="my-4">
                        <label for="" class="text-xs mr-2">タスク名：</label>
                        <input type="text" class="text-xs border rounded-lg px-4 py-2"
                            v-model="form.name">
                    </div>
                    <div class="my-4">
                        <label for="" class="text-xs mr-2">担当者：</label>
                        <input type="text" class="text-xs border rounded-lg px-4 py-2"
                            v-model="form.incharge_user">
                    </div>
                    <div class="my-4">
                        <label for="" class="text-xs mr-2">開始日：</label>
                        <input type="date" class="text-xs border rounded-lg px-4 py-2"
                            v-model="form.start_date">
                    </div>
                    <div class="my-4">
                        <label for="" class="text-xs mr-2">期限日：</label>
                        <input type="date" class="text-xs border rounded-lg px-4 py-2"
                            v-model="form.end_date">
                    </div>
                    <div class="my-4">
                        <label for="" class="text-xs mr-2">進捗度：</label>
                        <input type="number" class="text-xs border rounded-lg px-4 py-2"
                            v-model="form.percentage">
                    </div>
                    <div v-if="create_mode" class="flex items-center justify-between">
                        <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg flex items-center"
                            @click="saveTask">
                            ★
                            <!-- <svg class="w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg> -->
                            <span class="font-bold text-xs">タスク追加</span>
                        </button>
                    </div>
                    <div v-else  class="flex items-center justify-between">
                        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center"
                            @click="updateTask(form.id)">
                            ★
                            <!-- <svg class="w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg> -->
                            <span class="font-bold text-xs text-white">タスク更新</span>
                        </button>
                        <button class="bg-red-500 hover:bg-red-700 text-white py-2 px-4 rounded-lg flex items-center ml-2"
                            @click="deleteTask(form.id)">
                            ★
                            <!-- <svg class="w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg> -->
                            <span class="text-xs font-bold text-white">タスク削除</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment";

export default {
    name: 'Gantt',
    data() {
        return {
            // DBデータ
            categories: [],
            tasks: [],

            // カレンダー基礎情報
            start_month: '2021-07',
            end_month: '2021-11',
            block_size: 30,
            block_number: 0,
            calendars: [],
            // ウィンドウサイズ対応
            inner_width: '',
            inner_height: '',
            task_width: '',
            task_height: '',
            position_id: 0,
            // タスクバー領域表示
            header_height: '',
            // 本日基準
            today: moment(),
            // タスク移動
            draggingTask: '',
            // タスクバー移動・拡大縮小
            dragging: false,
            pageX: '',
            element: '',
            left: '',
            task_id: '',
            width: '',
            leftChanging: false,
            rightChanging: false,
            // タスク追加・更新・削除
            show: false,
            create_mode: true,
            form: {
                id: '',
                category_id: '',
                name: '',
                start_date: '',
                end_date: '',
                incharge_user: '',
                percentage: 0
            },
        }
    },
    methods: {
        // API接続（一覧取得）
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
        // カレンダー情報作成
        getDays(year, month, block_number) {
            const dayOfWeek = ['日', '月', '火', '水', '木', '金', '土'];
            let days = [];
            let date = moment(`${year}-${month}-01`);
            let num = date.daysInMonth();
            for(let i = 0; i < num; i++) {
                days.push({
                    day: date.date(),
                    dayOfWeek: dayOfWeek[date.day()],
                    block_number: block_number,
                });
                date.add(1, 'day');
                block_number++;
            }
            return days;
        },
        getCalendar() {
            let block_number = 0;
            let days;
            let start_month = moment(this.start_month);
            let end_month = moment(this.end_month);
            let between_month = end_month.diff(start_month, 'months');
            for(let i = 0; i <= between_month; i++) {
                days = this.getDays(start_month.year(), start_month.format('MM'), block_number);
                this.calendars.push({
                    date: start_month.format('YYYY年MM月'),
                    year: start_month.year(),
                    month: start_month.month(),
                    start_block_number: block_number,
                    calendar: days.length,
                    days: days
                });
                start_month.add(1, 'months');
                block_number = days[days.length - 1].block_number;
                block_number++;
            }
            return block_number;
        },
        // ウィンドウサイズ対応・タスクバー領域表示
        getWindowSize() {
            this.inner_width = window.innerWidth;
            this.inner_height = window.innerHeight;
            this.task_width = this.$refs.task.offsetWidth;
            this.task_height = this.$refs.task.offsetHeight;
            this.header_height = this.$refs.header.offsetHeight;
        },
        windowSizeCheck() {
            let height = this.lists.length - this.position_id;
            if(event.deltaY > 0 && height * 40 > this.calendarViewHeight) {
                this.position_id++;
            } else if(event.deltaY < 0 && this.position_id != 0) {
                this.position_id--;
            }
        },
        // 本日基準
        todayPosition() {
            this.$refs.calendar.scrollLeft = this.scrollDistance;
        },
        // タスク移動
        dragTask(draggingTask) {
            this.draggingTask = draggingTask;
        },
        dragOverTask(dragoveredTask) {
            let deleteIndex;
            let addIndex;
            // if(this.draggingTask.cat === 'task') {
            //     if(dragoveredTask.cat === 'task') {
            //         if(dragoveredTask.id !== this.draggingTask.id) {
            //             this.tasks.map((task,index) => {
            //                 if(task.id === this.draggingTask.id) {
            //                     deleteIndex = index;
            //                 }
            //             });
            //             this.tasks.map((task,index) => {
            //                 if(task.id === dragoveredTask.id) {
            //                     addIndex = index;
            //                 }
            //             });
            //             this.tasks.splice(deleteIndex, 1);
            //             this.draggingTask.category_id = dragoveredTask.category_id;
            //             this.tasks.splice(addIndex, 0, this.draggingTask);
            //         }
            //     } else {
            //         let updateTask = this.tasks.find(task => task.id === this.draggingTask.id);
            //         updateTask.category_id = dragoveredTask.category_id;
            //     }
            // }
            if(this.draggingTask.cat !== 'category') {
                if(dragoveredTask.cat === 'category') {
                    let updateTask = this.tasks.find(task => task.id === this.draggingTask.id);
                    updateTask['category_id'] = dragoveredTask['id'];
                } else {
                    if(dragoveredTask.id !== this.draggingTask.id) {
                        this.tasks.map((task,index) => {
                            if(task.id === this.draggingTask.id) {
                                deleteIndex = index;
                            }
                        });
                        this.tasks.map((task,index) => { 
                            if(task.id === dragoveredTask.id) {
                                addIndex = index;
                            } 
                        });
                        this.tasks.splice(deleteIndex, 1);
                        this.draggingTask['category_id'] = dragoveredTask['category_id'];
                        this.tasks.splice(addIndex, 0, this.draggingTask);
                    }
                }
            }
        },
        // タスクバー移動・拡大縮小
        mouseDownMove(task) {
            this.dragging = true;
            this.pageX = event.pageX;
            this.element = event.target;
            this.left = event.target.style.left;
            this.task_id = task.id;
        },
        mouseMove() {
            if(this.dragging) {
                let diff = this.pageX - event.pageX;
                this.element.style.left = `${parseInt(this.left.replace('px', '')) - diff}px`;
            }
        },
        mouseDownChange(task, direction) {
            direction === 'left' ? this.leftChanging = true : this.rightChanging = true;
            this.pageX = event.pageX;
            this.width = event.target.parentElement.style.width;
            this.left = event.target.parentElement.style.left;
            this.element = event.target.parentElement;
            this.task_id = task.id;
            event.stopPropagation();
        },
        mouseChange() {
            if(this.leftChanging) {
                let diff = this.pageX - event.pageX;
                if(parseInt(this.width.replace('px', '')) + diff > this.block_size) {
                    this.element.style.width = `${parseInt(this.width.replace('px', '')) + diff}px`;
                    this.element.style.left = `${parseInt(this.left.replace('px', '')) - diff}px`;
                }
            }
            if(this.rightChanging) {
                let diff = this.pageX - event.pageX;
                if(parseInt(this.width.replace('px', '')) - diff > this.block_size) {
                    this.element.style.width = `${parseInt(this.width.replace('px', '')) - diff}px`;
                }
            }
        },
        stopDrag() {
            if(this.dragging) {
                let diff = this.pageX - event.pageX;
                let days = Math.ceil(diff / this.block_size);
                if(days !== 0) {
                    let targetTask = this.tasks.find(task => task.id === this.task_id);
                    let targetTaskStart_date = moment(targetTask.start_date).add(-days, 'days')
                    let targetTaskEnd_date = moment(targetTask.end_date).add(-days, 'days');
                    targetTask.start_date = targetTaskStart_date.format('YYYY-MM-DD');
                    targetTask.end_date = targetTaskEnd_date.format('YYYY-MM-DD');
                    // API接続（タスク更新）
                    axios.put('/api/tasks/' + targetTask.id, targetTask)
                        .then((res) => {
                            this.$router.push({name: 'gantt'}, () => {});
                        });

                } else {
                    this.element.style.left = `${this.left.replace('px', '')}px`;
                }
            }
            if(this.leftChanging) {
                let diff = this.pageX - event.pageX;
                let days = Math.ceil(diff / this.block_size);
                if(days !== 0) {
                    let targetTask = this.tasks.find(task => task.id === this.task_id);
                    let targetTaskStart_date = moment(targetTask.start_date).add(-days, 'days');
                    let targetTaskEnd_date = moment(targetTask.end_date);
                    if(targetTaskEnd_date.diff(targetTaskStart_date, 'days') > 0) {
                        targetTask.start_date = targetTaskStart_date.format('YYYY-MM-DD');
                        // API接続（タスク更新）
                        axios.put('/api/tasks/' + targetTask.id, targetTask)
                            .then((res) => {
                                this.$router.push({name: 'gantt'}, () => {});
                            });

                    } else {
                        targetTask.start_date = targetTaskEnd_date.format('YYYY-MM-DD');
                    }
                } else {
                    this.element.style.width = this.width;
                    this.element.style.left = this.left;
                }
            }
            if(this.rightChanging) {
                let diff = this.pageX - event.pageX;
                let days = Math.ceil(diff / this.block_size);
                if(days === 1) {
                    this.element.style.width = this.width;
                } else if(days <= 2) {
                    days--;
                    let targetTask = this.tasks.find(task => task.id === this.task_id);
                    let targetTaskEnd_date = moment(targetTask.end_date).add(-days, 'days');
                    targetTask.end_date = targetTaskEnd_date.format('YYYY-MM-DD');
                    // API接続（タスク更新）
                    axios.put('/api/tasks/' + targetTask.id, targetTask)
                        .then((res) => {
                            this.$router.push({name: 'gantt'}, () => {});
                        });

                } else {
                    let targetTask = this.tasks.find(task => task.id === this.task_id);
                    let targetTaskStart_date = moment(targetTask.start_date);
                    let targetTaskEnd_date = moment(targetTask.end_date).add(-days, 'days');
                    if(targetTaskEnd_date.diff(targetTaskStart_date, 'days') >= 1) {
                        targetTask.end_date = targetTaskEnd_date.format('YYYY-MM-DD');
                        // API接続（タスク更新）
                        axios.put('/api/tasks/' + targetTask.id, targetTask)
                            .then((res) => {
                                this.$router.push({name: 'gantt'}, () => {});
                            });

                    } else {
                        targetTask.end_date = targetTaskStart_date.format('YYYY-MM-DD');
                    }
                }
            }
            this.dragging = false;
            this.leftChanging = false;
            this.rightChanging = false;
        },
        // カテゴリー開閉
        toggleCategory(category_id) {
            let category = this.categories.find(category => category.id === category_id);
            category.collapsed ? category.collapsed = false : category.collapsed = true;
        },
        // タスク追加
        addTask() {
            this.create_mode = true;
            this.form = {};
            this.show = true;
        },
        saveTask() {
            this.tasks.push(this.form);
            // API接続（タスク追加）
            axios.post('/api/tasks', this.form)
                .then((res) => {
                    this.$router.push({name: 'gantt'}, () => {});
                });

            this.form = {};
            this.show = false;
        },
        // タスク更新
        editTask(task) {
            this.create_mode = false;
            this.show = true;
            Object.assign(this.form, task);
            
        },
        updateTask(form_id) {
            let update_task = this.tasks.find(task => task.id === form_id);
            Object.assign(update_task, this.form);
            // API接続（タスク更新）
            axios.put('/api/tasks/' + update_task.id, update_task)
                .then((res) => {
                    this.$router.push({name: 'gantt'}, () => {});
                });

            this.form = {};
            this.show = false;
        },
        // タスク削除
        deleteTask(form_id) {
            let deleteIndex;
            this.tasks.map((task,index) => {
                if(task.id === form_id) {
                    deleteIndex = index;
                }
            });
            this.tasks.splice(deleteIndex, 1);
            // API接続（タスク削除）
            axios.delete('/api/tasks/' + form_id)
                .then((res) => {
                    this.$router.push({name: 'gantt'}, () => {});
                });

            this.form = {};
            this.show = false;
        }
    },
    mounted: function() {
        this.getCalendar();
        this.getWindowSize();
        this.$nextTick(() => {
            this.todayPosition();
        });
        window.addEventListener('resize', this.getWindowSize);
        window.addEventListener('wheel', this.windowSizeCheck);
        window.addEventListener('mousemove', this.mouseMove);
        window.addEventListener('mouseup', this.stopDrag);
        window.addEventListener('mousemove', this.mouseChange);

        // API接続（一覧取得）
        this.getCategories();
        this.getTasks();
    },
    computed: {
        // ウィンドウサイズ対応
        calendarViewWidth() {
            return this.inner_width - this.task_width;
        },
        displayTasks() {
            let display_task_number = Math.floor(this.calendarViewHeight / 40);
            return this.lists.slice(this.position_id, this.position_id + display_task_number);
        },
        // タスクバー領域表示
        calendarViewHeight() {
            return this.inner_height - this.task_height - this.header_height - 0;
        },
        // 本日基準
        scrollDistance() {
            let start_date = moment(this.start_month);
            let between_days = this.today.diff(start_date, 'days');
            return (between_days + 1) * this.block_size - this.calendarViewWidth / 2;
        },
        // カテゴリー・タスクデータのネスト化
        lists() {
            let lists = [];
            this.categories.map(category => {
                lists.push({ cat: 'category', ...category });
                this.tasks.map(task => {
                    if(task.category_id === category.id && category.collapsed === false) {
                        lists.push({ cat: 'task', ...task});
                    }
                })
            })
            return lists;
        },
        // タスクバー表示
        taskBars() {
            let start_date = moment(this.start_month);
            let top = 10;
            let left;
            let between;
            let start;
            let style;
            return this.displayTasks.map(task => {
                style = {};
                if(task.cat === 'task') {
                    let date_from = moment(task.start_date);
                    let date_to = moment(task.end_date);
                    between = date_to.diff(date_from, 'days');
                    between++;
                    start = date_from.diff(start_date, 'days');
                    left = start * this.block_size;
                    style = {
                        top: `${top}px`,
                        left: `${left}px`,
                        width: `${this.block_size * between}px`,
                    }
                }
                top = top + 40;
                return {
                    style,
                    task
                }
            })
        },
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
    position: relative;
    background-color: white;
    border-radius: 10px;
    padding: 40px;
}
</style>

<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('task.task')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('task.task')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <transition name="fade" v-if="hasPermission('create-task')">
                    <div class="card" v-if="showCreatePanel">
                        <div class="card-body">
                            <button class="btn btn-info btn-sm pull-right" v-if="showCreatePanel" @click="showCreatePanel = !showCreatePanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('task.add_new_task')}}</h4>
                            <task-form @completed="getTasks"></task-form>
                        </div>
                    </div>
                </transition>

                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('task.number')}}</label>
                                        <input class="form-control" name="title" v-model="filterTaskForm.number">
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('task.title')}}</label>
                                        <input class="form-control" name="title" v-model="filterTaskForm.title">
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('task.task_category')}}</label>
                                        <v-select label="name" track-by="id" v-model="selected_task_category" name="task_category_id" id="task_category_id" :options="task_categories" :placeholder="trans('task.select_task_category')" @select="onTaskCategorySelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onTaskCategoryRemove">
                                        </v-select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('task.task_priority')}}</label>
                                        <v-select label="name" track-by="id" v-model="selected_task_priority" name="task_priority_id" id="task_priority_id" :options="task_priorities" :placeholder="trans('task.select_task_priority')" @select="onTaskPrioritySelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onTaskPriorityRemove">
                                        </v-select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">{{trans('task.assigned_user')}}</label>
                                        <v-select label="name" track-by="id" v-model="selected_user" name="user_id" id="user_id" :options="users" :placeholder="trans('user.select_user')" @select="onUserSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onUserRemove">
                                        </v-select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">{{trans('task.type')}}</label>
                                                <select name="type" class="form-control" v-model="filterTaskForm.type" :placeholder="trans('general.select_one')">
                                                    <option value="">{{trans('general.select_one')}}</option>
                                                    <option value="owned">{{trans('task.owned')}}</option>
                                                    <option value="assigned">{{trans('task.assigned')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">{{trans('task.starred')}}</label>
                                                <select name="starred" class="form-control" v-model="filterTaskForm.starred" :placeholder="trans('general.select_one')">
                                                    <option value="">{{trans('general.select_one')}}</option>
                                                    <option value="starred">{{trans('task.starred')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">{{trans('task.archive')}}</label>
                                                <select name="is_archived" class="form-control" v-model="filterTaskForm.is_archived" :placeholder="trans('general.select_one')">
                                                    <option value="">{{trans('general.select_one')}}</option>
                                                    <option value="archived">{{trans('task.archived')}}</option>
                                                    <option value="unarchived">{{trans('task.unarchived')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterTaskForm.start_date_start" :end-date.sync="filterTaskForm.start_date_end" :label="trans('task.start_date')"></date-range-picker>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterTaskForm.due_date_start" :end-date.sync="filterTaskForm.due_date_end" :label="trans('task.due_date')"></date-range-picker>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterTaskForm.completed_at_start" :end-date.sync="filterTaskForm.completed_at_end" :label="trans('task.completed_at')"></date-range-picker>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('task.status')}}</label>
                                        <select name="status" class="form-control" v-model="filterTaskForm.status" :placeholder="trans('general.select_one')">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option value="unassigned">{{trans('task.unassigned')}}</option>
                                            <option value="requested">{{trans('task.task_sign_off_status',{status:trans('task.requested')})}}</option>
                                            <option value="rejected">{{trans('task.task_sign_off_status',{status:trans('task.rejected')})}}</option>
                                            <option value="cancelled">{{trans('task.task_sign_off_status',{status:trans('task.cancelled')})}}</option>
                                            <option value="pending">{{trans('task.pending')}}</option>
                                            <option value="approved">{{trans('task.completed')}}</option>
                                            <option value="overdue">{{trans('task.overdue')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('task.recurring')}}</label>
                                        <select name="is_recurring" class="form-control" v-model="filterTaskForm.is_recurring" :placeholder="trans('general.select_one')">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option value="1">{{trans('task.recurring')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="sort_by" class="form-control" v-model="filterTaskForm.sort_by">
                                            <option value="title">{{trans('task.title')}}</option>
                                            <option value="task_category_id">{{trans('task.task_category')}}</option>
                                            <option value="task_priority_id">{{trans('task.task_priority')}}</option>
                                            <option value="start_date">{{trans('task.start_date')}}</option>
                                            <option value="due_date">{{trans('task.due_date')}}</option>
                                            <option value="completed_at">{{trans('task.completed_at')}}</option>
                                            <option value="created_at">{{trans('task.created_at')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterTaskForm.order">
                                            <option value="asc">{{trans('general.ascending')}}</option>
                                            <option value="desc">{{trans('general.descending')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
                <div class="card" v-if="hasPermission('list-task')">
                    <div class="card-body">
                        <button class="btn btn-info btn-sm pull-right" v-if="!showFilterPanel" @click="showFilterPanel = !showFilterPanel"><i class="fas fa-filter"></i> {{trans('general.filter')}}</button>
                        <button class="btn btn-info btn-sm pull-right m-r-5" v-if="tasks.total && !showCreatePanel" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> {{trans('general.add_new')}}</button>
                        <h4 class="card-title">{{trans('task.task_list')}} <span v-if="filterTaskForm.user_id.length == 1">{{trans('general.of')+' '+getRatingUser}}</span></h4>
                        <h6 class="card-subtitle" v-if="tasks">{{trans('general.total_result_found',{'count' : tasks.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="tasks.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('task.number')}}</th>
                                        <th>{{trans('task.title')}}</th>
                                        <th>{{trans('task.category')}}</th>
                                        <th>{{trans('task.priority')}}</th>
                                        <th>{{trans('task.start_date')}}</th>
                                        <th>{{trans('task.due_date')}}</th>
                                        <th>{{trans('task.progress')}}</th>
                                        <th>{{trans('task.status')}}</th>
                                        <th v-if="filterTaskForm.user_id.length == 1">{{trans('task.rating')}}</th>
                                        <th>{{trans('task.owner')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="task in tasks.data">
                                        <td v-text="getTaskNumber(task)"></td>
                                        <td v-text="task.title"></td>
                                        <td v-text="task.task_category.name"></td>
                                        <td v-text="task.task_priority.name"></td>
                                        <td>{{ task.start_date | moment}}</td>
                                        <td>{{ task.due_date | moment}}</td>
                                        <td>
                                            <div class="progress" style="height: 10px;">
                                                <div :class="getTaskProgressColor(task)" role="progressbar" :style="getTaskProgressWidth(task)" :aria-valuenow="getTaskProgress(task)" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            {{ getTaskProgress(task) }} %
                                        </td>
                                        <td>
                                            <span v-for="status in getTaskStatus(task)" :class="['label','label-'+status.color]" style="margin-right:5px;">{{status.label}}</span>
                                        </td>
                                        <td v-if="filterTaskForm.user_id.length == 1" v-html="getRating(task)"></td>
                                        <td>{{ task.user_added.profile.first_name+' '+task.user_added.profile.last_name}}</td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <router-link :to="`/task/${task.uuid}`" class="btn btn-success btn-sm" v-tooltip="trans('task.view_task')"><i class="fas fa-arrow-circle-right"></i></router-link>
                                                <template v-if="task.sign_off_status === 'approved' && task.user_id == getAuthUser('id')">
                                                    <button class="btn btn-warning btn-sm" v-if="!task.is_archived" v-tooltip="trans('task.move_to_archive')" :key="`archive_${task.id}`" v-confirm="{ok: confirmToggleArchive(task)}" ><i class="fas fa-archive"></i></button>
                                                    <button class="btn btn-warning btn-sm" v-if="task.is_archived" v-tooltip="trans('task.remove_from_archive')" :key="`unarchive_${task.id}`" v-confirm="{ok: confirmToggleArchive(task)}" ><i class="fas fa-archive"></i></button>
                                                </template>
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-task')" v-tooltip="trans('task.edit_task')" @click.prevent="editTask(task)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-task')" :key="task.id" v-confirm="{ok: confirmDelete(task)}" v-tooltip="trans('task.delete_task')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!tasks.total" module="task" title="module_info_title" description="module_info_description" icon="tasks">
                            <div slot="btn">
                                <button class="btn btn-info btn-md" v-if="!showCreatePanel" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> {{trans('general.add_new')}}</button>
                            </div>
                        </module-info>
                        <pagination-record :page-length.sync="filterTaskForm.page_length" :records="tasks" @updateRecords="getTasks"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import taskForm from './form'
    import vSelect from 'vue-multiselect'
    import dateRangePicker from '../../components/date-range-picker'

    export default {
        components : { taskForm,vSelect,dateRangePicker },
        data() {
            return {
                tasks: {},
                filterTaskForm: {
                    number: '',
                    title: '',
                    task_category_id: [],
                    task_priority_id: [],
                    user_id: [],
                    type: '',
                    starred: '',
                    is_archived: 'unarchived',
                    start_date_start: '',
                    start_date_end: '',
                    due_date_start: '',
                    due_date_end: '',
                    completed_at_start: '',
                    completed_at_end: '',
                    is_recurring: '',
                    status: '',
                    sort_by : 'created_at',
                    order: 'desc',
                    page_length: helper.getConfig('page_length')
                },
                showCreatePanel: false,
                showFilterPanel: false,
                task_categories: [],
                selected_task_category: '',
                task_priorities: [],
                selected_task_priority: '',
                users: [],
                selected_user: '',
            };
        },
        mounted(){
            if(!helper.hasPermission('list-task')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            axios.get('/api/task/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.task_categories = response.task_categories;
                    this.task_priorities = response.task_priorities;
                    this.users = response.users;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });

            if(this.$route.path === '/task/create')
                this.showCreatePanel = true;
            this.getTasks();
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getTasks(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterTaskForm);
                axios.get('/api/task?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.tasks = response)
                    .catch(error => {
                        console.log(error);
                        helper.showDataErrorMsg(error);
                    });
            },
            editTask(task){
                this.$router.push('/task/'+task.uuid+'/edit');
            },
            confirmDelete(task){
                return dialog => this.deleteTask(task);
            },
            deleteTask(task){
                axios.delete('/api/task/'+task.uuid)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getTasks();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            confirmToggleArchive(task){
                return dialog => this.toggleArchive(task);
            },
            toggleArchive(task){
                axios.post('/api/task/'+task.uuid+'/archive')
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getTasks();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            getTaskStatus(task){
                return helper.getTaskStatus(task);
            },
            onTaskCategorySelect(selectedOption){
                this.filterTaskForm.task_category_id.push(selectedOption.id);
            },
            onTaskCategoryRemove(removedOption){
                this.filterTaskForm.task_category_id.splice(this.filterTaskForm.task_category_id.indexOf(removedOption.id), 1);
            },
            onTaskPrioritySelect(selectedOption){
                this.filterTaskForm.task_priority_id.push(selectedOption.id);
            },
            onTaskPriorityRemove(removedOption){
                this.filterTaskForm.task_priority_id.splice(this.filterTaskForm.task_priority_id.indexOf(removedOption.id), 1);
            },
            onUserSelect(selectedOption){
                this.filterTaskForm.user_id.push(selectedOption.id);
            },
            onUserRemove(removedOption){
                this.filterTaskForm.user_id.splice(this.filterTaskForm.user_id.indexOf(removedOption.id), 1);
            },
            getTaskProgress(task){
                return helper.getTaskProgress(task);
            },
            getTaskProgressColor(task){
                return helper.getTaskProgressColor(task);
            },
            getTaskProgressWidth(task){
                return 'width: '+this.getTaskProgress(task)+'%;';
            },
            getTaskNumber(task){
                return helper.getTaskNumber(task);
            },
            getRating(task){
                let user = task.user.filter(user => user.id == this.filterTaskForm.user_id[0]);
                return helper.getTaskUserRating(user[0],task);
            }
        },
        computed:{
            getRatingUser(){
                let user = this.users.filter(user => user.id == this.filterTaskForm.user_id[0]);
                return user[0].name;
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          }
        },
        watch: {
            filterTaskForm: {
                handler(val){
                    this.getTasks();
                },
                deep: true
            }
        }
    }
</script>

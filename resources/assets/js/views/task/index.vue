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
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-task')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task.add_new_task')}}</h4>
                        <task-form @completed="getTasks"></task-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8" v-if="hasPermission('list-task')">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('task.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterTaskForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group" v-show="projects">
                                        <label for="">{{trans('project.project')}}</label>
                                        <select v-model="filterTaskForm.project_id" class="custom-select col-12">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option v-for="project in projects" v-bind:value="project.id" v-bind:key="project.id">
                                            {{ project.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group" v-show="task_relevances">
                                        <label for="">{{trans('task_relevance.task_relevance')}}</label>
                                        <select v-model="filterTaskForm.task_relevance_id" class="custom-select col-12">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option v-for="task_relevance in task_relevances" v-bind:value="task_relevance.id" v-bind:key="task_relevance.id">
                                            {{ task_relevance.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>                              
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterTaskForm.sortBy">
                                            <option value="name">{{trans('task.name')}}</option>
                                            <option value="project_id">{{trans('project.project')}}</option>
                                            <option value="client_id">{{trans('client.client')}}</option>
                                            <option value="top_task_id">{{trans('task.top_task')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
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
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-info btn-sm pull-right" v-if="!showFilterPanel" @click="showFilterPanel = !showFilterPanel"><i class="fas fa-filter"></i> {{trans('general.filter')}}</button>
                        <h4 class="card-title">{{trans('task.task_list')}}</h4>
                        <h6 class="card-subtitle" v-if="tasks">{{trans('general.total_result_found',{'count' : tasks.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="tasks.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('task.name')}}</th>
                                        <th>{{trans('project.project')}}</th>
                                        <th>{{trans('task_relevance.task_relevance')}}</th>
                                        <th>{{trans('task_frequency.task_frequency')}}</th>
                                        <th>{{trans('task_complexity.task_complexity')}}</th>
                                        <th>{{trans('task_family.task_family')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="task in tasks.data" v-bind:key="task.id">
                                        <td v-text="task.name"></td>
                                        <td v-text="task.project.name"></td>
                                        <td v-text="task.task_relevance.name"></td>
                                        <td v-text="task.task_frequency.name"></td>
                                        <td v-text="task.task_complexity.name"></td>
                                        <td v-text="task.task_family.name"></td>
                                        <td>                                           
                                            <span><i class="fas fa-times"></i></span>
                                        </td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-task')" v-tooltip="trans('task.edit_task')" @click.prevent="editTask(task)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-task')" :key="task.id" v-confirm="{ok: confirmDelete(task)}" v-tooltip="trans('task.delete_task')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!tasks.total" module="task" title="module_info_title" description="module_info_description" icon="sitemap">
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

    export default {
        components : { taskForm },
        data() {
            return {
                tasks: {},
                filterTaskForm: {
                    name: '',
                    project_id: '',
                    task_relevance_id: '',
                    task_frequency_id: '',
                    task_complexity_id: '',
                    task_family_id: '',                   
                    sortBy : 'name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                projects: {},
                task_relevances: {},
                task_frequencys: {},
                task_complexitys: {},
                task_familys: {},
                showFilterPanel: false
            };
        },
        mounted(){
            if(!helper.hasPermission('list-task') && !helper.hasPermission('create-task')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
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
                    .then(response => {
                        this.tasks = response.tasks;
                        this.projects = response.projects;
                        this.task_relevances = response.task_relevances;
                        this.task_frequencys = response.task_frequencys;
                        this.task_complexitys = response.task_complexitys;
                        this.task_familys = response.task_familys;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editTask(task){
                this.$router.push('/task/'+task.id+'/edit');
            },
            confirmDelete(task){
                return dialog => this.deleteTask(task);
            },
            deleteTask(task){
                axios.delete('/api/task/'+task.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getTasks();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
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

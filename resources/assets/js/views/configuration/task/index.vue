<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('task.task')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/configuration">{{trans('configuration.configuration')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('task.task')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="task"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">

                                <div class="row">
                                    <div class="col-12 col-sm-4 col-md-4">
                                        <h4 class="card-title">{{trans('task.add_new_task_category')}}</h4>
                                        <task-category-form @completed="getTaskCategories"></task-category-form>

                                    </div>
                                    <div class="col-12 col-sm-8 col-md-8">
                                        <h4 class="card-title">{{trans('task.task_category_list')}}</h4>
                                        <h6 class="card-subtitle" v-if="task_categories">{{trans('general.total_result_found',{'count' : task_categories.total})}}</h6>
                                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                                        <div class="table-responsive">
                                            <table class="table" v-if="task_categories.total">
                                                <thead>
                                                    <tr>
                                                        <th>{{trans('task.task_category_name')}}</th>
                                                        <th>{{trans('task.task_category_description')}}</th>
                                                        <th class="table-option">{{trans('general.action')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="task_category in task_categories.data">
                                                        <td v-text="task_category.name"></td>
                                                        <td v-text="task_category.description"></td>
                                                        <td class="table-option">
                                                            <div class="btn-group">
                                                                <button class="btn btn-info btn-sm" v-tooltip="trans('task.edit_task_category')" @click.prevent="editTaskCategory(task_category)"><i class="fas fa-pencil-alt"></i></button>
                                                                <button class="btn btn-danger btn-sm" :key="`category_${task_category.id}`" v-confirm="{ok: confirmTaskCategoryDelete(task_category)}" v-tooltip="trans('task.delete_task_category')"><i class="fas fa-trash"></i></button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <pagination-record :page-length.sync="filterTaskCategoryForm.page_length" :records="task_categories" @updateRecords="getTaskCategories" @change.native="getTaskCategories"></pagination-record>
                                    </div>
                                </div>
                                <div class="row m-t-20">
                                    <div class="col-12 col-sm-4 col-md-4">
                                        <h4 class="card-title">{{trans('task.add_new_task_priority')}}</h4>
                                        <task-priority-form @completed="getTaskPriorities"></task-priority-form>

                                    </div>
                                    <div class="col-12 col-sm-8 col-md-8">
                                        <h4 class="card-title">{{trans('task.task_priority_list')}}</h4>
                                        <h6 class="card-subtitle" v-if="task_priorities">{{trans('general.total_result_found',{'count' : task_priorities.total})}}</h6>
                                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                                        <div class="table-responsive">
                                            <table class="table" v-if="task_priorities.total">
                                                <thead>
                                                    <tr>
                                                        <th>{{trans('task.task_priority_name')}}</th>
                                                        <th>{{trans('task.task_priority_description')}}</th>
                                                        <th class="table-option">{{trans('general.action')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="task_priority in task_priorities.data">
                                                        <td v-text="task_priority.name"></td>
                                                        <td v-text="task_priority.description"></td>
                                                        <td class="table-option">
                                                            <div class="btn-group">
                                                                <button class="btn btn-info btn-sm" v-tooltip="trans('task.edit_task_priority')" @click.prevent="editTaskPriority(task_priority)"><i class="fas fa-pencil-alt"></i></button>
                                                                <button class="btn btn-danger btn-sm" :key="`priority_${task_priority.id}`" v-confirm="{ok: confirmTaskPriorityDelete(task_priority)}" v-tooltip="trans('task.delete_task_priority')"><i class="fas fa-trash"></i></button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <pagination-record :page-length.sync="filterTaskPriorityForm.page_length" :records="task_priorities" @updateRecords="getTaskPriorities"></pagination-record>
                                    </div>
                                </div>
                                <div class="row m-t-20">
                                    <div class="col-12">
                                        <h4 class="card-title">{{trans('task.configuration')}}</h4>
                                        <form @submit.prevent="saveTaskConfiguration" @keydown="taskConfigForm.errors.clear($event.target.name)">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('task.prefix')}} <show-tip module="task" tip="tip_task_number_prefix" type="field"></show-tip> </label>
                                                        <input class="form-control" type="text" value="" v-model="taskConfigForm.task_number_prefix" name="task_number_prefix" :placeholder="trans('task.prefix')">
                                                        <show-error :form-name="taskConfigForm" prop-name="task_number_prefix"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('task.digit')}} <show-tip module="task" tip="tip_task_number_digit" type="field"></show-tip></label>
                                                        <input class="form-control" type="text" value="" v-model="taskConfigForm.task_number_digit" name="task_number_digit" :placeholder="trans('task.digit')">
                                                        <show-error :form-name="taskConfigForm" prop-name="task_number_digit"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('task.default_progress_type')}} <show-tip module="task" tip="tip_task_progress_type" type="field"></show-tip></label>
                                                        <div class="radio radio-info">
                                                            <input type="radio" value="manual" id="progress_type_manual" v-model="taskConfigForm.task_progress_type" :checked="taskConfigForm.task_progress_type === 'manual'" name="task_progress_type">
                                                            <label for="progress_type_manual"> {{trans('task.manual_progress')}} </label>
                                                        </div>
                                                        <div class="radio radio-info">
                                                            <input type="radio" value="sub_task" id="progress_type_sub_task" v-model="taskConfigForm.task_progress_type" :checked="taskConfigForm.task_progress_type === 'sub_task'" name="task_progress_type">
                                                            <label for="progress_type_sub_task"> {{trans('task.sub_task_progress')}} </label>
                                                        </div>
                                                        <div class="radio radio-info">
                                                            <input type="radio" value="question" id="progress_type_question" v-model="taskConfigForm.task_progress_type" :checked="taskConfigForm.task_progress_type === 'question'" name="task_progress_type">
                                                            <label for="progress_type_question"> {{trans('task.question')}} </label>
                                                        </div>
                                                        <show-error :form-name="taskConfigForm" prop-name="task_progress_type"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('task.default_rating_type')}} <show-tip module="task" tip="tip_task_rating_type" type="field"></show-tip></label>
                                                        <div class="radio radio-info">
                                                            <input type="radio" value="task_based" id="rating_type_task_based" v-model="taskConfigForm.task_rating_type" :checked="taskConfigForm.task_rating_type === 'task_based'" name="task_rating_type">
                                                            <label for="rating_type_task_based"> {{trans('task.task_based_rating')}} </label>
                                                        </div>
                                                        <div class="radio radio-info">
                                                            <input type="radio" value="sub_task_based" id="rating_type_sub_task_based" v-model="taskConfigForm.task_rating_type" :checked="taskConfigForm.task_rating_type === 'sub_task_based'" name="task_rating_type">
                                                            <label for="rating_type_sub_task_based"> {{trans('task.sub_task_based_rating')}} </label>
                                                        </div>
                                                        <show-error :form-name="taskConfigForm" prop-name="task_rating_type"></show-error>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-info waves-effect waves-light">{{trans('general.save')}}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import configurationSidebar from '../system-config-sidebar'
    import taskCategoryForm from './form-task-category'
    import taskPriorityForm from './form-task-priority'

    export default {
        components : { configurationSidebar,taskCategoryForm,taskPriorityForm },
        data() {
            return {
                task_categories: {},
                filterTaskCategoryForm: {
                    page_length: helper.getConfig('page_length')
                },
                task_priorities: {},
                filterTaskPriorityForm: {
                    page_length: helper.getConfig('page_length')
                },
                taskConfigForm: new Form({
                    task_progress_type: '',
                    task_rating_type: '',
                    task_number_prefix: '',
                    task_number_digit: '',
                    config_type: ''
                },false)
            };
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getTaskCategories();
            this.getTaskPriorities();

            this.taskConfigForm.task_progress_type = helper.getConfig('task_progress_type');
            this.taskConfigForm.task_rating_type = helper.getConfig('task_rating_type');
            this.taskConfigForm.task_number_prefix = helper.getConfig('task_number_prefix');
            this.taskConfigForm.task_number_digit = helper.getConfig('task_number_digit');
        },
        methods: {
            getTaskCategories(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterTaskCategoryForm);
                axios.get('/api/task-category?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.task_categories = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editTaskCategory(task_category){
                this.$router.push('/configuration/task-category/'+task_category.id+'/edit');
            },
            confirmTaskCategoryDelete(task_category){
                return dialog => this.deleteTaskCategory(task_category);
            },
            deleteTaskCategory(task_category){
                axios.delete('/api/task-category/'+task_category.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getTaskCategories();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getTaskPriorities(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterTaskPriorityForm);
                axios.get('/api/task-priority?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.task_priorities = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editTaskPriority(task_priority){
                this.$router.push('/configuration/task-priority/'+task_priority.id+'/edit');
            },
            confirmTaskPriorityDelete(task_priority){
                return dialog => this.deleteTaskPriority(task_priority);
            },
            deleteTaskPriority(task_priority){
                axios.delete('/api/task-priority/'+task_priority.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getTaskPriorities();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            saveTaskConfiguration(){
                this.taskConfigForm.config_type = 'task';
                this.taskConfigForm.post('/api/configuration')
                    .then(response => {
                        this.$store.dispatch('setConfig',this.taskConfigForm);
                        toastr.success(response.message);
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
        }
    }
</script>
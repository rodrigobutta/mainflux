<template>
    <form @submit.prevent="proceed" @keydown="taskForm.errors.clear($event.target.name)">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="">{{trans('task.title')}}</label>
                    <input class="form-control" type="text" value="" v-model="taskForm.title" name="title" :placeholder="trans('task.title')">
                    <show-error :form-name="taskForm" prop-name="title"></show-error>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('task.task_category')}}</label>
                            <v-select label="name" v-model="selected_task_category" name="task_category_id" id="task_category_id" :options="task_categories" :placeholder="trans('task.select_task_category')" @select="onTaskCategorySelect" @close="taskForm.errors.clear('task_category_id')" @remove="taskForm.task_category_id = ''"></v-select>
                            <show-error :form-name="taskForm" prop-name="task_category_id"></show-error>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('task.task_priority')}}</label>
                            <v-select label="name" v-model="selected_task_priority" name="task_priority_id" id="task_priority_id" :options="task_priorities" :placeholder="trans('task.select_task_priority')" @select="onTaskPrioritySelect" @close="taskForm.errors.clear('task_priority_id')" @remove="taskForm.task_priority_id = ''"></v-select>
                            <show-error :form-name="taskForm" prop-name="task_priority_id"></show-error>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('task.client')}}</label>
                            <v-select label="name" v-model="selected_client" name="client_id" id="client_id" :options="clients" :placeholder="trans('task.select_client')" @select="onClientSelect" @close="taskForm.errors.clear('client_id')" @remove="taskForm.client_id = ''"></v-select>
                            <show-error :form-name="taskForm" prop-name="client_id"></show-error>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('task.contractor')}}</label>
                            <v-select label="name" v-model="selected_contractor" name="contractor_id" id="contractor_id" :options="contractors" :placeholder="trans('task.select_contractor')" @select="onContractorSelect" @close="taskForm.errors.clear('contractor_id')" @remove="taskForm.contractor_id = ''"></v-select>
                            <show-error :form-name="taskForm" prop-name="contractor_id"></show-error>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="form-group">
                            <label for="">{{trans('task.project')}}</label>
                            <v-select label="name" v-model="selected_project" name="project_id" id="project_id" :options="projects" :placeholder="trans('task.select_project')" @select="onProjectSelect" @close="taskForm.errors.clear('project_id')" @remove="taskForm.project_id = ''"></v-select>
                            <show-error :form-name="taskForm" prop-name="project_id"></show-error>
                        </div>
                    </div>                   
                </div>
                <div class="form-group">
                    <label for="">{{trans('task.question_set')}}</label>
                    <v-select label="name" v-model="selected_question_set" name="question_set_id" id="question_set_id" :options="question_sets" :placeholder="trans('task.select_question_set')" @select="onQuestionSetSelect" @close="taskForm.errors.clear('question_set_id')" @remove="taskForm.question_set_id = ''"></v-select>
                    <show-error :form-name="taskForm" prop-name="question_set_id"></show-error>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('task.start_date')}} <show-tip module="task" tip="tip_task_start_date" type="field"></show-tip> </label>
                            <datepicker v-model="taskForm.start_date" :bootstrapStyling="true" @selected="taskForm.errors.clear('start_date')" :placeholder="trans('task.start_date')"></datepicker>
                            <show-error :form-name="taskForm" prop-name="start_date"></show-error>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('task.due_date')}} <show-tip module="task" tip="tip_task_due_date" type="field"></show-tip></label>
                            <datepicker v-model="taskForm.due_date" :bootstrapStyling="true" @selected="taskForm.errors.clear('due_date')" :placeholder="trans('task.due_date')"></datepicker>
                            <show-error :form-name="taskForm" prop-name="due_date"></show-error>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">{{trans('user.user')}} <show-tip module="task" tip="tip_assigned_user" type="field"></show-tip></label>
                    <v-select label="name" track-by="id" v-model="selected_users" name="user_id" id="user_id" :options="users" :placeholder="trans('user.select_user')" @select="onUserSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onUserRemove" :selected="selected_users">
                    </v-select>
                    <show-error :form-name="taskForm" prop-name="user_id"></show-error>
                </div>
                <div class="form-group">
                    <file-upload-input :button-text="trans('general.upload_document')" :token="taskForm.upload_token" module="task" :clear-file="clearTaskAttachment" :module-id="module_id || ''"></file-upload-input>
                </div>
                <div class="form-group" v-if="!uuid">
                    <switches class="m-l-20" v-model="taskForm.send_task_assign_email" theme="bootstrap" color="success"></switches> {{trans('task.send_task_assign_email')}} <show-tip module="task" tip="tip_send_task_assign_email" type="field"></show-tip>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <html-editor name="description" :model.sync="taskForm.description" :isUpdate="uuid ? true : false" @clearErrors="taskForm.errors.clear('description')"></html-editor>
                <show-error :form-name="taskForm" prop-name="description"></show-error>
            </div>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="uuid">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/task" class="btn btn-danger waves-effect waves-light" v-show="uuid">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    import uuid from 'uuid/v4'
    import htmlEditor from '../../components/html-editor'
    import fileUploadInput from '../../components/file-upload-input'
    import datepicker from 'vuejs-datepicker'
    import vSelect from 'vue-multiselect'
    import switches from 'vue-switches'

    export default {
        components: {datepicker,vSelect,htmlEditor,fileUploadInput,switches},
        data() {
            return {
                taskForm: new Form({
                    title : '',
                    description : '',
                    task_category_id: '',
                    task_priority_id: '',
                    client_id: '',
                    contractor_id: '',
                    project_id: '',
                    question_set_id: '',
                    start_date: '',
                    due_date: '',
                    user_id: [],
                    send_task_assign_email: 0,
                    upload_token: ''
                }),
                module_id: '',
                users: [],
                clients: [],
                contractors: [],
                projects: [],
                selected_users: '',
                task_categories: [],
                question_sets: [],
                selected_task_category: null,
                selected_question_set: null,
                selected_client: null,
                selected_contractor: null,
                selected_project: null,
                task_priorities: [],
                selected_task_priority: null,
                clearTaskAttachment: false
            };
        },
        props: ['uuid'],
        mounted() {
            if(!this.uuid)
            this.taskForm.upload_token = uuid();
            if(this.uuid)
                this.getTask();
            axios.get('/api/task/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.users = response.users;
                    this.task_categories = response.task_categories;
                    this.task_priorities = response.task_priorities;
                    this.question_sets = response.question_sets;
                    this.clients = response.clients;
                    this.contractors = response.contractors;
                    this.projects = response.projects;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            proceed(){
                if(this.uuid)
                    this.updateTask();
                else
                    this.storeTask();
            },
            storeTask(){
                this.taskForm.start_date = moment(this.taskForm.start_date).format('YYYY-MM-DD');
                this.taskForm.due_date = moment(this.taskForm.due_date).format('YYYY-MM-DD');
                this.taskForm.post('/api/task')
                    .then(response => {
                        toastr.success(response.message);
                        this.taskForm.description = '';
                        this.taskForm.upload_token = uuid();
                        this.clearTaskAttachment = true;
                        this.selected_task_priority = null;
                        this.selected_task_category = null;
                        this.selected_question_set = null;
                        this.selected_client = null;
                        this.selected_contractor = null;
                        this.selected_project = null;
                        this.selected_users = null;
                        this.taskForm.user_id = [];
                        this.$emit('completed');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getTask(){
                axios.get('/api/task/'+this.uuid)
                    .then(response => response.data)
                    .then(response => {
                        this.taskForm.title = response.task.title;
                        this.taskForm.description = response.task.description;
                        this.taskForm.start_date = response.task.start_date;
                        this.taskForm.due_date = response.task.due_date;
                        this.taskForm.upload_token = response.task.upload_token;
                        this.taskForm.task_category_id = response.task.task_category_id;
                        this.taskForm.task_priority_id = response.task.task_priority_id;
                        this.taskForm.question_set_id = response.task.question_set_id;
                        this.taskForm.client_id = response.task.client_id;
                        this.taskForm.contractor_id = response.task.contractor_id;
                        this.taskForm.project_id = response.task.project_id;
                        this.taskForm.user_id = response.user_id;
                        this.selected_users = response.selected_users;
                        this.selected_task_priority = response.selected_task_priority;
                        this.selected_task_category = response.selected_task_category;
                        this.selected_client = response.selected_client;
                        this.selected_contractor = response.selected_contractor;
                        this.selected_project = response.selected_project;
                        this.selected_question_set = response.selected_question_set;
                        this.module_id = response.task.id;
                    })
                    .catch(error => {
                        this.$router.push('/task');
                    });
            },
            updateTask(){
                this.taskForm.start_date = moment(this.taskForm.start_date).format('YYYY-MM-DD');
                this.taskForm.due_date = moment(this.taskForm.due_date).format('YYYY-MM-DD');
                this.taskForm.patch('/api/task/'+this.uuid)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/task');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            onUserSelect(selectedOption){
                this.taskForm.errors.clear('user_id');
                this.taskForm.user_id.push(selectedOption.id);
            },
            onUserRemove(removedOption){
                this.taskForm.user_id.splice(this.taskForm.user_id.indexOf(removedOption.id), 1);
            },
            onTaskCategorySelect(selectedOption){
                this.taskForm.task_category_id = selectedOption.id;
            },
            onTaskPrioritySelect(selectedOption){
                this.taskForm.task_priority_id = selectedOption.id;
            },
            onQuestionSetSelect(selectedOption){
                this.taskForm.question_set_id = selectedOption.id;
            },
            onClientSelect(selectedOption){
                this.taskForm.client_id = selectedOption.id;
            },
            onContractorSelect(selectedOption){
                this.taskForm.contractor_id = selectedOption.id;
            },
            onProjectSelect(selectedOption){
                this.taskForm.project_id = selectedOption.id;
            }
        }
    }
</script>

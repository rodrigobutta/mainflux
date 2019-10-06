<template>
    <form @submit.prevent="proceed" @keydown="jobForm.errors.clear($event.target.name)">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="">{{trans('job.title')}}</label>
                    <input class="form-control" type="text" value="" v-model="jobForm.title" name="title" :placeholder="trans('job.title')">
                    <show-error :form-name="jobForm" prop-name="title"></show-error>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('job.job_category')}}</label>
                            <v-select label="name" v-model="selected_job_category" name="job_category_id" id="job_category_id" :options="job_categories" :placeholder="trans('job.select_job_category')" @select="onJobCategorySelect" @close="jobForm.errors.clear('job_category_id')" @remove="jobForm.job_category_id = ''"></v-select>
                            <show-error :form-name="jobForm" prop-name="job_category_id"></show-error>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('job.job_priority')}}</label>
                            <v-select label="name" v-model="selected_job_priority" name="job_priority_id" id="job_priority_id" :options="job_priorities" :placeholder="trans('job.select_job_priority')" @select="onJobPrioritySelect" @close="jobForm.errors.clear('job_priority_id')" @remove="jobForm.job_priority_id = ''"></v-select>
                            <show-error :form-name="jobForm" prop-name="job_priority_id"></show-error>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('job.client')}} <sup>{{trans('general.admin_only')}}</sup></label>
                            <v-select label="name" v-model="selected_client" name="client_id" id="client_id" :options="clients" :placeholder="trans('job.select_client')" @select="onClientSelect" @close="jobForm.errors.clear('client_id')" @remove="jobForm.client_id = ''"></v-select>
                            <show-error :form-name="jobForm" prop-name="client_id"></show-error>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('job.contractor')}} <sup>{{trans('general.admin_only')}}</sup></label>
                            <v-select label="name" v-model="selected_contractor" name="contractor_id" id="contractor_id" :options="contractors" :placeholder="trans('job.select_contractor')" @select="onContractorSelect" @close="jobForm.errors.clear('contractor_id')" @remove="jobForm.contractor_id = ''"></v-select>
                            <show-error :form-name="jobForm" prop-name="contractor_id"></show-error>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="form-group">
                            <label for="">{{trans('job.project')}}</label>
                            <v-select label="name" v-model="selected_project" name="project_id" id="project_id" :options="projects" :placeholder="trans('job.select_project')" @select="onProjectSelect" @close="jobForm.errors.clear('project_id')" @remove="jobForm.project_id = ''"></v-select>
                            <show-error :form-name="jobForm" prop-name="project_id"></show-error>
                        </div>
                    </div>                   
                </div>
                <div class="form-group">
                    <label for="">{{trans('job.question_set')}}</label>
                    <v-select label="name" v-model="selected_question_set" name="question_set_id" id="question_set_id" :options="question_sets" :placeholder="trans('job.select_question_set')" @select="onQuestionSetSelect" @close="jobForm.errors.clear('question_set_id')" @remove="jobForm.question_set_id = ''"></v-select>
                    <show-error :form-name="jobForm" prop-name="question_set_id"></show-error>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('job.start_date')}} <show-tip module="job" tip="tip_job_start_date" type="field"></show-tip> </label>
                            <datepicker v-model="jobForm.start_date" :bootstrapStyling="true" @selected="jobForm.errors.clear('start_date')" :placeholder="trans('job.start_date')"></datepicker>
                            <show-error :form-name="jobForm" prop-name="start_date"></show-error>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('job.due_date')}} <show-tip module="job" tip="tip_job_due_date" type="field"></show-tip></label>
                            <datepicker v-model="jobForm.due_date" :bootstrapStyling="true" @selected="jobForm.errors.clear('due_date')" :placeholder="trans('job.due_date')"></datepicker>
                            <show-error :form-name="jobForm" prop-name="due_date"></show-error>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">{{trans('user.user')}} <show-tip module="job" tip="tip_assigned_user" type="field"></show-tip></label>
                    <v-select label="name" track-by="id" v-model="selected_users" name="user_id" id="user_id" :options="users" :placeholder="trans('user.select_user')" @select="onUserSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onUserRemove" :selected="selected_users">
                    </v-select>
                    <show-error :form-name="jobForm" prop-name="user_id"></show-error>
                </div>
                <div class="form-group">
                    <file-upload-input :button-text="trans('general.upload_document')" :token="jobForm.upload_token" module="job" :clear-file="clearJobAttachment" :module-id="module_id || ''"></file-upload-input>
                </div>
                <div class="form-group" v-if="!uuid">
                    <switches class="m-l-20" v-model="jobForm.send_job_assign_email" theme="bootstrap" color="success"></switches> {{trans('job.send_job_assign_email')}} <show-tip module="job" tip="tip_send_job_assign_email" type="field"></show-tip>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <html-editor name="description" :model.sync="jobForm.description" :isUpdate="uuid ? true : false" @clearErrors="jobForm.errors.clear('description')"></html-editor>
                <show-error :form-name="jobForm" prop-name="description"></show-error>
            </div>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="uuid">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/job" class="btn btn-danger waves-effect waves-light" v-show="uuid">{{trans('general.cancel')}}</router-link>
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
                jobForm: new Form({
                    title : '',
                    description : '',
                    job_category_id: '',
                    job_priority_id: '',
                    client_id: '',
                    contractor_id: '',
                    project_id: '',
                    question_set_id: '',
                    start_date: '',
                    due_date: '',
                    user_id: [],
                    send_job_assign_email: 0,
                    upload_token: ''
                }),
                module_id: '',
                users: [],
                clients: [],
                contractors: [],
                projects: [],
                selected_users: '',
                job_categories: [],
                question_sets: [],
                selected_job_category: null,
                selected_question_set: null,
                selected_client: null,
                selected_contractor: null,
                selected_project: null,
                job_priorities: [],
                selected_job_priority: null,
                clearJobAttachment: false
            };
        },
        props: ['uuid'],
        mounted() {
            if(!this.uuid)
            this.jobForm.upload_token = uuid();
            if(this.uuid)
                this.getJob();
            axios.get('/api/job/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.users = response.users;
                    this.job_categories = response.job_categories;
                    this.job_priorities = response.job_priorities;
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
                    this.updateJob();
                else
                    this.storeJob();
            },
            storeJob(){
                this.jobForm.start_date = moment(this.jobForm.start_date).format('YYYY-MM-DD');
                this.jobForm.due_date = moment(this.jobForm.due_date).format('YYYY-MM-DD');
                this.jobForm.post('/api/job')
                    .then(response => {
                        toastr.success(response.message);
                        this.jobForm.description = '';
                        this.jobForm.upload_token = uuid();
                        this.clearJobAttachment = true;
                        this.selected_job_priority = null;
                        this.selected_job_category = null;
                        this.selected_question_set = null;
                        this.selected_client = null;
                        this.selected_contractor = null;
                        this.selected_project = null;
                        this.selected_users = null;
                        this.jobForm.user_id = [];
                        this.$emit('completed');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getJob(){
                axios.get('/api/job/'+this.uuid)
                    .then(response => response.data)
                    .then(response => {
                        this.jobForm.title = response.job.title;
                        this.jobForm.description = response.job.description;
                        this.jobForm.start_date = response.job.start_date;
                        this.jobForm.due_date = response.job.due_date;
                        this.jobForm.upload_token = response.job.upload_token;
                        this.jobForm.job_category_id = response.job.job_category_id;
                        this.jobForm.job_priority_id = response.job.job_priority_id;
                        this.jobForm.question_set_id = response.job.question_set_id;
                        this.jobForm.client_id = response.job.client_id;
                        this.jobForm.contractor_id = response.job.contractor_id;
                        this.jobForm.project_id = response.job.project_id;
                        this.jobForm.user_id = response.user_id;
                        this.selected_users = response.selected_users;
                        this.selected_job_priority = response.selected_job_priority;
                        this.selected_job_category = response.selected_job_category;
                        this.selected_client = response.selected_client;
                        this.selected_contractor = response.selected_contractor;
                        this.selected_project = response.selected_project;
                        this.selected_question_set = response.selected_question_set;
                        this.module_id = response.job.id;
                    })
                    .catch(error => {
                        this.$router.push('/job');
                    });
            },
            updateJob(){
                this.jobForm.start_date = moment(this.jobForm.start_date).format('YYYY-MM-DD');
                this.jobForm.due_date = moment(this.jobForm.due_date).format('YYYY-MM-DD');
                this.jobForm.patch('/api/job/'+this.uuid)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/job');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            onUserSelect(selectedOption){
                this.jobForm.errors.clear('user_id');
                this.jobForm.user_id.push(selectedOption.id);
            },
            onUserRemove(removedOption){
                this.jobForm.user_id.splice(this.jobForm.user_id.indexOf(removedOption.id), 1);
            },
            onJobCategorySelect(selectedOption){
                this.jobForm.job_category_id = selectedOption.id;
            },
            onJobPrioritySelect(selectedOption){
                this.jobForm.job_priority_id = selectedOption.id;
            },
            onQuestionSetSelect(selectedOption){
                this.jobForm.question_set_id = selectedOption.id;
            },
            onClientSelect(selectedOption){
                this.jobForm.client_id = selectedOption.id;
            },
            onContractorSelect(selectedOption){
                this.jobForm.contractor_id = selectedOption.id;
            },
            onProjectSelect(selectedOption){
                this.jobForm.project_id = selectedOption.id;
            }
        }
    }
</script>

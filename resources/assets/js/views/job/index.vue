<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('job.job')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('job.job')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <transition name="fade" v-if="hasPermission('create-job')">
                    <div class="card" v-if="showCreatePanel">
                        <div class="card-body">
                            <button class="btn btn-info btn-sm pull-right" v-if="showCreatePanel" @click="showCreatePanel = !showCreatePanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('job.add_new_job')}}</h4>
                            <job-form @completed="getJobs"></job-form>
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
                                        <label for="">{{trans('job.number')}}</label>
                                        <input class="form-control" name="title" v-model="filterJobForm.number">
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('job.title')}}</label>
                                        <input class="form-control" name="title" v-model="filterJobForm.title">
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('job.job_category')}}</label>
                                        <v-select label="name" track-by="id" v-model="selected_job_category" name="job_category_id" id="job_category_id" :options="job_categories" :placeholder="trans('job.select_job_category')" @select="onJobCategorySelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onJobCategoryRemove">
                                        </v-select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('job.job_priority')}}</label>
                                        <v-select label="name" track-by="id" v-model="selected_job_priority" name="job_priority_id" id="job_priority_id" :options="job_priorities" :placeholder="trans('job.select_job_priority')" @select="onJobPrioritySelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onJobPriorityRemove">
                                        </v-select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('job.client')}}</label>
                                        <v-select label="name" track-by="id" v-model="selected_client" name="client_id" id="client_id" :options="clients" :placeholder="trans('job.select_client')" @select="onClientSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onClientRemove">
                                        </v-select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('job.contractor')}}</label>
                                        <v-select label="name" track-by="id" v-model="selected_contractor" name="contractor_id" id="contractor_id" :options="contractors" :placeholder="trans('job.select_contractor')" @select="onContractorSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onContractorRemove">
                                        </v-select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('job.project')}}</label>
                                        <v-select label="name" track-by="id" v-model="selected_project" name="project_id" id="project_id" :options="projects" :placeholder="trans('job.select_project')" @select="onProjectSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onProjectRemove">
                                        </v-select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">{{trans('job.assigned_user')}}</label>
                                        <v-select label="name" track-by="id" v-model="selected_user" name="user_id" id="user_id" :options="users" :placeholder="trans('user.select_user')" @select="onUserSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onUserRemove">
                                        </v-select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">{{trans('job.type')}}</label>
                                                <select name="type" class="form-control" v-model="filterJobForm.type" :placeholder="trans('general.select_one')">
                                                    <option value="">{{trans('general.select_one')}}</option>
                                                    <option value="owned">{{trans('job.owned')}}</option>
                                                    <option value="assigned">{{trans('job.assigned')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">{{trans('job.starred')}}</label>
                                                <select name="starred" class="form-control" v-model="filterJobForm.starred" :placeholder="trans('general.select_one')">
                                                    <option value="">{{trans('general.select_one')}}</option>
                                                    <option value="starred">{{trans('job.starred')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">{{trans('job.archive')}}</label>
                                                <select name="is_archived" class="form-control" v-model="filterJobForm.is_archived" :placeholder="trans('general.select_one')">
                                                    <option value="">{{trans('general.select_one')}}</option>
                                                    <option value="archived">{{trans('job.archived')}}</option>
                                                    <option value="unarchived">{{trans('job.unarchived')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterJobForm.start_date_start" :end-date.sync="filterJobForm.start_date_end" :label="trans('job.start_date')"></date-range-picker>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterJobForm.due_date_start" :end-date.sync="filterJobForm.due_date_end" :label="trans('job.due_date')"></date-range-picker>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterJobForm.completed_at_start" :end-date.sync="filterJobForm.completed_at_end" :label="trans('job.completed_at')"></date-range-picker>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('job.status')}}</label>
                                        <select name="status" class="form-control" v-model="filterJobForm.status" :placeholder="trans('general.select_one')">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option value="unassigned">{{trans('job.unassigned')}}</option>
                                            <option value="requested">{{trans('job.job_sign_off_status',{status:trans('job.requested')})}}</option>
                                            <option value="rejected">{{trans('job.job_sign_off_status',{status:trans('job.rejected')})}}</option>
                                            <option value="cancelled">{{trans('job.job_sign_off_status',{status:trans('job.cancelled')})}}</option>
                                            <option value="pending">{{trans('job.pending')}}</option>
                                            <option value="approved">{{trans('job.completed')}}</option>
                                            <option value="overdue">{{trans('job.overdue')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('job.recurring')}}</label>
                                        <select name="is_recurring" class="form-control" v-model="filterJobForm.is_recurring" :placeholder="trans('general.select_one')">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option value="1">{{trans('job.recurring')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="sort_by" class="form-control" v-model="filterJobForm.sort_by">
                                            <option value="title">{{trans('job.title')}}</option>
                                            <option value="job_category_id">{{trans('job.job_category')}}</option>
                                            <option value="job_priority_id">{{trans('job.job_priority')}}</option>
                                            <option value="start_date">{{trans('job.start_date')}}</option>
                                            <option value="due_date">{{trans('job.due_date')}}</option>
                                            <option value="completed_at">{{trans('job.completed_at')}}</option>
                                            <option value="created_at">{{trans('job.created_at')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterJobForm.order">
                                            <option value="asc">{{trans('general.ascending')}}</option>
                                            <option value="desc">{{trans('general.descending')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
                <div class="card" v-if="hasPermission('list-job')">
                    <div class="card-body">
                        <button class="btn btn-info btn-sm pull-right" v-if="!showFilterPanel" @click="showFilterPanel = !showFilterPanel"><i class="fas fa-filter"></i> {{trans('general.filter')}}</button>
                        <button class="btn btn-info btn-sm pull-right m-r-5" v-if="jobs.total && !showCreatePanel" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> {{trans('general.add_new')}}</button>
                        <h4 class="card-title">{{trans('job.job_list')}} <span v-if="filterJobForm.user_id.length == 1">{{trans('general.of')+' '+getRatingUser}}</span></h4>
                        <h6 class="card-subtitle" v-if="jobs">{{trans('general.total_result_found',{'count' : jobs.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="jobs.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('job.number')}}</th>
                                        <th>{{trans('job.project')}}</th>
                                        <th>{{trans('job.title')}}</th>
                                        <th>{{trans('job.category')}}</th>
                                        <th>{{trans('job.priority')}}</th>
                                        <th>{{trans('job.start_date')}}</th>
                                        <th>{{trans('job.due_date')}}</th>
                                        <th>{{trans('job.progress')}}</th>
                                        <th>{{trans('job.status')}}</th>
                                        <th v-if="filterJobForm.user_id.length == 1">{{trans('job.rating')}}</th>
                                        <th>{{trans('job.owner')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="job in jobs.data" v-bind:key="job.id">
                                        <td v-text="getJobNumber(job)"></td>
                                        <td><span v-if="job.project">{{ job.project.name }}</span></td>
                                        <td v-text="job.title"></td>
                                        <td v-text="job.job_category.name"></td>
                                        <td v-text="job.job_priority.name"></td>
                                        <td>{{ job.start_date | moment}}</td>
                                        <td>{{ job.due_date | moment}}</td>
                                        <td>
                                            <div class="progress" style="height: 10px;">
                                                <div :class="getJobProgressColor(job)" role="progressbar" :style="getJobProgressWidth(job)" :aria-valuenow="getJobProgress(job)" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            {{ getJobProgress(job) }} %
                                        </td>
                                        <td>
                                            <span v-for="status in getJobStatus(job)" :class="['label','label-'+status.color]" style="margin-right:5px;" v-bind:key="status.label">{{status.label}}</span>
                                        </td>
                                        <td v-if="filterJobForm.user_id.length == 1" v-html="getRating(job)"></td>
                                        <td>{{ job.user_added.profile.first_name+' '+job.user_added.profile.last_name}}</td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <router-link :to="`/job/${job.uuid}`" class="btn btn-success btn-sm" v-tooltip="trans('job.view_job')"><i class="fas fa-arrow-circle-right"></i></router-link>
                                                <template v-if="job.sign_off_status === 'approved' && job.user_id == getAuthUser('id')">
                                                    <button class="btn btn-warning btn-sm" v-if="!job.is_archived" v-tooltip="trans('job.move_to_archive')" :key="`archive_${job.id}`" v-confirm="{ok: confirmToggleArchive(job)}" ><i class="fas fa-archive"></i></button>
                                                    <button class="btn btn-warning btn-sm" v-if="job.is_archived" v-tooltip="trans('job.remove_from_archive')" :key="`unarchive_${job.id}`" v-confirm="{ok: confirmToggleArchive(job)}" ><i class="fas fa-archive"></i></button>
                                                </template>
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-job')" v-tooltip="trans('job.edit_job')" @click.prevent="editJob(job)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-job')" :key="job.id" v-confirm="{ok: confirmDelete(job)}" v-tooltip="trans('job.delete_job')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!jobs.total" module="job" title="module_info_title" description="module_info_description" icon="jobs">
                            <div slot="btn">
                                <button class="btn btn-info btn-md" v-if="!showCreatePanel" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> {{trans('general.add_new')}}</button>
                            </div>
                        </module-info>
                        <pagination-record :page-length.sync="filterJobForm.page_length" :records="jobs" @updateRecords="getJobs"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import jobForm from './form'
    import vSelect from 'vue-multiselect'
    import dateRangePicker from '../../components/date-range-picker'

    export default {
        components : { jobForm,vSelect,dateRangePicker },
        data() {
            return {
                jobs: {},
                filterJobForm: {
                    number: '',
                    title: '',
                    job_category_id: [],
                    job_priority_id: [],
                    client_id: [],
                    contractor_id: [],
                    project_id: [],
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
                job_categories: [],
                selected_job_category: '',
                selected_client: '',
                selected_contractor: '',
                selected_project: '',
                job_priorities: [],
                clients: [],
                contractors: [],
                projects: [],
                selected_job_priority: '',
                users: [],
                selected_user: '',
            };
        },
        mounted(){
            if(!helper.hasPermission('list-job')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            axios.get('/api/job/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.job_categories = response.job_categories;
                    this.job_priorities = response.job_priorities;
                    this.clients = response.clients;
                    this.contractors = response.contractors;
                    this.projects = response.projects;
                    this.users = response.users;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });

            if(this.$route.path === '/job/create')
                this.showCreatePanel = true;
            this.getJobs();
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getJobs(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterJobForm);
                axios.get('/api/job?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.jobs = response)
                    .catch(error => {
                        console.log(error);
                        helper.showDataErrorMsg(error);
                    });
            },
            editJob(job){
                this.$router.push('/job/'+job.uuid+'/edit');
            },
            confirmDelete(job){
                return dialog => this.deleteJob(job);
            },
            deleteJob(job){
                axios.delete('/api/job/'+job.uuid)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getJobs();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            confirmToggleArchive(job){
                return dialog => this.toggleArchive(job);
            },
            toggleArchive(job){
                axios.post('/api/job/'+job.uuid+'/archive')
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getJobs();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            getJobStatus(job){
                return helper.getJobStatus(job);
            },
            onJobCategorySelect(selectedOption){
                this.filterJobForm.job_category_id.push(selectedOption.id);
            },
            onJobCategoryRemove(removedOption){
                this.filterJobForm.job_category_id.splice(this.filterJobForm.job_category_id.indexOf(removedOption.id), 1);
            },
            onJobPrioritySelect(selectedOption){
                this.filterJobForm.job_priority_id.push(selectedOption.id);
            },
            onJobPriorityRemove(removedOption){
                this.filterJobForm.job_priority_id.splice(this.filterJobForm.job_priority_id.indexOf(removedOption.id), 1);
            },
            onUserSelect(selectedOption){
                this.filterJobForm.user_id.push(selectedOption.id);
            },
            onUserRemove(removedOption){
                this.filterJobForm.user_id.splice(this.filterJobForm.user_id.indexOf(removedOption.id), 1);
            },
            getJobProgress(job){
                return helper.getJobProgress(job);
            },
            getJobProgressColor(job){
                return helper.getJobProgressColor(job);
            },
            getJobProgressWidth(job){
                return 'width: '+this.getJobProgress(job)+'%;';
            },
            getJobNumber(job){
                return helper.getJobNumber(job);
            },
            getRating(job){
                let user = job.user.filter(user => user.id == this.filterJobForm.user_id[0]);
                return helper.getJobUserRating(user[0],job);
            },
            onClientSelect(selectedOption){
                this.filterJobForm.client_id.push(selectedOption.id);
            },
            onClientRemove(removedOption){
                this.filterJobForm.client_id.splice(this.filterJobForm.client_id.indexOf(removedOption.id), 1);
            },
            onContractorSelect(selectedOption){
                this.filterJobForm.contractor_id.push(selectedOption.id);
            },
            onContractorRemove(removedOption){
                this.filterJobForm.contractor_id.splice(this.filterJobForm.contractor_id.indexOf(removedOption.id), 1);
            },
            onProjectSelect(selectedOption){
                this.filterJobForm.project_id.push(selectedOption.id);
            },
            onProjectRemove(removedOption){
                this.filterJobForm.project_id.splice(this.filterJobForm.project_id.indexOf(removedOption.id), 1);
            },
        },
        computed:{
            getRatingUser(){
                let user = this.users.filter(user => user.id == this.filterJobForm.user_id[0]);
                return user[0].name;
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          }
        },
        watch: {
            filterJobForm: {
                handler(val){
                    this.getJobs();
                },
                deep: true
            }
        }
    }
</script>

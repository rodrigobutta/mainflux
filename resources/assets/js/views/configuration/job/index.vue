<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('job.job')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/configuration">{{trans('configuration.configuration')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('job.job')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="job"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">

                                <div class="row">
                                    <div class="col-12 col-sm-4 col-md-4">
                                        <h4 class="card-title">{{trans('job.add_new_job_category')}}</h4>
                                        <job-category-form @completed="getJobCategories"></job-category-form>

                                    </div>
                                    <div class="col-12 col-sm-8 col-md-8">
                                        <h4 class="card-title">{{trans('job.job_category_list')}}</h4>
                                        <h6 class="card-subtitle" v-if="job_categories">{{trans('general.total_result_found',{'count' : job_categories.total})}}</h6>
                                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                                        <div class="table-responsive">
                                            <table class="table" v-if="job_categories.total">
                                                <thead>
                                                    <tr>
                                                        <th>{{trans('job.job_category_name')}}</th>
                                                        <th>{{trans('job.job_category_description')}}</th>
                                                        <th class="table-option">{{trans('general.action')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="job_category in job_categories.data">
                                                        <td v-text="job_category.name"></td>
                                                        <td v-text="job_category.description"></td>
                                                        <td class="table-option">
                                                            <div class="btn-group">
                                                                <button class="btn btn-info btn-sm" v-tooltip="trans('job.edit_job_category')" @click.prevent="editJobCategory(job_category)"><i class="fas fa-pencil-alt"></i></button>
                                                                <button class="btn btn-danger btn-sm" :key="`category_${job_category.id}`" v-confirm="{ok: confirmJobCategoryDelete(job_category)}" v-tooltip="trans('job.delete_job_category')"><i class="fas fa-trash"></i></button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <pagination-record :page-length.sync="filterJobCategoryForm.page_length" :records="job_categories" @updateRecords="getJobCategories" @change.native="getJobCategories"></pagination-record>
                                    </div>
                                </div>
                                <div class="row m-t-20">
                                    <div class="col-12 col-sm-4 col-md-4">
                                        <h4 class="card-title">{{trans('job.add_new_job_priority')}}</h4>
                                        <job-priority-form @completed="getJobPriorities"></job-priority-form>

                                    </div>
                                    <div class="col-12 col-sm-8 col-md-8">
                                        <h4 class="card-title">{{trans('job.job_priority_list')}}</h4>
                                        <h6 class="card-subtitle" v-if="job_priorities">{{trans('general.total_result_found',{'count' : job_priorities.total})}}</h6>
                                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                                        <div class="table-responsive">
                                            <table class="table" v-if="job_priorities.total">
                                                <thead>
                                                    <tr>
                                                        <th>{{trans('job.job_priority_name')}}</th>
                                                        <th>{{trans('job.job_priority_description')}}</th>
                                                        <th class="table-option">{{trans('general.action')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="job_priority in job_priorities.data">
                                                        <td v-text="job_priority.name"></td>
                                                        <td v-text="job_priority.description"></td>
                                                        <td class="table-option">
                                                            <div class="btn-group">
                                                                <button class="btn btn-info btn-sm" v-tooltip="trans('job.edit_job_priority')" @click.prevent="editJobPriority(job_priority)"><i class="fas fa-pencil-alt"></i></button>
                                                                <button class="btn btn-danger btn-sm" :key="`priority_${job_priority.id}`" v-confirm="{ok: confirmJobPriorityDelete(job_priority)}" v-tooltip="trans('job.delete_job_priority')"><i class="fas fa-trash"></i></button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <pagination-record :page-length.sync="filterJobPriorityForm.page_length" :records="job_priorities" @updateRecords="getJobPriorities"></pagination-record>
                                    </div>
                                </div>
                                <div class="row m-t-20">
                                    <div class="col-12">
                                        <h4 class="card-title">{{trans('job.configuration')}}</h4>
                                        <form @submit.prevent="saveJobConfiguration" @keydown="jobConfigForm.errors.clear($event.target.name)">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('job.prefix')}} <show-tip module="job" tip="tip_job_number_prefix" type="field"></show-tip> </label>
                                                        <input class="form-control" type="text" value="" v-model="jobConfigForm.job_number_prefix" name="job_number_prefix" :placeholder="trans('job.prefix')">
                                                        <show-error :form-name="jobConfigForm" prop-name="job_number_prefix"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('job.digit')}} <show-tip module="job" tip="tip_job_number_digit" type="field"></show-tip></label>
                                                        <input class="form-control" type="text" value="" v-model="jobConfigForm.job_number_digit" name="job_number_digit" :placeholder="trans('job.digit')">
                                                        <show-error :form-name="jobConfigForm" prop-name="job_number_digit"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('job.default_progress_type')}} <show-tip module="job" tip="tip_job_progress_type" type="field"></show-tip></label>
                                                        <div class="radio radio-info">
                                                            <input type="radio" value="manual" id="progress_type_manual" v-model="jobConfigForm.job_progress_type" :checked="jobConfigForm.job_progress_type === 'manual'" name="job_progress_type">
                                                            <label for="progress_type_manual"> {{trans('job.manual_progress')}} </label>
                                                        </div>
                                                        <div class="radio radio-info">
                                                            <input type="radio" value="sub_job" id="progress_type_sub_job" v-model="jobConfigForm.job_progress_type" :checked="jobConfigForm.job_progress_type === 'sub_job'" name="job_progress_type">
                                                            <label for="progress_type_sub_job"> {{trans('job.sub_job_progress')}} </label>
                                                        </div>
                                                        <div class="radio radio-info">
                                                            <input type="radio" value="question" id="progress_type_question" v-model="jobConfigForm.job_progress_type" :checked="jobConfigForm.job_progress_type === 'question'" name="job_progress_type">
                                                            <label for="progress_type_question"> {{trans('job.question')}} </label>
                                                        </div>
                                                        <show-error :form-name="jobConfigForm" prop-name="job_progress_type"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('job.default_rating_type')}} <show-tip module="job" tip="tip_job_rating_type" type="field"></show-tip></label>
                                                        <div class="radio radio-info">
                                                            <input type="radio" value="job_based" id="rating_type_job_based" v-model="jobConfigForm.job_rating_type" :checked="jobConfigForm.job_rating_type === 'job_based'" name="job_rating_type">
                                                            <label for="rating_type_job_based"> {{trans('job.job_based_rating')}} </label>
                                                        </div>
                                                        <div class="radio radio-info">
                                                            <input type="radio" value="sub_job_based" id="rating_type_sub_job_based" v-model="jobConfigForm.job_rating_type" :checked="jobConfigForm.job_rating_type === 'sub_job_based'" name="job_rating_type">
                                                            <label for="rating_type_sub_job_based"> {{trans('job.sub_job_based_rating')}} </label>
                                                        </div>
                                                        <show-error :form-name="jobConfigForm" prop-name="job_rating_type"></show-error>
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
    import jobCategoryForm from './form-job-category'
    import jobPriorityForm from './form-job-priority'

    export default {
        components : { configurationSidebar,jobCategoryForm,jobPriorityForm },
        data() {
            return {
                job_categories: {},
                filterJobCategoryForm: {
                    page_length: helper.getConfig('page_length')
                },
                job_priorities: {},
                filterJobPriorityForm: {
                    page_length: helper.getConfig('page_length')
                },
                jobConfigForm: new Form({
                    job_progress_type: '',
                    job_rating_type: '',
                    job_number_prefix: '',
                    job_number_digit: '',
                    config_type: ''
                },false)
            };
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getJobCategories();
            this.getJobPriorities();

            this.jobConfigForm.job_progress_type = helper.getConfig('job_progress_type');
            this.jobConfigForm.job_rating_type = helper.getConfig('job_rating_type');
            this.jobConfigForm.job_number_prefix = helper.getConfig('job_number_prefix');
            this.jobConfigForm.job_number_digit = helper.getConfig('job_number_digit');
        },
        methods: {
            getJobCategories(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterJobCategoryForm);
                axios.get('/api/job-category?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.job_categories = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editJobCategory(job_category){
                this.$router.push('/configuration/job-category/'+job_category.id+'/edit');
            },
            confirmJobCategoryDelete(job_category){
                return dialog => this.deleteJobCategory(job_category);
            },
            deleteJobCategory(job_category){
                axios.delete('/api/job-category/'+job_category.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getJobCategories();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getJobPriorities(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterJobPriorityForm);
                axios.get('/api/job-priority?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.job_priorities = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editJobPriority(job_priority){
                this.$router.push('/configuration/job-priority/'+job_priority.id+'/edit');
            },
            confirmJobPriorityDelete(job_priority){
                return dialog => this.deleteJobPriority(job_priority);
            },
            deleteJobPriority(job_priority){
                axios.delete('/api/job-priority/'+job_priority.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getJobPriorities();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            saveJobConfiguration(){
                this.jobConfigForm.config_type = 'job';
                this.jobConfigForm.post('/api/configuration')
                    .then(response => {
                        this.$store.dispatch('setConfig',this.jobConfigForm);
                        toastr.success(response.message);
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
        }
    }
</script>
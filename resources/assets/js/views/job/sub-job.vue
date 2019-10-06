<template>
    <div>
        <button class="pull-right btn btn-sm btn-rounded btn-success" data-toggle="modal" v-if="hasPermission('create-sub-job')" data-target=".sub-job-form">{{trans('job.add_new_sub_job')}}</button>

        <sub-job-form :uuid="uuid" :suuid="editSubJobUuid" @completed="subJobComplete" @loaded="reset"></sub-job-form>

        <sub-job-detail :uuid="uuid" :suuid="showSubJobUuid" @updateStatus="updateJobProgress" @loaded="reset"></sub-job-detail>

        <h4 class="card-title m-t-20">{{trans('job.sub_job_list')}}</h4>
        <h6 class="card-subtitle" v-if="subJobs">{{trans('general.total_result_found',{'count' : subJobs.total})}}</h6>
        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
        <div class="table-responsive">
            <table class="table" v-if="subJobs.total">
                <thead>
                    <tr>
                        <th>{{trans('job.sub_job_title')}}</th>
                        <th>{{trans('job.sub_job_status')}}</th>
                        <th>{{trans('job.owner')}}</th>
                        <th>{{trans('job.created_at')}}</th>
                        <th class="table-option">{{trans('general.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="subJob in subJobs.data">
                        <td v-text="subJob.title"></td>
                        <td><span :class="['label','label-'+getSubJobStatus(subJob).color]">{{getSubJobStatus(subJob).label}}</span></td>
                        <td v-if="subJob.user_added">{{ subJob.user_added.profile.first_name+' '+subJob.user_added.profile.last_name}}</td>
                        <td>{{subJob.created_at | momentDateTime}}</td>
                        <td class="table-option">
                            <div class="btn-group">
                                <button class="btn btn-success btn-sm" v-tooltip="trans('job.view_sub_job')" data-toggle="modal" data-target=".sub-job-detail" @click="showSubJob(subJob)"><i class="fas fa-arrow-circle-right"></i></button>
                                <template v-if="hasRole('admin') || hasPermission('access-all-job') || subJob.job.user_id === getAuthUser('id') || subJob.user_id === getAuthUser('id')">
                                    <button class="btn btn-info btn-sm" v-if="hasPermission('edit-sub-job')" v-tooltip="trans('job.edit_sub_job')" data-toggle="modal" data-target=".sub-job-form" @click="editSubJob(subJob)"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-sub-job')" :key="subJob.id" v-confirm="{ok: confirmDeleteSubJob(subJob)}" v-tooltip="trans('job.delete_sub_job')"><i class="fas fa-trash"></i></button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination-record :page-length.sync="filterSubJobForm.page_length" :records="subJobs" @updateRecords="getSubJobs" @change.native="getSubJobs"></pagination-record>
    </div>
</template>

<script>
    import subJobForm from './sub-job-form'
    import subJobDetail from './sub-job-detail'

    export default {
        components: {subJobForm,subJobDetail},
        data(){
            return {
                subJobs: {},
                filterSubJobForm: {
                    sortBy : 'created_at',
                    order: 'desc',
                    page_length: helper.getConfig('page_length')
                },
                showSubJobUuid: '',
                editSubJobUuid: ''
            }
        },
        props: ['uuid'],
        mounted(){
            this.getSubJobs();
        },
        methods: {
            hasRole(role){
                return helper.hasRole(role);
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getSubJobs(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterSubJobForm);
                axios.get('/api/job/'+this.uuid+'/sub-job?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.subJobs = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
                this.suuid = '';
            },
            confirmDeleteSubJob(subJob){
                return dialog => this.deleteSubJob(subJob);
            },
            deleteSubJob(subJob){
                axios.delete('/api/job/'+this.uuid+'/sub-job/'+subJob.uuid)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.updateJobProgress();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getSubJobStatus(subJob){
                return helper.getSubJobStatus(subJob);
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            subJobComplete(){
                this.editSubJobUuid = '';
                this.updateJobProgress();
            },
            updateJobProgress(){
                this.getSubJobs();
                this.$emit('updateProgress')
            },
            showSubJob(subJob){
                this.showSubJobUuid = subJob.uuid;
            },
            editSubJob(subJob){
                this.editSubJobUuid = subJob.uuid;
            },
            reset(){
                this.showSubJobUuid = '';
                this.editSubJobUuid = '';
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          },
          momentDateTime(datetime) {
            return helper.formatDateTime(datetime);
          }
        },
        watch: {

        }
    }
</script>

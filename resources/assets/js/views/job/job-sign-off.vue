<template>
    <div>
        <p class="alert alert-info"><i class="fas fa-exclamation-triangle"></i> {{trans('job.tip_job_sign_off')}}</p>

        <template v-if="job.user_id == getAuthUser('id') && (job.sign_off_status == 'requested' || job.sign_off_status === 'approved')">
            <h4 class="card-title">{{trans('job.sign_off_action')}}</h4>
            <form @submit.prevent="signOffAction" @keydown="jobSignOffForm.errors.clear($event.target.name)">
                <div class="form-group">
                    <textarea class="form-control" type="text" value="" v-model="jobSignOffForm.sign_off_remarks" rows="2" name="sign_off_remarks" :placeholder="trans('job.sign_off_remarks')"></textarea>
                    <show-error :form-name="jobSignOffForm" prop-name="sign_off_remarks"></show-error>
                </div>
                <div class="form-group">
                    <button type="button" @click="signOffActionApprove" class="btn btn-info waves-effect waves-light" v-if="job.sign_off_status === 'requested'">{{trans('job.approve')}}</button>
                    <button type="button" @click="signOffActionReject" class="btn btn-danger waves-effect waves-light">{{trans('job.reject')}}</button>
                </div>
            </form>
        </template>

        <template v-if="users.indexOf(getAuthUser('id')) > -1 && job.sign_off_status != 'approved'">
            <h4 class="card-title">{{trans('job.sign_off')}}</h4>
            <form @submit.prevent="signOff" @keydown="jobSignOffForm.errors.clear($event.target.name)">
                <div class="form-group">
                    <textarea class="form-control" type="text" value="" v-model="jobSignOffForm.sign_off_remarks" rows="2" name="sign_off_remarks" :placeholder="trans('job.sign_off_remarks')"></textarea>
                    <show-error :form-name="jobSignOffForm" prop-name="sign_off_remarks"></show-error>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-info waves-effect waves-light" v-if="job.sign_off_status === null || job.sign_off_status === 'rejected' || job.sign_off_status === 'cancelled'">{{trans('job.request')}}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light" v-if="job.sign_off_status === 'requested'">{{trans('job.cancel_request')}}</button>
                </div>
            </form>
        </template>

        <h4 class="card-title m-t-20">{{trans('job.sign_off_log')}}</h4>
        <h6 class="card-subtitle" v-if="job_sign_off_logs">{{trans('general.total_result_found',{'count' : job_sign_off_logs.total})}}</h6>
        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
        <div class="table-responsive" v-if="job_sign_off_logs.total">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{trans('user.user')}}</th>
                        <th>{{trans('job.sign_off_remarks')}}</th>
                        <th>{{trans('job.status')}}</th>
                        <th class="text-right">{{trans('job.created_at')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="job_sign_off_log in job_sign_off_logs.data">
                        <td v-text="job_sign_off_log.user_added.profile.first_name+' '+job_sign_off_log.user_added.profile.last_name"></td>
                        <td v-text="job_sign_off_log.remarks"></td>
                        <td v-text="trans('job.'+job_sign_off_log.status)"></td>
                        <td class="text-right">{{job_sign_off_log.created_at | momentDateTime}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination-record :page-length.sync="filterJobSignOffRequestForm.page_length" :records="job_sign_off_logs" @updateRecords="getJobSignOffRequests" @change.native="getJobSignOffRequests"></pagination-record>
    </div>
</template>

<script>
    export default {
        components:{},
        data(){
            return {
                jobSignOffForm: new Form({
                    sign_off_remarks: '',
                    status: ''
                }),
                job_sign_off_logs: {},
                filterJobSignOffRequestForm: {
                    sortBy : 'created_at',
                    order: 'desc',
                    page_length: helper.getConfig('page_length')
                },
            }
        },
        props: ['uuid','job','users'],
        mounted(){
            this.getJobSignOffRequests();
        },
        methods: {
            signOff(){

                if(this.job.sign_off_status === null || this.job.sign_off_status === 'rejected' || this.job.sign_off_status === 'cancelled')
                    this.jobSignOffForm.status = 'requested';
                else if(this.job.sign_off_status === 'requested')
                    this.jobSignOffForm.status = 'cancelled';

                this.jobSignOffForm.post('/api/job/'+this.uuid+'/sign-off')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('signOffStatusUpdated');
                        this.getJobSignOffRequests();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    })
            },
            getJobSignOffRequests(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterJobSignOffRequestForm);
                axios.get('/api/job/'+this.uuid+'/sign-off?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.job_sign_off_logs = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            signOffAction(){
                this.jobSignOffForm.post('/api/job/'+this.uuid+'/sign-off-action')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('signOffStatusUpdated');
                        this.getJobSignOffRequests();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    })
            },
            signOffActionApprove(){
                this.jobSignOffForm.status = 'approved';
                this.signOffAction();
            },
            signOffActionReject(){
                this.jobSignOffForm.status = 'rejected';
                this.signOffAction();
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          },
          momentDateTime(datetime) {
            return helper.formatDateTime(datetime);
          }
        }
    }
</script>

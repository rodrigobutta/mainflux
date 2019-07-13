<template>
    <div>
        <p class="alert alert-info"><i class="fas fa-exclamation-triangle"></i> {{trans('task.tip_task_sign_off')}}</p>

        <template v-if="task.user_id == getAuthUser('id') && (task.sign_off_status == 'requested' || task.sign_off_status === 'approved')">
            <h4 class="card-title">{{trans('task.sign_off_action')}}</h4>
            <form @submit.prevent="signOffAction" @keydown="taskSignOffForm.errors.clear($event.target.name)">
                <div class="form-group">
                    <textarea class="form-control" type="text" value="" v-model="taskSignOffForm.sign_off_remarks" rows="2" name="sign_off_remarks" :placeholder="trans('task.sign_off_remarks')"></textarea>
                    <show-error :form-name="taskSignOffForm" prop-name="sign_off_remarks"></show-error>
                </div>
                <div class="form-group">
                    <button type="button" @click="signOffActionApprove" class="btn btn-info waves-effect waves-light" v-if="task.sign_off_status === 'requested'">{{trans('task.approve')}}</button>
                    <button type="button" @click="signOffActionReject" class="btn btn-danger waves-effect waves-light">{{trans('task.reject')}}</button>
                </div>
            </form>
        </template>

        <template v-if="users.indexOf(getAuthUser('id')) > -1 && task.sign_off_status != 'approved'">
            <h4 class="card-title">{{trans('task.sign_off')}}</h4>
            <form @submit.prevent="signOff" @keydown="taskSignOffForm.errors.clear($event.target.name)">
                <div class="form-group">
                    <textarea class="form-control" type="text" value="" v-model="taskSignOffForm.sign_off_remarks" rows="2" name="sign_off_remarks" :placeholder="trans('task.sign_off_remarks')"></textarea>
                    <show-error :form-name="taskSignOffForm" prop-name="sign_off_remarks"></show-error>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-info waves-effect waves-light" v-if="task.sign_off_status === null || task.sign_off_status === 'rejected' || task.sign_off_status === 'cancelled'">{{trans('task.request')}}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light" v-if="task.sign_off_status === 'requested'">{{trans('task.cancel_request')}}</button>
                </div>
            </form>
        </template>

        <h4 class="card-title m-t-20">{{trans('task.sign_off_log')}}</h4>
        <h6 class="card-subtitle" v-if="task_sign_off_logs">{{trans('general.total_result_found',{'count' : task_sign_off_logs.total})}}</h6>
        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
        <div class="table-responsive" v-if="task_sign_off_logs.total">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{trans('user.user')}}</th>
                        <th>{{trans('task.sign_off_remarks')}}</th>
                        <th>{{trans('task.status')}}</th>
                        <th class="text-right">{{trans('task.created_at')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="task_sign_off_log in task_sign_off_logs.data">
                        <td v-text="task_sign_off_log.user_added.profile.first_name+' '+task_sign_off_log.user_added.profile.last_name"></td>
                        <td v-text="task_sign_off_log.remarks"></td>
                        <td v-text="trans('task.'+task_sign_off_log.status)"></td>
                        <td class="text-right">{{task_sign_off_log.created_at | momentDateTime}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination-record :page-length.sync="filterTaskSignOffRequestForm.page_length" :records="task_sign_off_logs" @updateRecords="getTaskSignOffRequests" @change.native="getTaskSignOffRequests"></pagination-record>
    </div>
</template>

<script>
    export default {
        components:{},
        data(){
            return {
                taskSignOffForm: new Form({
                    sign_off_remarks: '',
                    status: ''
                }),
                task_sign_off_logs: {},
                filterTaskSignOffRequestForm: {
                    sortBy : 'created_at',
                    order: 'desc',
                    page_length: helper.getConfig('page_length')
                },
            }
        },
        props: ['uuid','task','users'],
        mounted(){
            this.getTaskSignOffRequests();
        },
        methods: {
            signOff(){

                if(this.task.sign_off_status === null || this.task.sign_off_status === 'rejected' || this.task.sign_off_status === 'cancelled')
                    this.taskSignOffForm.status = 'requested';
                else if(this.task.sign_off_status === 'requested')
                    this.taskSignOffForm.status = 'cancelled';

                this.taskSignOffForm.post('/api/task/'+this.uuid+'/sign-off')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('signOffStatusUpdated');
                        this.getTaskSignOffRequests();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    })
            },
            getTaskSignOffRequests(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterTaskSignOffRequestForm);
                axios.get('/api/task/'+this.uuid+'/sign-off?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.task_sign_off_logs = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            signOffAction(){
                this.taskSignOffForm.post('/api/task/'+this.uuid+'/sign-off-action')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('signOffStatusUpdated');
                        this.getTaskSignOffRequests();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    })
            },
            signOffActionApprove(){
                this.taskSignOffForm.status = 'approved';
                this.signOffAction();
            },
            signOffActionReject(){
                this.taskSignOffForm.status = 'rejected';
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

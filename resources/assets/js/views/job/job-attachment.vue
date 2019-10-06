<template>
    <div>
        <p class="alert alert-info"><i class="fas fa-exclamation-triangle"></i> {{trans('job.tip_job_attachment')}}</p>

        <button class="pull-right btn btn-sm btn-rounded btn-success" data-toggle="modal" data-target=".job-attachment-form">{{trans('job.add_new_attachment')}}</button>

        <job-attachment-form :uuid="uuid" :auuid="editAttachmentUuid" @completed="getAttachments" @loaded="reset"></job-attachment-form>

        <job-attachment-detail :uuid="uuid" :auuid="showAttachmentUuid" @loaded="reset"></job-attachment-detail>

        <h4 class="card-title m-t-20">{{trans('job.job_attachment_list')}}</h4>
        <h6 class="card-subtitle" v-if="job_attachments">{{trans('general.total_result_found',{'count' : job_attachments.total})}}</h6>
        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
        <div class="table-responsive" v-if="job_attachments.total">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{trans('job.job_attachment_title')}}</th>
                        <th>{{trans('job.owner')}}</th>
                        <th>{{trans('job.created_at')}}</th>
                        <th class="table-option">{{trans('general.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="job_attachment in job_attachments.data">
                        <td v-text="job_attachment.title"></td>
                        <td>{{job_attachment.user.profile.first_name+' '+job_attachment.user.profile.last_name}}</td>
                        <td>{{job_attachment.created_at | momentDateTime}}</td>
                        <td class="table-option">
                            <div class="btn-group">
                                <button class="btn btn-success btn-sm" v-tooltip="trans('job.view_job_attachment')" data-toggle="modal" data-target=".job-attachment-detail" @click="showAttachment(job_attachment)"><i class="fas fa-arrow-circle-right"></i></button>
                                <template v-if="job_attachment.user_id === getAuthUser('id')">
                                    <button class="btn btn-info btn-sm" v-tooltip="trans('job.edit_job_attachment')" data-toggle="modal" data-target=".job-attachment-form" @click="editAttachment(job_attachment)"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="btn btn-danger btn-sm" :key="job_attachment.id" v-confirm="{ok: ConfirmDeleteAttachment(job_attachment)}" v-tooltip="trans('job.delete_job_attachment')"><i class="fas fa-trash"></i></button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination-record :page-length.sync="filterAttachmentForm.page_length" :records="job_attachments" @updateRecords="getAttachments" @change.native="getAttachments"></pagination-record>
    </div>
</template>

<script>
    import jobAttachmentForm from './job-attachment-form'
    import jobAttachmentDetail from './job-attachment-detail'

    export default {
        components: {jobAttachmentForm,jobAttachmentDetail},
        data(){
            return {
                job_attachments: {},
                filterAttachmentForm: {
                    sortBy : 'created_at',
                    order: 'desc',
                    page_length: helper.getConfig('page_length')
                },
                showAttachmentUuid: '',
                editAttachmentUuid: '',
                showAttachmentFormModal: false,
                showAttachmentDetailModal: false,
            }
        },
        props: ['uuid'],
        mounted(){
            this.getAttachments();
        },
        methods: {
            getAttachments(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterAttachmentForm);
                axios.get('/api/job/'+this.uuid+'/attachment?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.job_attachments = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            ConfirmDeleteAttachment(job_attachment){
                return dialog => this.deleteAttachment(job_attachment);
            },
            deleteAttachment(job_attachment){
                axios.delete('/api/job/'+this.uuid+'/attachment/'+job_attachment.uuid)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getAttachments();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            showAttachment(job_attachment){
                this.showAttachmentUuid = job_attachment.uuid;
            },
            editAttachment(job_attachment){
                this.editAttachmentUuid = job_attachment.uuid;
            },
            reset(){
                this.showAttachmentUuid = '';
                this.editAttachmentUuid = '';
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

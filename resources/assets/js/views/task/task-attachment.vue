<template>
    <div>
        <p class="alert alert-info"><i class="fas fa-exclamation-triangle"></i> {{trans('task.tip_task_attachment')}}</p>

        <button class="pull-right btn btn-sm btn-rounded btn-success" data-toggle="modal" data-target=".task-attachment-form">{{trans('task.add_new_attachment')}}</button>

        <task-attachment-form :uuid="uuid" :auuid="editAttachmentUuid" @completed="getAttachments" @loaded="reset"></task-attachment-form>

        <task-attachment-detail :uuid="uuid" :auuid="showAttachmentUuid" @loaded="reset"></task-attachment-detail>

        <h4 class="card-title m-t-20">{{trans('task.task_attachment_list')}}</h4>
        <h6 class="card-subtitle" v-if="task_attachments">{{trans('general.total_result_found',{'count' : task_attachments.total})}}</h6>
        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
        <div class="table-responsive" v-if="task_attachments.total">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{trans('task.task_attachment_title')}}</th>
                        <th>{{trans('task.owner')}}</th>
                        <th>{{trans('task.created_at')}}</th>
                        <th class="table-option">{{trans('general.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="task_attachment in task_attachments.data">
                        <td v-text="task_attachment.title"></td>
                        <td>{{task_attachment.user.profile.first_name+' '+task_attachment.user.profile.last_name}}</td>
                        <td>{{task_attachment.created_at | momentDateTime}}</td>
                        <td class="table-option">
                            <div class="btn-group">
                                <button class="btn btn-success btn-sm" v-tooltip="trans('task.view_task_attachment')" data-toggle="modal" data-target=".task-attachment-detail" @click="showAttachment(task_attachment)"><i class="fas fa-arrow-circle-right"></i></button>
                                <template v-if="task_attachment.user_id === getAuthUser('id')">
                                    <button class="btn btn-info btn-sm" v-tooltip="trans('task.edit_task_attachment')" data-toggle="modal" data-target=".task-attachment-form" @click="editAttachment(task_attachment)"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="btn btn-danger btn-sm" :key="task_attachment.id" v-confirm="{ok: ConfirmDeleteAttachment(task_attachment)}" v-tooltip="trans('task.delete_task_attachment')"><i class="fas fa-trash"></i></button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination-record :page-length.sync="filterAttachmentForm.page_length" :records="task_attachments" @updateRecords="getAttachments" @change.native="getAttachments"></pagination-record>
    </div>
</template>

<script>
    import taskAttachmentForm from './task-attachment-form'
    import taskAttachmentDetail from './task-attachment-detail'

    export default {
        components: {taskAttachmentForm,taskAttachmentDetail},
        data(){
            return {
                task_attachments: {},
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
                axios.get('/api/task/'+this.uuid+'/attachment?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.task_attachments = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            ConfirmDeleteAttachment(task_attachment){
                return dialog => this.deleteAttachment(task_attachment);
            },
            deleteAttachment(task_attachment){
                axios.delete('/api/task/'+this.uuid+'/attachment/'+task_attachment.uuid)
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
            showAttachment(task_attachment){
                this.showAttachmentUuid = task_attachment.uuid;
            },
            editAttachment(task_attachment){
                this.editAttachmentUuid = task_attachment.uuid;
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

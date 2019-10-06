<template>
    <div class="modal fade job-attachment-form" tabindex="-1" role="dialog" aria-labelledby="jobAttachmentForm" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="jobAttachmentForm">{{local_id ? trans('job.edit_job_attachment') : trans('job.add_new_attachment')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="proceed" @keydown="jobAttachmentForm.errors.clear($event.target.name)">
                        <div class="form-group">
                            <label for="">{{trans('job.job_attachment_title')}}</label>
                            <input class="form-control" type="text" value="" v-model="jobAttachmentForm.title" name="title" :placeholder="trans('job.job_attachment_title')">
                            <show-error :form-name="jobAttachmentForm" prop-name="title"></show-error>
                        </div>
                        <div class="form-group">
                            <html-editor name="description" :model.sync="jobAttachmentForm.description" :isUpdate="updateEditorContent" @clearErrors="jobAttachmentForm.errors.clear('description')"></html-editor>
                            <show-error :form-name="jobAttachmentForm" prop-name="description"></show-error>
                        </div>
                        <div class="form-group">
                            <file-upload-input :button-text="trans('general.upload_document')" :token="jobAttachmentForm.upload_token" module="job_attachment" :clear-file="clearJobAttachmentAttachment" :module-id="module_id"></file-upload-input>
                        </div>
                        <button type="submit" class="btn btn-info waves-effect waves-light">
                            <span v-if="local_id">{{trans('general.update')}}</span>
                            <span v-else>{{trans('general.save')}}</span>
                        </button>
                        <button type="button" class="btn btn-danger waves-effect waves-light" @click="closeModal">{{trans('general.cancel')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import htmlEditor from '../../components/html-editor'
    import fileUploadInput from '../../components/file-upload-input'
    import uuid from 'uuid/v4'

    export default {
        components:{htmlEditor,fileUploadInput},
        data() {
            return {
                jobAttachmentForm: new Form({
                    title : '',
                    description : '',
                    upload_token: ''
                }),
                local_id: '',
                module_id: '',
                clearJobAttachmentAttachment: true,
                updateEditorContent: false,
            };
        },
        props: ['uuid','auuid'],
        mounted() {
            this.jobAttachmentForm.upload_token = uuid();
        },
        methods: {
            proceed(){
                if(this.local_id)
                    this.updateAttachment();
                else
                    this.storeAttachment();
            },
            storeAttachment(){
                this.jobAttachmentForm.post('/api/job/'+this.uuid+'/attachment')
                    .then(response => {
                        toastr.success(response.message);
                        this.clearJobAttachmentAttachment = !this.clearJobAttachmentAttachment;
                        this.$emit('completed');
                        this.jobAttachmentForm.upload_token = uuid();
                        this.closeModal();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getAttachment(){
                this.local_id = this.auuid;
                this.updateEditorContent = false;
                axios.get('/api/job/'+this.uuid+'/attachment/'+this.local_id)
                    .then(response => response.data)
                    .then(response => {
                        this.jobAttachmentForm.title = response.job_attachment.title;
                        this.jobAttachmentForm.description = response.job_attachment.description;
                        this.jobAttachmentForm.upload_token = response.job_attachment.upload_token;
                        this.module_id = response.job_attachment.id;
                        this.updateEditorContent = true;
                        this.$emit('loaded');
                    })
                    .catch(error => {
                        this.$router.push('/job/'+this.uuid);
                    });
            },
            updateAttachment(){
                this.jobAttachmentForm.patch('/api/job/'+this.uuid+'/attachment/'+this.local_id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/job/'+this.uuid);
                        this.closeModal();
                        this.$emit('completed');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            closeModal(){
                $('.job-attachment-form').modal('hide');
                this.$emit('completed');
            }
        },
        watch: {
            auuid(val) {
                if(val)
                    this.getAttachment();
            }
        }
    }
</script>

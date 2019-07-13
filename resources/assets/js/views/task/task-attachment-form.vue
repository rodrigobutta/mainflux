<template>
    <div class="modal fade task-attachment-form" tabindex="-1" role="dialog" aria-labelledby="taskAttachmentForm" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="taskAttachmentForm">{{local_id ? trans('task.edit_task_attachment') : trans('task.add_new_attachment')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="proceed" @keydown="taskAttachmentForm.errors.clear($event.target.name)">
                        <div class="form-group">
                            <label for="">{{trans('task.task_attachment_title')}}</label>
                            <input class="form-control" type="text" value="" v-model="taskAttachmentForm.title" name="title" :placeholder="trans('task.task_attachment_title')">
                            <show-error :form-name="taskAttachmentForm" prop-name="title"></show-error>
                        </div>
                        <div class="form-group">
                            <html-editor name="description" :model.sync="taskAttachmentForm.description" :isUpdate="updateEditorContent" @clearErrors="taskAttachmentForm.errors.clear('description')"></html-editor>
                            <show-error :form-name="taskAttachmentForm" prop-name="description"></show-error>
                        </div>
                        <div class="form-group">
                            <file-upload-input :button-text="trans('general.upload_document')" :token="taskAttachmentForm.upload_token" module="task_attachment" :clear-file="clearTaskAttachmentAttachment" :module-id="module_id"></file-upload-input>
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
                taskAttachmentForm: new Form({
                    title : '',
                    description : '',
                    upload_token: ''
                }),
                local_id: '',
                module_id: '',
                clearTaskAttachmentAttachment: true,
                updateEditorContent: false,
            };
        },
        props: ['uuid','auuid'],
        mounted() {
            this.taskAttachmentForm.upload_token = uuid();
        },
        methods: {
            proceed(){
                if(this.local_id)
                    this.updateAttachment();
                else
                    this.storeAttachment();
            },
            storeAttachment(){
                this.taskAttachmentForm.post('/api/task/'+this.uuid+'/attachment')
                    .then(response => {
                        toastr.success(response.message);
                        this.clearTaskAttachmentAttachment = !this.clearTaskAttachmentAttachment;
                        this.$emit('completed');
                        this.taskAttachmentForm.upload_token = uuid();
                        this.closeModal();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getAttachment(){
                this.local_id = this.auuid;
                this.updateEditorContent = false;
                axios.get('/api/task/'+this.uuid+'/attachment/'+this.local_id)
                    .then(response => response.data)
                    .then(response => {
                        this.taskAttachmentForm.title = response.task_attachment.title;
                        this.taskAttachmentForm.description = response.task_attachment.description;
                        this.taskAttachmentForm.upload_token = response.task_attachment.upload_token;
                        this.module_id = response.task_attachment.id;
                        this.updateEditorContent = true;
                        this.$emit('loaded');
                    })
                    .catch(error => {
                        this.$router.push('/task/'+this.uuid);
                    });
            },
            updateAttachment(){
                this.taskAttachmentForm.patch('/api/task/'+this.uuid+'/attachment/'+this.local_id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/task/'+this.uuid);
                        this.closeModal();
                        this.$emit('completed');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            closeModal(){
                $('.task-attachment-form').modal('hide');
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

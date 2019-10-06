<template>
    <div class="modal fade job-note-form" tabindex="-1" role="dialog" aria-labelledby="jobNoteForm" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="jobNoteForm">{{local_id ? trans('job.edit_job_note') : trans('job.add_new_note')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="proceed" @keydown="jobNoteForm.errors.clear($event.target.name)">
                        <div class="form-group">
                            <label for="">{{trans('job.job_note_title')}}</label>
                            <input class="form-control" type="text" value="" v-model="jobNoteForm.title" name="title" :placeholder="trans('job.job_note_title')">
                            <show-error :form-name="jobNoteForm" prop-name="title"></show-error>
                        </div>
                        <div class="form-group">
                            <html-editor name="description" :model.sync="jobNoteForm.description" :isUpdate="updateEditorContent" @clearErrors="jobNoteForm.errors.clear('description')"></html-editor>
                            <show-error :form-name="jobNoteForm" prop-name="description"></show-error>
                        </div>
                        <div class="form-group">
                            <label for="">{{trans('job.visibility')}}</label>
                            <div class="radio radio-info">
                                <input type="radio" value="0" id="visibility_private" v-model="jobNoteForm.is_public" :checked="!jobNoteForm.is_public" name="is_public">
                                <label for="visibility_private"> {{trans('job.private')}} </label>
                            </div>
                            <div class="radio radio-info">
                                <input type="radio" value="1" id="visibility_shared" v-model="jobNoteForm.is_public" :checked="jobNoteForm.is_public" name="is_public">
                                <label for="visibility_shared"> {{trans('job.shared')}} </label>
                            </div>
                            <show-error :form-name="jobNoteForm" prop-name="is_public"></show-error>
                        </div>
                        <div class="form-group">
                            <file-upload-input :button-text="trans('general.upload_document')" :token="jobNoteForm.upload_token" module="job_note" :clear-file="clearJobNoteAttachment" :module-id="module_id"></file-upload-input>
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
                jobNoteForm: new Form({
                    title : '',
                    description : '',
                    is_public: 0,
                    upload_token: ''
                }),
                module_id: '',
                clearJobNoteAttachment: true,
                updateEditorContent: false,
                local_id: ''
            };
        },
        props: ['uuid','nuuid'],
        mounted() {
            this.jobNoteForm.upload_token = uuid();
        },
        methods: {
            proceed(){
                if(this.local_id)
                    this.updateNote();
                else
                    this.storeNote();
            },
            storeNote(){
                this.jobNoteForm.post('/api/job/'+this.uuid+'/note')
                    .then(response => {
                        toastr.success(response.message);
                        this.clearJobNoteAttachment = !this.clearJobNoteAttachment;
                        this.$emit('completed');
                        this.jobNoteForm.upload_token = uuid();
                        this.closeModal();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getNote(){
                this.local_id = this.nuuid;
                this.updateEditorContent = false;
                axios.get('/api/job/'+this.uuid+'/note/'+this.local_id)
                    .then(response => response.data)
                    .then(response => {
                        this.jobNoteForm.title = response.job_note.title;
                        this.jobNoteForm.description = response.job_note.description;
                        this.jobNoteForm.is_public = response.job_note.is_public;
                        this.jobNoteForm.upload_token = response.job_note.upload_token;
                        this.module_id = response.job_note.id;
                        this.updateEditorContent = true;
                        this.$emit('loaded');
                    })
                    .catch(error => {
                        this.$router.push('/job/'+this.uuid);
                    });
            },
            updateNote(){
                this.jobNoteForm.patch('/api/job/'+this.uuid+'/note/'+this.local_id)
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
                $('.job-note-form').modal('hide');
                this.$emit('completed');
            }
        },
        watch: {
            nuuid(val) {
                if(val)
                    this.getNote();
            }
        }
    }
</script>

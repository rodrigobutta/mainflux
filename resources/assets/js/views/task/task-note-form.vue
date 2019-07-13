<template>
    <div class="modal fade task-note-form" tabindex="-1" role="dialog" aria-labelledby="taskNoteForm" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="taskNoteForm">{{local_id ? trans('task.edit_task_note') : trans('task.add_new_note')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="proceed" @keydown="taskNoteForm.errors.clear($event.target.name)">
                        <div class="form-group">
                            <label for="">{{trans('task.task_note_title')}}</label>
                            <input class="form-control" type="text" value="" v-model="taskNoteForm.title" name="title" :placeholder="trans('task.task_note_title')">
                            <show-error :form-name="taskNoteForm" prop-name="title"></show-error>
                        </div>
                        <div class="form-group">
                            <html-editor name="description" :model.sync="taskNoteForm.description" :isUpdate="updateEditorContent" @clearErrors="taskNoteForm.errors.clear('description')"></html-editor>
                            <show-error :form-name="taskNoteForm" prop-name="description"></show-error>
                        </div>
                        <div class="form-group">
                            <label for="">{{trans('task.visibility')}}</label>
                            <div class="radio radio-info">
                                <input type="radio" value="0" id="visibility_private" v-model="taskNoteForm.is_public" :checked="!taskNoteForm.is_public" name="is_public">
                                <label for="visibility_private"> {{trans('task.private')}} </label>
                            </div>
                            <div class="radio radio-info">
                                <input type="radio" value="1" id="visibility_shared" v-model="taskNoteForm.is_public" :checked="taskNoteForm.is_public" name="is_public">
                                <label for="visibility_shared"> {{trans('task.shared')}} </label>
                            </div>
                            <show-error :form-name="taskNoteForm" prop-name="is_public"></show-error>
                        </div>
                        <div class="form-group">
                            <file-upload-input :button-text="trans('general.upload_document')" :token="taskNoteForm.upload_token" module="task_note" :clear-file="clearTaskNoteAttachment" :module-id="module_id"></file-upload-input>
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
                taskNoteForm: new Form({
                    title : '',
                    description : '',
                    is_public: 0,
                    upload_token: ''
                }),
                module_id: '',
                clearTaskNoteAttachment: true,
                updateEditorContent: false,
                local_id: ''
            };
        },
        props: ['uuid','nuuid'],
        mounted() {
            this.taskNoteForm.upload_token = uuid();
        },
        methods: {
            proceed(){
                if(this.local_id)
                    this.updateNote();
                else
                    this.storeNote();
            },
            storeNote(){
                this.taskNoteForm.post('/api/task/'+this.uuid+'/note')
                    .then(response => {
                        toastr.success(response.message);
                        this.clearTaskNoteAttachment = !this.clearTaskNoteAttachment;
                        this.$emit('completed');
                        this.taskNoteForm.upload_token = uuid();
                        this.closeModal();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getNote(){
                this.local_id = this.nuuid;
                this.updateEditorContent = false;
                axios.get('/api/task/'+this.uuid+'/note/'+this.local_id)
                    .then(response => response.data)
                    .then(response => {
                        this.taskNoteForm.title = response.task_note.title;
                        this.taskNoteForm.description = response.task_note.description;
                        this.taskNoteForm.is_public = response.task_note.is_public;
                        this.taskNoteForm.upload_token = response.task_note.upload_token;
                        this.module_id = response.task_note.id;
                        this.updateEditorContent = true;
                        this.$emit('loaded');
                    })
                    .catch(error => {
                        this.$router.push('/task/'+this.uuid);
                    });
            },
            updateNote(){
                this.taskNoteForm.patch('/api/task/'+this.uuid+'/note/'+this.local_id)
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
                $('.task-note-form').modal('hide');
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

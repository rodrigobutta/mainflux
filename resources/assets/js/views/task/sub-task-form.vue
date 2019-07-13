<template>
    <div class="modal fade sub-task-form" tabindex="-1" role="dialog" aria-labelledby="subTaskForm" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="subTaskForm">{{local_id ? trans('task.edit_sub_task') : trans('task.add_new_sub_task')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="proceed" @keydown="subTaskForm.errors.clear($event.target.name)">
                        <div class="form-group">
                            <label for="">{{trans('task.sub_task_title')}}</label>
                            <input class="form-control" type="text" value="" v-model="subTaskForm.title" name="title" :placeholder="trans('task.sub_task_title')">
                            <show-error :form-name="subTaskForm" prop-name="title"></show-error>
                        </div>
                        <div class="form-group">
                            <html-editor name="description" :model.sync="subTaskForm.description" :isUpdate="updateEditorContent" @clearErrors="subTaskForm.errors.clear('description')"></html-editor>
                            <show-error :form-name="subTaskForm" prop-name="description"></show-error>
                        </div>
                        <div class="form-group">
                            <file-upload-input :button-text="trans('general.upload_document')" :token="subTaskForm.upload_token" module="sub_task" :clear-file="clearSubTaskAttachment" :module-id="module_id"></file-upload-input>
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
                subTaskForm: new Form({
                    title : '',
                    description : '',
                    upload_token: ''
                }),
                module_id: '',
                clearSubTaskAttachment: true,
                updateEditorContent: false,
                local_id: ''
            };
        },
        props: ['uuid','suuid'],
        mounted() {
            this.subTaskForm.upload_token = uuid();
        },
        methods: {
            proceed(){
                if(this.local_id)
                    this.updateSubTask();
                else
                    this.storeSubTask();
            },
            storeSubTask(){
                this.subTaskForm.post('/api/task/'+this.uuid+'/sub-task')
                    .then(response => {
                        toastr.success(response.message);
                        this.clearSubTaskAttachment = !this.clearSubTaskAttachment;
                        this.$emit('completed');
                        this.subTaskForm.upload_token = uuid();
                        this.module_id = '';
                        this.closeModal();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getSubTask(){
                this.local_id = this.suuid;
                this.updateEditorContent = false;
                axios.get('/api/task/'+this.uuid+'/sub-task/'+this.local_id)
                    .then(response => response.data)
                    .then(response => {
                        this.subTaskForm.title = response.sub_task.title;
                        this.subTaskForm.description = response.sub_task.description;
                        this.subTaskForm.upload_token = response.sub_task.upload_token;
                        this.module_id = response.sub_task.id;
                        this.updateEditorContent = true;
                        this.$emit('loaded');
                    })
                    .catch(error => {
                        this.$router.push('/task/'+this.uuid);
                    });
            },
            updateSubTask(){
                this.subTaskForm.patch('/api/task/'+this.uuid+'/sub-task/'+this.local_id)
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
                $('.sub-task-form').modal('hide');
                this.$emit('completed');
            }
        },
        watch: {
            suuid(val) {
                if(val)
                    this.getSubTask();
            }
        }
    }
</script>

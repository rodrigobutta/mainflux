<template>
    <div class="modal fade sub-job-form" tabindex="-1" role="dialog" aria-labelledby="subJobForm" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="subJobForm">{{local_id ? trans('job.edit_sub_job') : trans('job.add_new_sub_job')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="proceed" @keydown="subJobForm.errors.clear($event.target.name)">
                        <div class="form-group">
                            <label for="">{{trans('job.sub_job_title')}}</label>
                            <input class="form-control" type="text" value="" v-model="subJobForm.title" name="title" :placeholder="trans('job.sub_job_title')">
                            <show-error :form-name="subJobForm" prop-name="title"></show-error>
                        </div>
                        <div class="form-group">
                            <html-editor name="description" :model.sync="subJobForm.description" :isUpdate="updateEditorContent" @clearErrors="subJobForm.errors.clear('description')"></html-editor>
                            <show-error :form-name="subJobForm" prop-name="description"></show-error>
                        </div>
                        <div class="form-group">
                            <file-upload-input :button-text="trans('general.upload_document')" :token="subJobForm.upload_token" module="sub_job" :clear-file="clearSubJobAttachment" :module-id="module_id"></file-upload-input>
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
                subJobForm: new Form({
                    title : '',
                    description : '',
                    upload_token: ''
                }),
                module_id: '',
                clearSubJobAttachment: true,
                updateEditorContent: false,
                local_id: ''
            };
        },
        props: ['uuid','suuid'],
        mounted() {
            this.subJobForm.upload_token = uuid();
        },
        methods: {
            proceed(){
                if(this.local_id)
                    this.updateSubJob();
                else
                    this.storeSubJob();
            },
            storeSubJob(){
                this.subJobForm.post('/api/job/'+this.uuid+'/sub-job')
                    .then(response => {
                        toastr.success(response.message);
                        this.clearSubJobAttachment = !this.clearSubJobAttachment;
                        this.$emit('completed');
                        this.subJobForm.upload_token = uuid();
                        this.module_id = '';
                        this.closeModal();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getSubJob(){
                this.local_id = this.suuid;
                this.updateEditorContent = false;
                axios.get('/api/job/'+this.uuid+'/sub-job/'+this.local_id)
                    .then(response => response.data)
                    .then(response => {
                        this.subJobForm.title = response.sub_job.title;
                        this.subJobForm.description = response.sub_job.description;
                        this.subJobForm.upload_token = response.sub_job.upload_token;
                        this.module_id = response.sub_job.id;
                        this.updateEditorContent = true;
                        this.$emit('loaded');
                    })
                    .catch(error => {
                        this.$router.push('/job/'+this.uuid);
                    });
            },
            updateSubJob(){
                this.subJobForm.patch('/api/job/'+this.uuid+'/sub-job/'+this.local_id)
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
                $('.sub-job-form').modal('hide');
                this.$emit('completed');
            }
        },
        watch: {
            suuid(val) {
                if(val)
                    this.getSubJob();
            }
        }
    }
</script>

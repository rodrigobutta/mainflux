<template>
    <div class="modal fade job-config" tabindex="-1" role="dialog" aria-labelledby="jobConfig" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="jobConfig">{{trans('job.configuration')}}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submit" @keydown="jobConfigForm.errors.clear($event.target.name)">
                        <div class="form-group">
                            <label for="">{{trans('job.progress_type')}}</label>
                            <div class="radio radio-info">
                                <input type="radio" value="manual" id="progress_type_manual" v-model="jobConfigForm.progress_type" :checked="jobConfigForm.progress_type === 'manual'" name="progress_type">
                                <label for="progress_type_manual"> {{trans('job.manual_progress')}} </label>
                            </div>
                            <div class="radio radio-info">
                                <input type="radio" value="sub_job" id="progress_type_sub_job" v-model="jobConfigForm.progress_type" :checked="jobConfigForm.progress_type === 'sub_job'" name="progress_type">
                                <label for="progress_type_sub_job"> {{trans('job.sub_job_progress')}} </label>
                            </div>
                            <div class="radio radio-info">
                                <input type="radio" value="question" id="progress_type_question" v-model="jobConfigForm.progress_type" :checked="jobConfigForm.progress_type === 'question'" name="progress_type">
                                <label for="progress_type_question"> {{trans('job.question')}} </label>
                            </div>
                            <show-error :form-name="jobConfigForm" prop-name="progress_type"></show-error>
                        </div>
                        <div class="form-group">
                            <label for="">{{trans('job.rating_type')}}</label>
                            <div class="radio radio-info">
                                <input type="radio" value="job_based" id="rating_type_job_based" v-model="jobConfigForm.rating_type" :checked="jobConfigForm.rating_type === 'job_based'" name="rating_type">
                                <label for="rating_type_job_based"> {{trans('job.job_based_rating')}} </label>
                            </div>
                            <div class="radio radio-info">
                                <input type="radio" value="sub_job_based" id="rating_type_sub_job_based" v-model="jobConfigForm.rating_type" :checked="jobConfigForm.rating_type === 'sub_job_based'" name="rating_type">
                                <label for="rating_type_sub_job_based"> {{trans('job.sub_job_based_rating')}} </label>
                            </div>
                            <show-error :form-name="jobConfigForm" prop-name="rating_type"></show-error>
                        </div>
                        <button type="submit" class="btn btn-info waves-effect waves-light">{{trans('general.save')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        components:{},
        data() {
            return {
                jobConfigForm: new Form({
                    progress_type : 'manual',
                    rating_type: 'job_based'
                },false),
            }
        },
        props: ['uuid','job'],
        mounted() {
        },
        methods: {
            submit(){
                this.jobConfigForm.post('/api/job/'+this.uuid+'/config')
                    .then(response => {
                        toastr.success(response.message);
                        this.closeModal();
                        this.$emit('completed');

                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            closeModal(){
                $('.job-config').modal('hide');
            }
        },
        watch: {
            job(job){
                this.jobConfigForm.progress_type = job.progress_type;
                this.jobConfigForm.rating_type = job.rating_type;
            }
        }
    }
</script>

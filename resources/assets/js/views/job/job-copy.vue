<template>
    <div class="card" v-if="showPanel">
        <div class="card-body">
            <h4 class="card-title">{{trans('job.job_copy')}}</h4>
            <div class="row">
                <div class="col-12">
                    <form @submit.prevent="copy" @keydown="jobCopyForm.errors.clear($event.target.name)">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input id="assigned_user" type="checkbox" value="1" v-model="jobCopyForm.assigned_user">
                                        <label for="assigned_user"> {{trans('job.copy_assigned_user')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input id="sub_job" type="checkbox" value="1" v-model="jobCopyForm.sub_job">
                                        <label for="sub_job"> {{trans('job.copy_sub_job')}} </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input id="job_configuration" type="checkbox" value="1" v-model="jobCopyForm.job_configuration">
                                        <label for="job_configuration"> {{trans('job.copy_job_configuration')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input id="attachments" type="checkbox" value="1" v-model="jobCopyForm.attachments">
                                        <label for="attachments"> {{trans('job.copy_attachments')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input id="notes" type="checkbox" value="1" v-model="jobCopyForm.notes">
                                        <label for="notes"> {{trans('job.copy_notes')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input id="zero_progress" type="checkbox" value="1" v-model="jobCopyForm.zero_progress">
                                        <label for="zero_progress"> {{trans('job.set_zero_progress')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info waves-effect waves-light">{{trans('job.copy')}}</button>
                        <button type="button" class="btn btn-danger" v-if="showPanel" @click="toggle">{{trans('general.cancel')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        components: {},
        props: {
            uuid:{
                required: true,
            },
            showPanel:{
                required: true,
            }
        },
        data(){
            return {
                jobCopyForm: new Form({
                    assigned_user: false,
                    sub_job: false,
                    job_configuration: false,
                    attachments: false,
                    notes: false,
                    zero_progress: false,
                })
            }
        },
        watch: {
        },
        mounted(){
        },
        methods: {
            copy(){
                this.jobCopyForm.post('/api/job/'+this.uuid+'/copy')
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/job/'+response.new_job.uuid);
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });

            },
            toggle(){
                this.$emit('toggle');
            }
        },
        filters: {
        },
        computed: {

        }
    }
</script>

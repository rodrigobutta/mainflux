<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('job.edit_job')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/job">{{trans('job.job')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('job.edit_job')}}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('job.edit_job')}}</h4>
                        <job-form :uuid="uuid"></job-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import jobForm from './form';

    export default {
        components : { jobForm },
        data() {
            return {
                uuid:this.$route.params.uuid
            }
        },
        mounted(){
            if(!helper.hasPermission('edit-job')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
        }
    }
</script>

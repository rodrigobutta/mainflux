<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('task_frequency.edit_task_frequency')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('task_frequency.edit_task_frequency')}}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task_frequency.edit_task_frequency')}}</h4>
                        <task-frequency-form :id="id"></task-frequency-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import taskFrequencyForm from './form';

    export default {
        components : { taskFrequencyForm },
        data() {
            return {
                id:this.$route.params.id
            }
        },
        mounted(){
            if(!helper.hasPermission('edit-task-frequency')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
        }
    }
</script>

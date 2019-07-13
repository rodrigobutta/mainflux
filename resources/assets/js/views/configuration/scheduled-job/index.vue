<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('configuration.configuration')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/configuration/basic">{{trans('configuration.configuration')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('configuration.scheduled_job')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="scheduled-job"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <show-tip module="configuration" tip="tip_scheduled_job"></show-tip>
                                <h4 class="card-title">{{trans('configuration.scheduled_job')}}</h4>
                                <p>Add below cron command in your server:</p>
                                <blockquote>
                                    php /path-to-artisan schedule:run >> /dev/null 2>&1
                                </blockquote>
                                <div class="table-responsive">
                                    <table class="table table-stripped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Frequency</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Recurring Task</td>
                                                <td>Once per day at 00:00 AM</td>
                                            </tr>
                                            <tr>
                                                <td>Daily Backup</td>
                                                <td>Once per day at 01:00 AM</td>
                                            </tr>
                                            <tr>
                                                <td>Delete Temporary Upload</td>
                                                <td>Once per day at 02:00 AM</td>
                                            </tr>
                                            <tr>
                                                <td>Birthday/Anniversary wish to Staff/Customer</td>
                                                <td>Once per day at 09:00 AM</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import configurationSidebar from '../system-config-sidebar'

    export default {
        components : { configurationSidebar },
        data() {
            return {

            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
        },
        methods: {
        }
    }
</script>

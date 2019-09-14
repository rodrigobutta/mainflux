<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('configuration.configuration')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/configuration/basic">{{trans('configuration.configuration')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('general.menu')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="menu"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <show-tip module="configuration" tip="tip_menu"></show-tip>
                                <h4 class="card-title">{{trans('general.menu')}}</h4>
                                <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('user.user')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_user_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('department.department')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_department_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('client.client')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_client_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('contractor.contractor')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_contractor_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('designation.designation')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_designation_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('location.location')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_location_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('announcement.announcement')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_announcement_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('task.task')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_task_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('todo.todo')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_todo_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('message.message')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_message_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('configuration.configuration')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_configuration_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('backup.backup')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_backup_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('template.email_template')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_email_template_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('mail.email_log')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_email_log_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('activity.activity_log')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_activity_log_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('general.about')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_about_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('configuration.show_menu',{menu: trans('general.support')})}}</td>
                                                    <td>
                                                        <switches v-model="configForm.show_support_menu" theme="bootstrap" color="success"></switches>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="submit" class="btn btn-info waves-effect waves-light pull-right m-t-10">{{trans('general.save')}}</button>
                                </form>
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
    import switches from 'vue-switches'

    export default {
        components : { configurationSidebar,switches },
        data() {
            return {
                configForm: new Form({
                    show_user_menu: 0,
                    show_todo_menu: 0,
                    show_message_menu: 0,
                    show_configuration_menu: 0,
                    show_backup_menu: 0,
                    show_email_template_menu: 0,
                    show_email_log_menu: 0,
                    show_activity_log_menu: 0,
                    show_department_menu: 0,
                    show_client_menu: 0,
                    show_contractor_menu: 0,
                    show_designation_menu: 0,
                    show_location_menu: 0,
                    show_announcement_menu: 0,
                    show_task_menu: 0,                    
                    config_type: 'menu'
                },false)
            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.configForm.show_user_menu = helper.getConfig('show_user_menu');
            this.configForm.show_todo_menu = helper.getConfig('show_todo_menu');
            this.configForm.show_message_menu = helper.getConfig('show_message_menu');
            this.configForm.show_configuration_menu = helper.getConfig('show_configuration_menu');
            this.configForm.show_backup_menu = helper.getConfig('show_backup_menu');
            this.configForm.show_email_template_menu = helper.getConfig('show_email_template_menu');
            this.configForm.show_email_log_menu = helper.getConfig('show_email_log_menu');
            this.configForm.show_activity_log_menu = helper.getConfig('show_activity_log_menu');
            this.configForm.show_department_menu = helper.getConfig('show_department_menu');
            this.configForm.show_client_menu = helper.getConfig('show_client_menu');
            this.configForm.show_contractor_menu = helper.getConfig('show_contractor_menu');
            this.configForm.show_designation_menu = helper.getConfig('show_designation_menu');
            this.configForm.show_location_menu = helper.getConfig('show_location_menu');
            this.configForm.show_announcement_menu = helper.getConfig('show_announcement_menu');
            this.configForm.show_task_menu = helper.getConfig('show_task_menu');            
        },
        methods: {
            submit(){
                this.configForm.config_type = 'menu';
                this.configForm.show_user_menu = (this.configForm.show_user_menu) ? 1 : 0;
                this.configForm.show_todo_menu = (this.configForm.show_todo_menu) ? 1 : 0;
                this.configForm.show_message_menu = (this.configForm.show_message_menu) ? 1 : 0;
                this.configForm.show_configuration_menu = (this.configForm.show_configuration_menu) ? 1 : 0;
                this.configForm.show_backup_menu = (this.configForm.show_backup_menu) ? 1 : 0;
                this.configForm.show_email_template_menu = (this.configForm.show_email_template_menu) ? 1 : 0;
                this.configForm.show_email_log_menu = (this.configForm.show_email_log_menu) ? 1 : 0;
                this.configForm.show_activity_log_menu = (this.configForm.show_activity_log_menu) ? 1 : 0;
                this.configForm.show_department_menu = (this.configForm.show_department_menu) ? 1 : 0;
                this.configForm.show_client_menu = (this.configForm.show_client_menu) ? 1 : 0;
                this.configForm.show_contractor_menu = (this.configForm.show_contractor_menu) ? 1 : 0;
                this.configForm.show_designation_menu = (this.configForm.show_designation_menu) ? 1 : 0;
                this.configForm.show_location_menu = (this.configForm.show_location_menu) ? 1 : 0;
                this.configForm.show_announcement_menu = (this.configForm.show_announcement_menu) ? 1 : 0;
                this.configForm.show_task_menu = (this.configForm.show_task_menu) ? 1 : 0;                
                this.configForm.post('/api/configuration')
                    .then(response => {
                        this.$store.dispatch('setConfig',this.configForm);
                        toastr.success(response.message);
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>

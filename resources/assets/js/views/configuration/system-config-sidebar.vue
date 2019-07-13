<template>
    <div class="col-2 col-lg-2 col-md-2">
        <div class="list-group">
            <router-link to="/configuration/basic" :class="[menu === 'basic' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-home"></i></span> <span class="hidden-xs-down">{{trans('general.basic')}}</span> </router-link>
            <router-link to="/configuration/logo" :class="[menu === 'logo' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-image"></i></span> <span class="hidden-xs-down">{{trans('general.logo')}}</span> </router-link>
            <router-link to="/configuration/system" :class="[menu === 'system' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-cogs"></i></span> <span class="hidden-xs-down">{{trans('general.system')}}</span> </router-link>
            <router-link to="/configuration/mail" :class="[menu === 'mail' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-envelope"></i></span> <span class="hidden-xs-down">{{trans('mail.mail')}}</span> </router-link>
            <router-link v-if="getConfig('multilingual')" to="/configuration/locale" :class="[menu === 'locale' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-globe"></i></span> <span class="hidden-xs-down">{{trans('locale.locale')}}</span> </router-link>
            <router-link to="/configuration/role" :class="[menu === 'role' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-users"></i></span> <span class="hidden-xs-down">{{trans('role.role')}}</span> </router-link>
            <router-link to="/configuration/permission" :class="[menu === 'permission' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-users"></i></span> <span class="hidden-xs-down">{{trans('permission.permission')}}</span> </router-link>
            <router-link to="/configuration/menu" :class="[menu === 'menu' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-list"></i></span> <span class="hidden-xs-down">{{trans('general.menu')}}</span> </router-link>
            <router-link to="/configuration/sms" :class="[menu === 'sms' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-comment"></i></span> <span class="hidden-xs-down">{{trans('general.sms')}}</span> </router-link>
            <router-link to="/configuration/authentication" :class="[menu === 'authentication' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-sign-in"></i></span> <span class="hidden-xs-down">{{trans('auth.authentication')}}</span> </router-link>
            <router-link v-if="getConfig('ip_filter')" to="/configuration/ip-filter" :class="[menu === 'ip-filter' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-ellipsis-v"></i></span> <span class="hidden-xs-down">{{trans('ip_filter.ip_filter')}}</span> </router-link>
            <router-link to="/configuration/task" :class="[menu === 'task' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-tasks"></i></span> <span class="hidden-xs-down">{{trans('task.task')}}</span> </router-link>
            <router-link to="/configuration/question" :class="[menu === 'question' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="fas fa-question"></i></span> <span class="hidden-xs-down">{{trans('task.question')}}</span> </router-link>
            <router-link to="/configuration/scheduled-job" :class="[menu === 'scheduled-job' ? 'active' : '', 'list-group-item']"><span class="hidden-sm-up"><i class="far fa-clock"></i></span> <span class="hidden-xs-down">{{trans('general.scheduled_job')}}</span> </router-link>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            menu: {
                default: ''
            }
        },
        mounted(){
        },
        methods: {
            getConfig(config){
                return helper.getConfig(config);
            }
        }
    }
</script>

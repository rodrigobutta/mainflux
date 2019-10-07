<template>
	<aside class="left-sidebar">
        <div class="scroll-sidebar">

            <!-- <div class="user-profile">                
                <div class="profile-img"> <img :src="getAuthUser('avatar')" alt="user" /> </div>                
                <div class="profile-text"> <a href="#" class="dropdown-toggle link u-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">{{getAuthUser('full_name')}} <span class="caret"></span></a>
                    <div class="dropdown-menu animated flipInY">
                        <a href="/profile" class="dropdown-item"><i class="ti-user"></i> My Profile</a>                        
                        <a href="/inbox" class="dropdown-item"><i class="ti-email"></i> Inbox</a>
                        <div class="dropdown-divider"></div> <a href="#" class="dropdown-item"><i class="ti-settings"></i> Account Setting</a>
                        <div class="dropdown-divider"></div> <a href="#" @click.prevent="logout" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                    </div>
                </div>
            </div> -->

            
            
            <nav class="sidebar-nav m-t-20">
                <div class="text-center" v-if="getConfig('maintenance_mode')"><span class="badge badge-danger m-b-10">{{trans('configuration.under_maintenance')}}</span></div>
                <div class="text-center" v-if="!getConfig('mode')"><span class="badge badge-danger m-b-10">{{trans('configuration.test_mode')}}</span></div>
                <ul id="sidebarnav">
                    <li><router-link to="/home" exact><i class="fas fa-home fa-fw"></i> <span class="hide-menu">{{trans('general.home')}}</span></router-link></li>
                    <li v-if="hasPermission('list-user') && getConfig('show_user_menu')"><router-link to="/user" exact><i class="fas fa-users fa-fw"></i> <span class="hide-menu">{{trans('user.user')}}</span></router-link></li>                    
                    <li v-if="hasPermission('list-client') && getConfig('show_client_menu')"><router-link to="/client" exact><i class="fas fa-building fa-fw"></i> <span class="hide-menu">{{trans('client.client')}}</span></router-link></li>
                    <li v-if="hasPermission('list-contractor') && getConfig('show_contractor_menu')"><router-link to="/contractor" exact><i class="fas fa-truck fa-fw"></i> <span class="hide-menu">{{trans('contractor.contractor')}}</span></router-link></li>
                    <li>
                        <a class="has-arrow" href="#" aria-expanded="false"><i class="fas fa-cog fa-fw"></i> <span class="hide-menu">Task Config</span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li v-if="hasPermission('list-task-relevance') && getConfig('show_task_relevance_menu')"><router-link to="/task-relevance" exact><i class="fas fa-university fa-fw"></i> {{trans('task-relevance.task_relevance')}}</router-link></li>
                            <li v-if="hasPermission('list-task-frequency') && getConfig('show_task_frequency_menu')"><router-link to="/task-frequency" exact><i class="fas fa-university fa-fw"></i> {{trans('task-frequency.task_frequency')}}</router-link></li>
                            <li v-if="hasPermission('list-task-complexity') && getConfig('show_task_complexity_menu')"><router-link to="/task-complexity" exact><i class="fas fa-university fa-fw"></i> {{trans('task-complexity.task_complexity')}}</router-link></li>
                            <li v-if="hasPermission('list-task-family') && getConfig('show_task_family_menu')"><router-link to="/task-family" exact><i class="fas fa-university fa-fw"></i> {{trans('task-family.task_family')}}</router-link></li>
                        </ul>
                    </li>
                    <li v-if="hasPermission('list-asset') && getConfig('show_asset_menu')"><router-link to="/asset" exact><i class="fas fa-sitemap fa-fw"></i> <span class="hide-menu">{{trans('asset.asset')}}</span></router-link></li>
                    <li v-if="hasPermission('list-project') && getConfig('show_project_menu')"><router-link to="/project" exact><i class="fas fa-sitemap fa-fw"></i> <span class="hide-menu">{{trans('project.project')}}</span></router-link></li>
                    <li v-if="hasPermission('list-job') && getConfig('show_job_menu')"><router-link to="/job" exact><i class="fas fa-jobs fa-fw"></i> <span class="hide-menu">{{trans('job.job')}}</span></router-link></li>
                    <li>
                        <a class="has-arrow" href="#" aria-expanded="false"><i class="fas fa-file fa-fw"></i> <span class="hide-menu">****</span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li v-if="hasPermission('list-department') && getConfig('show_department_menu')"><router-link to="/department" exact><i class="fas fa-university fa-fw"></i> {{trans('department.department')}}</router-link></li>
                            <li v-if="hasPermission('list-designation') && getConfig('show_designation_menu')"><router-link to="/designation" exact><i class="fas fa-sitemap fa-fw"></i> {{trans('designation.designation')}}</router-link></li>
                            <li v-if="hasPermission('list-location') && getConfig('show_location_menu')"><router-link to="/location" exact><i class="fas fa-code-branch fa-fw"></i> {{trans('location.location')}}</router-link></li>
                            <li v-if="hasPermission('list-user') && getConfig('show_announcement_menu')"><router-link to="/announcement" exact><i class="fas fa-bullhorn fa-fw"></i> <span class="hide-menu">{{trans('announcement.announcement')}}</span></router-link></li>                    
                        </ul>
                    </li>
                    <li>
                        <a class="has-arrow" href="#" aria-expanded="false"><i class="fas fa-file fa-fw"></i> <span class="hide-menu">{{trans('general.report')}}</span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li><router-link to="/report/job/summary"><i class="fas fa-angle-double-right"></i> {{trans('job.job_summary')}}</router-link></li>
                        </ul>
                    </li>
                    <li v-if="hasPermission('access-message') && getConfig('show_message_menu')"><router-link to="/message" exact><i class="fas fa-envelope-open fa-fw"></i> <span class="hide-menu">{{trans('message.message')}}</span></router-link></li>
                </ul>
            </nav>
        </div>
        <div class="sidebar-footer">
            <router-link v-if="hasPermission('access-configuration')" to="/configuration" class="link" v-tooltip="trans('configuration.configuration')"><i class="fas fa-cogs"></i></router-link>
            <router-link to="/profile" class="link" v-tooltip="trans('user.profile')"><i class="fas fa-user"></i></router-link>
            <a href="#" class="link" v-tooltip="trans('auth.logout')" @click.prevent="logout"><i class="fas fa-power-off"></i></a>
        </div>
    </aside>
</template>

<script>
    export default {
        mounted() {

            // console.log(this.$store.state.auth.roles)


        },
        methods : {
            logout(){
                helper.logout().then(() => {
                    this.$store.dispatch('resetAuthUserDetail');
                    this.$router.push('/login');
                })
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getConfig(config){
                return helper.getConfig(config);
            }
        },
        computed: {
        }
    }
</script>

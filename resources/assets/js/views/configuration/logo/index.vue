<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('configuration.configuration')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/configuration/basic">{{trans('configuration.configuration')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('general.logo')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="logo"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <div class="row">
                                    <div class="col-12 col-lg-6 col-md-6">
                                        <h4 class="card-title">{{trans('general.logo_type',{type:trans('general.sidebar')})}}</h4>
                                        <upload-image id="sidebar_logo" upload-path="/configuration/logo/sidebar" remove-path="/configuration/logo/sidebar/remove" :image-source="logo.sidebar" @uploaded="updateSidebarLogo" @removed="updateSidebarLogo"></upload-image>
                                    </div>
                                    <div class="col-12 col-lg-6 col-md-6">
                                        <h4 class="card-title">{{trans('general.logo_type',{type:trans('general.main')})}}</h4>
                                        <upload-image id="logo" upload-path="/configuration/logo/main" remove-path="/configuration/logo/main/remove" :image-source="logo.main" @uploaded="updateMainLogo" @removed="updateMainLogo"></upload-image>
                                    </div>
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
    import uploadImage from '../../../components/upload-image'

    export default {
        components : { configurationSidebar,uploadImage },
        data() {
            return {
                logo: {
                    main: '',
                    sidebar: ''
                }
            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.logo.main = helper.getConfig('main_logo');
            this.logo.sidebar = helper.getConfig('sidebar_logo');
        },
        methods: {
            updateMainLogo(val){
                this.$store.dispatch('setConfig',{
                    main_logo: val
                });
            },
            updateSidebarLogo(val){
                this.$store.dispatch('setConfig',{
                    sidebar_logo: val
                });
            }
        }
    }
</script>

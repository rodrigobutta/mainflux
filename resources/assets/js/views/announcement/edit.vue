<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('announcement.edit_announcement')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/announcement">{{trans('announcement.announcement')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('announcement.edit_announcement')}}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('announcement.edit_announcement')}}</h4>
                        <announcement-form :id="id"></announcement-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import announcementForm from './form';
    export default {
        components : { announcementForm },
        data() {
            return {
                id:this.$route.params.id
            }
        },
        mounted(){
            if(!helper.hasPermission('edit-announcement')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('announcement')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }
        }
    }
</script>

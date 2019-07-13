<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('message.message')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/message">{{trans('message.message')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('message.edit_draft')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <message-sidebar menu="draft" :statistics="statistics"></message-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <h4 class="card-title">{{trans('message.edit_draft')}}</h4>

                                <message-form :uuid="uuid"></message-form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import messageForm from './form';
    import messageSidebar from './message-sidebar'

    export default {
        components : { messageForm,messageSidebar },
        data() {
            return {
                uuid:this.$route.params.uuid,
                statistics: {
                    sent: 0,
                    inbox: 0,
                    draft: 0,
                    trash: 0
                }
            };
        },
        mounted(){
            if(!helper.hasPermission('access-message')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('message')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            axios.post('/api/message/statistics')
                .then(response => response.data)
                .then(response => {
                    this.statistics.inbox = response.inbox;
                    this.statistics.sent = response.sent;
                    this.statistics.draft = response.draft;
                    this.statistics.trash = response.trash;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        }
    }
</script>

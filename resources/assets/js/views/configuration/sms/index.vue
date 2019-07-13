<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('configuration.configuration')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/configuration/basic">{{trans('configuration.configuration')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('sms.sms')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="sms"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <show-tip module="sms" tip="tip_sms"></show-tip>
                                <h4 class="card-title">{{trans('sms.sms')}}</h4>
                                <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                                    <div class="form-group">
                                        <label for="">{{trans('sms.api_key')}}</label>
                                        <input class="form-control" type="text" value="" v-model="configForm.nexmo_api_key" name="nexmo_api_key" :placeholder="trans('sms.api_key')">
                                        <show-error :form-name="configForm" prop-name="nexmo_api_key"></show-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{trans('sms.api_secret')}}</label>
                                        <input class="form-control" type="text" value="" v-model="configForm.nexmo_api_secret" name="nexmo_api_secret" :placeholder="trans('sms.api_secret')">
                                        <show-error :form-name="configForm" prop-name="nexmo_api_secret"></show-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{trans('sms.sender_mobile')}}</label>
                                        <input class="form-control" type="text" value="" v-model="configForm.nexmo_sender_mobile" name="nexmo_sender_mobile" :placeholder="trans('sms.sender_mobile')">
                                        <show-error :form-name="configForm" prop-name="nexmo_sender_mobile"></show-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{trans('sms.receiver_mobile')}} <show-tip module="sms" tip="tip_receiver_mobile" type="field"></show-tip></label>
                                        <input class="form-control" type="text" value="" v-model="configForm.nexmo_receiver_mobile" name="nexmo_receiver_mobile" :placeholder="trans('sms.receiver_mobile')">
                                        <show-error :form-name="configForm" prop-name="nexmo_receiver_mobile"></show-error>
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

    export default {
        components : { configurationSidebar },
        data() {
            return {
                configForm: new Form({
                    nexmo_api_key: '',
                    nexmo_api_secret: '',
                    nexmo_sender_mobile: '',
                    nexmo_receiver_mobile: '',
                    config_type: ''
                },false)
            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            axios.get('/api/configuration')
                .then(response => response.data)
                .then(response => {
                    this.configForm = helper.formAssign(this.configForm, response);
                }).catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            submit(){
                this.configForm.config_type = 'sms';
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

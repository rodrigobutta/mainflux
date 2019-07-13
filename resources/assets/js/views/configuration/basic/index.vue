<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('configuration.configuration')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('configuration.configuration')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="basic"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <show-tip module="configuration" tip="tip_basic_configuration"></show-tip>
                                <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                                    <h4 class="card-title">{{trans('general.basic')}}</h4>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.company_name')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.company_name" name="company_name" :placeholder="trans('configuration.company_name')">
                                                <show-error :form-name="configForm" prop-name="company_name"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.contact_person')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.contact_person" name="contact_person" :placeholder="trans('configuration.contact_person')">
                                                <show-error :form-name="configForm" prop-name="contact_person"></show-error>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="card-title">{{trans('configuration.contact')}}</h4>
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.address_line_1')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.address_line_1" name="address_line_1" :placeholder="trans('configuration.address_line_1')">
                                                <show-error :form-name="configForm" prop-name="address_line_1"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.address_line_2')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.address_line_2" name="address_line_2" :placeholder="trans('configuration.address_line_2')">
                                                <show-error :form-name="configForm" prop-name="address_line_2"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.city')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.city" name="city" :placeholder="trans('configuration.city')">
                                                <show-error :form-name="configForm" prop-name="city"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.state')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.state" name="state" :placeholder="trans('configuration.state')">
                                                <show-error :form-name="configForm" prop-name="state"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.zipcode')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.zipcode" name="zipcode" :placeholder="trans('configuration.zipcode')">
                                                <show-error :form-name="configForm" prop-name="zipcode"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.country')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.country" name="country" :placeholder="trans('configuration.country')">
                                                <show-error :form-name="configForm" prop-name="country"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.phone')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.phone" name="phone" :placeholder="trans('configuration.phone')">
                                                <show-error :form-name="configForm" prop-name="phone"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.fax')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.fax" name="fax" :placeholder="trans('configuration.fax')">
                                                <show-error :form-name="configForm" prop-name="fax"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.email')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.email" name="email" :placeholder="trans('configuration.email')">
                                                <show-error :form-name="configForm" prop-name="email"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.website')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.website" name="website" :placeholder="trans('configuration.website')">
                                                <show-error :form-name="configForm" prop-name="website"></show-error>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-info waves-effect waves-light m-t-10 pull-right">{{trans('general.save')}}</button>
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
    import vSelect from 'vue-multiselect'
    import datepicker from 'vuejs-datepicker'

    export default {
        components : { configurationSidebar,vSelect,datepicker },
        data() {
            return {
                configForm: new Form({
                    company_name : '',
                    contact_person: '',
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    zipcode: '',
                    country: '',
                    phone: '',
                    fax: '',
                    email: '',
                    website: '',
                    config_type: ''
                }, false),
            };
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
                this.configForm.config_type = 'basic';
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

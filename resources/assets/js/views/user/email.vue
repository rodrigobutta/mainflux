<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('user.user_detail')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/user">{{trans('user.user')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('user.email')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-8 col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <user-sidebar menu="email" :id="id"></user-sidebar>
                            <div class="col-9 col-lg-9 col-md-9">
                                <h4 class="card-title">{{trans('user.email')}}</h4>
                                <form @submit.prevent="submit" @keydown="emailForm.errors.clear($event.target.name)">
                                    <div class="form-group">
                                        <label for="">{{trans('template.template')}}</label>
                                        <v-select label="name" v-model="selected_template" name="template_id" id="template_id" :options="templates" :placeholder="trans('template.select_template')" @select="getTemplateContent" @close="emailForm.errors.clear('template_id')" @remove="emailForm.template_id = ''"></v-select>
                                        <show-error :form-name="emailForm" prop-name="template_id"></show-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{trans('template.subject')}}</label>
                                        <input class="form-control" type="text" value="" v-model="emailForm.subject" name="subject" :placeholder="trans('user.subject')">
                                        <show-error :form-name="emailForm" prop-name="subject"></show-error>
                                    </div>
                                    <div class="form-group">
                                        <html-editor name="body" :model.sync="emailForm.body" isUpdate="true" :reload-content="reload_content" @clearErrors="emailForm.errors.clear('body')"></html-editor>
                                        <show-error :form-name="emailForm" prop-name="body"></show-error>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-info pull-right">{{trans('message.send')}}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <user-summary :user="user"></user-summary>
        </div>
    </div>
</template>


<script>
    import userSidebar from './user-sidebar'
    import userSummary from './summary'
    import vSelect from 'vue-multiselect'
    import htmlEditor from '../../components/html-editor'

    export default {
        components : { userSidebar,userSummary,vSelect,htmlEditor },
        data() {
            return {
                id:this.$route.params.id,
                user: '',
                templates: [],
                emailForm: new Form({
                    template_id: '',
                    subject: '',
                    body: ''
                }),
                selected_template: null,
                reload_content: false,
            };
        },
        mounted(){
            if(!helper.hasPermission('email-user')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('email_template')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            axios.get('/api/user/'+this.id)
                .then(response => response.data)
                .then(response => {
                    this.user = response.user;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });

            if(helper.getConfig('email_template'))
            axios.get('/api/email-template/user/lists')
                .then(response => response.data)
                .then(response => {
                    this.templates = response;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                })
        },
        methods: {
            getTemplateContent(selectedOption){
                this.emailForm.template_id = selectedOption.id
                axios.get('/api/email-template/'+selectedOption.id+'/content?user_id='+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.emailForm.subject = response.mail_data.subject;
                        this.reload_content = true;
                        this.emailForm.body = response.mail_data.body;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
                this.reload_content = false;
            },
            submit(){
                this.emailForm.patch('/api/user/'+this.id+'/email')
                    .then(response => {
                        toastr.success(response.message);
                        this.selected_template= null;
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>

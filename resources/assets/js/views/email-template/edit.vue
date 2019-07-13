<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('template.edit_template')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/email-template">{{trans('template.email_template')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('template.edit_template')}}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('template.edit_template')}}</h4>
                        <form @submit.prevent="submit" @keydown="templateForm.errors.clear($event.target.name)">
                            <div class="form-group">
                                <label for="">{{trans('template.subject')}}</label>
                                <input class="form-control" type="text" value="" v-model="templateForm.subject" name="subject" :placeholder="trans('template.subject')">
                                <show-error :form-name="templateForm" prop-name="subject"></show-error>
                            </div>
                            <div class="form-group">
                                <html-editor name="body" :model.sync="templateForm.body" isUpdate="true" @clearErrors="templateForm.errors.clear('body')"></html-editor>
                                <show-error :form-name="templateForm" prop-name="body"></show-error>
                            </div>
                            <div class="help-block">{{trans('template.available_fields')}}: {{fields}}</div>
                            <button type="submit" class="btn btn-info waves-effect waves-light pull-right m-t-10">{{trans('general.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import htmlEditor from '../../components/html-editor'

    export default {
        components : { htmlEditor },
        data() {
            return {
                id:this.$route.params.id,
                templateForm: new Form({
                    subject: '',
                    body: '',
                }),
                fields: ''
            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('email_template')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            axios.get('/api/email-template/'+this.id)
                .then(response => response.data)
                .then(response => {
                    this.templateForm.subject = response.email_template.subject;
                    this.templateForm.body = response.email_template.body;
                    this.fields = response.fields;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    this.$router.push('/email-template');
                })
        },
        methods: {
            submit(){
                this.templateForm.patch('/api/email-template/'+this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/email-template');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>

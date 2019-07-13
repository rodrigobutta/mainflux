<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('template.template')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('template.template')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <transition name="fade">
                    <div class="card" v-if="showCreatePanel">
                        <div class="card-body">
                            <button class="btn btn-info btn-sm pull-right" v-if="showCreatePanel" @click="showCreatePanel = !showCreatePanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('template.add_new_template')}}</h4>
                            <form @submit.prevent="submit" @keydown="templateForm.errors.clear($event.target.name)">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="">{{trans('template.name')}}</label>
                                            <input class="form-control" type="text" value="" v-model="templateForm.name" name="name" :placeholder="trans('template.name')">
                                            <show-error :form-name="templateForm" prop-name="name"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">                                    
                                        <div class="form-group">
                                            <label for="">{{trans('template.category')}}</label>
                                            <v-select label="name" v-model="selected_category" name="category" id="category" :options="categories" :placeholder="trans('template.select_category')" @select="onCategorySelect" @close="templateForm.errors.clear('category')" @remove="templateForm.category = ''"></v-select>
                                            <show-error :form-name="templateForm" prop-name="category"></show-error>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info waves-effect waves-light pull-right">{{trans('general.save')}}</button>
                            </form>
                        </div>
                    </div>
                </transition>

                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-info btn-sm pull-right" v-if="email_templates.total && !showCreatePanel" @click="showCreatePanel = !showCreatePanel">{{trans('template.add_new_template')}}</button>
                        <h4 class="card-title">{{trans('template.template_list')}}</h4>
                        <h6 class="card-subtitle" v-if="email_templates">{{trans('general.total_result_found',{'count' : email_templates.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="email_templates.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('template.name')}}</th>
                                        <th>{{trans('template.category')}}</th>
                                        <th>{{trans('template.subject')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="email_template in email_templates.data">
                                        <td v-text="email_template.name"></td>
                                        <td v-text="toWord(email_template.category)"></td>
                                        <td v-text="email_template.subject"></td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-tooltip="trans('template.edit_template')" @click.prevent="editEmailTemplate(email_template)"><i class="fas fa-edit"></i></button>
                                                <button v-if="!email_template.is_default" :key="email_template.id" class="btn btn-danger btn-sm" v-confirm="{ok: confirmDelete(email_template)}" v-tooltip="trans('template.delete_template')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!email_templates.total" module="template" title="module_info_title" description="module_info_description" icon="envelope">
                            <div slot="btn">
                                <button class="btn btn-info btn-md" v-if="!showCreatePanel" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> {{trans('general.add_new')}}</button>
                            </div>
                        </module-info>
                        <pagination-record :page-length.sync="filterEmailTemplateForm.page_length" :records="email_templates" @updateRecords="getEmailTemplates" @change.native="getEmailTemplates"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import vSelect from 'vue-multiselect'

    export default {
        components : { vSelect },
        data() {
            return {
                showCreatePanel: false,
                email_templates: {
                    total: 0,
                    data: []
                },
                filterEmailTemplateForm: {
                    page_length: helper.getConfig('page_length')
                },
                templateForm: new Form({
                    name: '',
                    category: ''
                }),
                categories: [
                    {name: i18n.user.user, id: 'user'}
                ],
                selected_category: null
            };
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

            this.getEmailTemplates();
        },
        methods: {
            getEmailTemplates(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterEmailTemplateForm);
                axios.get('/api/email-template?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.email_templates = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editEmailTemplate(email_template){
                this.$router.push('/email-template/'+email_template.id+'/edit');
            },
            confirmDelete(email_template){
                return dialog => this.deleteEmailTemplate(email_template);
            },
            deleteEmailTemplate(email_template){
                axios.delete('/api/email-template/'+email_template.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getEmailTemplates();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            toWord(value){
                return helper.toWord(value);
            },
            submit(){
                this.templateForm.post('/api/email-template')
                    .then(response => {
                        toastr.success(response.message);
                        this.selected_category = null;
                        this.getEmailTemplates();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            onCategorySelect(selectedOption){
                this.templateForm.category = selectedOption.id;
            }
        }
    }
</script>
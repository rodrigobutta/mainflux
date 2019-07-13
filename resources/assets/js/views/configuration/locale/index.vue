<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('configuration.configuration')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/configuration/basic">{{trans('configuration.configuration')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('locale.locale')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="locale"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <show-tip module="locale" tip="tip_locale"></show-tip>
                                <div class="row">
                                    <div class="col-12 col-sm-4 col-md-4">
                                        <h4 class="card-title">{{trans('locale.add_new_locale')}}</h4>
                                        <locale-form @completed="getLocales"></locale-form>
                                        <div class="clearfix"></div>

                                        <h4 class="card-title m-t-20">{{trans('locale.add_new_word')}} <show-tip module="locale" tip="tip_add_word" type="field"></show-tip></h4>
                                        <form @submit.prevent="addWord" @keydown="addWordForm.errors.clear($event.target.name)">
                                            <div class="form-group">
                                                <label for="">{{trans('locale.word')}}</label>
                                                <input class="form-control" type="text" value="" v-model="addWordForm.word" name="word" :placeholder="trans('locale.word')">
                                                <show-error :form-name="addWordForm" prop-name="word"></show-error>
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{trans('locale.module')}}</label>
                                                <v-select v-model="addWordForm.module" name="module" id="module" :options="modules" :placeholder="trans('locale.select_module')" @select="addWordForm.errors.clear('module')" @remove="addWordForm.module = ''"></v-select>
                                                <show-error :form-name="addWordForm" prop-name="module"></show-error>
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{trans('locale.translation')}}</label>
                                                <input class="form-control" type="text" value="" v-model="addWordForm.translation" name="translation" :placeholder="trans('locale.translation')">
                                                <show-error :form-name="addWordForm" prop-name="translation"></show-error>
                                            </div>
                                            <button type="submit" class="btn btn-info waves-effect waves-light pull-right">
                                                <span v>{{trans('general.add')}}</span>
                                            </button>
                                        </form>

                                    </div>
                                    <div class="col-12 col-sm-8 col-md-8">
                                        <h4 class="card-title">{{trans('locale.locale_list')}}</h4>
                                        <h6 class="card-subtitle" v-if="locales">{{trans('general.total_result_found',{count:locales.total})}}</h6>
                                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                                        <div class="table-responsive" v-if="locales.total">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{trans('general.name')}}</th>
                                                        <th>{{trans('locale.locale')}}</th>
                                                        <th class="table-option">{{trans('general.action')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="locale in locales.data">
                                                        <td v-text="locale.name"></td>
                                                        <td v-text="locale.locale"></td>
                                                        <td class="table-option">
                                                            <div class="btn-group">
                                                                <button class="btn btn-info btn-sm" v-tooltip="trans('locale.edit_locale')" @click.prevent="editLocale(locale)"><i class="fas fa-edit"></i></button>
                                                                <router-link :to="`/configuration/locale/${locale.locale}/auth`" class="btn btn-success btn-sm" v-tooltip="trans('locale.translation')"><i class="fas fa-arrow-circle-right"></i></router-link>
                                                                <button class="btn btn-danger btn-sm" :key="locale.id" v-if="locale.locale !== 'en'" v-confirm="{ok: confirmDelete(locale)}" v-tooltip="trans('locale.delete_locale')"><i class="fas fa-trash"></i></button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <module-info v-if="!locales.total" module="locale" title="module_info_title" description="module_info_description" icon="globe"></module-info>
                                        <pagination-record :page-length.sync="filterLocaleForm.page_length" :records="locales" @updateRecords="getLocales" @change.native="getLocales"></pagination-record>
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
    import localeForm from './form'
    import vSelect from 'vue-multiselect'

    export default {
        components : { configurationSidebar,localeForm,vSelect },
        data() {
            return {
                locales: {
                    total: 0,
                    data: []
                },
                filterLocaleForm: {
                    page_length: helper.getConfig('page_length')
                },
                addWordForm: new Form({
                    word: '',
                    translation: '',
                    module: ''
                }),
                modules: []
            };
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('multilingual')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            this.getLocales();
        },
        methods: {
            getLocales(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterLocaleForm);
                axios.get('/api/locale?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.locales = response.locales;
                        this.modules = response.modules;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editLocale(locale){
                this.$router.push('/configuration/locale/'+locale.id+'/edit');
            },
            confirmDelete(locale){
                return dialog => this.deleteLocale(locale);
            },
            deleteLocale(locale){
                axios.delete('/api/locale/'+locale.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getLocales();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            addWord(){
                this.addWordForm.post('/api/locale/add-word')
                    .then(response => {
                        toastr.success(response.message)
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>

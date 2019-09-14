<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('contractor.contractor')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('contractor.contractor')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-contractor')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('contractor.add_new_contractor')}}</h4>
                        <contractor-form @completed="getContractors"></contractor-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8" v-if="hasPermission('list-contractor')">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('contractor.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterContractorForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterContractorForm.sortBy">
                                            <option value="name">{{trans('contractor.name')}}</option>
                                            <option value="description">{{trans('contractor.description')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterContractorForm.order">
                                            <option value="asc">{{trans('general.ascending')}}</option>
                                            <option value="desc">{{trans('general.descending')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-info btn-sm pull-right" v-if="!showFilterPanel" @click="showFilterPanel = !showFilterPanel"><i class="fas fa-filter"></i> {{trans('general.filter')}}</button>
                        <h4 class="card-title">{{trans('contractor.contractor_list')}}</h4>
                        <h6 class="card-subtitle" v-if="contractors">{{trans('general.total_result_found',{'count' : contractors.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="contractors.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('contractor.name')}}</th>
                                        <th>{{trans('contractor.description')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="contractor in contractors.data" v-bind:key="contractor">
                                        <td v-text="contractor.name"></td>
                                        <td v-text="contractor.description"></td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-contractor')" v-tooltip="trans('contractor.edit_contractor')" @click.prevent="editContractor(contractor)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-contractor')" :key="contractor.id" v-confirm="{ok: confirmDelete(contractor)}" v-tooltip="trans('contractor.delete_contractor')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>                            
                        <module-info v-if="!contractors.total" module="contractor" title="module_info_title" description="module_info_description" icon="bank">
                        </module-info>
                        <pagination-record :page-length.sync="filterContractorForm.page_length" :records="contractors" @updateRecords="getContractors"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import contractorForm from './form'

    export default {
        components : {
            contractorForm
        },
        data() {
            return {
                contractors: {},
                filterContractorForm: {
                    name: '',
                    sortBy : 'name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                showFilterPanel: false
            };
        },
        mounted(){
            if(!helper.hasPermission('list-contractor') && !helper.hasPermission('create-contractor')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getContractors();


            var self = this;
            self.$nextTick(function(){

            });

            
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getContractors(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterContractorForm);
                axios.get('/api/contractor?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.contractors = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editContractor(contractor){
                this.$router.push('/contractor/'+contractor.id+'/edit');
            },
            confirmDelete(contractor){
                return dialog => this.deleteContractor(contractor);
            },
            deleteContractor(contractor){
                axios.delete('/api/contractor/'+contractor.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getContractors();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        watch: {
            filterContractorForm: {
                handler(val){
                    this.getContractors();
                },
                deep: true
            }
        }
    }
</script>

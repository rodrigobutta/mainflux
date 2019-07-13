<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('designation.designation')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('designation.designation')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-designation')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('designation.add_new_designation')}}</h4>
                        <designation-form @completed="getDesignations"></designation-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8" v-if="hasPermission('list-designation')">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('designation.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterDesignationForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group" v-show="departments">
                                        <label for="">{{trans('department.department')}}</label>
                                        <select v-model="filterDesignationForm.department_id" class="custom-select col-12">
                                          <option value="">{{trans('general.select_one')}}</option>
                                          <option v-for="department in departments" v-bind:value="department.id">
                                            {{ department.name }}
                                          </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group" v-show="designations">
                                        <label for="">{{trans('designation.top_designation')}}</label>
                                        <select v-model="filterDesignationForm.top_designation_id" class="custom-select col-12">
                                          <option value="">{{trans('general.select_one')}}</option>
                                          <option v-for="designation in top_designations" v-bind:value="designation.id">
                                            {{ designation.name }}
                                          </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterDesignationForm.sortBy">
                                            <option value="name">{{trans('designation.name')}}</option>
                                            <option value="department_id">{{trans('department.department')}}</option>
                                            <option value="top_designation_id">{{trans('designation.top_designation')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterDesignationForm.order">
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
                        <h4 class="card-title">{{trans('designation.designation_list')}}</h4>
                        <h6 class="card-subtitle" v-if="designations">{{trans('general.total_result_found',{'count' : designations.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="designations.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('designation.name')}}</th>
                                        <th>{{trans('department.department')}}</th>
                                        <th>{{trans('designation.top_designation')}}</th>
                                        <th>{{trans('designation.default')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="designation in designations.data">
                                        <td v-text="designation.name"></td>
                                        <td v-text="designation.department.name"></td>
                                        <td v-text="designation.top_designation_id ? designation.parent.name : ''"></td>
                                        <td>
                                            <span v-if="designation.is_default"><i class="fas fa-check"></i></span>
                                            <span v-else><i class="fas fa-times"></i></span>
                                        </td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-designation')" v-tooltip="trans('designation.edit_designation')" @click.prevent="editDesignation(designation)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-designation')" :key="designation.id" v-confirm="{ok: confirmDelete(designation)}" v-tooltip="trans('designation.delete_designation')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!designations.total" module="designation" title="module_info_title" description="module_info_description" icon="sitemap">
                        </module-info>
                        <pagination-record :page-length.sync="filterDesignationForm.page_length" :records="designations" @updateRecords="getDesignations"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import designationForm from './form'

    export default {
        components : { designationForm },
        data() {
            return {
                designations: {},
                filterDesignationForm: {
                    name: '',
                    department_id: '',
                    top_designation_id: '',
                    sortBy : 'name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                departments: {},
                top_designations: {},
                showFilterPanel: false
            };
        },
        mounted(){
            if(!helper.hasPermission('list-designation') && !helper.hasPermission('create-designation')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getDesignations();
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getDesignations(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterDesignationForm);
                axios.get('/api/designation?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.designations = response.designations;
                        this.top_designations = response.top_designations;
                        this.departments = response.departments;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editDesignation(designation){
                this.$router.push('/designation/'+designation.id+'/edit');
            },
            confirmDelete(designation){
                return dialog => this.deleteDesignation(designation);
            },
            deleteDesignation(designation){
                axios.delete('/api/designation/'+designation.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getDesignations();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        watch: {
            filterDesignationForm: {
                handler(val){
                    this.getDesignations();
                },
                deep: true
            }
        }
    }
</script>

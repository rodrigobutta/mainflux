<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('department.department')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('department.department')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-department')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('department.add_new_department')}}</h4>
                        <department-form @completed="getDepartments"></department-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8" v-if="hasPermission('list-department')">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('department.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterDepartmentForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterDepartmentForm.sortBy">
                                            <option value="name">{{trans('department.name')}}</option>
                                            <option value="description">{{trans('department.description')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterDepartmentForm.order">
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
                        <h4 class="card-title">{{trans('department.department_list')}}</h4>
                        <h6 class="card-subtitle" v-if="departments">{{trans('general.total_result_found',{'count' : departments.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="departments.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('department.name')}}</th>
                                        <th>{{trans('department.description')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="department in departments.data">
                                        <td v-text="department.namedepartments"></td>
                                        <td v-text="department.description"></td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-department')" v-tooltip="trans('department.edit_department')" @click.prevent="editDepartment(department)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-department')" :key="department.id" v-confirm="{ok: confirmDelete(department)}" v-tooltip="trans('department.delete_department')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <draggable v-model="items">
                            <transition-group>                       
                                <ul v-for="item in items" :key="item.id">
                                    <li class="item">{{ item.title }}</li>
                                </ul>                                
                            </transition-group>
                        </draggable>
                        
                            
                        <module-info v-if="!departments.total" module="department" title="module_info_title" description="module_info_description" icon="bank">
                        </module-info>
                        <pagination-record :page-length.sync="filterDepartmentForm.page_length" :records="departments" @updateRecords="getDepartments"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import departmentForm from './form'
    import draggable from 'vuedraggable'

    export default {
        components : {
            departmentForm,
            draggable
        },
        data() {
            return {
                departments: {},
                filterDepartmentForm: {
                    name: '',
                    sortBy : 'name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                showFilterPanel: false,

                items: [
                    {id: 1, title: "Item 1"},
                    {id: 2, title: "Item 2"},
                    {id: 3, title: "Item 3"}
                ]
                
            };
        },
        mounted(){
            if(!helper.hasPermission('list-department') && !helper.hasPermission('create-department')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getDepartments();


            var self = this;
            self.$nextTick(function(){

            });

            
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getDepartments(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterDepartmentForm);
                axios.get('/api/department?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.departments = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editDepartment(department){
                this.$router.push('/department/'+department.id+'/edit');
            },
            confirmDelete(department){
                return dialog => this.deleteDepartment(department);
            },
            deleteDepartment(department){
                axios.delete('/api/department/'+department.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getDepartments();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        watch: {
            filterDepartmentForm: {
                handler(val){
                    this.getDepartments();
                },
                deep: true
            }
        }
    }
</script>

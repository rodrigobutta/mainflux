<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('task-family.task_family')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('task-family.task_family')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-task-family')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task-family.add_new_task_family')}}</h4>
                        <task-family-form @completed="getTaskFamilys"></task-family-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8" v-if="hasPermission('list-task-family')">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('task-family.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterTaskFamilyForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('task-family.code')}}</label>
                                        <input class="form-control" name="code" v-model="filterTaskFamilyForm.code">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterTaskFamilyForm.sortBy">
                                            <option value="name">{{trans('task-family.name')}}</option>
                                            <option value="description">{{trans('task-family.description')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterTaskFamilyForm.order">
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
                        <h4 class="card-title">{{trans('task-family.task_family_list')}}</h4>
                        <h6 class="card-subtitle" v-if="taskFamilys">{{trans('general.total_result_found',{'count' : taskFamilys.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="taskFamilys.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('task-family.name')}}</th>
                                        <th>{{trans('task-family.code')}}</th>
                                        <th>{{trans('task-family.description')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="task_family in taskFamilys.data" v-bind:key="task_family.id">
                                        <td v-text="task_family.name"></td>
                                        <td v-text="task_family.code"></td>
                                        <td v-text="task_family.description"></td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-task-family')" v-tooltip="trans('task-family.edit_task_family')" @click.prevent="editContractor(task_family)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-task-family')" :key="task_family.id" v-confirm="{ok: confirmDelete(task_family)}" v-tooltip="trans('task-family.delete_task_family')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>                            
                        <module-info v-if="!taskFamilys.total" module="task_family" title="module_info_title" description="module_info_description" icon="bank">
                        </module-info>
                        <pagination-record :page-length.sync="filterTaskFamilyForm.page_length" :records="taskFamilys" @updateRecords="getTaskFamilys"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import taskFamilyForm from './form'

    export default {
        components : {
            taskFamilyForm
        },
        data() {
            return {
                taskFamilys: {},
                filterTaskFamilyForm: {
                    name: '',
                    code: '',
                    sortBy : 'name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                showFilterPanel: false
            };
        },
        mounted(){
            if(!helper.hasPermission('list-task-family') && !helper.hasPermission('create-task-family')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getTaskFamilys();


            var self = this;
            self.$nextTick(function(){

            });

            
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getTaskFamilys(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterTaskFamilyForm);
                axios.get('/api/task-family?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.taskFamilys = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editContractor(item){
                this.$router.push('/task-family/'+item.id+'/edit');
            },
            confirmDelete(item){
                return dialog => this.deleteContractor(item);
            },
            deleteContractor(item){
                axios.delete('/api/task-family/'+item.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getTaskFamilys();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        watch: {
            filterTaskFamilyForm: {
                handler(val){
                    this.getTaskFamilys();
                },
                deep: true
            }
        }
    }
</script>

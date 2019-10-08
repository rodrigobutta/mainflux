<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('task_complexity.task_complexity')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('task_complexity.task_complexity')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-task-complexity')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task_complexity.add_new_task_complexity')}}</h4>
                        <task-complexity-form @completed="getTaskComplexitys"></task-complexity-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8" v-if="hasPermission('list-task-complexity')">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('task_complexity.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterTaskComplexityForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('task_complexity.code')}}</label>
                                        <input class="form-control" name="code" v-model="filterTaskComplexityForm.code">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterTaskComplexityForm.sortBy">
                                            <option value="name">{{trans('task_complexity.name')}}</option>
                                            <option value="description">{{trans('task_complexity.description')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterTaskComplexityForm.order">
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
                        <h4 class="card-title">{{trans('task_complexity.task_complexity_list')}}</h4>
                        <h6 class="card-subtitle" v-if="taskComplexitys">{{trans('general.total_result_found',{'count' : taskComplexitys.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="taskComplexitys.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('task_complexity.name')}}</th>
                                        <th>{{trans('task_complexity.code')}}</th>
                                        <th>{{trans('task_complexity.description')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="task_complexity in taskComplexitys.data" v-bind:key="task_complexity.id">
                                        <td v-text="task_complexity.name"></td>
                                        <td v-text="task_complexity.code"></td>
                                        <td v-text="task_complexity.description"></td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-task-complexity')" v-tooltip="trans('task_complexity.edit_task_complexity')" @click.prevent="editContractor(task_complexity)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-task-complexity')" :key="task_complexity.id" v-confirm="{ok: confirmDelete(task_complexity)}" v-tooltip="trans('task_complexity.delete_task_complexity')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>                            
                        <module-info v-if="!taskComplexitys.total" module="task_complexity" title="module_info_title" description="module_info_description" icon="bank">
                        </module-info>
                        <pagination-record :page-length.sync="filterTaskComplexityForm.page_length" :records="taskComplexitys" @updateRecords="getTaskComplexitys"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import taskComplexityForm from './form'

    export default {
        components : {
            taskComplexityForm
        },
        data() {
            return {
                taskComplexitys: {},
                filterTaskComplexityForm: {
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
            if(!helper.hasPermission('list-task-complexity') && !helper.hasPermission('create-task-complexity')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getTaskComplexitys();


            var self = this;
            self.$nextTick(function(){

            });

            
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getTaskComplexitys(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterTaskComplexityForm);
                axios.get('/api/task-complexity?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.taskComplexitys = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editContractor(item){
                this.$router.push('/task-complexity/'+item.id+'/edit');
            },
            confirmDelete(item){
                return dialog => this.deleteContractor(item);
            },
            deleteContractor(item){
                axios.delete('/api/task-complexity/'+item.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getTaskComplexitys();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        watch: {
            filterTaskComplexityForm: {
                handler(val){
                    this.getTaskComplexitys();
                },
                deep: true
            }
        }
    }
</script>

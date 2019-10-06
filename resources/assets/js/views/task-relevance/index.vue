<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('task-relevance.task_relevance')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('task-relevance.task_relevance')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-task-relevance')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task-relevance.add_new_task_relevance')}}</h4>
                        <task-relevance-form @completed="getTaskRelevances"></task-relevance-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8" v-if="hasPermission('list-task-relevance')">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('task-relevance.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterTaskRelevanceForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('task-relevance.code')}}</label>
                                        <input class="form-control" name="code" v-model="filterTaskRelevanceForm.code">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterTaskRelevanceForm.sortBy">
                                            <option value="name">{{trans('task-relevance.name')}}</option>
                                            <option value="description">{{trans('task-relevance.description')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterTaskRelevanceForm.order">
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
                        <h4 class="card-title">{{trans('task-relevance.task_relevance_list')}}</h4>
                        <h6 class="card-subtitle" v-if="taskRelevances">{{trans('general.total_result_found',{'count' : taskRelevances.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="taskRelevances.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('task-relevance.name')}}</th>
                                        <th>{{trans('task-relevance.code')}}</th>
                                        <th>{{trans('task-relevance.description')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="task_relevance in taskRelevances.data" v-bind:key="task_relevance.id">
                                        <td v-text="task_relevance.name"></td>
                                        <td v-text="task_relevance.code"></td>
                                        <td v-text="task_relevance.description"></td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-task-relevance')" v-tooltip="trans('task-relevance.edit_task_relevance')" @click.prevent="editContractor(task_relevance)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-task-relevance')" :key="task_relevance.id" v-confirm="{ok: confirmDelete(task_relevance)}" v-tooltip="trans('task-relevance.delete_task_relevance')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>                            
                        <module-info v-if="!taskRelevances.total" module="task_relevance" title="module_info_title" description="module_info_description" icon="bank">
                        </module-info>
                        <pagination-record :page-length.sync="filterTaskRelevanceForm.page_length" :records="taskRelevances" @updateRecords="getTaskRelevances"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import taskRelevanceForm from './form'

    export default {
        components : {
            taskRelevanceForm
        },
        data() {
            return {
                taskRelevances: {},
                filterTaskRelevanceForm: {
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
            if(!helper.hasPermission('list-task-relevance') && !helper.hasPermission('create-task-relevance')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getTaskRelevances();


            var self = this;
            self.$nextTick(function(){

            });

            
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getTaskRelevances(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterTaskRelevanceForm);
                axios.get('/api/task-relevance?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.taskRelevances = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editContractor(item){
                this.$router.push('/task-relevance/'+item.id+'/edit');
            },
            confirmDelete(item){
                return dialog => this.deleteContractor(item);
            },
            deleteContractor(item){
                axios.delete('/api/task-relevance/'+item.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getTaskRelevances();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        watch: {
            filterTaskRelevanceForm: {
                handler(val){
                    this.getTaskRelevances();
                },
                deep: true
            }
        }
    }
</script>

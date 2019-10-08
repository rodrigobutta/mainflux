<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('task_frequency.task_frequency')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('task_frequency.task_frequency')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-task-frequency')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task_frequency.add_new_task_frequency')}}</h4>
                        <task-frequency-form @completed="getTaskFrequencys"></task-frequency-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8" v-if="hasPermission('list-task-frequency')">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('task_frequency.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterTaskFrequencyForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('task_frequency.code')}}</label>
                                        <input class="form-control" name="code" v-model="filterTaskFrequencyForm.code">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterTaskFrequencyForm.sortBy">
                                            <option value="name">{{trans('task_frequency.name')}}</option>
                                            <option value="description">{{trans('task_frequency.description')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterTaskFrequencyForm.order">
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
                        <h4 class="card-title">{{trans('task_frequency.task_frequency_list')}}</h4>
                        <h6 class="card-subtitle" v-if="taskFrequencys">{{trans('general.total_result_found',{'count' : taskFrequencys.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="taskFrequencys.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('task_frequency.name')}}</th>
                                        <th>{{trans('task_frequency.code')}}</th>
                                        <th>{{trans('task_frequency.description')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="task_frequency in taskFrequencys.data" v-bind:key="task_frequency.id">
                                        <td v-text="task_frequency.name"></td>
                                        <td v-text="task_frequency.code"></td>
                                        <td v-text="task_frequency.description"></td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-task-frequency')" v-tooltip="trans('task_frequency.edit_task_frequency')" @click.prevent="editContractor(task_frequency)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-task-frequency')" :key="task_frequency.id" v-confirm="{ok: confirmDelete(task_frequency)}" v-tooltip="trans('task_frequency.delete_task_frequency')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>                            
                        <module-info v-if="!taskFrequencys.total" module="task_frequency" title="module_info_title" description="module_info_description" icon="bank">
                        </module-info>
                        <pagination-record :page-length.sync="filterTaskFrequencyForm.page_length" :records="taskFrequencys" @updateRecords="getTaskFrequencys"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import taskFrequencyForm from './form'

    export default {
        components : {
            taskFrequencyForm
        },
        data() {
            return {
                taskFrequencys: {},
                filterTaskFrequencyForm: {
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
            if(!helper.hasPermission('list-task-frequency') && !helper.hasPermission('create-task-frequency')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getTaskFrequencys();


            var self = this;
            self.$nextTick(function(){

            });

            
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getTaskFrequencys(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterTaskFrequencyForm);
                axios.get('/api/task-frequency?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.taskFrequencys = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editContractor(item){
                this.$router.push('/task-frequency/'+item.id+'/edit');
            },
            confirmDelete(item){
                return dialog => this.deleteContractor(item);
            },
            deleteContractor(item){
                axios.delete('/api/task-frequency/'+item.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getTaskFrequencys();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        watch: {
            filterTaskFrequencyForm: {
                handler(val){
                    this.getTaskFrequencys();
                },
                deep: true
            }
        }
    }
</script>

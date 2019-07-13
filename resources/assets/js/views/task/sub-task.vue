<template>
    <div>
        <button class="pull-right btn btn-sm btn-rounded btn-success" data-toggle="modal" v-if="hasPermission('create-sub-task')" data-target=".sub-task-form">{{trans('task.add_new_sub_task')}}</button>

        <sub-task-form :uuid="uuid" :suuid="editSubTaskUuid" @completed="subTaskComplete" @loaded="reset"></sub-task-form>

        <sub-task-detail :uuid="uuid" :suuid="showSubTaskUuid" @updateStatus="updateTaskProgress" @loaded="reset"></sub-task-detail>

        <h4 class="card-title m-t-20">{{trans('task.sub_task_list')}}</h4>
        <h6 class="card-subtitle" v-if="subTasks">{{trans('general.total_result_found',{'count' : subTasks.total})}}</h6>
        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
        <div class="table-responsive">
            <table class="table" v-if="subTasks.total">
                <thead>
                    <tr>
                        <th>{{trans('task.sub_task_title')}}</th>
                        <th>{{trans('task.sub_task_status')}}</th>
                        <th>{{trans('task.owner')}}</th>
                        <th>{{trans('task.created_at')}}</th>
                        <th class="table-option">{{trans('general.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="subTask in subTasks.data">
                        <td v-text="subTask.title"></td>
                        <td><span :class="['label','label-'+getSubTaskStatus(subTask).color]">{{getSubTaskStatus(subTask).label}}</span></td>
                        <td v-if="subTask.user_added">{{ subTask.user_added.profile.first_name+' '+subTask.user_added.profile.last_name}}</td>
                        <td>{{subTask.created_at | momentDateTime}}</td>
                        <td class="table-option">
                            <div class="btn-group">
                                <button class="btn btn-success btn-sm" v-tooltip="trans('task.view_sub_task')" data-toggle="modal" data-target=".sub-task-detail" @click="showSubTask(subTask)"><i class="fas fa-arrow-circle-right"></i></button>
                                <template v-if="hasRole('admin') || hasPermission('access-all-task') || subTask.task.user_id === getAuthUser('id') || subTask.user_id === getAuthUser('id')">
                                    <button class="btn btn-info btn-sm" v-if="hasPermission('edit-sub-task')" v-tooltip="trans('task.edit_sub_task')" data-toggle="modal" data-target=".sub-task-form" @click="editSubTask(subTask)"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-sub-task')" :key="subTask.id" v-confirm="{ok: confirmDeleteSubTask(subTask)}" v-tooltip="trans('task.delete_sub_task')"><i class="fas fa-trash"></i></button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination-record :page-length.sync="filterSubTaskForm.page_length" :records="subTasks" @updateRecords="getSubTasks" @change.native="getSubTasks"></pagination-record>
    </div>
</template>

<script>
    import subTaskForm from './sub-task-form'
    import subTaskDetail from './sub-task-detail'

    export default {
        components: {subTaskForm,subTaskDetail},
        data(){
            return {
                subTasks: {},
                filterSubTaskForm: {
                    sortBy : 'created_at',
                    order: 'desc',
                    page_length: helper.getConfig('page_length')
                },
                showSubTaskUuid: '',
                editSubTaskUuid: ''
            }
        },
        props: ['uuid'],
        mounted(){
            this.getSubTasks();
        },
        methods: {
            hasRole(role){
                return helper.hasRole(role);
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getSubTasks(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterSubTaskForm);
                axios.get('/api/task/'+this.uuid+'/sub-task?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.subTasks = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
                this.suuid = '';
            },
            confirmDeleteSubTask(subTask){
                return dialog => this.deleteSubTask(subTask);
            },
            deleteSubTask(subTask){
                axios.delete('/api/task/'+this.uuid+'/sub-task/'+subTask.uuid)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.updateTaskProgress();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getSubTaskStatus(subTask){
                return helper.getSubTaskStatus(subTask);
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            subTaskComplete(){
                this.editSubTaskUuid = '';
                this.updateTaskProgress();
            },
            updateTaskProgress(){
                this.getSubTasks();
                this.$emit('updateProgress')
            },
            showSubTask(subTask){
                this.showSubTaskUuid = subTask.uuid;
            },
            editSubTask(subTask){
                this.editSubTaskUuid = subTask.uuid;
            },
            reset(){
                this.showSubTaskUuid = '';
                this.editSubTaskUuid = '';
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          },
          momentDateTime(datetime) {
            return helper.formatDateTime(datetime);
          }
        },
        watch: {

        }
    }
</script>

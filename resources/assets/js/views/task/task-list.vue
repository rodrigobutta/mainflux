<template>
    <div>
        <h6 class="card-subtitle" v-if="tasks">{{trans('general.total_result_found',{'count' : tasks.total})}}</h6>
        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
        <div class="table-responsive">
            <table class="table" v-if="tasks.total">
                <thead>
                    <tr>
                        <th>{{trans('task.number')}}</th>
                        <th>{{trans('task.title')}}</th>
                        <th>{{trans('task.category')}}</th>
                        <th>{{trans('task.priority')}}</th>
                        <th>{{trans('task.start_date')}}</th>
                        <th>{{trans('task.due_date')}}</th>
                        <th>{{trans('task.progress')}}</th>
                        <th>{{trans('task.status')}}</th>
                        <th>{{trans('task.owner')}}</th>
                        <th class="table-option">{{trans('general.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="task in tasks.data">
                        <td v-text="getTaskNumber(task)"></td>
                        <td v-text="task.title"></td>
                        <td v-text="task.task_category.name"></td>
                        <td v-text="task.task_priority.name"></td>
                        <td>{{ task.start_date | moment}}</td>
                        <td>{{ task.due_date | moment}}</td>
                        <td>
                            <div class="progress" style="height: 10px;">
                                <div :class="getTaskProgressColor(task)" role="progressbar" :style="getTaskProgressWidth(task)" :aria-valuenow="getTaskProgress(task)" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            {{ getTaskProgress(task) }} %
                        </td>
                        <td>
                            <span v-for="status in getTaskStatus(task)" :class="['label','label-'+status.color]" style="margin-right:5px;">{{status.label}}</span>
                        </td>
                        <td>{{ task.user_added.profile.first_name+' '+task.user_added.profile.last_name}}</td>
                        <td class="table-option">
                            <div class="btn-group">
                                <router-link :to="`/task/${task.uuid}`" class="btn btn-success btn-sm" v-tooltip="trans('task.view_task')"><i class="fas fa-arrow-circle-right"></i></router-link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['option'],
        data(){
            return {
                tasks: []
            }
        },
        mounted(){
            this.getTasks(this.option);
        },
        methods: {
            getTasks(val){
                let url = '';
                if(val === 'starred')
                    url = '/api/task?page=1&page_length=5&sortBy=created_at&order=desc&starred=1';
                else if(val === 'owned')
                    url = '/api/task?page=1&page_length=5&sortBy=created_at&order=desc&type=owned';
                else if(val === 'pending' || val === 'unassigned' || val === 'overdue')
                    url = '/api/task?page=1&page_length=5&sortBy=created_at&order=desc&status='+val;
                else
                    url = '/api/task?page=1&page_length=5&sortBy=created_at&order=desc';
                axios.get(url)
                    .then(response => response.data)
                    .then(response => this.tasks = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getTaskNumber(task){
                return helper.getTaskNumber(task);
            },
            getTaskProgressColor(task){
                return helper.getTaskProgressColor(task);
            },
            getTaskProgressWidth(task){
                return 'width: '+this.getTaskProgress(task)+'%;';
            },
            getTaskProgress(task){
                return helper.getTaskProgress(task);
            },
            getTaskStatus(task){
                return helper.getTaskStatus(task);
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          }
        }
    }
</script>

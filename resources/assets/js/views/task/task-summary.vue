<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('general.report')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('task.task_summary')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task.task_summary')}}</h4>
                        <h6 class="card-subtitle" v-if="reports.length">{{trans('general.total_result_found',{'count' : reports.length})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive">
                            <table class="table" v-if="reports.length">
                                <thead>
                                    <tr>
                                        <th>{{trans('user.name')}}</th>
                                        <th>{{trans('task.owned')}}</th>
                                        <th>{{trans('task.assigned')}}</th>
                                        <th>{{trans('task.pending')}}</th>
                                        <th>{{trans('task.completed')}}</th>
                                        <th>{{trans('task.overdue')}}</th>
                                        <th>{{trans('task.rating')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="report in reports">
                                        <td>{{getUserNameWithDesignationAndDepartment(report.user)}}</td>
                                        <td v-text="report.owned_tasks"></td>
                                        <td v-text="report.assigned_tasks"></td>
                                        <td v-text="report.pending_tasks"></td>
                                        <td v-text="report.completed_tasks"></td>
                                        <td v-text="report.overdue_tasks"></td>
                                        <td v-html="generateRatingStar(report.rating)"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        components : {},
        data() {
            return {
                reports: {}
            };
        },
        mounted(){
            this.getReport();
        },
        methods: {
            getReport(){
                axios.get('/api/report/task/summary')
                    .then(response => response.data)
                    .then(response => this.reports = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getUserNameWithDesignationAndDepartment(user){
                return helper.getUserNameWithDesignationAndDepartment(user);
            },
            generateRatingStar(rating){
                return helper.generateRatingStar(rating);
            }
        },
        watch: {
        }
    }
</script>

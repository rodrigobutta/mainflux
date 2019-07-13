<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('general.home')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">{{trans('general.home')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task.dashboard_task_count',{type: trans('task.total')})}}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0"><i class="fas fa-tasks fa-lg pull-right text-info"></i> <span class="pull-left">{{task_stats.total}}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task.dashboard_task_count',{type: trans('task.owned')})}}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0"><i class="fas fa-user fa-lg pull-right text-success"></i> <span class="pull-left">{{task_stats.owned}}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"><small>{{trans('task.dashboard_task_count',{type: trans('task.unassigned')})}}</small></h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0"><i class="fas fa-user-times fa-lg pull-right text-warning"></i> <span class="pull-left">{{task_stats.unassigned}}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task.dashboard_task_count',{type: trans('task.pending')})}}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0"><i class="fas fa-spinner fa-lg pull-right text-danger"></i> <span class="pull-left">{{task_stats.pending}}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task.dashboard_task_count',{type: trans('task.overdue')})}}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0"><i class="fas fa-fire fa-lg pull-right text-danger"></i> <span class="pull-left">{{task_stats.overdue}}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task.dashboard_task_count',{type: trans('task.completed')})}}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0"><i class="fas fa-battery-full fa-lg pull-right text-success"></i> <span class="pull-left">{{task_stats.completed}}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task.task_category')}}</h4>
                        <doughnut-graph :graph="graph.task_category" v-show="graph.task_category.labels.length"></doughnut-graph>
                        <h6 class="card-subtitle" v-if="!graph.task_category.labels.length">{{trans('general.no_result_found')}}</h6>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task.task_priority')}}</h4>
                        <doughnut-graph :graph="graph.task_priority" v-show="graph.task_priority.labels.length"></doughnut-graph>
                        <h6 class="card-subtitle" v-if="!graph.task_priority.labels.length">{{trans('general.no_result_found')}}</h6>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task.status')}}</h4>
                        <doughnut-graph :graph="graph.task_status" v-show="graph.task_status.labels.length"></doughnut-graph>
                        <h6 class="card-subtitle" v-if="!graph.task_status.labels.length">{{trans('general.no_result_found')}}</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#starred" role="tab" @click="showHideTabs('showStarredTab')">{{trans('task.starred')}}</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#pending" role="tab" @click="showHideTabs('showPendingTab')">{{trans('task.pending')}}</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#overdue" role="tab" @click="showHideTabs('showOverdueTab')">{{trans('task.overdue')}}</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#owned" role="tab" @click="showHideTabs('showOwnedTab')">{{trans('task.owned')}}</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#unassigned" role="tab" @click="showHideTabs('showUnassignedTab')">{{trans('task.unassigned')}}</a> </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="starred" role="tabpanel">
                            <div class="card-body" v-if="tabs.showStarredTab">
                                <task-list option="starred"></task-list>
                            </div>
                        </div>
                        <div class="tab-pane" id="pending" role="tabpanel">
                            <div class="card-body" v-if="tabs.showPendingTab">
                                <task-list option="pending"></task-list>
                            </div>
                        </div>
                        <div class="tab-pane" id="overdue" role="tabpanel">
                            <div class="card-body" v-if="tabs.showOverdueTab">
                                <task-list option="overdue"></task-list>
                            </div>
                        </div>
                        <div class="tab-pane" id="owned" role="tabpanel">
                            <div class="card-body" v-if="tabs.showOwnedTab">
                                <task-list option="owned"></task-list>
                            </div>
                        </div>
                        <div class="tab-pane" id="unassigned" role="tabpanel">
                            <div class="card-body" v-if="tabs.showUnassignedTab">
                                <task-list option="unassigned"></task-list>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6" v-if="getConfig('todo')">
                <div class="card">
                    <div class="card-body">
                        <div class="message-scroll">
                            <router-link to="/todo" class="btn btn-success btn-xs pull-right">{{trans('general.view_all')}}</router-link>
                            <h4 class="card-title">{{trans('todo.pending_todo')}}</h4>
                            <h6 class="card-subtitle" v-if="!pending_todos.length">{{trans('general.no_result_found')}}</h6>
                            <div class="table-responsive" v-if="pending_todos.length">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{trans('todo.title')}}</th>
                                            <th class="table-option">{{trans('todo.due_date')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="todo in pending_todos">
                                            <td>
                                                <button class="btn btn-xs btn-danger m-r-5" @click="toggleTodo(todo)"><i class="fas fa-times"></i></button> {{todo.title}}
                                            </td>
                                            <td class="table-option">{{todo.date | moment}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <h4 class="card-title">{{trans('todo.recently_completed_todo')}}</h4>
                            <h6 class="card-subtitle" v-if="!completed_todos.length">{{trans('general.no_result_found')}}</h6>
                            <div class="table-responsive" v-if="completed_todos.length">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{trans('todo.title')}}</th>
                                            <th>{{trans('todo.due_date')}}</th>
                                            <th class="table-option">{{trans('todo.completed_at')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="todo in completed_todos">
                                            <td>
                                                <button class="btn btn-xs btn-success m-r-5" @click="toggleTodo(todo)"><i class="fas fa-check"></i></button> <span style="text-decoration: line-through;">{{todo.title}}</span>
                                            </td>
                                            <td>{{todo.date | moment}}</td>
                                            <td class="table-option">{{todo.completed_at | momentDateTime}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="message-scroll">
                            <h4 class="card-title">{{trans('task.user_rating_top_chart')}}</h4>
                            <div class="comment-widgets">
                                <div class="d-flex flex-row comment-row" v-for="top_chart in top_charts" style="padding: 0 15px;">
                                    <div class="p-2"><span class="round"><img :src="getAvatar(top_chart.user)" alt="user" width="50"></span></div>
                                    <div class="comment-text w-100">
                                        <h5>{{top_chart.user.profile.first_name+' '+top_chart.user.profile.last_name+' ('+top_chart.user.profile.designation.name+' '+top_chart.user.profile.designation.department.name+')'}}</h5>
                                        <span class="m-b-5" v-html="generateRatingStar(top_chart.rating)"></span> (<span>{{top_chart.task_count+' '+trans('task.task')}}</span>)
                                    </div>
                                </div>
                                <h6 class="card-subtitle" v-if="!top_charts.length">{{trans('general.no_result_found')}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6" v-if="getConfig('announcement')">
                <div class="card">
                    <div class="card-body">
                        <div class="message-scroll">
                            <h4 class="card-title">{{trans('announcement.announcement')}}</h4>
                            <div class="comment-widgets">
                                <div class="d-flex flex-row comment-row" v-for="announcement in announcements">
                                    <div class="p-2"><span class="round"><img :src="getAvatar(announcement.user_added)" alt="user" width="50"></span></div>
                                    <div class="comment-text w-100" style="cursor: pointer;" @click="announcement_id = announcement.id" data-toggle="modal" data-target=".announcement-detail">
                                        <h5>{{announcement.user_added.profile.first_name+' '+announcement.user_added.profile.last_name+' ('+announcement.user_added.profile.designation.name+' '+announcement.user_added.profile.designation.department.name+')'}}</h5>
                                        <p class="m-b-5">{{announcement.title}}</p>
                                        <div class="comment-footer">
                                            <span class="text-muted pull-right"><small>{{trans('announcement.posted_on')}}: {{announcement.created_at | momentDateTime}}</small></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h6 class="card-subtitle" v-if="!announcements.length">{{trans('general.no_result_found')}}</h6>
                        </div>
                        <announcement-detail :id="announcement_id"></announcement-detail>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6" v-if="getConfig('activity_log')">
                <div class="card">
                    <div class="card-body">
                        <div class="message-scroll">
                            <h4 class="card-title">{{trans('activity.activity_log')}}</h4>
                            <h6 class="card-subtitle" v-if="!activity_logs.length">{{trans('general.no_result_found')}}</h6>
                            <div class="table-responsive" v-if="activity_logs.length">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th v-if="hasAdminRole()">{{trans('user.user')}}</th>
                                            <th>{{trans('activity.activity')}}</th>
                                            <th class="table-option">{{trans('activity.date_time')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="activity_log in activity_logs">
                                            <td v-if="hasAdminRole()" v-text="activity_log.user.profile.first_name+' '+activity_log.user.profile.last_name"></td>
                                            <td>{{trans('activity.'+activity_log.activity,{activity: trans('activity.'+activity_log.module)})}}</td>
                                            <td class="table-option">{{activity_log.created_at | momentDateTime }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6" >
                <div class="card">
                    <div class="card-body">
                        <div class="message-scroll">
                            <h4 class="card-title">TESTS</h4>
                            <div class="">

                                <input type="text" v-model="notificationText"><br />
                                <button class="btn btn-success" @click="sendNotification()">Send!</button>
                            
                            </div>                            
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6" v-if="getConfig('activity_log')">
                <div class="card">
                    <div class="card-body">
                        <div class="message-scroll">
                            <h4 class="card-title">{{trans('activity.activity_log')}}</h4>
                            <h6 class="card-subtitle" v-if="!activity_logs.length">{{trans('general.no_result_found')}}</h6>
                            <div class="table-responsive" v-if="activity_logs.length">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th v-if="hasAdminRole()">{{trans('user.user')}}</th>
                                            <th>{{trans('activity.activity')}}</th>
                                            <th class="table-option">{{trans('activity.date_time')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="activity_log in activity_logs">
                                            <td v-if="hasAdminRole()" v-text="activity_log.user.profile.first_name+' '+activity_log.user.profile.last_name"></td>
                                            <td>{{trans('activity.'+activity_log.activity,{activity: trans('activity.'+activity_log.module)})}}</td>
                                            <td class="table-option">{{activity_log.created_at | momentDateTime }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import announcementDetail from '../announcement/detail'
    import doughnutGraph from '../graph/doughnut-graph'
    import taskList from '../task/task-list'

    export default {
        components: {announcementDetail,doughnutGraph,taskList},
        data() {
            return {
                task_stats: {},
                activity_logs: {},
                pending_todos: [],
                completed_todos: [],
                announcements: [],
                announcement_id: '',
                top_charts: [],
                graph: {
                    task_category: {
                        labels: []
                    },
                    task_priority: {
                        labels: []
                    },
                    task_status: {
                        labels: []
                    },
                },
                tabs: {
                    showStarredTab: true,
                    showPendingTab: false,
                    showOverdueTab: false,
                    showOwnedTab: false,
                    showUnassignedTab: false
                },
                notificationText: ''
            }
        },
        mounted(){
            axios.get('/api/dashboard')
                .then(response => response.data)
                .then(response => {
                    this.task_stats = response.task_stats;
                    this.activity_logs = response.activity_logs;
                    this.announcements = response.announcements;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                })

            if(this.getConfig('todo'))
                this.getRecentTodo();

            this.getUserRatingTopChart();

            this.getGraphData();
        },
        methods: {
            showHideTabs(activeTab){
                for(let tab in this.tabs)
                    if(tab !== activeTab)
                    this.tabs[tab] = false;
                this.tabs[activeTab] = true;
            },
            getRecentTodo(){
                axios.post('/api/todo/recent')
                    .then(response => response.data)
                    .then(response => {
                        this.pending_todos = response.pending_todos;
                        this.completed_todos = response.completed_todos;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    })
            },
            toggleTodo(todo){
                axios.post('/api/todo/'+todo.id+'/status')
                    .then(response => response.data)
                    .then(response => {
                        this.getRecentTodo();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg();
                    });
            },
            getGraphData(){
                axios.post('/api/task/graph')
                    .then(response => response.data)
                    .then(response => {
                        this.graph = response;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg();
                    });
            },
            getStatus(todo){
                return todo.status ? ('<span class="label label-success">'+i18n.todo.complete+'</span>') : ('<span class="label label-danger">'+i18n.todo.incomplete+'</span>') ;
            },
            hasRole(role){
                return helper.hasRole(role);
            },
            hasAdminRole(){
                return helper.hasAdminRole();
            },
            getAvatar(user){
                return helper.getAvatar(user);
            },
            getConfig(name){
                return helper.getConfig(name);
            },
            getUserRatingTopChart(){
                axios.post('/api/task/rating/chart')
                    .then(response => response.data)
                    .then(response => this.top_charts = response)
                    .catch(error => {
                        helper.showDataErrorMsg();
                    });
            },
            generateRatingStar(rating){
                return helper.generateRatingStar(rating);
            },
            sendNotification() {
                axios.post('/notification', {text: this.notificationText})
                    .then((response) => {
                        this.notificationText = '';
                    });
            }
        },
        computed: {
        },
        filters: {
            momentDateTime(date) {
                return helper.formatDateTime(date);
            },
            moment(date) {
                return helper.formatDate(date);
            }
        },
    }
</script>

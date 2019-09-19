<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('task.task_detail')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/task">{{trans('task.task')}}</router-link></li>
                    <li class="breadcrumb-item active">{{task.title}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="row m-b-5">
                    <div class="col-12 col-md-8 p-0">
                        <div class="btn btn-group">
                            <router-link v-if="is_editable" :to="`/task/${task.uuid}/edit`" class="btn btn-warning btn-sm"><i class="fas fa-pencil-alt"></i> {{trans('general.edit')}}</router-link>
                            <button v-if="is_editable" @click="toggleCopyPanel" class="btn btn-info btn-sm"><i class="far fa-file"></i> {{trans('task.copy')}}</button>
                            <button v-if="is_editable" @click="toggleRecurringPanel" class="btn btn-danger btn-sm"><i class="fas fa-repeat"></i> {{trans('task.recurring')}}</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 p-0">
                        <div class="btn btn-group pull-right">
                            <button @click="createTask" v-if="hasPermission('create-task')" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> {{trans('task.add_new_task')}}</button>
                            <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-task')" :key="task.id" v-confirm="{ok: confirmDelete(task)}"><i class="fas fa-trash"></i> {{trans('general.delete')}}</button>
                            <button class="btn btn-info btn-sm pull-right" v-if="hasAdminRole() || hasPermission('access-all-task') || task.user_id == getAuthUser('id')" data-toggle="modal" data-target=".task-config"><i class="fas fa-cog"></i></button>
                        </div>
                    </div>
                </div>
                <transition name="fade">
                    <task-recurring v-if="is_editable" :uuid="uuid" :show-panel="showRecurringPanel" @toggle="toggleRecurringPanel" @recurringUpdated="getTask"></task-recurring>
                </transition>
                <transition name="fade">
                    <task-copy v-if="is_editable" :uuid="uuid" :show-panel="showCopyPanel" @toggle="toggleCopyPanel"></task-copy>
                </transition>
                <task-config :uuid="uuid" :task="task" @completed="getTask"></task-config>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-xs-6 b-r">
                                <span class="pull-right" style="cursor: pointer;"><i :class="['fa','fa-star','fa-2x',isTaskStarred ? 'starred' : '']" @click="toggleStar(task)"></i></span>
                                <strong>{{trans('task.number')}}</strong>
                                <br>
                                <p class="text-muted">#{{getTaskNumber(task)}}</p>
                                <strong>{{trans('task.title')}}</strong>
                                <br>
                                <p class="text-muted">{{task.title}}</p>

                                <div class="user-profile pull-right"><div class="profile-img"> <img :src="getAvatar(task.user_added)" alt="user" /> </div></div>
                                <strong>{{trans('task.owner')}}</strong>
                                <br>
                                <p class="text-muted">{{ task.user_added.profile.first_name+' '+task.user_added.profile.last_name}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>{{trans('task.category')}}</strong>
                                <br>
                                <p class="text-muted">{{task.task_category.name}}</p>
                                <strong>{{trans('task.priority')}}</strong>
                                <br>
                                <p class="text-muted">{{task.task_priority.name}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>{{trans('task.start_date')}}</strong>
                                <br>
                                <p class="text-muted">{{task.start_date | moment}}</p>
                                <strong>{{trans('task.due_date')}}</strong>
                                <br>
                                <p class="text-muted">{{task.due_date | moment}}</p>
                                <template v-if="task.completed_at && task.sign_off_status === 'approved'">
                                    <strong>{{trans('task.completed_at')}}</strong>
                                    <br>
                                    <p class="text-muted">{{task.completed_at | momentDateTime}}</p>
                                </template>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <div class="ribbon-wrapper card" style="margin-right: -15px;padding-top: 10px;" v-for="status in getTaskStatus(task)" v-bind:key="status.label">
                                    <div :class="['ribbon','ribbon-'+status.color,'ribbon-right']">{{status.label}}</div>
                                </div>
                                <div class="ribbon-wrapper card" v-if="task.is_recurring" style="margin-right: -15px;padding-top: 10px;">
                                    <div :class="['ribbon','ribbon-danger','ribbon-right']" @click="toggleRecurringPanel" style="cursor:pointer;"><i class="fas fa-repeat"></i> {{trans('task.recurring')}}</div>
                                </div>
                                <div class="ribbon-wrapper card" style="margin-right: -15px;padding-top: 10px;" v-if="task.is_archived">
                                    <template v-if="task.user_id == getAuthUser('id') && task.sign_off_status === 'approved'">
                                        <div :class="['ribbon','ribbon-warning','ribbon-right']" :key="`archive_${task.id}`" v-confirm="{ok: confirmToggleArchive(task)}" style="cursor:pointer;" v-tooltip="trans('task.remove_from_archive')">{{trans('task.archived')}}</div>
                                    </template>
                                    <template v-else>
                                        <div :class="['ribbon','ribbon-warning','ribbon-right']">{{trans('task.archived')}}</div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-10">
                            <div class="col-12">
                                <div class="progress" style="height: 20px;" v-if="task.progress_type === 'sub_task' || task.progress_type === 'question' || task.sign_off_status === 'requested' || task.sign_off_status === 'approved'">
                                    <div class="progress-bar bg-success" role="progressbar" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100" :style="`width:${progress}%;`">{{progress}}% Complete</div>
                                </div>
                                <div v-else style="padding:0px;">
                                    <label for="">Progress ({{taskProgressForm.progress}}%)</label><br />
                                    <range-slider class="slider" min="0" max="100" step="1" v-model="taskProgressForm.progress" @change="sliderChange"></range-slider>
                                </div>
                                <span><small>{{trans('task.progress_type')}}: {{trans('task.'+task.progress_type+'_progress')}}</small></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('task.assigned_user')}}</h4>
                        <h6 class="card-subtitle" v-if="task.user.length">{{trans('general.total_result_found',{'count' : task.user.length})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <br />
                        <div class="d-flex flex-row m-b-20" v-if="task.user" v-for="user in task.user" v-bind:key="user.id">
                            <div class=""><img :src="getAvatar(user)" alt="user" class="img-circle" width="70"></div>
                            <div class="p-l-20">
                                <h4 class="font-medium">{{user.profile.first_name+' '+user.profile.last_name}}</h4>
                                <h6 v-if="user.profile.designation">{{user.profile.designation.name+' '+trans('general.in')+' '+user.profile.designation.department.name}}</h6>
                                <span class="time"><small>{{trans('task.assigned_at')}} {{user.pivot.created_at | momentDateTime}}</small></span> <br />
                                <span v-html="getTaskUserRating(user,task)"></span>
                            </div>
                        </div>
                        <div class="card-subtitle" v-if="!task.user">{{trans('task.no_assigned_user')}}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 col-md-8">
                <div class="card">
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#detail" role="tab" @click="showHideTabs('showDetailTab')">{{trans('task.detail')}}</a> </li>
                        <li class="nav-item" v-if="task.question_set_id"> <a class="nav-link" data-toggle="tab" href="#question" role="tab" @click="showHideTabs('showQuestionTab')">{{trans('task.question')}}</a> </li>
                        <li class="nav-item" v-if="progress == 100"> <a class="nav-link" data-toggle="tab" href="#sign-off" role="tab" @click="showHideTabs('showTaskSignOffTab')">{{trans('task.sign_off')}}</a> </li>
                        <li class="nav-item" v-if="isRatingAllowed"> <a class="nav-link" data-toggle="tab" href="#rating" role="tab" @click="showHideTabs('showTaskRatingTab')">{{trans('task.rating')}}</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#sub-task" role="tab" @click="showHideTabs('showSubTaskTab')">{{trans('task.sub_task')}}</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#comment" role="tab" @click="showHideTabs('showTaskCommentTab')">{{trans('task.comment')}}</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#note" role="tab" @click="showHideTabs('showTaskNoteTab')">{{trans('task.note')}}</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#attachment" role="tab" @click="showHideTabs('showTaskAttachmentTab')">{{trans('task.attachment')}}</a> </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="detail" role="tabpanel">
                            <div class="card-body" v-if="tabs.showDetailTab">
                                <div v-html="task.description"></div>
                                <div v-if="attachments.length">
                                    <ul style="list-style: none;padding: 0;" class="m-t-10">
                                        <li v-for="attachment in attachments" v-bind:key="attachment.id">
                                            <a :href="`/task/${task.uuid}/attachment/${attachment.uuid}/download?token=${authToken}`"><i class="fas fa-paperclip"></i> {{attachment.user_filename}}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="tab-pane" id="question" role="tabpanel" v-if="task.question_set">
                            <div class="card-body" v-if="tabs.showQuestionTab">
                                <h4 class="card-title">{{trans('task.answer_to_questions')}}</h4>
                                <template v-if="!is_locked">
                                    <form @submit.prevent="submit" @keydown="questionForm.errors.clear($event.target.name)">
                                        <template v-for="answer in questionForm.answers" >
                                            <div class="row" v-bind:key="answer.id">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">{{answer.question}}</label>
                                                        <div class="radio radio-info">
                                                            <input type="radio" value="yes" :id="`answer_${answer.id}_1`" v-model="answer.answer" :checked="answer.answer == 'yes'" :name="`answer_${answer.id}`" @click="questionForm.errors.clear(`answer_${answer.id}`)">
                                                            <label :for="`answer_${answer.id}_1`" class="m-r-20"> {{trans('list.yes')}} </label>
                                                            <input type="radio" value="no" :id="`answer_${answer.id}_0`" v-model="answer.answer" :checked="answer.answer == 'no'" :name="`answer_${answer.id}`" @click="questionForm.errors.clear(`answer_${answer.id}`)">
                                                            <label :for="`answer_${answer.id}_0`"> {{trans('list.no')}} </label>
                                                        </div>
                                                        <show-error :form-name="questionForm" :prop-name="`answer_${answer.id}`"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-8">
                                                    <div class="form-group">
                                                        <autosize-textarea rows="2" class="form-control" v-model="answer.description" :placeholder="trans('task.answer_description')" name="description"></autosize-textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-info pull-right">{{trans('general.save')}}</button>
                                        </div>
                                    </form>
                                </template>
                                <template v-else>
                                    <div v-for="answer in questionForm.answers" v-bind:key="answer.id">
                                        <h6>
                                            <span v-if="answer.answer == 'yes'" class="text-success m-r-20"><i class="fas fa-check-circle"></i></span>
                                            <span v-else class="text-danger"><i class="fas fa-times-circle m-r-20"></i></span>
                                            {{answer.question}}
                                            <p v-if="answer.description" class="m-r-30 m-t-10" style="color:#484848;">{{answer.description}}</p>
                                        </h6>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="tab-pane" id="sign-off" role="tabpanel" v-if="progress == 100">
                            <div class="card-body" v-if="tabs.showTaskSignOffTab">
                                <task-sign-off :uuid="uuid" :task="task" :users="assigned_user" @signOffStatusUpdated="getTask"></task-sign-off>
                            </div>
                        </div>
                        <div class="tab-pane" id="rating" role="tabpanel" v-if="isRatingAllowed">
                            <div class="card-body" v-if="tabs.showTaskRatingTab">
                                <task-rating :uuid="uuid" :task="task" @ratingComplete="getTask"></task-rating>
                            </div>
                        </div>
                        <div class="tab-pane" id="sub-task" role="tabpanel">
                            <div class="card-body" v-if="tabs.showSubTaskTab">
                                <sub-task :uuid="uuid" @updateProgress="getTask"></sub-task>
                            </div>
                        </div>
                        <div class="tab-pane" id="comment" role="tabpanel">
                            <div class="card-body" v-if="tabs.showTaskCommentTab">
                                <task-comment :uuid="uuid"></task-comment>
                            </div>
                        </div>
                        <div class="tab-pane" id="note" role="tabpanel">
                            <div class="card-body" v-if="tabs.showTaskNoteTab">
                                <task-note :uuid="uuid"></task-note>
                            </div>
                        </div>
                        <div class="tab-pane" id="attachment" role="tabpanel">
                            <div class="card-body" v-if="tabs.showTaskAttachmentTab">
                                <task-attachment :uuid="uuid"></task-attachment>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import taskSignOff from './task-sign-off'
    import subTask from './sub-task'
    import taskComment from './task-comment'
    import taskNote from './task-note'
    import taskAttachment from './task-attachment'
    import taskConfig from './task-config'
    import taskRecurring from './task-recurring'
    import taskCopy from './task-copy'
    import taskRating from './task-rating'
    import rangeSlider from 'vue-range-slider'
    import autosizeTextarea from '../../components/autosize-textarea'

    export default {
        components:{subTask,taskComment,taskNote,taskAttachment,taskConfig,taskRecurring,taskCopy,rangeSlider,taskSignOff,taskRating, autosizeTextarea},
        data() {
            return {
                uuid:this.$route.params.uuid,
                task: {
                    user_added:{
                        profile: {

                        }
                    },
                    task_category: {

                    },
                    task_priority: {

                    },
                    user:[]
                },
                questionForm: new Form({
                    question_set_id: '',
                    answers: []
                }),
                is_locked: 0,
                assigned_user: [],
                progress: 0,
                is_editable: false,
                attachments: [],
                starred_tasks: [],
                question_set: {},
                showRecurringPanel: false,
                showCopyPanel: false,
                taskProgressForm: new Form({
                    progress: 0
                },false),
                tabs: {
                    showDetailTab: true,
                    showQuestionTab: false,
                    showTaskSignOffTab: false,
                    showTaskRatingTab: false,
                    showSubTaskTab: false,
                    showTaskCommentTab: false,
                    showTaskNoteTab: false,
                    showTaskAttachmentTab: false,
                }
            }
        },
        mounted(){
            this.getTask();
        },
        methods: {
            showHideTabs(activeTab){
                for(let tab in this.tabs)
                    if(tab !== activeTab)
                    this.tabs[tab] = false;
                this.tabs[activeTab] = true;
            },
            hasRole(role){
                return helper.hasRole(role);
            },
            hasAdminRole(){
                return helper.hasAdminRole();
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            sliderChange(value){
                this.taskProgressForm.progress = value;
                this.updateProgress();
            },
            getAvatar(user){
                return helper.getAvatar(user);
            },
            getTask(){
                axios.get('/api/task/'+this.uuid)
                    .then(response => response.data)
                    .then(response => {
                        this.task = response.task;
                        this.is_locked = response.is_locked;
                        this.assigned_user = response.user_id;
                        this.is_editable = response.is_editable;
                        this.attachments = response.attachments;
                        this.starred_tasks = response.starred_tasks;
                        this.taskProgressForm.progress = response.task.progress;
                        this.progress = helper.getTaskProgress(this.task);
                        this.question_set = response.question_set;

                        this.questionForm.question_set_id = this.task.question_set_id;

                        if(this.question_set) {
                            this.questionForm.answers = [];
                            this.question_set.questions.forEach(question => {
                                let answer = this.task.answers.find( o => o.question_id === question.id);
                                this.questionForm.answers.push({
                                        'question': question.question,
                                        'id': question.id,
                                        'answer': (answer && answer.answer) ? 'yes' : 'no',
                                        'description': (answer) ? answer.description : ''
                                    });
                            });
                        }
                    })
                    .catch(error => {
                        this.$router.push('/task');
                    });
                },
            confirmDelete(task){
                return dialog => this.deleteTask(task);
            },
            deleteTask(task){
                axios.delete('/api/task/'+task.uuid)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/task');
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            createTask(){
                this.$router.push('/task/create');
            },
            toggleStar(task){
                axios.post('/api/task/'+task.uuid+'/star')
                    .then(response => response.data)
                    .then(response => {
                        if(this.isTaskStarred)
                            this.starred_tasks.splice(this.starred_tasks.indexOf(this.getAuthUser('id')), 1);
                        else
                            this.starred_tasks.push(this.getAuthUser('id'));
                    })
                    .catch(error => {

                    });
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            toggleRecurringPanel(){
                this.showRecurringPanel = !this.showRecurringPanel;
                this.showCopyPanel = false;
            },
            toggleCopyPanel(){
                this.showCopyPanel = !this.showCopyPanel;
                this.showRecurringPanel = false;
            },
            updateProgress(){
                this.taskProgressForm.post('/api/task/'+this.uuid+'/progress')
                    .then(response => {
                        this.getTask();
                        toastr.success(response.message);
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            confirmToggleArchive(task){
                return dialog => this.toggleArchive(task);
            },
            toggleArchive(task){
                axios.post('/api/task/'+task.uuid+'/archive')
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getTask();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getTaskStatus(task){
                return helper.getTaskStatus(task);
            },
            getTaskUserRating(user,task){
                return helper.getTaskUserRating(user,task);
            },
            getTaskNumber(task){
                return helper.getTaskNumber(task);
            },
            submit(){
                this.questionForm.post('/api/task/'+this.task.uuid+'/answer')
                    .then(response => {
                        toastr.success(response.message);
                        this.questionForm.answers = [];
                        this.getTask();
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
        },
        computed: {
            authToken(){
                return localStorage.getItem('auth_token');
            },
            isTaskStarred(){
                if(this.starred_tasks.indexOf(this.getAuthUser('id')) != -1)
                    return 1;

                return 0;
            },
            isRatingAllowed(){
                return this.task.sign_off_status === 'approved' ? 1 : 0;
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          },
          momentDateTime(datetime) {
            return helper.formatDateTime(datetime);
          },
        },
        watch: {
            '$route.params.uuid'(newId, oldId) {
                this.uuid = newId;
                this.getTask()
            },
        }
    }
</script>
<style>
    .slider{
        width: 100%;
    }
    .range-slider-fill{
        background-color: green;
    }
</style>

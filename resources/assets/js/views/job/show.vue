<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('job.job_detail')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/job">{{trans('job.job')}}</router-link></li>
                    <li class="breadcrumb-item active">{{job.title}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="row m-b-5">
                    <div class="col-12 col-md-8 p-0">
                        <div class="btn btn-group">
                            <router-link v-if="is_editable" :to="`/job/${job.uuid}/edit`" class="btn btn-warning btn-sm"><i class="fas fa-pencil-alt"></i> {{trans('general.edit')}}</router-link>
                            <button v-if="is_editable" @click="toggleCopyPanel" class="btn btn-info btn-sm"><i class="far fa-file"></i> {{trans('job.copy')}}</button>
                            <button v-if="is_editable" @click="toggleRecurringPanel" class="btn btn-danger btn-sm"><i class="fas fa-repeat"></i> {{trans('job.recurring')}}</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 p-0">
                        <div class="btn btn-group pull-right">
                            <button @click="createJob" v-if="hasPermission('create-job')" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> {{trans('job.add_new_job')}}</button>
                            <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-job')" :key="job.id" v-confirm="{ok: confirmDelete(job)}"><i class="fas fa-trash"></i> {{trans('general.delete')}}</button>
                            <button class="btn btn-info btn-sm pull-right" v-if="hasAdminRole() || hasPermission('access-all-job') || job.user_id == getAuthUser('id')" data-toggle="modal" data-target=".job-config"><i class="fas fa-cog"></i></button>
                        </div>
                    </div>
                </div>
                <transition name="fade">
                    <job-recurring v-if="is_editable" :uuid="uuid" :show-panel="showRecurringPanel" @toggle="toggleRecurringPanel" @recurringUpdated="getJob"></job-recurring>
                </transition>
                <transition name="fade">
                    <job-copy v-if="is_editable" :uuid="uuid" :show-panel="showCopyPanel" @toggle="toggleCopyPanel"></job-copy>
                </transition>
                <job-config :uuid="uuid" :job="job" @completed="getJob"></job-config>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-xs-6 b-r">
                                <span class="pull-right" style="cursor: pointer;"><i :class="['fa','fa-star','fa-2x',isJobStarred ? 'starred' : '']" @click="toggleStar(job)"></i></span>
                                <strong>{{trans('job.number')}}</strong>
                                <br>
                                <p class="text-muted">#{{getJobNumber(job)}}</p>
                                <strong>{{trans('job.title')}}</strong>
                                <br>
                                <p class="text-muted">{{job.title}}</p>

                                <div class="user-profile pull-right"><div class="profile-img"> <img :src="getAvatar(job.user_added)" alt="user" /> </div></div>
                                <strong>{{trans('job.owner')}}</strong>
                                <br>
                                <p class="text-muted">{{ job.user_added.profile.first_name+' '+job.user_added.profile.last_name}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>{{trans('job.category')}}</strong>
                                <br>
                                <p class="text-muted">{{job.job_category.name}}</p>
                                <strong>{{trans('job.priority')}}</strong>
                                <br>
                                <p class="text-muted">{{job.job_priority.name}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>{{trans('job.start_date')}}</strong>
                                <br>
                                <p class="text-muted">{{job.start_date | moment}}</p>
                                <strong>{{trans('job.due_date')}}</strong>
                                <br>
                                <p class="text-muted">{{job.due_date | moment}}</p>
                                <template v-if="job.completed_at && job.sign_off_status === 'approved'">
                                    <strong>{{trans('job.completed_at')}}</strong>
                                    <br>
                                    <p class="text-muted">{{job.completed_at | momentDateTime}}</p>
                                </template>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <div class="ribbon-wrapper card" style="margin-right: -15px;padding-top: 10px;" v-for="status in getJobStatus(job)" v-bind:key="status.label">
                                    <div :class="['ribbon','ribbon-'+status.color,'ribbon-right']">{{status.label}}</div>
                                </div>
                                <div class="ribbon-wrapper card" v-if="job.is_recurring" style="margin-right: -15px;padding-top: 10px;">
                                    <div :class="['ribbon','ribbon-danger','ribbon-right']" @click="toggleRecurringPanel" style="cursor:pointer;"><i class="fas fa-repeat"></i> {{trans('job.recurring')}}</div>
                                </div>
                                <div class="ribbon-wrapper card" style="margin-right: -15px;padding-top: 10px;" v-if="job.is_archived">
                                    <template v-if="job.user_id == getAuthUser('id') && job.sign_off_status === 'approved'">
                                        <div :class="['ribbon','ribbon-warning','ribbon-right']" :key="`archive_${job.id}`" v-confirm="{ok: confirmToggleArchive(job)}" style="cursor:pointer;" v-tooltip="trans('job.remove_from_archive')">{{trans('job.archived')}}</div>
                                    </template>
                                    <template v-else>
                                        <div :class="['ribbon','ribbon-warning','ribbon-right']">{{trans('job.archived')}}</div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-10">
                            <div class="col-12">
                                <div class="progress" style="height: 20px;" v-if="job.progress_type === 'sub_job' || job.progress_type === 'question' || job.sign_off_status === 'requested' || job.sign_off_status === 'approved'">
                                    <div class="progress-bar bg-success" role="progressbar" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100" :style="`width:${progress}%;`">{{progress}}% Complete</div>
                                </div>
                                <div v-else style="padding:0px;">
                                    <label for="">Progress ({{jobProgressForm.progress}}%)</label><br />
                                    <range-slider class="slider" min="0" max="100" step="1" v-model="jobProgressForm.progress" @change="sliderChange"></range-slider>
                                </div>
                                <span><small>{{trans('job.progress_type')}}: {{trans('job.'+job.progress_type+'_progress')}}</small></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('job.assigned_user')}}</h4>
                        <h6 class="card-subtitle" v-if="job.user.length">{{trans('general.total_result_found',{'count' : job.user.length})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <br />
                        <div class="d-flex flex-row m-b-20" v-if="job.user" v-for="user in job.user" v-bind:key="user.id">
                            <div class=""><img :src="getAvatar(user)" alt="user" class="img-circle" width="70"></div>
                            <div class="p-l-20">
                                <h4 class="font-medium">{{user.profile.first_name+' '+user.profile.last_name}}</h4>
                                <h6 v-if="user.profile.designation">{{user.profile.designation.name+' '+trans('general.in')+' '+user.profile.designation.department.name}}</h6>
                                <span class="time"><small>{{trans('job.assigned_at')}} {{user.pivot.created_at | momentDateTime}}</small></span> <br />
                                <span v-html="getJobUserRating(user,job)"></span>
                            </div>
                        </div>
                        <div class="card-subtitle" v-if="!job.user">{{trans('job.no_assigned_user')}}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 col-md-8">
                <div class="card">
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#detail" role="tab" @click="showHideTabs('showDetailTab')">{{trans('job.detail')}}</a> </li>
                        <li class="nav-item" v-if="job.question_set_id"> <a class="nav-link" data-toggle="tab" href="#question" role="tab" @click="showHideTabs('showQuestionTab')">{{trans('job.question')}}</a> </li>
                        <li class="nav-item" v-if="progress == 100"> <a class="nav-link" data-toggle="tab" href="#sign-off" role="tab" @click="showHideTabs('showJobSignOffTab')">{{trans('job.sign_off')}}</a> </li>
                        <li class="nav-item" v-if="isRatingAllowed"> <a class="nav-link" data-toggle="tab" href="#rating" role="tab" @click="showHideTabs('showJobRatingTab')">{{trans('job.rating')}}</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#sub-job" role="tab" @click="showHideTabs('showSubJobTab')">{{trans('job.sub_job')}}</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#comment" role="tab" @click="showHideTabs('showJobCommentTab')">{{trans('job.comment')}}</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#note" role="tab" @click="showHideTabs('showJobNoteTab')">{{trans('job.note')}}</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#attachment" role="tab" @click="showHideTabs('showJobAttachmentTab')">{{trans('job.attachment')}}</a> </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="detail" role="tabpanel">
                            <div class="card-body" v-if="tabs.showDetailTab">
                                <div v-html="job.description"></div>
                                <div v-if="attachments.length">
                                    <ul style="list-style: none;padding: 0;" class="m-t-10">
                                        <li v-for="attachment in attachments" v-bind:key="attachment.id">
                                            <a :href="`/job/${job.uuid}/attachment/${attachment.uuid}/download?token=${authToken}`"><i class="fas fa-paperclip"></i> {{attachment.user_filename}}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="tab-pane" id="question" role="tabpanel" v-if="job.question_set">
                            <div class="card-body" v-if="tabs.showQuestionTab">
                                <h4 class="card-title">{{trans('job.answer_to_questions')}}</h4>
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
                                                        <autosize-textarea rows="2" class="form-control" v-model="answer.description" :placeholder="trans('job.answer_description')" name="description"></autosize-textarea>
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
                            <div class="card-body" v-if="tabs.showJobSignOffTab">
                                <job-sign-off :uuid="uuid" :job="job" :users="assigned_user" @signOffStatusUpdated="getJob"></job-sign-off>
                            </div>
                        </div>
                        <div class="tab-pane" id="rating" role="tabpanel" v-if="isRatingAllowed">
                            <div class="card-body" v-if="tabs.showJobRatingTab">
                                <job-rating :uuid="uuid" :job="job" @ratingComplete="getJob"></job-rating>
                            </div>
                        </div>
                        <div class="tab-pane" id="sub-job" role="tabpanel">
                            <div class="card-body" v-if="tabs.showSubJobTab">
                                <sub-job :uuid="uuid" @updateProgress="getJob"></sub-job>
                            </div>
                        </div>
                        <div class="tab-pane" id="comment" role="tabpanel">
                            <div class="card-body" v-if="tabs.showJobCommentTab">
                                <job-comment :uuid="uuid"></job-comment>
                            </div>
                        </div>
                        <div class="tab-pane" id="note" role="tabpanel">
                            <div class="card-body" v-if="tabs.showJobNoteTab">
                                <job-note :uuid="uuid"></job-note>
                            </div>
                        </div>
                        <div class="tab-pane" id="attachment" role="tabpanel">
                            <div class="card-body" v-if="tabs.showJobAttachmentTab">
                                <job-attachment :uuid="uuid"></job-attachment>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import jobSignOff from './job-sign-off'
    import subJob from './sub-job'
    import jobComment from './job-comment'
    import jobNote from './job-note'
    import jobAttachment from './job-attachment'
    import jobConfig from './job-config'
    import jobRecurring from './job-recurring'
    import jobCopy from './job-copy'
    import jobRating from './job-rating'
    import rangeSlider from 'vue-range-slider'
    import autosizeTextarea from '../../components/autosize-textarea'

    export default {
        components:{subJob,jobComment,jobNote,jobAttachment,jobConfig,jobRecurring,jobCopy,rangeSlider,jobSignOff,jobRating, autosizeTextarea},
        data() {
            return {
                uuid:this.$route.params.uuid,
                job: {
                    user_added:{
                        profile: {

                        }
                    },
                    job_category: {

                    },
                    job_priority: {

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
                starred_jobs: [],
                question_set: {},
                showRecurringPanel: false,
                showCopyPanel: false,
                jobProgressForm: new Form({
                    progress: 0
                },false),
                tabs: {
                    showDetailTab: true,
                    showQuestionTab: false,
                    showJobSignOffTab: false,
                    showJobRatingTab: false,
                    showSubJobTab: false,
                    showJobCommentTab: false,
                    showJobNoteTab: false,
                    showJobAttachmentTab: false,
                }
            }
        },
        mounted(){
            this.getJob();
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
                this.jobProgressForm.progress = value;
                this.updateProgress();
            },
            getAvatar(user){
                return helper.getAvatar(user);
            },
            getJob(){
                axios.get('/api/job/'+this.uuid)
                    .then(response => response.data)
                    .then(response => {
                        this.job = response.job;
                        this.is_locked = response.is_locked;
                        this.assigned_user = response.user_id;
                        this.is_editable = response.is_editable;
                        this.attachments = response.attachments;
                        this.starred_jobs = response.starred_jobs;
                        this.jobProgressForm.progress = response.job.progress;
                        this.progress = helper.getJobProgress(this.job);
                        this.question_set = response.question_set;

                        this.questionForm.question_set_id = this.job.question_set_id;

                        if(this.question_set) {
                            this.questionForm.answers = [];
                            this.question_set.questions.forEach(question => {
                                let answer = this.job.answers.find( o => o.question_id === question.id);
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
                        this.$router.push('/job');
                    });
                },
            confirmDelete(job){
                return dialog => this.deleteJob(job);
            },
            deleteJob(job){
                axios.delete('/api/job/'+job.uuid)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/job');
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            createJob(){
                this.$router.push('/job/create');
            },
            toggleStar(job){
                axios.post('/api/job/'+job.uuid+'/star')
                    .then(response => response.data)
                    .then(response => {
                        if(this.isJobStarred)
                            this.starred_jobs.splice(this.starred_jobs.indexOf(this.getAuthUser('id')), 1);
                        else
                            this.starred_jobs.push(this.getAuthUser('id'));
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
                this.jobProgressForm.post('/api/job/'+this.uuid+'/progress')
                    .then(response => {
                        this.getJob();
                        toastr.success(response.message);
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            confirmToggleArchive(job){
                return dialog => this.toggleArchive(job);
            },
            toggleArchive(job){
                axios.post('/api/job/'+job.uuid+'/archive')
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getJob();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getJobStatus(job){
                return helper.getJobStatus(job);
            },
            getJobUserRating(user,job){
                return helper.getJobUserRating(user,job);
            },
            getJobNumber(job){
                return helper.getJobNumber(job);
            },
            submit(){
                this.questionForm.post('/api/job/'+this.job.uuid+'/answer')
                    .then(response => {
                        toastr.success(response.message);
                        this.questionForm.answers = [];
                        this.getJob();
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
        },
        computed: {
            authToken(){
                return localStorage.getItem('auth_token');
            },
            isJobStarred(){
                if(this.starred_jobs.indexOf(this.getAuthUser('id')) != -1)
                    return 1;

                return 0;
            },
            isRatingAllowed(){
                return this.job.sign_off_status === 'approved' ? 1 : 0;
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
                this.getJob()
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

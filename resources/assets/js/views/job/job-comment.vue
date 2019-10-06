<template>
    <div>
        <p class="alert alert-info"><i class="fas fa-exclamation-triangle"></i> {{trans('job.tip_job_comment')}}</p>

        <job-comment-form :uuid="uuid" @completed="getComments"></job-comment-form>

        <div class="comment-widgets comment-scroll" v-if="job_comments.length">
            <div class="d-flex flex-row comment-row" v-for="job_comment in job_comments">
                <div class="p-2"><span class="round"><img :src="getAvatar(job_comment.user)" alt="user" width="50"></span></div>
                <div class="comment-text w-100">
                    <span class="pull-right" style="cursor: pointer;margin-left: 15px;" v-if="job_comment.user_id === getAuthUser('id')" :key="job_comment.id" v-confirm="{ok: confirmDelete(job_comment)}" v-tooltip="trans('job.delete_comment')"><i class="fas fa-trash"></i></span>
                    <span class="text-muted pull-right" style="font-size: 12px;"><i class="fas fa-clock"></i> {{job_comment.created_at | momentFromNow}}</span>
                    <h5>{{job_comment.user.profile.first_name+' '+job_comment.user.profile.last_name}}</h5>
                    <p class="m-b-5" v-html="job_comment.comment"></p>
                    <div class="" style="padding-left:25px;margin-bottom:-30px;">
                        <job-comment-form :uuid="uuid" :id="job_comment.id" @completed="getComments" type="reply"></job-comment-form>
                    </div>
                    <div class="d-flex flex-row comment-row" v-if="job_comment.reply" v-for="reply in orderedReplies(job_comment.reply)" style="margin-bottom: -40px;padding-right: 0;">
                        <div class="p-2"><span class="round"><img :src="getAvatar(reply.user)" alt="user" width="50"></span></div>
                        <div class="comment-text w-100">
                            <span class="pull-right" style="cursor: pointer;margin-left: 15px;" v-if="job_comment.user_id === getAuthUser('id')" :key="reply.id" v-confirm="{ok: confirmDelete(reply)}" v-tooltip="trans('job.delete_comment')"><i class="fas fa-trash"></i></span>
                            <span class="text-muted pull-right" style="font-size: 12px;"><i class="far fa-clock"></i> {{reply.created_at | momentFromNow}}</span>
                            <h5>{{reply.user.profile.first_name+' '+reply.user.profile.last_name}}</h5>
                            <p class="m-b-5" v-html="reply.comment"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h6 class="card-subtitle" v-if="!job_comments.length">{{trans('general.no_result_found')}}</h6>
    </div>
</template>

<script>
    import jobCommentForm from './job-comment-form'

    export default {
        components: {jobCommentForm},
        data(){
            return {
                job_comments: {}
            }
        },
        props: ['uuid'],
        mounted(){
            this.getComments();
        },
        methods: {
            getComments(){
                axios.get('/api/job/'+this.uuid+'/comment')
                    .then(response => response.data)
                    .then(response => {
                        this.job_comments = response;
                    })
                    .catch(error => {

                    });
            },
            getAvatar(user){
                if(user.profile.avatar)
                    return '/'+user.profile.avatar;
                else
                    return '/images/avatar.png';
            },
            orderedReplies(replies) {
                return _orderBy(replies, 'created_at','desc')
            },
            confirmDelete(job_comment){
                return dialog => this.deleteComment(job_comment);
            },
            deleteComment(job_comment){
                axios.delete('/api/job/'+this.uuid+'/comment/'+job_comment.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getComments();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          },
          momentDateTime(datetime) {
            return helper.formatDateTime(datetime);
          },
          momentFromNow(datetime){
            return helper.formatDateTimeFromNow(datetime);
          }
        },
        computed: {
        },
        watch: {

        }
    }
</script>

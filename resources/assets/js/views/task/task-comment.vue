<template>
    <div>
        <p class="alert alert-info"><i class="fas fa-exclamation-triangle"></i> {{trans('task.tip_task_comment')}}</p>

        <task-comment-form :uuid="uuid" @completed="getComments"></task-comment-form>

        <div class="comment-widgets comment-scroll" v-if="task_comments.length">
            <div class="d-flex flex-row comment-row" v-for="task_comment in task_comments">
                <div class="p-2"><span class="round"><img :src="getAvatar(task_comment.user)" alt="user" width="50"></span></div>
                <div class="comment-text w-100">
                    <span class="pull-right" style="cursor: pointer;margin-left: 15px;" v-if="task_comment.user_id === getAuthUser('id')" :key="task_comment.id" v-confirm="{ok: confirmDelete(task_comment)}" v-tooltip="trans('task.delete_comment')"><i class="fas fa-trash"></i></span>
                    <span class="text-muted pull-right" style="font-size: 12px;"><i class="fas fa-clock"></i> {{task_comment.created_at | momentFromNow}}</span>
                    <h5>{{task_comment.user.profile.first_name+' '+task_comment.user.profile.last_name}}</h5>
                    <p class="m-b-5" v-html="task_comment.comment"></p>
                    <div class="" style="padding-left:25px;margin-bottom:-30px;">
                        <task-comment-form :uuid="uuid" :id="task_comment.id" @completed="getComments" type="reply"></task-comment-form>
                    </div>
                    <div class="d-flex flex-row comment-row" v-if="task_comment.reply" v-for="reply in orderedReplies(task_comment.reply)" style="margin-bottom: -40px;padding-right: 0;">
                        <div class="p-2"><span class="round"><img :src="getAvatar(reply.user)" alt="user" width="50"></span></div>
                        <div class="comment-text w-100">
                            <span class="pull-right" style="cursor: pointer;margin-left: 15px;" v-if="task_comment.user_id === getAuthUser('id')" :key="reply.id" v-confirm="{ok: confirmDelete(reply)}" v-tooltip="trans('task.delete_comment')"><i class="fas fa-trash"></i></span>
                            <span class="text-muted pull-right" style="font-size: 12px;"><i class="far fa-clock"></i> {{reply.created_at | momentFromNow}}</span>
                            <h5>{{reply.user.profile.first_name+' '+reply.user.profile.last_name}}</h5>
                            <p class="m-b-5" v-html="reply.comment"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h6 class="card-subtitle" v-if="!task_comments.length">{{trans('general.no_result_found')}}</h6>
    </div>
</template>

<script>
    import taskCommentForm from './task-comment-form'

    export default {
        components: {taskCommentForm},
        data(){
            return {
                task_comments: {}
            }
        },
        props: ['uuid'],
        mounted(){
            this.getComments();
        },
        methods: {
            getComments(){
                axios.get('/api/task/'+this.uuid+'/comment')
                    .then(response => response.data)
                    .then(response => {
                        this.task_comments = response;
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
            confirmDelete(task_comment){
                return dialog => this.deleteComment(task_comment);
            },
            deleteComment(task_comment){
                axios.delete('/api/task/'+this.uuid+'/comment/'+task_comment.id)
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

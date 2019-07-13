<template>
    <div class="modal fade task-attachment-detail" tabindex="-1" role="dialog" aria-labelledby="taskAttachmentDetail" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="taskAttachmentDetail">{{task_attachment.title}}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row" v-if="task_attachment.user">
                        <div class="col-md-4 col-xs-4 b-r">
                            <div class="user-profile pull-right"><div class="profile-img"> <img :src="getAvatar(task_attachment.user)" alt="user" /> </div></div>
                            <strong>{{trans('task.owner')}}</strong>
                            <br />
                            <p class="text-muted">{{ task_attachment.user.profile.first_name+' '+task_attachment.user.profile.last_name}}</p>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <strong>{{trans('task.created_at')}}</strong>
                            <br>
                            <p class="text-muted">{{task_attachment.created_at | momentDateTime}}</p>
                        </div>
                    </div>
                    <hr />
                    <div class="m-t-20" v-html="task_attachment.description"></div>
                    <div v-if="attachments.length">
                        <ul style="list-style: none;padding: 0;" class="m-t-10">
                            <li v-for="attachment in attachments">
                                <a :href="`/task/${task_attachment.task.uuid}/attachment/${task_attachment.uuid}/attachment/${attachment.uuid}/download?token=${authToken}`"><i class="fas fa-paperclip"></i> {{attachment.user_filename}}</a>
                            </li>
                        </ul>
                    </div>
                    <hr />
                    <p><i class="far fa-clock"></i> <small>{{trans('task.updated_at')}} {{task_attachment.updated_at | momentDateTime}}</small></p>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        components:{},
        data() {
            return {
                task_attachment: {},
                attachments: [],
                local_id: ''
            };
        },
        props: ['uuid','auuid'],
        mounted() {
        },
        methods: {
            getAttachment(){
                this.local_id = this.auuid;
                axios.get('/api/task/'+this.uuid+'/attachment/'+this.local_id)
                    .then(response => response.data)
                    .then(response => {
                        this.task_attachment = response.task_attachment;
                        this.attachments = response.attachments;
                        this.$emit('loaded');
                    })
                    .catch(error => {
                        this.$router.push('/task/'+this.uuid);
                    });
            },
            getAvatar(user){
                if(user.profile.avatar)
                    return '/'+user.profile.avatar;
                else
                    return '/images/avatar.png';
            }
        },
        computed: {
            authToken(){
                return localStorage.getItem('auth_token');
            }
        },
        watch: {
            auuid(val) {
                if(val)
                    this.getAttachment();
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          },
          momentDateTime(datetime) {
            return helper.formatDateTime(datetime);
          },
        }
    }
</script>

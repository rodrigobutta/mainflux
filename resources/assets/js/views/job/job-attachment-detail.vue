<template>
    <div class="modal fade job-attachment-detail" tabindex="-1" role="dialog" aria-labelledby="jobAttachmentDetail" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="jobAttachmentDetail">{{job_attachment.title}}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row" v-if="job_attachment.user">
                        <div class="col-md-4 col-xs-4 b-r">
                            <div class="user-profile pull-right"><div class="profile-img"> <img :src="getAvatar(job_attachment.user)" alt="user" /> </div></div>
                            <strong>{{trans('job.owner')}}</strong>
                            <br />
                            <p class="text-muted">{{ job_attachment.user.profile.first_name+' '+job_attachment.user.profile.last_name}}</p>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <strong>{{trans('job.created_at')}}</strong>
                            <br>
                            <p class="text-muted">{{job_attachment.created_at | momentDateTime}}</p>
                        </div>
                    </div>
                    <hr />
                    <div class="m-t-20" v-html="job_attachment.description"></div>
                    <div v-if="attachments.length">
                        <ul style="list-style: none;padding: 0;" class="m-t-10">
                            <li v-for="attachment in attachments">
                                <a :href="`/job/${job_attachment.job.uuid}/attachment/${job_attachment.uuid}/attachment/${attachment.uuid}/download?token=${authToken}`"><i class="fas fa-paperclip"></i> {{attachment.user_filename}}</a>
                            </li>
                        </ul>
                    </div>
                    <hr />
                    <p><i class="far fa-clock"></i> <small>{{trans('job.updated_at')}} {{job_attachment.updated_at | momentDateTime}}</small></p>
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
                job_attachment: {},
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
                axios.get('/api/job/'+this.uuid+'/attachment/'+this.local_id)
                    .then(response => response.data)
                    .then(response => {
                        this.job_attachment = response.job_attachment;
                        this.attachments = response.attachments;
                        this.$emit('loaded');
                    })
                    .catch(error => {
                        this.$router.push('/job/'+this.uuid);
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

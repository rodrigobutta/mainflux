<template>
    <div class="modal fade job-note-detail" tabindex="-1" role="dialog" aria-labelledby="jobNoteDetail" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="jobNoteDetail">{{job_note.title}}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row" v-if="job_note.user">
                        <div class="col-md-4 col-xs-4 b-r">
                            <div class="user-profile pull-right"><div class="profile-img"> <img :src="getAvatar(job_note.user)" alt="user" /> </div></div>
                            <strong>{{trans('job.owner')}}</strong>
                            <br />
                            <p class="text-muted">{{ job_note.user.profile.first_name+' '+job_note.user.profile.last_name}}</p>
                        </div>
                        <div class="col-md-4 col-xs-4 b-r">
                            <strong>{{trans('job.created_at')}}</strong>
                            <br>
                            <p class="text-muted">{{job_note.created_at | momentDateTime}}</p>
                            <strong>{{trans('job.updated_at')}}</strong>
                            <br>
                            <p class="text-muted">{{job_note.updated_at | momentDateTime}}</p>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <div class="ribbon-wrapper card" style="margin-right: -15px;">
                                <div :class="['ribbon','ribbon-'+getNoteVisibility(job_note).color,'ribbon-right']">{{getNoteVisibility(job_note).label}}</div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="m-t-20" v-html="job_note.description"></div>
                    <div v-if="attachments.length">
                        <ul style="list-style: none;padding: 0;" class="m-t-10">
                            <li v-for="attachment in attachments">
                                <a :href="`/job/${job_note.job.uuid}/note/${job_note.uuid}/attachment/${attachment.uuid}/download?token=${authToken}`"><i class="fas fa-paperclip"></i> {{attachment.user_filename}}</a>
                            </li>
                        </ul>
                    </div>
                    <hr />
                    <p><i class="far fa-clock"></i> <small>{{trans('job.updated_at')}} {{job_note.updated_at | momentDateTime}}</small></p>
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
                job_note: {},
                attachments: [],
                local_id: ''
            };
        },
        props: ['uuid','nuuid'],
        mounted() {
        },
        methods: {
            getNote(){
                this.local_id = this.nuuid;
                axios.get('/api/job/'+this.uuid+'/note/'+this.local_id)
                    .then(response => response.data)
                    .then(response => {
                        this.job_note = response.job_note;
                        this.attachments = response.attachments;
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
            },
            getNoteVisibility(job_note){
                if(job_note.is_public)
                    return {'color': 'success','label': i18n.job.shared};
                else
                    return {'color': 'info','label': i18n.job.private};
            }
        },
        computed: {
            authToken(){
                return localStorage.getItem('auth_token');
            }
        },
        watch: {
            nuuid(val) {
                if(val)
                    this.getNote();
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

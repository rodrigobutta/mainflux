<template>
    <div class="modal fade task-note-detail" tabindex="-1" role="dialog" aria-labelledby="taskNoteDetail" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="taskNoteDetail">{{task_note.title}}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row" v-if="task_note.user">
                        <div class="col-md-4 col-xs-4 b-r">
                            <div class="user-profile pull-right"><div class="profile-img"> <img :src="getAvatar(task_note.user)" alt="user" /> </div></div>
                            <strong>{{trans('task.owner')}}</strong>
                            <br />
                            <p class="text-muted">{{ task_note.user.profile.first_name+' '+task_note.user.profile.last_name}}</p>
                        </div>
                        <div class="col-md-4 col-xs-4 b-r">
                            <strong>{{trans('task.created_at')}}</strong>
                            <br>
                            <p class="text-muted">{{task_note.created_at | momentDateTime}}</p>
                            <strong>{{trans('task.updated_at')}}</strong>
                            <br>
                            <p class="text-muted">{{task_note.updated_at | momentDateTime}}</p>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <div class="ribbon-wrapper card" style="margin-right: -15px;">
                                <div :class="['ribbon','ribbon-'+getNoteVisibility(task_note).color,'ribbon-right']">{{getNoteVisibility(task_note).label}}</div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="m-t-20" v-html="task_note.description"></div>
                    <div v-if="attachments.length">
                        <ul style="list-style: none;padding: 0;" class="m-t-10">
                            <li v-for="attachment in attachments">
                                <a :href="`/task/${task_note.task.uuid}/note/${task_note.uuid}/attachment/${attachment.uuid}/download?token=${authToken}`"><i class="fas fa-paperclip"></i> {{attachment.user_filename}}</a>
                            </li>
                        </ul>
                    </div>
                    <hr />
                    <p><i class="far fa-clock"></i> <small>{{trans('task.updated_at')}} {{task_note.updated_at | momentDateTime}}</small></p>
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
                task_note: {},
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
                axios.get('/api/task/'+this.uuid+'/note/'+this.local_id)
                    .then(response => response.data)
                    .then(response => {
                        this.task_note = response.task_note;
                        this.attachments = response.attachments;
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
            },
            getNoteVisibility(task_note){
                if(task_note.is_public)
                    return {'color': 'success','label': i18n.task.shared};
                else
                    return {'color': 'info','label': i18n.task.private};
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

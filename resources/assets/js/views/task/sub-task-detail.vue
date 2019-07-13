<template>
    <div class="modal fade sub-task-detail" tabindex="-1" role="dialog" aria-labelledby="subTaskDetail" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="subTaskDetail">{{sub_task.title}}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row" v-if="sub_task.user_added">
                        <div class="col-md-3 col-xs-6 b-r">
                            <div class="user-profile pull-right"><div class="profile-img"> <img :src="getAvatar(sub_task.user_added)" alt="user" /> </div></div>
                            <strong>{{trans('task.owner')}}</strong>
                            <br />
                            <p class="text-muted">{{ sub_task.user_added.profile.first_name+' '+sub_task.user_added.profile.last_name}}</p>
                        </div>
                        <div class="col-md-3 col-xs-6 b-r">
                            <template v-if="sub_task.status">
                                <strong>{{trans('task.completed_at')}}</strong>
                                <br />
                                <p class="text-muted">{{sub_task.completed_at | momentDateTime}}</p>
                                <button class="btn btn-danger btn-xs" :key="sub_task.id" v-confirm="{ok: confirmToggleStatus(sub_task)}">{{trans('task.mark_as_incomplete')}}</button>
                            </template>
                            <template v-else>
                                <button class="btn btn-success btn-xs" :key="sub_task.id" v-confirm="{ok: confirmToggleStatus(sub_task)}">{{trans('task.mark_as_complete')}}</button>
                            </template>
                        </div>
                        <div class="col-md-3 col-xs-6 b-r">
                            <strong>{{trans('task.created_at')}}</strong>
                            <br>
                            <p class="text-muted">{{sub_task.created_at | momentDateTime}}</p>
                            <strong>{{trans('task.updated_at')}}</strong>
                            <br>
                            <p class="text-muted">{{sub_task.updated_at | momentDateTime}}</p>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="ribbon-wrapper card" style="margin-right: -15px;">
                                <div :class="['ribbon','ribbon-'+getSubTaskStatus(sub_task).color,'ribbon-right']">{{getSubTaskStatus(sub_task).label}}</div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="m-t-20" v-html="sub_task.description"></div>
                    <div v-if="attachments.length">
                        <ul style="list-style: none;padding: 0;" class="m-t-10">
                            <li v-for="attachment in attachments">
                                <a :href="`/task/${sub_task.task.uuid}/sub-task/${sub_task.uuid}/attachment/${attachment.uuid}/download?token=${authToken}`"><i class="fas fa-paperclip"></i> {{attachment.user_filename}}</a>
                            </li>
                        </ul>
                    </div>
                    <hr />
                    <p><i class="far fa-clock"></i> <small>{{trans('task.updated_at')}} {{sub_task.updated_at | momentDateTime}}</small></p>
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
                sub_task: {},
                attachments: [],
                local_id: ''
            };
        },
        props: ['uuid','suuid'],
        mounted() {
        },
        methods: {
            getSubTask(){
                this.local_id = this.suuid;
                axios.get('/api/task/'+this.uuid+'/sub-task/'+this.local_id)
                    .then(response => response.data)
                    .then(response => {
                        this.sub_task = response.sub_task;
                        this.attachments = response.attachments;
                    })
                    .catch(error => {
                        this.$router.push('/task/'+this.uuid);
                    });
            },
            getSubTaskStatus(sub_task){
                return helper.getSubTaskStatus(sub_task);
            },
            getAvatar(user){
                if(user.profile.avatar)
                    return '/'+user.profile.avatar;
                else
                    return '/images/avatar.png';
            },
            confirmToggleStatus(sub_task){
                return dialog => this.toggleStatus(sub_task);
            },
            toggleStatus(sub_task){
                axios.post('/api/task/'+this.uuid+'/sub-task/'+this.local_id+'/toggle-status')
                    .then(response => response.data)
                    .then(response => {
                        this.getSubTask();
                        this.$emit('updateStatus');
                        toastr.success(response.message);
                    })
                    .catch(error => {
                    });
            }
        },
        computed: {
            authToken(){
                return localStorage.getItem('auth_token');
            }
        },
        watch: {
            suuid(val) {
                if(val)
                    this.getSubTask();
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

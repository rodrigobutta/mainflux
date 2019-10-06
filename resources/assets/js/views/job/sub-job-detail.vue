<template>
    <div class="modal fade sub-job-detail" tabindex="-1" role="dialog" aria-labelledby="subJobDetail" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="subJobDetail">{{sub_job.title}}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row" v-if="sub_job.user_added">
                        <div class="col-md-3 col-xs-6 b-r">
                            <div class="user-profile pull-right"><div class="profile-img"> <img :src="getAvatar(sub_job.user_added)" alt="user" /> </div></div>
                            <strong>{{trans('job.owner')}}</strong>
                            <br />
                            <p class="text-muted">{{ sub_job.user_added.profile.first_name+' '+sub_job.user_added.profile.last_name}}</p>
                        </div>
                        <div class="col-md-3 col-xs-6 b-r">
                            <template v-if="sub_job.status">
                                <strong>{{trans('job.completed_at')}}</strong>
                                <br />
                                <p class="text-muted">{{sub_job.completed_at | momentDateTime}}</p>
                                <button class="btn btn-danger btn-xs" :key="sub_job.id" v-confirm="{ok: confirmToggleStatus(sub_job)}">{{trans('job.mark_as_incomplete')}}</button>
                            </template>
                            <template v-else>
                                <button class="btn btn-success btn-xs" :key="sub_job.id" v-confirm="{ok: confirmToggleStatus(sub_job)}">{{trans('job.mark_as_complete')}}</button>
                            </template>
                        </div>
                        <div class="col-md-3 col-xs-6 b-r">
                            <strong>{{trans('job.created_at')}}</strong>
                            <br>
                            <p class="text-muted">{{sub_job.created_at | momentDateTime}}</p>
                            <strong>{{trans('job.updated_at')}}</strong>
                            <br>
                            <p class="text-muted">{{sub_job.updated_at | momentDateTime}}</p>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="ribbon-wrapper card" style="margin-right: -15px;">
                                <div :class="['ribbon','ribbon-'+getSubJobStatus(sub_job).color,'ribbon-right']">{{getSubJobStatus(sub_job).label}}</div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="m-t-20" v-html="sub_job.description"></div>
                    <div v-if="attachments.length">
                        <ul style="list-style: none;padding: 0;" class="m-t-10">
                            <li v-for="attachment in attachments">
                                <a :href="`/job/${sub_job.job.uuid}/sub-job/${sub_job.uuid}/attachment/${attachment.uuid}/download?token=${authToken}`"><i class="fas fa-paperclip"></i> {{attachment.user_filename}}</a>
                            </li>
                        </ul>
                    </div>
                    <hr />
                    <p><i class="far fa-clock"></i> <small>{{trans('job.updated_at')}} {{sub_job.updated_at | momentDateTime}}</small></p>
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
                sub_job: {},
                attachments: [],
                local_id: ''
            };
        },
        props: ['uuid','suuid'],
        mounted() {
        },
        methods: {
            getSubJob(){
                this.local_id = this.suuid;
                axios.get('/api/job/'+this.uuid+'/sub-job/'+this.local_id)
                    .then(response => response.data)
                    .then(response => {
                        this.sub_job = response.sub_job;
                        this.attachments = response.attachments;
                    })
                    .catch(error => {
                        this.$router.push('/job/'+this.uuid);
                    });
            },
            getSubJobStatus(sub_job){
                return helper.getSubJobStatus(sub_job);
            },
            getAvatar(user){
                if(user.profile.avatar)
                    return '/'+user.profile.avatar;
                else
                    return '/images/avatar.png';
            },
            confirmToggleStatus(sub_job){
                return dialog => this.toggleStatus(sub_job);
            },
            toggleStatus(sub_job){
                axios.post('/api/job/'+this.uuid+'/sub-job/'+this.local_id+'/toggle-status')
                    .then(response => response.data)
                    .then(response => {
                        this.getSubJob();
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
                    this.getSubJob();
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

<template>
    <div class="modal fade announcement-detail" tabindex="-1" role="dialog" aria-labelledby="announcementDetail" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="announcementDetail">{{announcement.title}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row" v-if="announcement.user_added">
                        <div class="col-md-4 col-xs-6 b-r">
                            <div class="user-profile pull-right"><div class="profile-img"> <img :src="getAvatar(announcement.user_added)" alt="user" /> </div></div>
                            <strong>{{trans('announcement.posted_by')}}</strong>
                            <br />
                            <p class="text-muted">{{ announcement.user_added.profile.first_name+' '+announcement.user_added.profile.last_name+' ('+announcement.user_added.profile.designation.name+' '+announcement.user_added.profile.designation.department.name+')'}}</p>
                            <div class="ribbon-wrapper card" style="margin-left: -15px;">
                                <div :class="['ribbon','ribbon-'+announcementStatus.color]">{{announcementStatus.label}}</div>
                            </div>
                            <div class="ribbon-wrapper card" style="margin-left: -15px;" v-if="announcement.is_public">
                                <div :class="['ribbon','ribbon-success']">{{trans('announcement.public')}}</div>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-6 b-r">
                            <strong>{{trans('announcement.start_date')}}</strong>
                            <br />
                            <p class="text-muted">{{ announcement.start_date | moment}}</p>
                            <strong>{{trans('announcement.end_date')}}</strong>
                            <br />
                            <p class="text-muted">{{ announcement.end_date | moment}}</p>
                        </div>
                        <div class="col-md-4 col-xs-6">
                            <strong>{{trans('announcement.created_at')}}</strong>
                            <br>
                            <p class="text-muted">{{announcement.created_at | momentDateTime}}</p>
                            <strong>{{trans('announcement.updated_at')}}</strong>
                            <br>
                            <p class="text-muted">{{announcement.updated_at | momentDateTime}}</p>
                        </div>
                    </div>
                    <hr />
                    <template v-if="announcement.user_id == getAuthUser('id') && !announcement.is_public">
                        <h4 class="card-title">{{trans('announcement.restricted_to')}}: {{trans(announcement.restricted_to+'.'+announcement.restricted_to)}}</h4>
                        <ol v-if="announcement.designation.length">
                            <li v-for="designation in announcement.designation">{{designation.name}}</li>
                        </ol>
                        <ol v-if="announcement.location.length">
                            <li v-for="location in announcement.location">{{location.name}}</li>
                        </ol>
                        <ol v-if="announcement.user.length">
                            <li v-for="user in announcement.user">{{user.profile.first_name+' '+user.profile.last_name+' ('+user.profile.designation.name+' '+user.profile.designation.department.name+')'}}</li>
                        </ol>
                    <hr />
                    </template>
                    <div class="m-t-20" v-html="announcement.description"></div>
                    <div v-if="attachments.length">
                        <ul style="list-style: none;padding: 0;" class="m-t-10">
                            <li v-for="attachment in attachments">
                                <a :href="`/announcement/${announcement.id}/attachment/${attachment.uuid}/download?token=${authToken}`"><i class="fas fa-paperclip"></i> {{attachment.user_filename}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                announcement: {},
                attachments: []
            }
        },
        props: ['id'],
        mounted(){
        },
        methods: {
            getAnnouncement(){
                axios.get('/api/announcement/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.announcement = response.announcement;
                        this.attachments = response.attachments;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        this.closeModal();
                    })
            },
            closeModal(){
                $('.announcement-detail').modal('hide');
            },
            getAvatar(user){
                if(user.profile.avatar)
                    return '/'+user.profile.avatar;
                else
                    return '/images/avatar.png';
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            }
        },
        watch: {
            id(val) {
                if(val)
                    this.getAnnouncement();
                else
                    this.closeModal();
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
        computed: {
            authToken(){
                return localStorage.getItem('auth_token');
            },
            announcementStatus(){
                if(this.announcement.start_date <= moment(new Date()).format('YYYY-MM-DD') && this.announcement.end_date >= moment(new Date()).format('YYYY-MM-DD'))
                    return {'label': i18n.announcement.active, 'color': 'success'};
                else
                    return {'label': i18n.announcement.inactive, 'color': 'danger'};
            }
        }
    }
</script>

<template>
    <form @submit.prevent="proceed" @keydown="announcementForm.errors.clear($event.target.name)">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="">{{trans('announcement.title')}}</label>
                    <input class="form-control" type="text" value="" v-model="announcementForm.title" name="title" :placeholder="trans('announcement.title')">
                    <show-error :form-name="announcementForm" prop-name="title"></show-error>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('announcement.start_date')}}</label>
                            <datepicker v-model="announcementForm.start_date" :bootstrapStyling="true" @selected="announcementForm.errors.clear('start_date')" :placeholder="trans('announcement.start_date')"></datepicker>
                            <show-error :form-name="announcementForm" prop-name="start_date"></show-error>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">{{trans('announcement.end_date')}}</label>
                            <datepicker v-model="announcementForm.end_date" :bootstrapStyling="true" @selected="announcementForm.errors.clear('end_date')" :placeholder="trans('announcement.end_date')"></datepicker>
                            <show-error :form-name="announcementForm" prop-name="end_date"></show-error>
                        </div>
                    </div>
                </div>
                <div class="form-group" v-if="hasAdminRole()">
                    <switches class="m-l-20" v-model="announcementForm.is_public" theme="bootstrap" color="success"></switches> {{trans('announcement.is_public')}} <show-tip module="announcement" tip="tip_is_public" type="field"></show-tip>
                </div>
                <template v-if="!announcementForm.is_public">
                    <div class="form-group">
                        <label for="">{{trans('announcement.restricted_to')}} <show-tip module="announcement" tip="tip_restricted_to" type="field"></show-tip></label>
                        <select name="restricted_to" class="form-control" v-model="announcementForm.restricted_to" :placeholder="trans('general.select_one')" @change="announcementForm.errors.clear('restricted_to')">
                            <option value="" disabled>{{trans('general.select_one')}}</option>
                            <option value="designation">{{trans('designation.designation')}}</option>
                            <option value="location">{{trans('location.location')}}</option>
                            <option value="user">{{trans('user.user')}}</option>
                        </select>
                        <show-error :form-name="announcementForm" prop-name="restricted_to"></show-error>
                    </div>
                    <div class="form-group" v-if="announcementForm.restricted_to === 'designation'">
                        <label for="">{{trans('designation.designation')}}</label>
                        <v-select label="name" track-by="id" v-model="selected_designations" name="designation_id" id="designation_id" :options="designations" :placeholder="trans('designation.select_designation')" @select="onDesignationSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onDesignationRemove" :selected="selected_designations">
                        </v-select>
                        <show-error :form-name="announcementForm" prop-name="designation_id"></show-error>
                    </div>
                    <div class="form-group" v-if="announcementForm.restricted_to === 'location'">
                        <label for="">{{trans('location.location')}}</label>
                        <v-select label="name" track-by="id" v-model="selected_locations" name="location_id" id="location_id" :options="locations" :placeholder="trans('location.select_location')" @select="onLocationSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onLocationRemove" :selected="selected_locations">
                        </v-select>
                        <show-error :form-name="announcementForm" prop-name="location_id"></show-error>
                    </div>
                    <div class="form-group" v-if="announcementForm.restricted_to === 'user'">
                        <label for="">{{trans('user.user')}}</label>
                        <v-select label="name" track-by="id" v-model="selected_users" name="user_id" id="user_id" :options="users" :placeholder="trans('user.select_user')" @select="onUserSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onUserRemove" :selected="selected_users">
                        </v-select>
                        <show-error :form-name="announcementForm" prop-name="user_id"></show-error>
                    </div>
                </template>
                <div class="form-group">
                    <file-upload-input :button-text="trans('general.upload_document')" :token="announcementForm.upload_token" module="announcement" :clear-file="clearAnnouncementAttachment" :module-id="id || ''"></file-upload-input>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <html-editor name="description" :model.sync="announcementForm.description" :isUpdate="id ? true : false" @clearErrors="announcementForm.errors.clear('description')"></html-editor>
                <show-error :form-name="announcementForm" prop-name="description"></show-error>
            </div>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/announcement" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    import uuid from 'uuid/v4'
    import htmlEditor from '../../components/html-editor'
    import fileUploadInput from '../../components/file-upload-input'
    import datepicker from 'vuejs-datepicker'
    import vSelect from 'vue-multiselect'
    import switches from 'vue-switches'

    export default {
        components: {datepicker,vSelect,htmlEditor,fileUploadInput,switches},
        data() {
            return {
                announcementForm: new Form({
                    title : '',
                    description : '',
                    start_date: '',
                    end_date: '',
                    is_public: 0,
                    restricted_to: '',
                    designation_id: [],
                    location_id: [],
                    user_id: [],
                    upload_token: ''
                }),
                designations: [],
                selected_designations: '',
                locations: [],
                selected_locations: '',
                users: [],
                selected_users: '',
                clearAnnouncementAttachment: false
            };
        },
        props: ['id'],
        mounted() {
            if(!this.id)
            this.announcementForm.upload_token = uuid();
            if(this.id)
                this.getAnnouncement();
            axios.get('/api/announcement/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.designations = response.designations;
                    this.locations = response.locations;
                    this.users = response.users;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateAnnouncement();
                else
                    this.storeAnnouncement();
            },
            storeAnnouncement(){
                this.announcementForm.start_date = moment(this.announcementForm.start_date).format('YYYY-MM-DD');
                this.announcementForm.end_date = moment(this.announcementForm.end_date).format('YYYY-MM-DD');
                this.announcementForm.is_public = (this.announcementForm.is_public) ? 1 : 0;
                this.announcementForm.post('/api/announcement')
                    .then(response => {
                        toastr.success(response.message);
                        this.announcementForm.description = '';
                        this.announcementForm.upload_token = uuid();
                        this.clearAnnouncementAttachment = true;
                        this.selected_designations = null;
                        this.selected_locations = null;
                        this.selected_users = null;
                        this.announcementForm.designation_id = [];
                        this.announcementForm.location_id = [];
                        this.announcementForm.user_id = [];
                        this.$emit('completed');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getAnnouncement(){
                axios.get('/api/announcement/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.announcementForm.title = response.announcement.title;
                        this.announcementForm.description = response.announcement.description;
                        this.announcementForm.start_date = response.announcement.start_date;
                        this.announcementForm.end_date = response.announcement.end_date;
                        this.announcementForm.upload_token = response.announcement.upload_token;
                        this.announcementForm.is_public = response.announcement.is_public;
                        this.announcementForm.restricted_to = response.announcement.restricted_to;
                        this.announcementForm.designation_id = response.designation_id;
                        this.selected_designations = response.selected_designations;
                        this.announcementForm.location_id = response.location_id;
                        this.selected_locations = response.selected_locations;
                        this.announcementForm.user_id = response.user_id;
                        this.selected_users = response.selected_users;
                    })
                    .catch(error => {
                        this.$router.push('/announcement');
                    });
            },
            updateAnnouncement(){
                this.announcementForm.start_date = moment(this.announcementForm.start_date).format('YYYY-MM-DD');
                this.announcementForm.end_date = moment(this.announcementForm.end_date).format('YYYY-MM-DD');
                this.announcementForm.is_public = (this.announcementForm.is_public) ? 1 : 0;
                this.announcementForm.patch('/api/announcement/'+this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/announcement');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            onDesignationSelect(selectedOption){
                this.announcementForm.errors.clear('designation_id');
                this.announcementForm.designation_id.push(selectedOption.id);
            },
            onDesignationRemove(removedOption){
                this.announcementForm.designation_id.splice(this.announcementForm.designation_id.indexOf(removedOption.id), 1);
            },
            onLocationSelect(selectedOption){
                this.announcementForm.errors.clear('location_id');
                this.announcementForm.location_id.push(selectedOption.id);
            },
            onLocationRemove(removedOption){
                this.announcementForm.location_id.splice(this.announcementForm.location_id.indexOf(removedOption.id), 1);
            },
            onUserSelect(selectedOption){
                this.announcementForm.errors.clear('user_id');
                this.announcementForm.user_id.push(selectedOption.id);
            },
            onUserRemove(removedOption){
                this.announcementForm.user_id.splice(this.announcementForm.user_id.indexOf(removedOption.id), 1);
            },
            hasAdminRole(){
                return helper.hasAdminRole();
            }
        }
    }
</script>
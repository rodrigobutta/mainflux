<template>
    <div>
        <p><small>{{trans('job.rating_type')}}: {{trans('job.'+job.rating_type+'_rating')}}</small></p>
        <template v-if="job.rating_type === 'job_based' && job.user.length">
            <show-tip module="job" tip="tip_job_rating_job_based"></show-tip>
            <form @submit.prevent="submitJobRating" @keydown="jobRatingForm.errors.clear($event.target.name)">
                <div v-for="row in jobRatingForm.row" class="row m-b-10">
                    <div class="col-6 col-md-4">
                        <div class="d-flex flex-row">
                            <div class=""><img :src="getAvatar(row.user)" alt="user" class="img-circle" width="70"></div>
                            <div class="p-l-20">
                                <h4 class="font-medium">{{row.user.profile.first_name+' '+row.user.profile.last_name}}</h4>
                                <h6 v-if="row.user.profile.designation">{{row.user.profile.designation.name+' '+trans('general.in')+' '+row.user.profile.designation.department.name}}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="form-group">
                            <select name="rating" class="form-control" v-model="row.rating" :placeholder="trans('job.rating')">
                                <option disabled value="">{{trans('job.rating')}}</option>
                                <option value="1">{{trans('job.rating_star',{star:1})}}</option>
                                <option value="2">{{trans('job.rating_star',{star:2})}}</option>
                                <option value="3">{{trans('job.rating_star',{star:3})}}</option>
                                <option value="4">{{trans('job.rating_star',{star:4})}}</option>
                                <option value="5">{{trans('job.rating_star',{star:5})}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <autosize-textarea row="2" class="form-control" v-model="row.remarks" :placeholder="trans('job.rating_remarks')" name="remarks"></autosize-textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-info waves-effect waves-light pull-right">{{trans('general.save')}}</button>
                <div class="clearfix"></div>
            </form>
        </template>
        <template v-if="job.rating_type === 'sub_job_based'">
            <show-tip module="job" tip="tip_job_rating_sub_job_based"></show-tip>
            <form @submit.prevent="submitSubJobRating" @keydown="subJobRatingForm.errors.clear($event.target.name)">
                <div class="form-group">
                    <label>{{trans('user.select_user')}}</label>
                    <select name="user_id" class="form-control" v-model="subJobRatingForm.user_id" :placeholder="trans('user.select_user')" @change="updateSubJobRating">
                        <option disabled value="">{{trans('user.select_user')}}</option>
                        <option v-for="user in job.user" :value="user.id">{{user.profile.first_name+' '+user.profile.last_name}}</option>
                    </select>
                </div>
                <template v-if="subJobRatingForm.user_id">
                    <div v-for="row in subJobRatingForm.row" class="row">
                        <div class="col-6 col-md-4">
                            <div class="form-group">
                                <label>{{row.sub_job.title}}</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="form-group">
                                <select name="rating" class="form-control" v-model="row.rating" :placeholder="trans('job.rating')">
                                    <option disabled value="">{{trans('job.rating')}}</option>
                                    <option value="1">{{trans('job.rating_star',{star:1})}}</option>
                                    <option value="2">{{trans('job.rating_star',{star:2})}}</option>
                                    <option value="3">{{trans('job.rating_star',{star:3})}}</option>
                                    <option value="4">{{trans('job.rating_star',{star:4})}}</option>
                                    <option value="5">{{trans('job.rating_star',{star:5})}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <autosize-textarea row="2" class="form-control" v-model="row.remarks" :placeholder="trans('job.rating_remarks')" name="remarks"></autosize-textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info waves-effect waves-light pull-right">{{trans('general.save')}}</button>
                </template>
                <div class="clearfix"></div>
            </form>
        </template>
    </div>
</template>

<script>
    import autosizeTextarea from '../../components/autosize-textarea'

    export default {
        components:{autosizeTextarea},
        data() {
            return {
                jobRatingForm: new Form({
                    row: []
                },false),
                subJobRatingForm: new Form({
                    user_id: '',
                    row: []
                },false)
            }
        },
        props: ['uuid','job'],
        mounted() {
            this.initialize();
        },
        methods: {
            initialize(){
                let vm = this;
                this.jobRatingForm.row = [];
                this.subJobRatingForm.row = [];
                if(this.job.rating_type === 'job_based')
                    this.job.user.forEach(function(user){
                        vm.jobRatingForm.row.push({
                            user: user,
                            user_id: user.id,
                            remarks: user.pivot.remarks,
                            rating: user.pivot.rating || ''
                        });
                    })
                else if(this.job.rating_type === 'sub_job_based')
                    this.job.sub_job.forEach(function(sub_job){
                        vm.subJobRatingForm.row.push({
                            sub_job: sub_job,
                            sub_job_id: sub_job.id,
                            remarks: null,
                            rating: ''
                        });
                    })
            },
            getAvatar(user){
                return helper.getAvatar(user);
            },
            updateSubJobRating(){
                let vm = this;
                this.subJobRatingForm.row.forEach(function(row){
                    let sub_job_rating = row.sub_job.sub_job_rating.filter(sub_job_rating => sub_job_rating.user_id == vm.subJobRatingForm.user_id);
                    if(sub_job_rating.length){
                        row.remarks = sub_job_rating[0].remarks;
                        row.rating = sub_job_rating[0].rating;
                    } else {
                        row.remarks = '';
                        row.rating = '';
                    }
                });
            },
            submitJobRating(){
                this.jobRatingForm.post('/api/job/'+this.job.uuid+'/rating')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('ratingComplete');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            submitSubJobRating(){
                this.subJobRatingForm.post('/api/job/'+this.job.uuid+'/rating/sub-job')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('ratingComplete');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        },
        watch: {
            job(val){
                this.initialize();
                this.updateSubJobRating();
            }
        }
    }
</script>

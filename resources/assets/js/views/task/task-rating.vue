<template>
    <div>
        <p><small>{{trans('task.rating_type')}}: {{trans('task.'+task.rating_type+'_rating')}}</small></p>
        <template v-if="task.rating_type === 'task_based' && task.user.length">
            <show-tip module="task" tip="tip_task_rating_task_based"></show-tip>
            <form @submit.prevent="submitTaskRating" @keydown="taskRatingForm.errors.clear($event.target.name)">
                <div v-for="row in taskRatingForm.row" class="row m-b-10">
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
                            <select name="rating" class="form-control" v-model="row.rating" :placeholder="trans('task.rating')">
                                <option disabled value="">{{trans('task.rating')}}</option>
                                <option value="1">{{trans('task.rating_star',{star:1})}}</option>
                                <option value="2">{{trans('task.rating_star',{star:2})}}</option>
                                <option value="3">{{trans('task.rating_star',{star:3})}}</option>
                                <option value="4">{{trans('task.rating_star',{star:4})}}</option>
                                <option value="5">{{trans('task.rating_star',{star:5})}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <autosize-textarea row="2" class="form-control" v-model="row.remarks" :placeholder="trans('task.rating_remarks')" name="remarks"></autosize-textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-info waves-effect waves-light pull-right">{{trans('general.save')}}</button>
                <div class="clearfix"></div>
            </form>
        </template>
        <template v-if="task.rating_type === 'sub_task_based'">
            <show-tip module="task" tip="tip_task_rating_sub_task_based"></show-tip>
            <form @submit.prevent="submitSubTaskRating" @keydown="subTaskRatingForm.errors.clear($event.target.name)">
                <div class="form-group">
                    <label>{{trans('user.select_user')}}</label>
                    <select name="user_id" class="form-control" v-model="subTaskRatingForm.user_id" :placeholder="trans('user.select_user')" @change="updateSubTaskRating">
                        <option disabled value="">{{trans('user.select_user')}}</option>
                        <option v-for="user in task.user" :value="user.id">{{user.profile.first_name+' '+user.profile.last_name}}</option>
                    </select>
                </div>
                <template v-if="subTaskRatingForm.user_id">
                    <div v-for="row in subTaskRatingForm.row" class="row">
                        <div class="col-6 col-md-4">
                            <div class="form-group">
                                <label>{{row.sub_task.title}}</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="form-group">
                                <select name="rating" class="form-control" v-model="row.rating" :placeholder="trans('task.rating')">
                                    <option disabled value="">{{trans('task.rating')}}</option>
                                    <option value="1">{{trans('task.rating_star',{star:1})}}</option>
                                    <option value="2">{{trans('task.rating_star',{star:2})}}</option>
                                    <option value="3">{{trans('task.rating_star',{star:3})}}</option>
                                    <option value="4">{{trans('task.rating_star',{star:4})}}</option>
                                    <option value="5">{{trans('task.rating_star',{star:5})}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <autosize-textarea row="2" class="form-control" v-model="row.remarks" :placeholder="trans('task.rating_remarks')" name="remarks"></autosize-textarea>
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
                taskRatingForm: new Form({
                    row: []
                },false),
                subTaskRatingForm: new Form({
                    user_id: '',
                    row: []
                },false)
            }
        },
        props: ['uuid','task'],
        mounted() {
            this.initialize();
        },
        methods: {
            initialize(){
                let vm = this;
                this.taskRatingForm.row = [];
                this.subTaskRatingForm.row = [];
                if(this.task.rating_type === 'task_based')
                    this.task.user.forEach(function(user){
                        vm.taskRatingForm.row.push({
                            user: user,
                            user_id: user.id,
                            remarks: user.pivot.remarks,
                            rating: user.pivot.rating || ''
                        });
                    })
                else if(this.task.rating_type === 'sub_task_based')
                    this.task.sub_task.forEach(function(sub_task){
                        vm.subTaskRatingForm.row.push({
                            sub_task: sub_task,
                            sub_task_id: sub_task.id,
                            remarks: null,
                            rating: ''
                        });
                    })
            },
            getAvatar(user){
                return helper.getAvatar(user);
            },
            updateSubTaskRating(){
                let vm = this;
                this.subTaskRatingForm.row.forEach(function(row){
                    let sub_task_rating = row.sub_task.sub_task_rating.filter(sub_task_rating => sub_task_rating.user_id == vm.subTaskRatingForm.user_id);
                    if(sub_task_rating.length){
                        row.remarks = sub_task_rating[0].remarks;
                        row.rating = sub_task_rating[0].rating;
                    } else {
                        row.remarks = '';
                        row.rating = '';
                    }
                });
            },
            submitTaskRating(){
                this.taskRatingForm.post('/api/task/'+this.task.uuid+'/rating')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('ratingComplete');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            submitSubTaskRating(){
                this.subTaskRatingForm.post('/api/task/'+this.task.uuid+'/rating/sub-task')
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
            task(val){
                this.initialize();
                this.updateSubTaskRating();
            }
        }
    }
</script>

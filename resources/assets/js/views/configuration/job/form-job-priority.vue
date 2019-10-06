<template>
    <form @submit.prevent="proceed" @keydown="jobPriorityForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('job.job_priority_name')}}</label>
            <input class="form-control" type="text" value="" v-model="jobPriorityForm.name" name="name" :placeholder="trans('job.job_priority_name')">
            <show-error :form-name="jobPriorityForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('job.job_priority_description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="jobPriorityForm.description" rows="2" name="description" :placeholder="trans('job.job_priority_description')"></textarea>
            <show-error :form-name="jobPriorityForm" prop-name="description"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/configuration/job" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    export default {
        components: {},
        data() {
            return {
                jobPriorityForm: new Form({
                    name : '',
                    description : ''
                })
            };
        },
        props: ['id'],
        mounted() {
            if(this.id)
                this.getJobPriority();
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateJobPriority();
                else
                    this.storeJobPriority();
            },
            storeJobPriority(){
                this.jobPriorityForm.post('/api/job-priority')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getJobPriority(){
                axios.get('/api/job-priority/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.jobPriorityForm.name = response.name;
                        this.jobPriorityForm.description = response.description;
                    })
                    .catch(error => {
                        this.$router.push('/configuration/job');
                    });
            },
            updateJobPriority(){
                this.jobPriorityForm.patch('/api/job-priority/'+this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/configuration/job');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
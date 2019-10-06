<template>
    <form @submit.prevent="proceed" @keydown="jobCategoryForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('job.job_category_name')}}</label>
            <input class="form-control" type="text" value="" v-model="jobCategoryForm.name" name="name" :placeholder="trans('job.job_category_name')">
            <show-error :form-name="jobCategoryForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('job.job_category_description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="jobCategoryForm.description" rows="2" name="description" :placeholder="trans('job.job_category_description')"></textarea>
            <show-error :form-name="jobCategoryForm" prop-name="description"></show-error>
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
                jobCategoryForm: new Form({
                    name : '',
                    description : ''
                })
            };
        },
        props: ['id'],
        mounted() {
            if(this.id)
                this.getJobCategory();
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateJobCategory();
                else
                    this.storeJobCategory();
            },
            storeJobCategory(){
                this.jobCategoryForm.post('/api/job-category')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getJobCategory(){
                axios.get('/api/job-category/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.jobCategoryForm.name = response.name;
                        this.jobCategoryForm.description = response.description;
                    })
                    .catch(error => {
                        this.$router.push('/configuration/job');
                    });
            },
            updateJobCategory(){
                this.jobCategoryForm.patch('/api/job-category/'+this.id)
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
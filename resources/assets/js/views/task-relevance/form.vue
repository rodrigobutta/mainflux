<template>
    <form @submit.prevent="proceed" @keydown="taskRelevanceForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('task-relevance.name')}}</label>
            <input class="form-control" type="text" value="" v-model="taskRelevanceForm.name" name="name" :placeholder="trans('task-relevance.name')">
            <show-error :form-name="taskRelevanceForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task-relevance.code')}}</label>
            <input class="form-control" type="text" value="" v-model="taskRelevanceForm.code" name="code" :placeholder="trans('task-relevance.code')">
            <show-error :form-name="taskRelevanceForm" prop-name="code"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task-relevance.description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="taskRelevanceForm.description" rows="2" name="description" :placeholder="trans('task-relevance.description')"></textarea>
            <show-error :form-name="taskRelevanceForm" prop-name="description"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/task-relevance" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    export default {
        data() {
            return {
                taskRelevanceForm: new Form({
                    'name' : '',
                    'code' : '',
                    'description' : ''
                })
            };
        },
        props: ['id'],
        mounted() {
            if(this.id)
                this.getContractor();
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateContractor();
                else
                    this.storeContractor();
            },
            storeContractor(){
                this.taskRelevanceForm.post('/api/task-relevance')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed')
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getContractor(){
                axios.get('/api/task-relevance/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.taskRelevanceForm.name = response.name;
                        this.taskRelevanceForm.code = response.code;
                        this.taskRelevanceForm.description = response.description;
                    })
                    .catch(error => {
                        this.$router.push('/task-relevance');
                    });
            },
            updateContractor(){
                this.taskRelevanceForm.patch('/api/task-relevance/'+this.id)
                    .then(response => {
                            toastr.success(response.message);
                            this.$router.push('/task-relevance');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>

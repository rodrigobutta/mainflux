<template>
    <form @submit.prevent="proceed" @keydown="taskFrequencyForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('task_frequency.name')}}</label>
            <input class="form-control" type="text" value="" v-model="taskFrequencyForm.name" name="name" :placeholder="trans('task_frequency.name')">
            <show-error :form-name="taskFrequencyForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task_frequency.code')}}</label>
            <input class="form-control" type="text" value="" v-model="taskFrequencyForm.code" name="code" :placeholder="trans('task_frequency.code')">
            <show-error :form-name="taskFrequencyForm" prop-name="code"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task_frequency.description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="taskFrequencyForm.description" rows="2" name="description" :placeholder="trans('task_frequency.description')"></textarea>
            <show-error :form-name="taskFrequencyForm" prop-name="description"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/task-frequency" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    export default {
        data() {
            return {
                taskFrequencyForm: new Form({
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
                this.taskFrequencyForm.post('/api/task-frequency')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed')
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getContractor(){
                axios.get('/api/task-frequency/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.taskFrequencyForm.name = response.name;
                        this.taskFrequencyForm.code = response.code;
                        this.taskFrequencyForm.description = response.description;
                    })
                    .catch(error => {
                        this.$router.push('/task-frequency');
                    });
            },
            updateContractor(){
                this.taskFrequencyForm.patch('/api/task-frequency/'+this.id)
                    .then(response => {
                            toastr.success(response.message);
                            this.$router.push('/task-frequency');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>

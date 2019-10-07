<template>
    <form @submit.prevent="proceed" @keydown="taskComplexityForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('task-complexity.name')}}</label>
            <input class="form-control" type="text" value="" v-model="taskComplexityForm.name" name="name" :placeholder="trans('task-complexity.name')">
            <show-error :form-name="taskComplexityForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task-complexity.code')}}</label>
            <input class="form-control" type="text" value="" v-model="taskComplexityForm.code" name="code" :placeholder="trans('task-complexity.code')">
            <show-error :form-name="taskComplexityForm" prop-name="code"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task-complexity.description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="taskComplexityForm.description" rows="2" name="description" :placeholder="trans('task-complexity.description')"></textarea>
            <show-error :form-name="taskComplexityForm" prop-name="description"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/task-complexity" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    export default {
        data() {
            return {
                taskComplexityForm: new Form({
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
                this.taskComplexityForm.post('/api/task-complexity')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed')
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getContractor(){
                axios.get('/api/task-complexity/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.taskComplexityForm.name = response.name;
                        this.taskComplexityForm.code = response.code;
                        this.taskComplexityForm.description = response.description;
                    })
                    .catch(error => {
                        this.$router.push('/task-complexity');
                    });
            },
            updateContractor(){
                this.taskComplexityForm.patch('/api/task-complexity/'+this.id)
                    .then(response => {
                            toastr.success(response.message);
                            this.$router.push('/task-complexity');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>

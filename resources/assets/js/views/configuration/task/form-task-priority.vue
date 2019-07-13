<template>
    <form @submit.prevent="proceed" @keydown="taskPriorityForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('task.task_priority_name')}}</label>
            <input class="form-control" type="text" value="" v-model="taskPriorityForm.name" name="name" :placeholder="trans('task.task_priority_name')">
            <show-error :form-name="taskPriorityForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task.task_priority_description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="taskPriorityForm.description" rows="2" name="description" :placeholder="trans('task.task_priority_description')"></textarea>
            <show-error :form-name="taskPriorityForm" prop-name="description"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/configuration/task" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    export default {
        components: {},
        data() {
            return {
                taskPriorityForm: new Form({
                    name : '',
                    description : ''
                })
            };
        },
        props: ['id'],
        mounted() {
            if(this.id)
                this.getTaskPriority();
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateTaskPriority();
                else
                    this.storeTaskPriority();
            },
            storeTaskPriority(){
                this.taskPriorityForm.post('/api/task-priority')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getTaskPriority(){
                axios.get('/api/task-priority/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.taskPriorityForm.name = response.name;
                        this.taskPriorityForm.description = response.description;
                    })
                    .catch(error => {
                        this.$router.push('/configuration/task');
                    });
            },
            updateTaskPriority(){
                this.taskPriorityForm.patch('/api/task-priority/'+this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/configuration/task');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
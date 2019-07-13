<template>
    <form @submit.prevent="proceed" @keydown="taskCategoryForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('task.task_category_name')}}</label>
            <input class="form-control" type="text" value="" v-model="taskCategoryForm.name" name="name" :placeholder="trans('task.task_category_name')">
            <show-error :form-name="taskCategoryForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task.task_category_description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="taskCategoryForm.description" rows="2" name="description" :placeholder="trans('task.task_category_description')"></textarea>
            <show-error :form-name="taskCategoryForm" prop-name="description"></show-error>
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
                taskCategoryForm: new Form({
                    name : '',
                    description : ''
                })
            };
        },
        props: ['id'],
        mounted() {
            if(this.id)
                this.getTaskCategory();
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateTaskCategory();
                else
                    this.storeTaskCategory();
            },
            storeTaskCategory(){
                this.taskCategoryForm.post('/api/task-category')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getTaskCategory(){
                axios.get('/api/task-category/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.taskCategoryForm.name = response.name;
                        this.taskCategoryForm.description = response.description;
                    })
                    .catch(error => {
                        this.$router.push('/configuration/task');
                    });
            },
            updateTaskCategory(){
                this.taskCategoryForm.patch('/api/task-category/'+this.id)
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
<template>
    <form @submit.prevent="proceed" @keydown="taskFamilyForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('task_family.name')}}</label>
            <input class="form-control" type="text" value="" v-model="taskFamilyForm.name" name="name" :placeholder="trans('task_family.name')">
            <show-error :form-name="taskFamilyForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task_family.code')}}</label>
            <input class="form-control" type="text" value="" v-model="taskFamilyForm.code" name="code" :placeholder="trans('task_family.code')">
            <show-error :form-name="taskFamilyForm" prop-name="code"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task_family.description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="taskFamilyForm.description" rows="2" name="description" :placeholder="trans('task_family.description')"></textarea>
            <show-error :form-name="taskFamilyForm" prop-name="description"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/task-family" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    export default {
        data() {
            return {
                taskFamilyForm: new Form({
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
                this.taskFamilyForm.post('/api/task-family')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed')
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getContractor(){
                axios.get('/api/task-family/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.taskFamilyForm.name = response.name;
                        this.taskFamilyForm.code = response.code;
                        this.taskFamilyForm.description = response.description;
                    })
                    .catch(error => {
                        this.$router.push('/task-family');
                    });
            },
            updateContractor(){
                this.taskFamilyForm.patch('/api/task-family/'+this.id)
                    .then(response => {
                            toastr.success(response.message);
                            this.$router.push('/task-family');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>

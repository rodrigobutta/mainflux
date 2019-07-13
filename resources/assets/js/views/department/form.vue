<template>
    <form @submit.prevent="proceed" @keydown="departmentForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('department.name')}}</label>
            <input class="form-control" type="text" value="" v-model="departmentForm.name" name="name" :placeholder="trans('department.name')">
            <show-error :form-name="departmentForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('department.description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="departmentForm.description" rows="2" name="description" :placeholder="trans('department.description')"></textarea>
            <show-error :form-name="departmentForm" prop-name="description"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/department" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    export default {
        data() {
            return {
                departmentForm: new Form({
                    'name' : '',
                    'description' : ''
                })
            };
        },
        props: ['id'],
        mounted() {
            if(this.id)
                this.getDepartment();
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateDepartment();
                else
                    this.storeDepartment();
            },
            storeDepartment(){
                this.departmentForm.post('/api/department')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed')
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getDepartment(){
                axios.get('/api/department/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.departmentForm.name = response.name;
                        this.departmentForm.description = response.description;
                    })
                    .catch(error => {
                        this.$router.push('/department');
                    });
            },
            updateDepartment(){
                this.departmentForm.patch('/api/department/'+this.id)
                    .then(response => {
                            toastr.success(response.message);
                            this.$router.push('/department');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>

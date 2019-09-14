<template>
    <form @submit.prevent="proceed" @keydown="contractorForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('contractor.name')}}</label>
            <input class="form-control" type="text" value="" v-model="contractorForm.name" name="name" :placeholder="trans('contractor.name')">
            <show-error :form-name="contractorForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('contractor.description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="contractorForm.description" rows="2" name="description" :placeholder="trans('contractor.description')"></textarea>
            <show-error :form-name="contractorForm" prop-name="description"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/contractor" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    export default {
        data() {
            return {
                contractorForm: new Form({
                    'name' : '',
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
                this.contractorForm.post('/api/contractor')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed')
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getContractor(){
                axios.get('/api/contractor/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.contractorForm.name = response.name;
                        this.contractorForm.description = response.description;
                    })
                    .catch(error => {
                        this.$router.push('/contractor');
                    });
            },
            updateContractor(){
                this.contractorForm.patch('/api/contractor/'+this.id)
                    .then(response => {
                            toastr.success(response.message);
                            this.$router.push('/contractor');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>

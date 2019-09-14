<template>
    <form @submit.prevent="proceed" @keydown="clientForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('client.name')}}</label>
            <input class="form-control" type="text" value="" v-model="clientForm.name" name="name" :placeholder="trans('client.name')">
            <show-error :form-name="clientForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('client.description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="clientForm.description" rows="2" name="description" :placeholder="trans('client.description')"></textarea>
            <show-error :form-name="clientForm" prop-name="description"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/client" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    export default {
        data() {
            return {
                clientForm: new Form({
                    'name' : '',
                    'description' : ''
                })
            };
        },
        props: ['id'],
        mounted() {
            if(this.id)
                this.getClient();
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateClient();
                else
                    this.storeClient();
            },
            storeClient(){
                this.clientForm.post('/api/client')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed')
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getClient(){
                axios.get('/api/client/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.clientForm.name = response.name;
                        this.clientForm.description = response.description;
                    })
                    .catch(error => {
                        this.$router.push('/client');
                    });
            },
            updateClient(){
                this.clientForm.patch('/api/client/'+this.id)
                    .then(response => {
                            toastr.success(response.message);
                            this.$router.push('/client');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>

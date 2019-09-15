<template>
    <form @submit.prevent="proceed" @keydown="assetForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('asset.name')}}</label>
            <input class="form-control" type="text" value="" v-model="assetForm.name" name="name" :placeholder="trans('asset.name')">
            <show-error :form-name="assetForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('department.department')}}</label>
            <v-select label="name" v-model="selected_department" name="department_id" id="department_id" :options="departments" :placeholder="trans('department.select_department')" @select="onDepartmentSelect" @close="assetForm.errors.clear('department_id')" @remove="assetForm.department_id = ''"></v-select>
            <show-error :form-name="assetForm" prop-name="department_id"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('client.client')}}</label>
            <v-select label="name" v-model="selected_client" name="client_id" id="client_id" :options="clients" :placeholder="trans('client.select_client')" @select="onClientSelect" @close="assetForm.errors.clear('client_id')" @remove="assetForm.client_id = ''"></v-select>
            <show-error :form-name="assetForm" prop-name="client_id"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('asset.top_asset')}} <show-tip module="asset" tip="tip_top_asset" type="field"></show-tip> </label>
            <v-select label="name" v-model="selected_top_asset" name="top_asset_id" id="top_asset_id" :options="top_assets" :placeholder="trans('asset.select_top_asset')" @select="onTopAssetSelect" @close="assetForm.errors.clear('top_asset_id')" @remove="assetForm.top_asset_id = ''"></v-select>
            <show-error :form-name="assetForm" prop-name="top_asset_id"></show-error>
        </div>
        <div class="form-group">
            <switches class="m-l-20" v-model="assetForm.is_default" theme="bootstrap" color="success"></switches> {{trans('asset.default')}} <show-tip module="asset" tip="tip_default_asset" type="field"></show-tip>
        </div>
        <div class="form-group">
            <label for="">{{trans('asset.description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="assetForm.description" rows="2" name="description" :placeholder="trans('asset.description')"></textarea>
            <show-error :form-name="assetForm" prop-name="description"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/asset" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    import vSelect from 'vue-multiselect'
    import switches from 'vue-switches'

    export default {
        components: {vSelect,switches},
        data() {
            return {
                assetForm: new Form({
                    name : '',
                    department_id : '',
                    client_id : '',
                    top_asset_id: '',
                    description: '',
                    is_default:  false
                }),
                departments: [],
                clients: [],
                top_assets: [],
                selected_department: null,
                selected_client: null,
                selected_top_asset: null
            };
        },
        props: ['id'],
        mounted() {
            if(this.id) 
                this.getAsset();
            
            axios.get('/api/asset/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.departments = response.departments;
                    this.clients = response.clients;
                    if(!this.id)
                        this.top_assets = response.top_assets;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateAsset();
                else
                    this.storeAsset();
            },
            storeAsset(){
                this.assetForm.post('/api/asset')
                    .then(response => {
                        toastr.success(response.message);
                        this.top_assets.push(response.new_asset);
                        this.$emit('completed');
                        this.selected_department = null;
                        this.selected_client = null;
                        this.selected_top_asset = null;
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getAsset(){
                axios.get('/api/asset/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.assetForm.name = response.asset.name;
                        this.assetForm.department_id = response.asset.department_id;
                        this.assetForm.client_id = response.asset.client_id;
                        this.assetForm.top_asset_id = response.asset.top_asset_id;
                        this.assetForm.is_default = response.asset.is_default;
                        this.selected_department = response.selected_department;
                        this.selected_client = response.selected_client;
                        this.selected_top_asset = response.selected_top_asset;
                        this.top_assets = response.top_assets;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        this.$router.push('/asset');
                    });
            },
            updateAsset(){
                this.assetForm.patch('/api/asset/'+this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/asset');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            onTopAssetSelect(selectedOption){
                this.assetForm.top_asset_id = selectedOption.id;
            },
            onDepartmentSelect(selectedOption){
                this.assetForm.department_id = selectedOption.id;
            },
            onClientSelect(selectedOption){
                this.assetForm.client_id = selectedOption.id;
            }
        }
    }
</script>

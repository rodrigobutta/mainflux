<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('asset.asset')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('asset.asset')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-asset')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('asset.add_new_asset')}}</h4>
                        <asset-form @completed="getAssets"></asset-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8" v-if="hasPermission('list-asset')">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('asset.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterAssetForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group" v-show="departments">
                                        <label for="">{{trans('department.department')}}</label>
                                        <select v-model="filterAssetForm.department_id" class="custom-select col-12">
                                          <option value="">{{trans('general.select_one')}}</option>
                                          <option v-for="department in departments" v-bind:value="department.id">
                                            {{ department.name }}
                                          </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group" v-show="assets">
                                        <label for="">{{trans('asset.top_asset')}}</label>
                                        <select v-model="filterAssetForm.top_asset_id" class="custom-select col-12">
                                          <option value="">{{trans('general.select_one')}}</option>
                                          <option v-for="asset in top_assets" v-bind:value="asset.id">
                                            {{ asset.name }}
                                          </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterAssetForm.sortBy">
                                            <option value="name">{{trans('asset.name')}}</option>
                                            <option value="department_id">{{trans('department.department')}}</option>
                                            <option value="top_asset_id">{{trans('asset.top_asset')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterAssetForm.order">
                                            <option value="asc">{{trans('general.ascending')}}</option>
                                            <option value="desc">{{trans('general.descending')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-info btn-sm pull-right" v-if="!showFilterPanel" @click="showFilterPanel = !showFilterPanel"><i class="fas fa-filter"></i> {{trans('general.filter')}}</button>
                        <h4 class="card-title">{{trans('asset.asset_list')}}</h4>
                        <h6 class="card-subtitle" v-if="assets">{{trans('general.total_result_found',{'count' : assets.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="assets.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('asset.name')}}</th>
                                        <th>{{trans('department.department')}}</th>
                                        <th>{{trans('asset.top_asset')}}</th>
                                        <th>{{trans('asset.default')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="asset in assets.data">
                                        <td v-text="asset.name"></td>
                                        <td v-text="asset.department.name"></td>
                                        <td v-text="asset.top_asset_id ? asset.parent.name : ''"></td>
                                        <td>
                                            <span v-if="asset.is_default"><i class="fas fa-check"></i></span>
                                            <span v-else><i class="fas fa-times"></i></span>
                                        </td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-asset')" v-tooltip="trans('asset.edit_asset')" @click.prevent="editAsset(asset)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-asset')" :key="asset.id" v-confirm="{ok: confirmDelete(asset)}" v-tooltip="trans('asset.delete_asset')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!assets.total" module="asset" title="module_info_title" description="module_info_description" icon="sitemap">
                        </module-info>
                        <pagination-record :page-length.sync="filterAssetForm.page_length" :records="assets" @updateRecords="getAssets"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import assetForm from './form'

    export default {
        components : { assetForm },
        data() {
            return {
                assets: {},
                filterAssetForm: {
                    name: '',
                    department_id: '',
                    top_asset_id: '',
                    sortBy : 'name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                departments: {},
                top_assets: {},
                showFilterPanel: false
            };
        },
        mounted(){
            if(!helper.hasPermission('list-asset') && !helper.hasPermission('create-asset')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getAssets();
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getAssets(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterAssetForm);
                axios.get('/api/asset?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.assets = response.assets;
                        this.top_assets = response.top_assets;
                        this.departments = response.departments;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editAsset(asset){
                this.$router.push('/asset/'+asset.id+'/edit');
            },
            confirmDelete(asset){
                return dialog => this.deleteAsset(asset);
            },
            deleteAsset(asset){
                axios.delete('/api/asset/'+asset.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getAssets();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        watch: {
            filterAssetForm: {
                handler(val){
                    this.getAssets();
                },
                deep: true
            }
        }
    }
</script>

<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('location.location')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('location.location')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-location')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('location.add_new_location')}}</h4>
                        <location-form @completed="getLocations"></location-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8" v-if="hasPermission('list-location')">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('location.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterLocationForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group" v-show="locations">
                                        <label for="">{{trans('location.top_location')}}</label>
                                        <select v-model="filterLocationForm.top_location_id" class="custom-select col-12">
                                          <option value="">{{trans('general.select_one')}}</option>
                                          <option v-for="location in top_locations" v-bind:value="location.id">
                                            {{ location.name }}
                                          </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterLocationForm.sortBy">
                                            <option value="name">{{trans('location.name')}}</option>
                                            <option value="top_location_id">{{trans('location.top_location')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterLocationForm.order">
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
                        <h4 class="card-title">{{trans('location.location_list')}}</h4>
                        <h6 class="card-subtitle" v-if="locations">{{trans('general.total_result_found',{'count' : locations.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="locations.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('location.name')}}</th>
                                        <th>{{trans('location.top_location')}}</th>
                                        <th>{{trans('location.default')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="location in locations.data">
                                        <td v-text="location.name"></td>
                                        <td v-text="location.top_location_id ? location.parent.name : ''"></td>
                                        <td>
                                            <span v-if="location.is_default"><i class="fas fa-check"></i></span>
                                            <span v-else><i class="fas fa-times"></i></span>
                                        </td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-location')" v-tooltip="trans('location.edit_location')" @click.prevent="editLocation(location)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-location')" :key="location.id" v-confirm="{ok: confirmDelete(location)}" v-tooltip="trans('location.delete_location')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!locations.total" module="location" title="module_info_title" description="module_info_description" icon="code-fork">
                        </module-info>
                        <pagination-record :page-length.sync="filterLocationForm.page_length" :records="locations" @updateRecords="getLocations"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import locationForm from './form'

    export default {
        components : { locationForm },
        data() {
            return {
                locations: {},
                filterLocationForm: {
                    name: '',
                    top_location_id: '',
                    sortBy : 'name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                top_locations: {},
                showFilterPanel: false
            };
        },
        mounted(){
            if(!helper.hasPermission('list-location') && !helper.hasPermission('create-location')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getLocations();
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getLocations(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterLocationForm);
                axios.get('/api/location?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.locations = response.locations;
                        this.top_locations = response.top_locations;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editLocation(location){
                this.$router.push('/location/'+location.id+'/edit');
            },
            confirmDelete(location){
                return dialog => this.deleteLocation(location);
            },
            deleteLocation(location){
                axios.delete('/api/location/'+location.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getLocations();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        watch: {
            filterLocationForm: {
                handler(val){
                    this.getLocations();
                },
                deep: true
            }
        }
    }
</script>

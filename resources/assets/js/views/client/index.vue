<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('client.client')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('client.client')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-client')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('client.add_new_client')}}</h4>
                        <client-form @completed="getClients"></client-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8" v-if="hasPermission('list-client')">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('client.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterClientForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterClientForm.sortBy">
                                            <option value="name">{{trans('client.name')}}</option>
                                            <option value="description">{{trans('client.description')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterClientForm.order">
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
                        <h4 class="card-title">{{trans('client.client_list')}}</h4>
                        <h6 class="card-subtitle" v-if="clients">{{trans('general.total_result_found',{'count' : clients.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="clients.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('client.name')}}</th>
                                        <th>{{trans('client.description')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="client in clients.data" v-bind:key="client">
                                        <td v-text="client.name"></td>
                                        <td v-text="client.description"></td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-client')" v-tooltip="trans('client.edit_client')" @click.prevent="editClient(client)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-client')" :key="client.id" v-confirm="{ok: confirmDelete(client)}" v-tooltip="trans('client.delete_client')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!clients.total" module="client" title="module_info_title" description="module_info_description" icon="bank">
                        </module-info>
                        <pagination-record :page-length.sync="filterClientForm.page_length" :records="clients" @updateRecords="getClients"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import clientForm from './form'
   
    export default {
        components : {
            clientForm
        },
        data() {
            return {
                clients: {},
                filterClientForm: {
                    name: '',
                    sortBy : 'name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                showFilterPanel: false
            };
        },
        mounted(){
            if(!helper.hasPermission('list-client') && !helper.hasPermission('create-client')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getClients();


            var self = this;
            self.$nextTick(function(){

            });

            
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getClients(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterClientForm);
                axios.get('/api/client?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.clients = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editClient(client){
                this.$router.push('/client/'+client.id+'/edit');
            },
            confirmDelete(client){
                return dialog => this.deleteClient(client);
            },
            deleteClient(client){
                axios.delete('/api/client/'+client.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getClients();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        watch: {
            filterClientForm: {
                handler(val){
                    this.getClients();
                },
                deep: true
            }
        }
    }
</script>

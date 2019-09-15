<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('user.user')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('user.user')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('user.first_name')}}</label>
                                        <input class="form-control" name="first_name" v-model="filterUserForm.first_name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('user.last_name')}}</label>
                                        <input class="form-control" name="last_name" v-model="filterUserForm.last_name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('user.email')}}</label>
                                        <input class="form-control" name="email" v-model="filterUserForm.email">
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('role.role')}}</label>
                                        <select v-model="filterUserForm.role_id" class="custom-select col-12">
                                          <option value="">{{trans('general.select_one')}}</option>
                                          <option v-for="role in roles" v-bind:value="role.id" v-bind:key="role.id">
                                            {{ role.name }}
                                          </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('designation.designation')}}</label>
                                        <select v-model="filterUserForm.designation_id" class="custom-select col-12">
                                          <option value="">{{trans('general.select_one')}}</option>
                                          <option v-for="designation in designations" v-bind:value="designation.id" v-bind:key="designation.id">
                                            {{ designation.name }}
                                          </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('location.location')}}</label>
                                        <select v-model="filterUserForm.location_id" class="custom-select col-12">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option v-for="location in locations" v-bind:value="location.id" v-bind:key="location.id">
                                            {{ location.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('client.client')}}</label>
                                        <select v-model="filterUserForm.client_id" class="custom-select col-12">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option v-for="client in clients" v-bind:value="client.id" v-bind:key="client.id">
                                            {{ client.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('contractor.contractor')}}</label>
                                        <select v-model="filterUserForm.contractor_id" class="custom-select col-12">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option v-for="contractor in contractors" v-bind:value="contractor.id" v-bind:key="contractor.id">
                                            {{ contractor.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('user.status')}}</label>
                                        <select v-model="filterUserForm.status" class="custom-select col-12">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option value="activated">{{trans('user.status_activated')}}</option>
                                            <option value="pending_activation">{{trans('user.status_pending_activation')}}</option>
                                            <option value="pending_approval">{{trans('user.status_pending_approval')}}</option>
                                            <option value="banned">{{trans('user.status_banned')}}</option>
                                            <option value="disapproved">{{trans('user.status_disapproved')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-6">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterUserForm.created_at_start_date" :end-date.sync="filterUserForm.created_at_end_date" :label="trans('user.created_at')"></date-range-picker>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterUserForm.sort_by">
                                            <option value="first_name">{{trans('user.first_name')}}</option>
                                            <option value="last_name">{{trans('user.last_name')}}</option>
                                            <option value="email">{{trans('user.email')}}</option>
                                            <option value="status">{{trans('user.status')}}</option>
                                            <option value="created_at">{{trans('user.created_at')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterUserForm.order">
                                            <option value="asc">{{trans('general.ascending')}}</option>
                                            <option value="desc">{{trans('general.descending')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
                <transition name="fade" v-if="hasPermission('create-user')">
                    <div class="card" v-if="showCreatePanel">
                        <div class="card-body">
                            <button class="btn btn-info btn-sm pull-right" v-if="showCreatePanel" @click="showCreatePanel = !showCreatePanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('user.add_new_user')}}</h4>
                            <form @submit.prevent="submit" @keydown="userForm.errors.clear($event.target.name)">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('user.first_name')}}</label>
                                                    <input class="form-control" type="text" value="" v-model="userForm.first_name" name="first_name" :placeholder="trans('user.first_name')">
                                                    <show-error :form-name="userForm" prop-name="first_name"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('user.last_name')}}</label>
                                                    <input class="form-control" type="text" value="" v-model="userForm.last_name" name="last_name" :placeholder="trans('user.last_name')">
                                                    <show-error :form-name="userForm" prop-name="last_name"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('user.email')}}</label>
                                                    <input class="form-control" type="text" value="" v-model="userForm.email" name="email" :placeholder="trans('user.email')">
                                                    <show-error :form-name="userForm" prop-name="email"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('role.role')}}</label>
                                                    <v-select label="name" track-by="id" v-model="selected_roles" name="role_id" id="role_id" :options="roles" :placeholder="trans('role.select_role')" @select="onRoleSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onRoleRemove" :selected="selected_roles">
                                                    </v-select>
                                                    <show-error :form-name="userForm" prop-name="role_id"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('user.password')}}</label>
                                                    <input class="form-control" type="password" value="" v-model="userForm.password" name="password" :placeholder="trans('user.password')">
                                                    <show-error :form-name="userForm" prop-name="password"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('user.password_confirmation')}}</label>
                                                    <input class="form-control" type="password" value="" v-model="userForm.password_confirmation" name="password_confirmation" :placeholder="trans('user.password_confirmation')">
                                                    <show-error :form-name="userForm" prop-name="password_confirmation"></show-error>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('designation.designation')}}</label>
                                                    <v-select label="name" v-model="selected_designation" name="designation_id" id="designation_id" :options="designations" :placeholder="trans('designation.select_designation')" @select="onDesignationSelect" @close="userForm.errors.clear('designation_id')" @remove="userForm.designation_id = ''"></v-select>
                                                    <show-error :form-name="userForm" prop-name="designation_id"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('location.location')}}</label>
                                                    <v-select label="name" v-model="selected_location" name="location_id" id="location_id" :options="locations" :placeholder="trans('location.select_location')" @select="onLocationSelect" @close="userForm.errors.clear('location_id')" @remove="userForm.location_id = ''"></v-select>
                                                    <show-error :form-name="userForm" prop-name="location_id"></show-error>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('client.client')}}</label>
                                                    <v-select label="name" v-model="selected_client" name="client_id" id="client_id" :options="clients" :placeholder="trans('client.select_client')" @select="onClientSelect" @close="userForm.errors.clear('client_id')" @remove="userForm.client_id = ''"></v-select>
                                                    <show-error :form-name="userForm" prop-name="client_id"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('contractor.contractor')}}</label>
                                                    <v-select label="name" v-model="selected_contractor" name="contractor_id" id="contractor_id" :options="contractors" :placeholder="trans('contractor.select_contractor')" @select="onContractorSelect" @close="userForm.errors.clear('contractor_id')" @remove="userForm.contractor_id = ''"></v-select>
                                                    <show-error :form-name="userForm" prop-name="contractor_id"></show-error>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('user.address_line_1')}}</label>
                                                    <input class="form-control" type="text" value="" v-model="userForm.address_line_1" name="address_line_1" :placeholder="trans('user.address_line_1')">
                                                    <show-error :form-name="userForm" prop-name="address_line_1"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('user.address_line_2')}}</label>
                                                    <input class="form-control" type="text" value="" v-model="userForm.address_line_2" name="address_line_2" :placeholder="trans('user.address_line_2')">
                                                    <show-error :form-name="userForm" prop-name="address_line_2"></show-error>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('user.city')}}</label>
                                                    <input class="form-control" type="text" value="" v-model="userForm.city" name="city" :placeholder="trans('user.city')">
                                                    <show-error :form-name="userForm" prop-name="city"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('user.state')}}</label>
                                                    <input class="form-control" type="text" value="" v-model="userForm.state" name="state" :placeholder="trans('user.state')">
                                                    <show-error :form-name="userForm" prop-name="state"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('user.zipcode')}}</label>
                                                    <input class="form-control" type="text" value="" v-model="userForm.zipcode" name="zipcode" :placeholder="trans('user.zipcode')">
                                                    <show-error :form-name="userForm" prop-name="zipcode"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('user.country')}}</label>
                                                    <select class="form-control" name="country_id" v-model="userForm.country_id">
                                                        <option value="">{{trans('user.country')}}</option>
                                                        <option v-for="country in countries" :value="country.value" v-bind:key="country">{{country.text}}</option>
                                                    </select>
                                                    <show-error :form-name="userForm" prop-name="country_id"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <switches class="m-l-20" v-model="userForm.send_email" theme="bootstrap" color="success"></switches> {{trans('user.send_welcome_email')}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info waves-effect waves-light">{{trans('general.add')}}</button>
                            </form>
                        </div>
                    </div>
                </transition>
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-info btn-sm pull-right" v-if="users.total && !showCreatePanel && hasPermission('create-user')" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-user-plus"></i> {{trans('user.create_user')}}</button>
                        <button class="btn btn-info btn-sm pull-right m-r-5" v-if="!showFilterPanel" @click="showFilterPanel = !showFilterPanel"><i class="fas fa-filter"></i> {{trans('general.filter')}}</button>
                        <h4 class="card-title">{{trans('user.user_list')}}</h4>
                        <h6 class="card-subtitle" v-if="users">{{trans('general.total_result_found',{'count' : users.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="hasPermission('list-user') && users.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('user.name')}}</th>
                                        <th>{{trans('user.email')}}</th>
                                        <th>{{trans('role.role')}}</th>
                                        <th>{{trans('designation.designation')}}</th>
                                        <th>{{trans('location.location')}}</th>
                                        <th>{{trans('client.client')}} / {{trans('contractor.contractor')}}</th>
                                        <th>{{trans('user.status')}}</th>
                                        <!-- <th>{{trans('user.created_at')}}</th> -->
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users.data" v-bind:key="user.id">
                                        <td><img :src="getAvatar(user)" class="img-circle" width="55" /></td>
                                        <td v-text="user.profile.first_name+' '+user.profile.last_name"></td>
                                        <td v-text="user.email"></td>
                                        <td>
                                            <ul style="list-style: none; padding:0;">
                                                <li v-for="role in user.roles" v-bind:key="role.id">{{role.name}}</li>
                                            </ul>
                                        </td>
                                        <td v-text="user.profile.designation_id ? user.profile.designation.name : ''"></td>
                                        <td v-text="user.profile.location_id ? user.profile.location.name : ''"></td>
                                        <td v-text="(user.profile.client_id ? user.profile.client.name : '') + (user.profile.contractor_id ? user.profile.contractor.name : '')"></td>
                                        <td>
                                            <span v-for="status in getUserStatus(user)" :class="['label','label-'+status.color,'m-r-5']" v-bind:key="status.label">{{status.label}}</span>
                                        </td>
                                        <!-- <td>{{user.created_at | moment}}</td> -->
                                        <td class="table-option">
                                            <div class="btn-group">

                                                <router-link :to="`/user/${user.id}`" class="btn btn-info btn-sm" v-tooltip="trans('user.view_user')"><i class="fas fa-arrow-circle-right"></i></router-link>
                                                <button class="btn btn-danger btn-sm" :key="user.id" v-if="hasPermission('delete-user')" v-confirm="{ok: confirmDelete(user)}" v-tooltip="trans('user.delete_user')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!users.total" module="user" title="module_info_title" description="module_info_description" icon="users">
                            <div slot="btn">
                                <button class="btn btn-info btn-md" v-if="hasPermission('create-user') && !showCreatePanel" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> {{trans('general.add_new')}}</button>
                            </div>
                        </module-info>
                        <pagination-record :page-length.sync="filterUserForm.page_length" :records="users" @updateRecords="getUsers"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import switches from 'vue-switches'
    import vSelect from 'vue-multiselect'
    import dateRangePicker from '../../components/date-range-picker'

    export default {
        components: {switches,vSelect,dateRangePicker},
        data() {
            return {
                showCreatePanel: false,
                users: {
                    total: 0,
                    data: []
                },
                filterUserForm: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    role_id: '',
                    designation_id: '',
                    location_id: '',
                    client_id: '',
                    contractor_id: '',
                    status: '',
                    created_at_start_date: '',
                    created_at_end_date: '',
                    sort_by : 'first_name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                userForm: new Form({
                    first_name: '',
                    last_name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role_id: [],
                    designation_id: '',
                    location_id: '',
                    client_id: '',
                    contractor_id: '',
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    zipcode: '',
                    country_id: '',
                    send_email: ''
                }),
                countries: [],
                roles: [],
                selected_roles: '',
                designations: [],
                locations: [],
                clients: [],
                contractors: [],
                selected_designation: null,
                selected_location: null,
                selected_client: null,
                selected_contractor: null,
                showFilterPanel: false
            };
        },
        mounted(){
            if(!helper.hasPermission('list-user') && !helper.hasPermission('create-user')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            this.fetchPreRequisites();
            this.getUsers();
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            fetchPreRequisites(){
                axios.get('/api/user/pre-requisite')
                    .then(response => response.data)
                    .then(response => {
                        this.countries = response.countries;
                        this.roles = response.roles;
                        this.designations = response.designations;
                        this.locations = response.locations;
                        this.clients = response.clients;
                        this.contractors = response.contractorons;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error)
                    });
            },
            onRoleSelect(selectedOption){
                this.userForm.errors.clear('role_id');
                this.userForm.role_id.push(selectedOption.id);
            },
            onRoleRemove(removedOption){
                this.userForm.role_id.splice(this.userForm.role_id.indexOf(removedOption.id), 1);
            },
            submit(){
                this.userForm.post('/api/user')
                    .then(response => {
                        toastr.success(response.message);
                        this.selected_roles = null;
                        this.userForm.role_id = [];
                        this.selected_designation = null;
                        this.selected_location = null;
                        this.selected_client = null;
                        this.selected_contractor = null;
                        this.getUsers();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getUsers(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterUserForm);
                axios.get('/api/user?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.users = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    })
            },
            confirmDelete(user){
                return dialog => this.deleteUser(user);
            },
            deleteUser(user){
                axios.delete('/api/user/'+user.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getUsers();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getUserStatus(user){
                return helper.getUserStatus(user);
            },
            getAvatar(user){
                return helper.getAvatar(user);
            },
            onDesignationSelect(selectedOption){
                this.userForm.designation_id = selectedOption.id;
            },
            onLocationSelect(selectedOption){
                this.userForm.location_id = selectedOption.id;
            },
            onClienmtSelect(selectedOption){
                this.userForm.client_id = selectedOption.id;
            },
            onContractorSelect(selectedOption){
                this.userForm.contractor_id = selectedOption.id;
            }
        },
        watch: {
            filterUserForm: {
                handler(val){
                    this.getUsers();
                },
                deep: true
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          }
        }
    }
</script>

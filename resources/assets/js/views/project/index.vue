<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('project.project')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('project.project')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-project')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('project.add_new_project')}}</h4>
                        <project-form @completed="getProjects"></project-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8" v-if="hasPermission('list-project')">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('project.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterProjectForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group" v-show="departments">
                                        <label for="">{{trans('department.department')}}</label>
                                        <select v-model="filterProjectForm.department_id" class="custom-select col-12">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option v-for="department in departments" v-bind:value="department.id" v-bind:key="department.id">
                                            {{ department.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group" v-show="clients">
                                        <label for="">{{trans('client.client')}}</label>
                                        <select v-model="filterProjectForm.client_id" class="custom-select col-12">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option v-for="client in clients" v-bind:value="client.id" v-bind:key="client.id">
                                            {{ client.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group" v-show="contractors">
                                        <label for="">{{trans('contractor.contractor')}}</label>
                                        <select v-model="filterProjectForm.contractor_id" class="custom-select col-12">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option v-for="contractor in contractors" v-bind:value="contractor.id" v-bind:key="contractor.id">
                                            {{ contractor.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group" v-show="projects">
                                        <label for="">{{trans('project.top_project')}}</label>
                                        <select v-model="filterProjectForm.top_project_id" class="custom-select col-12">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option v-for="project in top_projects" v-bind:value="project.id" v-bind:key="project.id">
                                            {{ project.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterProjectForm.sortBy">
                                            <option value="name">{{trans('project.name')}}</option>
                                            <option value="department_id">{{trans('department.department')}}</option>
                                            <option value="client_id">{{trans('client.client')}}</option>
                                            <option value="contractor_id">{{trans('contractor.contractor')}}</option>
                                            <option value="top_project_id">{{trans('project.top_project')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterProjectForm.order">
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
                        <h4 class="card-title">{{trans('project.project_list')}}</h4>
                        <h6 class="card-subtitle" v-if="projects">{{trans('general.total_result_found',{'count' : projects.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="projects.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('project.name')}}</th>
                                        <th>{{trans('department.department')}}</th>
                                        <th>{{trans('client.client')}}</th>
                                        <th>{{trans('contractor.contractor')}}</th>
                                        <th>{{trans('project.top_project')}}</th>
                                        <th>{{trans('project.default')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="project in projects.data" v-bind:key="project.id">
                                        <td v-text="project.name"></td>
                                        <td v-text="project.department.name"></td>
                                        <td v-text="project.client.name"></td>
                                        <td v-text="project.contractor.name"></td>
                                        <td v-text="project.top_project_id ? project.parent.name : ''"></td>
                                        <td>
                                            <span v-if="project.is_default"><i class="fas fa-check"></i></span>
                                            <span v-else><i class="fas fa-times"></i></span>
                                        </td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-project')" v-tooltip="trans('project.edit_project')" @click.prevent="editProject(project)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-project')" :key="project.id" v-confirm="{ok: confirmDelete(project)}" v-tooltip="trans('project.delete_project')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!projects.total" module="project" title="module_info_title" description="module_info_description" icon="sitemap">
                        </module-info>
                        <pagination-record :page-length.sync="filterProjectForm.page_length" :records="projects" @updateRecords="getProjects"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import projectForm from './form'

    export default {
        components : { projectForm },
        data() {
            return {
                projects: {},
                filterProjectForm: {
                    name: '',
                    department_id: '',
                    client_id: '',
                    contractor_id: '',
                    top_project_id: '',
                    sortBy : 'name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                departments: {},
                clients: {},
                contractors: {},
                top_projects: {},
                showFilterPanel: false
            };
        },
        mounted(){
            if(!helper.hasPermission('list-project') && !helper.hasPermission('create-project')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getProjects();
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getProjects(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterProjectForm);
                axios.get('/api/project?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.projects = response.projects;
                        this.top_projects = response.top_projects;
                        this.departments = response.departments;
                        this.clients = response.clients;
                        this.contractors = response.contractors;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editProject(project){
                this.$router.push('/project/'+project.id+'/edit');
            },
            confirmDelete(project){
                return dialog => this.deleteProject(project);
            },
            deleteProject(project){
                axios.delete('/api/project/'+project.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getProjects();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        watch: {
            filterProjectForm: {
                handler(val){
                    this.getProjects();
                },
                deep: true
            }
        }
    }
</script>

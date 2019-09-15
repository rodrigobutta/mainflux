<template>
    <form @submit.prevent="proceed" @keydown="projectForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('project.name')}}</label>
            <input class="form-control" type="text" value="" v-model="projectForm.name" name="name" :placeholder="trans('project.name')">
            <show-error :form-name="projectForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('department.department')}}</label>
            <v-select label="name" v-model="selected_department" name="department_id" id="department_id" :options="departments" :placeholder="trans('department.select_department')" @select="onDepartmentSelect" @close="projectForm.errors.clear('department_id')" @remove="projectForm.department_id = ''"></v-select>
            <show-error :form-name="projectForm" prop-name="department_id"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('client.client')}}</label>
            <v-select label="name" v-model="selected_client" name="client_id" id="client_id" :options="clients" :placeholder="trans('client.select_client')" @select="onClientSelect" @close="projectForm.errors.clear('client_id')" @remove="projectForm.client_id = ''"></v-select>
            <show-error :form-name="projectForm" prop-name="client_id"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('contractor.contractor')}}</label>
            <v-select label="name" v-model="selected_contractor" name="contractor_id" id="contractor_id" :options="contractors" :placeholder="trans('contractor.select_contractor')" @select="onContractorSelect" @close="projectForm.errors.clear('contractor_id')" @remove="projectForm.contractor_id = ''"></v-select>
            <show-error :form-name="projectForm" prop-name="contractor_id"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('project.top_project')}} <show-tip module="project" tip="tip_top_project" type="field"></show-tip> </label>
            <v-select label="name" v-model="selected_top_project" name="top_project_id" id="top_project_id" :options="top_projects" :placeholder="trans('project.select_top_project')" @select="onTopProjectSelect" @close="projectForm.errors.clear('top_project_id')" @remove="projectForm.top_project_id = ''"></v-select>
            <show-error :form-name="projectForm" prop-name="top_project_id"></show-error>
        </div>
        <div class="form-group">
            <switches class="m-l-20" v-model="projectForm.is_default" theme="bootstrap" color="success"></switches> {{trans('project.default')}} <show-tip module="project" tip="tip_default_project" type="field"></show-tip>
        </div>
        <div class="form-group">
            <label for="">{{trans('project.description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="projectForm.description" rows="2" name="description" :placeholder="trans('project.description')"></textarea>
            <show-error :form-name="projectForm" prop-name="description"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/project" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    import vSelect from 'vue-multiselect'
    import switches from 'vue-switches'

    export default {
        components: {vSelect,switches},
        data() {
            return {
                projectForm: new Form({
                    name : '',
                    department_id : '',
                    client_id : '',
                    contractor_id : '',
                    top_project_id: '',
                    description: '',
                    is_default:  false
                }),
                departments: [],
                clients: [],
                contractors: [],
                top_projects: [],
                selected_department: null,
                selected_client: null,
                selected_contractor: null,
                selected_top_project: null
            };
        },
        props: ['id'],
        mounted() {
            if(this.id) 
                this.getProject();
            
            axios.get('/api/project/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.departments = response.departments;
                    this.clients = response.clients;
                    this.contractors = response.contractors;
                    if(!this.id)
                        this.top_projects = response.top_projects;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateProject();
                else
                    this.storeProject();
            },
            storeProject(){
                this.projectForm.post('/api/project')
                    .then(response => {
                        toastr.success(response.message);
                        this.top_projects.push(response.new_project);
                        this.$emit('completed');
                        this.selected_department = null;
                        this.selected_client = null;
                        this.selected_contractor = null;
                        this.selected_top_project = null;
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getProject(){
                axios.get('/api/project/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.projectForm.name = response.project.name;
                        this.projectForm.department_id = response.project.department_id;
                        this.projectForm.client_id = response.project.client_id;
                        this.projectForm.contractor_id = response.project.contractor_id;
                        this.projectForm.top_project_id = response.project.top_project_id;
                        this.projectForm.is_default = response.project.is_default;
                        this.selected_department = response.selected_department;
                        this.selected_client = response.selected_client;
                        this.selected_contractor = response.selected_contractor;
                        this.selected_top_project = response.selected_top_project;
                        this.top_projects = response.top_projects;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        this.$router.push('/project');
                    });
            },
            updateProject(){
                this.projectForm.patch('/api/project/'+this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/project');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            onTopProjectSelect(selectedOption){
                this.projectForm.top_project_id = selectedOption.id;
            },
            onDepartmentSelect(selectedOption){
                this.projectForm.department_id = selectedOption.id;
            },
            onClientSelect(selectedOption){
                this.projectForm.client_id = selectedOption.id;
            },
            onContractorSelect(selectedOption){
                this.projectForm.contractor_id = selectedOption.id;
            }
        }
    }
</script>

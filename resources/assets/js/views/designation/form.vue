<template>
    <form @submit.prevent="proceed" @keydown="designationForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('designation.name')}}</label>
            <input class="form-control" type="text" value="" v-model="designationForm.name" name="name" :placeholder="trans('designation.name')">
            <show-error :form-name="designationForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('department.department')}}</label>
            <v-select label="name" v-model="selected_department" name="department_id" id="department_id" :options="departments" :placeholder="trans('department.select_department')" @select="onDepartmentSelect" @close="designationForm.errors.clear('department_id')" @remove="designationForm.department_id = ''"></v-select>
            <show-error :form-name="designationForm" prop-name="department_id"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('designation.top_designation')}} <show-tip module="designation" tip="tip_top_designation" type="field"></show-tip> </label>
            <v-select label="name" v-model="selected_top_designation" name="top_designation_id" id="top_designation_id" :options="top_designations" :placeholder="trans('designation.select_top_designation')" @select="onTopDesignationSelect" @close="designationForm.errors.clear('top_designation_id')" @remove="designationForm.top_designation_id = ''"></v-select>
            <show-error :form-name="designationForm" prop-name="top_designation_id"></show-error>
        </div>
        <div class="form-group">
            <switches class="m-l-20" v-model="designationForm.is_default" theme="bootstrap" color="success"></switches> {{trans('designation.default')}} <show-tip module="designation" tip="tip_default_designation" type="field"></show-tip>
        </div>
        <div class="form-group">
            <label for="">{{trans('designation.description')}}</label>
            <textarea class="form-control" type="text" value="" v-model="designationForm.description" rows="2" name="description" :placeholder="trans('designation.description')"></textarea>
            <show-error :form-name="designationForm" prop-name="description"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/designation" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    import vSelect from 'vue-multiselect'
    import switches from 'vue-switches'

    export default {
        components: {vSelect,switches},
        data() {
            return {
                designationForm: new Form({
                    name : '',
                    department_id : '',
                    top_designation_id: '',
                    description: '',
                    is_default:  false
                }),
                departments: [],
                top_designations: [],
                selected_department: null,
                selected_top_designation: null
            };
        },
        props: ['id'],
        mounted() {
            if(this.id) 
                this.getDesignation();
            
            axios.get('/api/designation/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.departments = response.departments;
                    if(!this.id)
                        this.top_designations = response.top_designations;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateDesignation();
                else
                    this.storeDesignation();
            },
            storeDesignation(){
                this.designationForm.post('/api/designation')
                    .then(response => {
                        toastr.success(response.message);
                        this.top_designations.push(response.new_designation);
                        this.$emit('completed');
                        this.selected_department = null;
                        this.selected_top_designation = null;
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getDesignation(){
                axios.get('/api/designation/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.designationForm.name = response.designation.name;
                        this.designationForm.department_id = response.designation.department_id;
                        this.designationForm.top_designation_id = response.designation.top_designation_id;
                        this.designationForm.is_default = response.designation.is_default;
                        this.selected_department = response.selected_department;
                        this.selected_top_designation = response.selected_top_designation;
                        this.top_designations = response.top_designations;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        this.$router.push('/designation');
                    });
            },
            updateDesignation(){
                this.designationForm.patch('/api/designation/'+this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/designation');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            onTopDesignationSelect(selectedOption){
                this.designationForm.top_designation_id = selectedOption.id;
            },
            onDepartmentSelect(selectedOption){
                this.designationForm.department_id = selectedOption.id;
            }
        }
    }
</script>

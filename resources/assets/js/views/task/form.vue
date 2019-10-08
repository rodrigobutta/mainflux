<template>
    <form @submit.prevent="proceed" @keydown="taskForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('task.name')}}</label>
            <input class="form-control" type="text" value="" v-model="taskForm.name" name="name" :placeholder="trans('task.name')">
            <show-error :form-name="taskForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('project.project')}}</label>
            <v-select label="name" v-model="selected_project" name="project_id" id="project_id" :options="projects" :placeholder="trans('project.select_project')" @select="onProjectSelect" @close="taskForm.errors.clear('project_id')" @remove="taskForm.project_id = ''"></v-select>
            <show-error :form-name="taskForm" prop-name="project_id"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task_relevance.task_relevance')}}</label>
            <v-select label="name" v-model="selected_task_relevance" name="task_relevance_id" id="task_relevance_id" :options="task_relevances" :placeholder="trans('task_relevance.select_task_relevance')" @select="onTaskRelevanceSelect" @close="taskForm.errors.clear('task_relevance_id')" @remove="taskForm.task_relevance_id = ''"></v-select>
            <show-error :form-name="taskForm" prop-name="task_relevance_id"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task_frequency.task_frequency')}}</label>
            <v-select label="name" v-model="selected_task_frequency" name="task_frequency_id" id="task_frequency_id" :options="task_frequencys" :placeholder="trans('task_frequency.select_task_frequency')" @select="onTaskFrequencySelect" @close="taskForm.errors.clear('task_frequency_id')" @remove="taskForm.task_frequency_id = ''"></v-select>
            <show-error :form-name="taskForm" prop-name="task_frequency_id"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task_complexity.task_complexity')}}</label>
            <v-select label="name" v-model="selected_task_complexity" name="task_complexity_id" id="task_complexity_id" :options="task_complexitys" :placeholder="trans('task_complexity.select_task_complexity')" @select="onTaskComplexitySelect" @close="taskForm.errors.clear('task_complexity_id')" @remove="taskForm.task_complexity_id = ''"></v-select>
            <show-error :form-name="taskForm" prop-name="task_complexity_id"></show-error>
        </div>
        <div class="form-group">
            <label for="">{{trans('task_family.task_family')}}</label>
            <v-select label="name" v-model="selected_task_family" name="task_family_id" id="task_family_id" :options="task_familys" :placeholder="trans('task_family.select_task_family')" @select="onTaskFamilySelect" @close="taskForm.errors.clear('task_family_id')" @remove="taskForm.task_family_id = ''"></v-select>
            <show-error :form-name="taskForm" prop-name="task_family_id"></show-error>
        </div>
       
        <button type="submit" class="btn btn-info waves-effect waves-light">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/task" class="btn btn-danger waves-effect waves-light" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    import vSelect from 'vue-multiselect'
    import switches from 'vue-switches'

    export default {
        components: {vSelect,switches},
        data() {
            return {
                taskForm: new Form({
                    name : '',
                    project_id : '',
                    task_relevance_id : '',
                    task_frequency_id : '',
                    task_complexity_id : '',
                    task_family_id : ''
                }),
                projects: [],
                task_relevances: [],                
                task_frequencys: [],                
                task_complexitys: [],                
                task_familys: [],                
                selected_project: null,
                selected_task_relevance: null,
                selected_task_frequency: null,
                selected_task_complexity: null,
                selected_task_family: null
            };
        },
        props: ['id'],
        mounted() {
            if(this.id) 
                this.getTask();
            
            axios.get('/api/task/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.projects = response.projects;
                    this.task_relevances = response.task_relevances;                    
                    this.task_frequencys = response.task_frequencys;                    
                    this.task_complexitys = response.task_complexitys;                    
                    this.task_familys = response.task_familys;                    
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateTask();
                else
                    this.storeTask();
            },
            storeTask(){
                this.taskForm.post('/api/task')
                    .then(response => {
                        toastr.success(response.message);
                        this.top_tasks.push(response.new_task);
                        this.$emit('completed');
                        this.selected_project = null;
                        this.selected_task_relevance = null;                      
                        this.selected_task_frequency = null;                      
                        this.selected_task_complexity = null;                      
                        this.selected_task_family = null;                                              
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getTask(){
                axios.get('/api/task/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.taskForm.name = response.task.name;
                        this.taskForm.project_id = response.task.project_id;
                        this.taskForm.task_relevance_id = response.task.task_relevance_id;
                        this.taskForm.task_frequency_id = response.task.task_frequency_id;
                        this.taskForm.task_complexity_id = response.task.task_complexity_id;
                        this.taskForm.task_family_id = response.task.task_family_id;
                        this.selected_project = response.selected_project;
                        this.selected_task_relevance = response.selected_task_relevance;                      
                        this.selected_task_frequency = response.selected_task_frequency;                      
                        this.selected_task_complexity = response.selected_task_complexity;                      
                        this.selected_task_family = response.selected_task_family;                      
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        this.$router.push('/task');
                    });
            },
            updateTask(){
                this.taskForm.patch('/api/task/'+this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/task');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            onTopTaskSelect(selectedOption){
                this.taskForm.top_task_id = selectedOption.id;
            },
            onProjectSelect(selectedOption){
                this.taskForm.project_id = selectedOption.id;
            },
            onTaskRelevanceSelect(selectedOption){
                this.taskForm.task_relevance_id = selectedOption.id;
            },
            onTaskFrequencySelect(selectedOption){
                this.taskForm.task_frequency_id = selectedOption.id;
            },
            onTaskComplexitySelect(selectedOption){
                this.taskForm.task_complexity_id = selectedOption.id;
            },
            onTaskFamilySelect(selectedOption){
                this.taskForm.task_family_id = selectedOption.id;
            }
        }
    }
</script>

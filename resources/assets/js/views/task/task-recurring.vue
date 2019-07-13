<template>
    <div class="card" v-if="showPanel">
        <div class="card-body">
            <h4 class="card-title">{{trans('task.recurring_task')}}</h4>
            <div class="row">
                <div class="col-12 col-lg-4 col-md-4 col-sm-4">
                    <form @submit.prevent="update" @keydown="recurrenceForm.errors.clear($event.target.name)">
                        <div class="form-group">
                            <switches class="m-l-20" v-model="recurrenceForm.is_recurring" theme="bootstrap" color="success"></switches> {{trans('task.recurring')}}
                        </div>
                        <div v-if="recurrenceForm.is_recurring">
                            <div class="form-group">
                                <label>{{trans('task.next_recurring_date')}}</label>
                                <span v-if="next_recurring_date">{{next_recurring_date | moment}}</span>
                                <span v-else class="has-error"><strong>-</strong></span>
                            </div>
                            <div class="form-group">
                                <label for="">{{trans('task.recurrence_start_date')}}</label>
                                <datepicker v-model="recurrenceForm.recurrence_start_date" :bootstrapStyling="true" @selected="recurrenceForm.errors.clear('recurrence_start_date')" :placeholder="trans('task.recurrence_start_date')"></datepicker>
                                <show-error :form-name="recurrenceForm" prop-name="recurrence_start_date"></show-error>
                            </div>
                            <div class="form-group">
                                <label for="">{{trans('task.recurrence_end_date')}}</label>
                                <datepicker v-model="recurrenceForm.recurrence_end_date" :bootstrapStyling="true" @selected="recurrenceForm.errors.clear('recurrence_end_date')" :placeholder="trans('task.recurrence_end_date')"></datepicker>
                                <show-error :form-name="recurrenceForm" prop-name="recurrence_end_date"></show-error>
                            </div>
                            <div class="form-group">
                                <label for="">{{trans('task.recurring_frequency')}}</label>
                                <v-select label="name" v-model="selected_recurring_frequency" name="recurring_frequency" id="recurring_frequency" :options="recurring_frequencies" :placeholder="trans('task.select_frequency')" @select="onRecurringFrequencySelect" @close="recurrenceForm.errors.clear('recurring_frequency')" @remove="recurrenceForm.recurring_frequency = ''"></v-select>
                                <show-error :form-name="recurrenceForm" prop-name="recurring_frequency"></show-error>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info waves-effect waves-light">{{trans('general.save')}}</button>
                        <button type="button" class="btn btn-danger" v-if="showPanel" @click="toggle">{{trans('general.cancel')}}</button>
                    </form>
                </div>
                <div class="col-12 col-lg-8 col-md-8 col-sm-8">
                    <h4 class="card-title">{{trans('task.recurring_task_list')}}</h4>
                    <h6 class="card-subtitle" v-if="recurring_tasks">{{trans('general.total_result_found',{'count' : recurring_tasks.total})}}</h6>
                    <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered" v-if="recurring_tasks.total">
                            <thead>
                                <tr>
                                    <th>{{trans('task.title')}}</th>
                                    <th>{{trans('task.start_date')}}</th>
                                    <th>{{trans('task.due_date')}}</th>
                                    <th>{{trans('task.status')}}</th>
                                    <th class="table-option-sm">{{trans('general.action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="recurring_task in recurring_tasks.data">
                                    <td v-text="recurring_task.title"></td>
                                    <td>{{recurring_task.start_date | moment}}</td>
                                    <td>{{recurring_task.due_date | moment}}</td>
                                    <td>
                                        <span v-for="status in getTaskStatus(recurring_task)" :class="['label','label-'+status.color]" style="margin-right:5px;">{{status.label}}</span>
                                    </td>
                                    <td class="table-option-sm">
                                        <router-link :to="`/task/${recurring_task.uuid}`" class="btn btn-info btn-sm"><i class="fas fa-arrow-circle-right"></i></router-link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <pagination-record :page-length.sync="filterRecurringTaskForm.page_length" :records="recurring_tasks" @updateRecords="getRecurringTasks" @change.native="getRecurringTasks"></pagination-record>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import switches from 'vue-switches'
    import vSelect from 'vue-multiselect'
    import datepicker from 'vuejs-datepicker'

    export default {
        components: {switches,vSelect,datepicker},
        props: {
            uuid:{
                required: true,
            },
            showPanel:{
                required: true,
            }
        },
        data(){
            return {
                recurrenceForm: new Form({
                    is_recurring: false,
                    recurrence_start_date: '',
                    recurring_frequency: '',
                    recurrence_end_date: ''
                },false),
                recurring_frequencies: [],
                selected_recurring_frequency: null,
                recurring_tasks: {},
                filterRecurringTaskForm: {
                    page_length: helper.getConfig('page_length')
                }
            }
        },
        watch: {
            showPanel(val){
                if(val)
                    this.getRecurringTasks();
            },
            uuid(val){
                this.getRecurringTasks();
            }
        },
        mounted(){
        },
        methods: {
            update(){
                this.recurrenceForm.recurrence_start_date = moment(this.recurrenceForm.recurrence_start_date).format('YYYY-MM-DD');
                this.recurrenceForm.recurrence_end_date = moment(this.recurrenceForm.recurrence_end_date).format('YYYY-MM-DD');
                this.recurrenceForm.post('/api/task/'+this.uuid+'/recurrence')
                    .then(response => {
                        this.getRecurringTasks();
                        toastr.success(response.message);
                        this.$emit('recurringUpdated');
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });

            },
            toggle(){
                this.$emit('toggle');
            },
            getRecurringTasks(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterEmailLogForm);
                axios.get('/api/task/'+this.uuid+'/recurring?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.recurrenceForm.is_recurring = response.task.is_recurring;
                        this.recurrenceForm.recurrence_start_date = response.task.recurrence_start_date;
                        this.recurrenceForm.recurrence_end_date = response.task.recurrence_end_date;
                        this.recurrenceForm.recurring_frequency = response.task.recurring_frequency;
                        this.recurring_frequencies = response.recurring_frequencies;
                        this.selected_recurring_frequency = response.selected_recurring_frequency;
                        this.recurring_tasks = response.recurring_tasks;
                        this.next_recurring_date = response.next_recurring_date;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getTaskStatus(task){
                return helper.getTaskStatus(task);
            },
            onRecurringFrequencySelect(selectedOption){
                this.recurrenceForm.recurring_frequency = selectedOption.id;
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          },
          momentDateTime(datetime){
            return helper.formatDateTime(datetime);
          }
        },
        computed: {

        }
    }
</script>

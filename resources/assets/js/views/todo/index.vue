<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('todo.todo')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('todo.todo')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('todo.add_new_todo')}}</h4>
                        <todo-form @completed="getTodos"></todo-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">{{trans('todo.keyword')}}</label>
                                        <input class="form-control" name="keyword" v-model="filterTodoForm.keyword">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <switches v-model="filterTodoForm.status" theme="bootstrap" color="success"></switches> {{trans('todo.show_completed')}}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterTodoForm.start_date" :end-date.sync="filterTodoForm.end_date" :label="trans('general.date_between')"></date-range-picker>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterTodoForm.sort_by">
                                            <option value="title">{{trans('todo.title')}}</option>
                                            <option value="created_at">{{trans('todo.date')}}</option>
                                            <option value="status">{{trans('todo.status')}}</option>
                                            <option value="description">{{trans('todo.description')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterTodoForm.order">
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
                        <button class="btn btn-info btn-sm pull-right" v-if="todos.total && !showFilterPanel" @click="showFilterPanel = !showFilterPanel"><i class="fas fa-filter"></i> {{trans('general.filter')}}</button>
                        <h4 class="card-title">{{trans('todo.todo_list')}}</h4>
                        <h6 class="card-subtitle" v-if="todos">{{trans('general.total_result_found',{'count' : todos.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="todos.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('todo.title')}}</th>
                                        <th>{{trans('todo.date')}}</th>
                                        <th>{{trans('todo.status')}}</th>
                                        <th>{{trans('todo.description')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="todo in todos.data">
                                        <td v-text="todo.title"></td>
                                        <td>{{todo.date | moment}}</td>
                                        <td v-html="getStatus(todo)"></td>
                                        <td v-text="todo.description"></td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-secondary btn-sm" v-tooltip="todo.status ? trans('todo.mark_as_incomplete') : trans('todo.mark_as_complete')" @click.prevent="toggleStatus(todo)">
                                                    <i :class="['fa', (todo.status ?  'fa-times' : 'fa-check')]"></i>
                                                </button>
                                                <button class="btn btn-info btn-sm" v-tooltip="trans('todo.edit_todo')" @click.prevent="editTodo(todo)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" :key="todo.id" v-confirm="{ok: confirmDelete(todo)}" v-tooltip="trans('todo.delete_todo')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!todos.total" module="todo" title="module_info_title" description="module_info_description" icon="check-circle">
                            <div slot="btn">
                                <button class="btn btn-info btn-md" v-if="!showCreatePanel" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> {{trans('general.add_new')}}</button>
                            </div>
                        </module-info>
                        <pagination-record :page-length.sync="filterTodoForm.page_length" :records="todos" @updateRecords="getTodos"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import todoForm from './form'
    import switches from 'vue-switches'
    import datepicker from 'vuejs-datepicker'
    import dateRangePicker from '../../components/date-range-picker'

    export default {
        components : { todoForm,switches,datepicker,dateRangePicker },
        data() {
            return {
                todos: {
                    total: 0,
                    data: []
                },
                filterTodoForm: {
                    keyword: '',
                    status: false,
                    start_date: '',
                    end_date: '',
                    sort_by : 'created_at',
                    order: 'desc',
                    page_length: helper.getConfig('page_length')
                },
                showFilterPanel: false
            };
        },
        mounted(){
            if(!helper.hasPermission('access-todo')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('todo')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            this.getTodos();
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getTodos(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterTodoForm);
                axios.get('/api/todo?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.todos = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editTodo(todo){
                this.$router.push('/todo/'+todo.id+'/edit');
            },
            confirmDelete(todo){
                return dialog => this.deleteTodo(todo);
            },
            deleteTodo(todo){
                axios.delete('/api/todo/'+todo.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getTodos();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getStatus(todo){
                return todo.status ? ('<span class="label label-success">'+i18n.todo.complete+'</span>') : ('<span class="label label-danger">'+i18n.todo.incomplete+'</span>') ;
            },
            toggleStatus(todo){
                axios.post('/api/todo/'+todo.id+'/status')
                    .then(response => response.data)
                    .then(response => {
                        this.getTodos();
                        toastr.success(response.message);
                    })
                    .catch(error => {
                        helper.showDataErrorMsg();
                    });
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          }
        },
        watch: {
            filterTodoForm: {
                handler(val){
                    this.getTodos();
                },
                deep: true
            }
        }
    }
</script>

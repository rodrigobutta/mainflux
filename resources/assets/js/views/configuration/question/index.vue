<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('configuration.configuration')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/configuration/basic">{{trans('configuration.configuration')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('task.question')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="question"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <div class="row">
                                    <div class="col-12 col-sm-6 col-md-6">
                                        <h4 class="card-title">{{trans('task.add_new_question_set')}}</h4>
                                        <form @submit.prevent="submit" @keydown="questionForm.errors.clear($event.target.name)">
                                            <div class="form-group">
                                                <label for="">{{trans('task.question_set_name')}}</label>
                                                <input class="form-control" type="text" value="" v-model="questionForm.name" name="name" :placeholder="trans('task.question_set_name')">
                                                <show-error :form-name="questionForm" prop-name="name"></show-error>
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{trans('task.question_set_description')}}</label>
                                                <textarea class="form-control" type="text" value="" v-model="questionForm.description" rows="2" name="description" :placeholder="trans('task.question_set_description')"></textarea>
                                                <show-error :form-name="questionForm" prop-name="description"></show-error>
                                            </div>
                                            <template>
                                                <button type="button" class="btn btn-info waves-effect waves-light pull-right btn-sm" @click="addQuestion" v-tooltip="trans('task.add_new_question')"><i class="fas fa-plus"></i></button>
                                                <h4 class="card-title">{{trans('task.add_new_question')}}</h4>
                                                <div class="form-group" v-for="(question,index) in questionForm.questions">
                                                    <label for="">{{trans('task.question')}} {{index + 1}}</label>
                                                    <button type="button" class="btn btn-danger waves-effect waves-light pull-right btn-sm" :key="index" v-confirm="{ok: confirmRemoveQuestion(question)}" v-tooltip="trans('task.remove_question')"><i class="fas fa-trash"></i></button>
                                                    <input class="form-control" type="text" value="" v-model="question.question" :name="`question_${index}`" :placeholder="trans('task.question')">
                                                    <show-error :form-name="questionForm" :prop-name="`question_${index}`"></show-error>
                                                </div>
                                            </template>
                                            <button type="submit" class="btn btn-info waves-effect waves-light pull-right">
                                                <span>{{trans('general.save')}}</span>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-6">
                                        <h4 class="card-title">{{trans('task.question_set_list')}}</h4>
                                        <h6 class="card-subtitle" v-if="question_sets">{{trans('general.total_result_found',{'count' : question_sets.total})}}</h6>
                                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                                        <div class="table-responsive" v-if="question_sets.total">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{trans('task.question_set_name')}}</th>
                                                        <th class="table-option">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="question_set in question_sets.data">
                                                        <td v-text="question_set.name"></td>
                                                        <td class="table-option">
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".question-set-detail" @click="fetchQuestionSet(question_set)" v-tooltip="trans('task.view_question_set')"><i class="fas fa-arrow-circle-right"></i></button>
                                                                <button class="btn btn-danger btn-sm" :key="question_set.id" v-confirm="{ok: confirmDelete(question_set)}" v-tooltip="trans('question_set.delete_role')"><i class="fas fa-trash"></i></button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <pagination-record :page-length.sync="filterQuestionSetForm.page_length" :records="question_sets" @updateRecords="getQuestionSets" @change.native="getQuestionSets"></pagination-record>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade question-set-detail" tabindex="-1" role="dialog" aria-labelledby="questionSetDetail" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="questionSetDetail">{{trans('task.question_set')}}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body" v-if="question_set">
                        <h4>{{question_set.name}}
                            <span class="pull-right">{{trans('task.created_at')}} {{question_set.created_at | moment}}</span>
                        </h4>
                        <p>{{question_set.description}}</p>
                        <h4>{{trans('task.questions')}}</h4>

                        <ol>
                            <li v-for="question in question_set.questions" v-text="question.question"></li>
                        </ol>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">{{trans('general.close')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import configurationSidebar from '../system-config-sidebar'

    export default {
        components : { configurationSidebar },
        data() {
            return {
                questionForm: new Form({
                    name: '',
                    description: '',
                    questions: []
                }),
                question_sets: {
                    total: 0,
                    data: []
                },
                filterQuestionSetForm: {
                    page_length: helper.getConfig('page_length')
                },
                question_set: {}
            };
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            this.addQuestion();
            this.getQuestionSets();
        },
        methods: {
            addQuestion(){
                this.questionForm.questions.push({
                    question: ''
                })
            },
            confirmRemoveQuestion(question){
                return dialog => this.removeQuestion(question);
            },
            removeQuestion(question){
                let idx = this.questionForm.questions.indexOf(question);
                if(idx > -1)
                    this.questionForm.questions.splice(idx, 1);
            },
            getQuestionSets(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterQuestionSetForm);
                axios.get('/api/question-set?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.question_sets = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            submit(){
                this.questionForm.post('/api/question-set')
                    .then(response => {
                        toastr.success(response.message);
                        this.getQuestionSets();
                        this.questionForm.questions = [];
                        this.addQuestion();
                    })
                    .catch(error => {
                        console.log(error);
                        helper.showErrorMsg(error);
                    })
            },
            confirmDelete(question_set){
                return dialog => this.deleteQuestionSet(question_set);
            },
            deleteQuestionSet(question_set){
                axios.delete('/api/question-set/'+question_set.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getQuestionSets();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            fetchQuestionSet(question_set){
                axios.get('/api/question-set/'+question_set.id)
                    .then(response => response.data)
                    .then(response => {
                        this.question_set = response;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        filters: {
          moment(date) {
            return helper.formatDateTime(date);
          }
        }
    }
</script>

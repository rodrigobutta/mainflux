<template>
    <div>
        <p class="alert alert-info"><i class="fas fa-exclamation-triangle"></i> {{trans('task.tip_task_note')}}</p>

        <button class="pull-right btn btn-sm btn-rounded btn-success" data-toggle="modal" data-target=".task-note-form">{{trans('task.add_new_note')}}</button>

        <task-note-form :uuid="uuid" :nuuid="editNoteUuid" @completed="getNotes" @loaded="reset"></task-note-form>

        <task-note-detail :uuid="uuid" :nuuid="showNoteUuid" @loaded="reset"></task-note-detail>

        <h4 class="card-title m-t-20">{{trans('task.task_note_list')}}</h4>
        <h6 class="card-subtitle" v-if="task_notes">{{trans('general.total_result_found',{'count' : task_notes.total})}}</h6>
        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
        <div class="table-responsive" v-if="task_notes.total">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{trans('task.task_note_title')}}</th>
                        <th>{{trans('task.visibility')}}</th>
                        <th>{{trans('task.owner')}}</th>
                        <th>{{trans('task.created_at')}}</th>
                        <th class="table-option">{{trans('general.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="task_note in task_notes.data">
                        <td v-text="task_note.title"></td>
                        <td v-html="getNoteVisibility(task_note)"></td>
                        <td>{{task_note.user.profile.first_name+' '+task_note.user.profile.last_name}}</td>
                        <td>{{task_note.created_at | momentDateTime}}</td>
                        <td class="table-option">
                            <div class="btn-group">
                                <button class="btn btn-success btn-sm" v-tooltip="trans('task.view_task_note')" data-toggle="modal" data-target=".task-note-detail" @click="showNote(task_note)"><i class="fas fa-arrow-circle-right"></i></button>
                                <template v-if="task_note.user_id === getAuthUser('id')">
                                    <button class="btn btn-info btn-sm" v-tooltip="trans('task.edit_task_note')" data-toggle="modal" data-target=".task-note-form" @click="editNote(task_note)"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="btn btn-danger btn-sm" :key="task_note.id" v-confirm="{ok: ConfirmDeleteNote(task_note)}" v-tooltip="trans('task.delete_task_note')"><i class="fas fa-trash"></i></button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination-record :page-length.sync="filterNoteForm.page_length" :records="task_notes" @updateRecords="getNotes" @change.native="getNotes"></pagination-record>
    </div>
</template>

<script>
    import taskNoteForm from './task-note-form'
    import taskNoteDetail from './task-note-detail'

    export default {
        components: {taskNoteForm,taskNoteDetail},
        data(){
            return {
                task_notes: {},
                filterNoteForm: {
                    sortBy : 'created_at',
                    order: 'desc',
                    page_length: helper.getConfig('page_length')
                },
                showNoteUuid: '',
                editNoteUuid: ''
            }
        },
        props: ['uuid'],
        mounted(){
            this.getNotes();
        },
        methods: {
            getNotes(page){
                if (typeof page === 'undefined') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterNoteForm);
                axios.get('/api/task/'+this.uuid+'/note?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.task_notes = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
                this.nuuid = '';
            },
            ConfirmDeleteNote(task_note){
                return dialog => this.deleteNote(task_note);
            },
            deleteNote(task_note){
                axios.delete('/api/task/'+this.uuid+'/note/'+task_note.uuid)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getNotes();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            getNoteVisibility(task_note){
                if(task_note.is_public)
                    return '<span class="label label-success">'+i18n.task.shared+'</span>';
                else
                    return '<span class="label label-info">'+i18n.task.private+'</span>';
            },
            showNote(task_note){
                this.showNoteUuid = task_note.uuid;
            },
            editNote(task_note){
                this.editNoteUuid = task_note.uuid;
            },
            reset(){
                this.showNoteUuid = '';
                this.editNoteUuid = '';
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          },
          momentDateTime(datetime) {
            return helper.formatDateTime(datetime);
          }
        },
        watch: {

        }
    }
</script>

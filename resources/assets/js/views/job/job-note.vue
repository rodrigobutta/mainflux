<template>
    <div>
        <p class="alert alert-info"><i class="fas fa-exclamation-triangle"></i> {{trans('job.tip_job_note')}}</p>

        <button class="pull-right btn btn-sm btn-rounded btn-success" data-toggle="modal" data-target=".job-note-form">{{trans('job.add_new_note')}}</button>

        <job-note-form :uuid="uuid" :nuuid="editNoteUuid" @completed="getNotes" @loaded="reset"></job-note-form>

        <job-note-detail :uuid="uuid" :nuuid="showNoteUuid" @loaded="reset"></job-note-detail>

        <h4 class="card-title m-t-20">{{trans('job.job_note_list')}}</h4>
        <h6 class="card-subtitle" v-if="job_notes">{{trans('general.total_result_found',{'count' : job_notes.total})}}</h6>
        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
        <div class="table-responsive" v-if="job_notes.total">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{trans('job.job_note_title')}}</th>
                        <th>{{trans('job.visibility')}}</th>
                        <th>{{trans('job.owner')}}</th>
                        <th>{{trans('job.created_at')}}</th>
                        <th class="table-option">{{trans('general.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="job_note in job_notes.data">
                        <td v-text="job_note.title"></td>
                        <td v-html="getNoteVisibility(job_note)"></td>
                        <td>{{job_note.user.profile.first_name+' '+job_note.user.profile.last_name}}</td>
                        <td>{{job_note.created_at | momentDateTime}}</td>
                        <td class="table-option">
                            <div class="btn-group">
                                <button class="btn btn-success btn-sm" v-tooltip="trans('job.view_job_note')" data-toggle="modal" data-target=".job-note-detail" @click="showNote(job_note)"><i class="fas fa-arrow-circle-right"></i></button>
                                <template v-if="job_note.user_id === getAuthUser('id')">
                                    <button class="btn btn-info btn-sm" v-tooltip="trans('job.edit_job_note')" data-toggle="modal" data-target=".job-note-form" @click="editNote(job_note)"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="btn btn-danger btn-sm" :key="job_note.id" v-confirm="{ok: ConfirmDeleteNote(job_note)}" v-tooltip="trans('job.delete_job_note')"><i class="fas fa-trash"></i></button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination-record :page-length.sync="filterNoteForm.page_length" :records="job_notes" @updateRecords="getNotes" @change.native="getNotes"></pagination-record>
    </div>
</template>

<script>
    import jobNoteForm from './job-note-form'
    import jobNoteDetail from './job-note-detail'

    export default {
        components: {jobNoteForm,jobNoteDetail},
        data(){
            return {
                job_notes: {},
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
                axios.get('/api/job/'+this.uuid+'/note?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.job_notes = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
                this.nuuid = '';
            },
            ConfirmDeleteNote(job_note){
                return dialog => this.deleteNote(job_note);
            },
            deleteNote(job_note){
                axios.delete('/api/job/'+this.uuid+'/note/'+job_note.uuid)
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
            getNoteVisibility(job_note){
                if(job_note.is_public)
                    return '<span class="label label-success">'+i18n.job.shared+'</span>';
                else
                    return '<span class="label label-info">'+i18n.job.private+'</span>';
            },
            showNote(job_note){
                this.showNoteUuid = job_note.uuid;
            },
            editNote(job_note){
                this.editNoteUuid = job_note.uuid;
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

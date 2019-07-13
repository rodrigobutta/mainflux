<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('message.message')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/message">{{trans('message.message')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('message.draft')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <message-sidebar menu="draft" :statistics="statistics"></message-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">

                                <h4 class="card-title">{{trans('message.draft')}}</h4>
                                <h6 class="card-subtitle" v-if="messages">{{trans('general.total_result_found',{'count' : messages.total})}}</h6>
                                <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                                <div class="table-responsive">
                                    <table class="table" v-if="messages.total">
                                        <thead>
                                            <tr>
                                                <th>{{trans('message.recipient')}}</th>
                                                <th>{{trans('message.subject')}}</th>
                                                <th></th>
                                                <th>{{trans('message.drafted_at')}}</th>
                                                <th class="table-option">{{trans('general.action')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="draft in messages.data">
                                                <td>
                                                    <span v-if="draft.to_user_id">
                                                        {{draft.user_to.email}}
                                                    </span>
                                                </td>
                                                <td v-text="draft.subject"></td>
                                                <td><i class="fas fa-paperclip" v-if="draft.has_attachment"></i></td>
                                                <td>{{ draft.updated_at | momentDateTime }}</td>
                                                <td class="table-option">
                                                    <div class="btn-group">
                                                        <router-link :to="`/message/${draft.uuid}/draft`" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></router-link>
                                                        <button class="btn btn-danger btn-sm" :key="draft.id" v-confirm="{ok: confirmDelete(draft)}" v-tooltip="trans('message.delete_draft')"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <pagination-record :page-length.sync="filterMessageForm.page_length" :records="messages" @updateRecords="getMessages" @change.native="getMessages"></pagination-record>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import messageSidebar from './message-sidebar'

    export default {
        components : { messageSidebar },
        data() {
            return {
                messages: {
                    total: 0,
                    data: []
                },
                filterMessageForm: {
                    page_length: helper.getConfig('page_length')
                },
                statistics: {
                    sent: 0,
                    inbox: 0,
                    draft: 0,
                    trash: 0
                }
            };
        },
        mounted(){
            if(!helper.hasPermission('access-message')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('message')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            this.getMessages();
            this.getStatistics();
        },
        methods: {
            getStatistics(){
                axios.post('/api/message/statistics')
                    .then(response => response.data)
                    .then(response => {
                        this.statistics.inbox = response.inbox;
                        this.statistics.sent = response.sent;
                        this.statistics.draft = response.draft;
                        this.statistics.trash = response.trash;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getMessages(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterMessageForm);
                axios.get('/api/message/draft?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.messages = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            confirmDelete(draft){
                return dialog => this.deleteDraft(draft);
            },
            deleteDraft(draft){
                axios.delete('/api/message/'+draft.uuid+'/draft')
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.statistics.draft -= 1;
                        this.getMessages();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        filters: {
          momentDateTime(date) {
            return helper.formatDateTime(date);
          }
        }
    }
</script>

<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('mail.email_log')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('mail.email_log')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('mail.email_log')}}</h4>
                        <h6 class="card-subtitle" v-if="email_logs">{{trans('general.total_result_found',{'count' : email_logs.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="email_logs.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('mail.receiver')}}</th>
                                        <th>{{trans('mail.subject')}}</th>
                                        <th>{{trans('mail.sent_at')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="email_log in email_logs.data">
                                        <td v-text="email_log.to_address"></td>
                                        <td v-text="email_log.subject"></td>
                                        <td>{{email_log.created_at | moment }}</td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".email-log-detail" @click="fetchEmailLog(email_log)" v-tooltip="trans('mail.view')"><i class="fas fa-arrow-circle-right"></i></button>
                                                <button class="btn btn-danger btn-sm" :key="email_log.id" v-confirm="{ok: confirmDelete(email_log)}" v-tooltip="trans('general.delete')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!email_logs.total" module="mail" title="module_info_email_log_title" description="module_info_email_log_description" icon="envelope"></module-info>
                        <pagination-record :page-length.sync="filterEmailLogForm.page_length" :records="email_logs" @updateRecords="getEmailLogs" @change.native="getEmailLogs"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade email-log-detail" tabindex="-1" role="dialog" aria-labelledby="emailLogDetail" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="emailLogDetail">{{trans('mail.email')}}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body" v-if="email_log">
                        <h4>{{email_log.subject}}
                            <span class="pull-right">{{email_log.created_at | moment}}</span>
                        </h4>
                        <p>{{trans('mail.sender')+': '+email_log.from_address}}</p>
                        <p>{{trans('mail.receiver')+': '+email_log.to_address}}</p>
                        <div v-html="email_log.body"></div>
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
    export default {
        components: {},
        data(){
            return {
                email_logs: {
                    total: 0,
                    data: []
                },
                filterEmailLogForm: {
                    page_length: helper.getConfig('page_length')
                },
                email_log: {}
            };
        },
        mounted(){
            if(!helper.featureAvailable('email_log')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            this.getEmailLogs();
        },
        methods: {
            getEmailLogs(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterEmailLogForm);
                axios.get('/api/email-log?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.email_logs = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            fetchEmailLog(email_log){
                axios.get('/api/email-log/'+email_log.id)
                    .then(response => response.data)
                    .then(response => {
                        this.email_log = response;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            confirmDelete(email_log){
                return dialog => this.deleteEmailLog(email_log);
            },
            deleteEmailLog(email_log){
                axios.delete('/api/email-log/'+email_log.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getEmailLogs();
                    }).catch(error => {
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
